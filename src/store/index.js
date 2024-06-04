import { createStore } from "vuex"
import router from "@/router"
import * as notification from '@/store/modules/notification.js'
import * as tables from '@/store/modules/tables.js'
import * as menus from '@/store/modules/menus.js'
import * as members from '@/store/modules/members.js'
import * as gacha from '@/store/modules/gacha.js'
import * as bills from '@/store/modules/bills.js'
import * as orders from '@/store/modules/orders.js'
import * as coupons from '@/store/modules/coupons.js'

import apiClient from "@/components/services/api"

const store = createStore({
    modules: {
        notification,
        tables,
        menus,
        members,
        gacha,
        bills,
        orders,
        coupons,
    },
    state: {
        token: "",
        key: "",
        user: {
            phoneNumber: "",
            memberName: "",
            email: "",
            points: "",
            role: ""
        },
        mycoupons: []
    },
    mutations: {
        SET_TOKEN(state, token = { jwt: "", key: state.key }) {
            state.token = token.jwt
            state.key = token.key
            localStorage.setItem('token', token.jwt)
            localStorage.setItem('key', token.key)
        },

        SET_USER(state, user = {
            phoneNumber: "",
            memberName: "",
            email: "",
            points: "",
            role: ""
        },
        ) {
            state.user.phoneNumber = user.phoneNumber
            state.user.memberName = user.memberName
            state.user.email = user.email
            state.user.points = user.points
            state.user.role = user.role
        },

        SET_MYCOUPON(state, mycoupons=[]){
            state.mycoupons = mycoupons
        }

    },
    actions: {
        credential({ commit }, user) {
            apiClient.post("/api/credential.php", user).then(response => {
                if (response.data.status == 0) {
                    commit('SET_TOKEN', response.data.result)
                }
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
                location.reload()
                // console.log(response.status) อาจได้ใช้
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        isValidToken({ commit, state }) {
            apiClient.post("/api/isValidToken.php", { token: state.token, key: state.key }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_USER", response.data.result)
                } else {
                    commit("SET_TOKEN")
                    commit("SET_USER")
                    commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        getUserInfo({ commit, state }) {
            apiClient.post("/api/member/getUserInfo.php", { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_USER", response.data.result)
                } else {
                    commit("SET_TOKEN")
                    commit("SET_USER")
                    commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        userLogout({ commit }) {
            commit("SET_TOKEN", "")
            commit("SET_USER")
            commit("ADD_NOTIFY", { message: "Logout Successful", status: 0 })
            router.push({ name: 'home' })
        },

        userBuyCoupon({ commit, state }, coupon) {
            apiClient.post('/api/member/buyCoupon.php', { token: state.token, key: state.key, coupon }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        myCoupon({ commit, state }) {
            apiClient.post('/api/member/myCoupons.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    // Make all coupons have Information about menus through ID
                    const menus = JSON.parse(JSON.stringify(state.menus.menus))
                    response.data.result.map(item => {
                        item['menu'] = menus.filter(menu => {
                            return menu['menuID'] == item['menuID']
                        })[0]
                    })
                    commit("SET_MYCOUPON", response.data.result)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        getCoupons({ commit, state }) {
            apiClient.post('/api/member/getCoupons.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {

                    // Make all coupons have Information about menus through ID
                    const menus = JSON.parse(JSON.stringify(state.menus.menus))
                    response.data.result.map(item => {
                        item['menu'] = menus.filter(menu => {
                            return menu['menuID'] == item['menuID']
                        })[0]
                        delete item['menu']['menuID']
                    })
                    commit("SET_COUPONS", response.data.result)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addCoupon({ commit, state }, coupon) {
            apiClient.post('/api/manager/addCoupon.php', { token: state.token, coupon }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        // Manager || Staff
        //// Tables
        getTablesData({ commit, state }) {
            apiClient.post('/api/manager/getTables.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_TABLES", response.data.result)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addTable({ commit, state }, capacity) {
            apiClient.post('/api/manager/addTable.php', { token: state.token, capacity: capacity }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_TABLES", response.data.result)
                }
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Menus
        getMenuData({ commit, state }) {
            apiClient.post('/api/manager/getMenus.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_MENUS", response.data.result.menus)
                    commit("SET_CATEGORIES", response.data.result.categories)
                }
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addMenu({ commit, state }, menu) {
            apiClient.post('/api/manager/addMenu.php', { token: state.token, menu: menu }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addCategory({ commit, state }, category) {
            apiClient.post('/api/manager/addCategory.php', { token: state.token, name: category }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateMenu({ commit, state }, menu) {
            apiClient.post('/api/manager/updateMenu.php', { token: state.token, menu: menu }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        deleteMenu({ commit, state }, menuID) {
            apiClient.post('/api/manager/deleteMenu.php', { token: state.token, menuID: menuID }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Members
        getMembers({ commit, state }) {
            apiClient.post('/api/manager/getMembers.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_MEMBERS", response.data.result)
                }
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateStatus({ commit, state }, user) {
            apiClient.post('/api/manager/updateStatusUser.php', { token: state.token, user: user }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateRole({ commit, state }, user) {
            apiClient.post('/api/manager/updateRoleUser.php', { token: state.token, user: user }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Gacha
        getGachaData({ commit, state }) {
            apiClient.post('/api/manager/getGacha.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    const gacha_ban = response.data.result['gacha_banner']
                    const gacha_item = response.data.result['gacha_item']
                    gacha_item.map(item => {
                        item['menu'] = store.getters.getMenus.filter(item2 => {
                            if (item['menuID'] == item2['menuID']) {
                                return item2
                            }
                        })[0]
                    })

                    const rank_rarity = { 'MYTHIC': 6, 'LEGENDARY': 5, 'EPIC': 4, 'RARE': 3, 'UNCOMMON': 2, 'COMMON': 1 }
                    gacha_ban.map(item => {
                        item['menus'] = gacha_item.filter(item2 => {

                            item2['rank_rarity'] = rank_rarity[item2['rarity']]
                            item2['discount_price'] = item2['menu']['price'] - (item2['menu']['price'] * Number(item2['discount']))

                            if (item2['gachaID'] == item['gachaID']) {
                                return item2
                            }
                        })
                    })
                    commit("SET_GACHA", gacha_ban)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        buyGacha({ commit, state }, gacha) {
            apiClient.post('/api/member/buyGacha.php', { token: state.token, gacha }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addGacha({ commit, state }, gacha) {
            apiClient.post('/api/manager/addGacha.php', { token: state.token, gacha }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateGacha({ commit, state }, gacha) {
            apiClient.post('/api/manager/updateGacha.php', { token: state.token, gacha: gacha }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        deleteGacha({ commit, state }, gachaID) {
            apiClient.post('/api/manager/deleteGacha.php', { token: state.token, gachaID }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        addGachaItem({ commit, state }, item) {
            apiClient.post('/api/manager/addGachaItem.php', { token: state.token, item }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateGachaRarity({ commit, state }, menu) {
            apiClient.post('/api/manager/updateGachaRarity.php', { token: state.token, menu: menu }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        updateGachaDiscount({ commit, state }, menu) {
            apiClient.post('/api/manager/updateGachaDiscount.php', { token: state.token, menu: menu }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })
            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Bills
        getBillsData({ commit, state }) {
            apiClient.post('/api/manager/getBills.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_BILLS", response.data.result)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        // Staff
        // Tables Code
        randomCodeTable({ commit, state }, tableID) {
            apiClient.post('/api/staff/randomCodeTable.php', { token: state.token, tableID }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        getOrders({ commit, state }) {
            apiClient.post('/api/staff/getOrders.php', { token: state.token }).then(response => {
                if (response.data.status == 0) {
                    commit("SET_ORDERFODDS", response.data.result.orderFoods)
                    commit("SET_ORDERSERVE", response.data.result.orderServe)
                }

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Chef
        completeOrder({ commit, state }, order) {
            apiClient.post('/api/staff/chefCompleteOrder.php', { token: state.token, order }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        //// Waiter
        serveOrder({ commit, state }, order) {
            apiClient.post('/api/staff/waiterCompleteOrder.php', { token: state.token, order }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        },

        checkBill({ commit, state }, bill) {
            apiClient.post('/api/staff/checkBill.php', { token: state.token, bill }).then(response => {
                commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

            }).catch(error => {
                router.push({ name: 'errorPage', params: { error: error } })
            })
        }

    },
    getters: {
        isCredential(state) {
            return !state.token == ''
        },
        getUserInfo(state) {
            return state.user
        },
        getMyCoupon(state){
            return state.mycoupons
        }
    }
})

export default store