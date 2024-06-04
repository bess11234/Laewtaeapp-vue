export const state = {
    orderFoods: [
        
    ],
    orderServe: [

    ]
}

export const mutations = {
    SET_ORDERFODDS(state, orderFoods=[]) {
        state.orderFoods = orderFoods
    },

    SET_ORDERSERVE(state, orderServe=[]) {
        state.orderServe = orderServe
    },
}

export const getters = {
    getOrderFoods(state) {
        return state.orderFoods
    },
    
    getOrderServe(state){
        return state.orderServe
    }
}