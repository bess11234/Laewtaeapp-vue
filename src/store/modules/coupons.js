export const states = {
    coupons: []
}

export const mutations = {
    SET_COUPONS(state, coupons=[]){
        state.coupons = coupons
    }
}

export const getters = {
    getCoupons(state){
        return state.coupons
    }
}