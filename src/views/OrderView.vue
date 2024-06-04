<script setup>
import store from '@/store';
import { computed, ref, reactive } from 'vue';
import apiClient from '@/components/services/api';
import router from '@/router';

import FoodComponent from '@/components/FoodComponent.vue'

store.dispatch('getMenuData')

const tableInfo = reactive({
    code: "",
    id: ""
})

const codeModalShow = computed(() => tableInfo.id == "")

const cartShow = ref(false)

const category_select = ref(0)

const orderInput = ref({})

const menus = computed(() => {
    const data = store.getters.getMenus
    data.forEach(item => {
        orderInput.value[item.menuID] = { amount: 0, name: item.menuName, total: computed(() => orderInput.value[item.menuID]['amount'] * item.price) }
    })
    return data
})

const orderDisplay = computed(() => {
    const data = {}
    Object.keys(orderInput.value).forEach(id => {
        if (orderInput.value[id]['amount'] > 0) data[id] = orderInput.value[id]
    })
    return data
})

const orderAmount = computed(() => {
    let count = 0
    menus.value.forEach(item => {
        count += orderInput.value[item.menuID]['amount'] > 0 ? 1 : 0
    })
    return count
})

const menuDisplay = computed(() => menus.value.filter(item => {
    if (category_select.value != 0)
        return item.categoryName == category_select.value
    else return true
}
))
const categories = computed(() => store.getters.getCategories)

const increment = (id) => {
    orderInput.value[id]['amount'] += 1
    if (orderInput.value[id]['amount'] > 99) orderInput.value[id]['amount'] = 99
}

const decrement = (id) => {
    orderInput.value[id]['amount'] -= 1
    if (orderInput.value[id]['amount'] < 0) orderInput.value[id]['amount'] = 0
}

const enterCode = () => {
    apiClient.post('/api/member/enterCode.php', { token: store.state.token, code: tableInfo.code }).then(response => {
        if (response.data.status == 0) {
            tableInfo.id = response.data.result
        }
        store.commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

    }).catch(error => {
        router.push({ name: 'errorPage', params: { error: error } })
    })
}

const enterOrder = () => {
    apiClient.post('/api/member/enterOrder.php', { token: store.state.token, code: tableInfo.code, orders: orderDisplay.value }).then(response => {
        console.log(response.data)
        if (response.data.status == 0) {
            tableInfo.id = response.data.result
        }
        store.commit("ADD_NOTIFY", { message: response.data.message, status: response.data.status })

    }).catch(error => {
        router.push({ name: 'errorPage', params: { error: error } })
    })
}
</script>

<template>
    <section class="md:m-16 m-8">

        <h1 class="text-3xl text-center p-6">Table: {{ tableInfo.id == "" ? '...' : tableInfo.id }}</h1>
        <!-- Cart Icon -->
        <div class="relative" @click="cartShow = !cartShow">
            <div class="absolute top-0 -mt-6 -ml-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </div>
            <div class="absolute top-0 -mt-9 -ml-1 bg-rose-300 rounded-full px-2">
                <span class="text-xs">{{ orderAmount }}</span>
            </div>

            <!-- Cart modal -->
            <transition name="fade">
                <div class="absolute top-0 bg-neutral-700 p-3 px-5 -ml-5 mt-2 z-10 min-w-[400px] rounded-lg"
                    v-show="cartShow">
                    <div class="flex flex-col gap-2">
                        <div class="grid grid-cols-3 gap-1 justify-between border-b">
                            <p class="place-self-start">MenuName</p>
                            <p class="place-self-center">Amount</p>
                            <p class="place-self-end">Total</p>
                        </div>
                        <div class="bg-neutral-700/80" v-for="(orderMenu, key) in orderDisplay" :key="key">
                            <div class="grid grid-cols-3 gap-1 justify-between">
                                <p class="place-self-start">{{ orderMenu.name }}</p>
                                <p class="place-self-center">{{ orderInput[key]['amount'] }}</p>
                                <p class="place-self-end">{{ orderInput[key]['total'] }}</p>
                            </div>
                        </div>
                        <div class="flex-auto" v-if="Object.keys(orderDisplay).length > 0">
                            <button class="bg-blue-400 hover:bg-blue-400/85 py-1 size-full rounded-lg" @click="enterOrder">Order</button>
                        </div>
                    </div>

                </div>
            </transition>
        </div>

        <!-- Code Input Modal -->
        <div class="fixed bg-black/50 w-[100%] h-[100%] z-40 top-0 left-0 right-0" v-show="codeModalShow">
            <div class="w-2/3 my-80 bg-neutral-100 text-black m-auto rounded-md min-w-[350px] max-w-[600px]">
                <form @submit.prevent="enterCode">
                    <div class="flex flex-col gap-1 p-6">
                        <h1 class="text-3xl font-bold p-1 text-center">Please enter code here</h1>
                        <input type="text" class="p-3 text-center border-none focus:outline-none rounded"
                            placeholder="CODE" v-model.trim="tableInfo.code">
                        <button @keypress.enter="enterCode"
                            class="bg-teal-400 mt-2 hover:bg-teal-500/85 py-2 size-full rounded-lg text-white">Enter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex flex-col gap-12" @click="cartShow = false">
            <div
                class="md:flex md:flex-row md:flex-wrap grid sm:grid-cols-3 grid-cols-2 gap-3 m-auto justify-center items-center ">
                <div class="bg-neutral-700/40 rounded-lg py-2 px-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                    @click="category_select = 0" :class="{ 'opacity-50': category_select != 0 }">
                    <div class="flex flex-col">
                        <div class="flex flex-row gap-1 items-center size-fit m-auto">
                            <h1 class="text-[1.25rem] text-center text-sm text-neutral-100">All</h1>
                        </div>
                    </div>
                </div>

                <div v-for="category in categories" :key="category.categoryID"
                    class="bg-neutral-700/40 rounded-lg py-2 px-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                    @click="category_select = category.name"
                    :class="{ 'opacity-50': category_select != category.name }">
                    <div class="flex flex-col">
                        <div class="flex flex-row gap-1 items-center size-fit m-auto">
                            <h1 class="text-[1.25rem] text-center text-sm text-neutral-100">{{ category.name }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-row gap-3 flex-wrap justify-center">

                <FoodComponent v-for="menu in menuDisplay" :key="menu.menuID" :menu="menu" @increment="increment"
                    @decrement="decrement">
                    <span>{{ orderInput[menu.menuID]['amount'] }}</span>
                </FoodComponent>
            </div>
        </div>

    </section>
</template>

<style></style>