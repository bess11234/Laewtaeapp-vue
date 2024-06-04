export const state = {
    bills: [],
}

export const mutations = {
    SET_BILLS(state, bills=[]){
        state.bills = bills
    },
}

export const getters = {
    getBills(state){
        return state.bills
    },
}