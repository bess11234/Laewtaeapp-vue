export const state = {
    menus: [],
    menuCategories: []
}

export const mutations = {
    SET_MENUS(state, menus=[]){
        state.menus = menus
    },
    SET_CATEGORIES(state, categories=[]){
        state.menuCategories = categories
    }
}

export const getters = {
    getMenus(state){
        return state.menus
    },
    getCategories(state){
        return state.menuCategories
    }
}