export const state = {
    gacha: [],
}

export const mutations = {
    SET_GACHA(state, gacha=[]){
        state.gacha = gacha
    },
}

export const getters = {
    getGacha(state){
        return state.gacha
    },
}