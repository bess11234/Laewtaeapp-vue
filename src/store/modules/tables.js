export const state = {
    tables: []
}

export const mutations = {
    SET_TABLES(state, tables = []) {
        state.tables = tables
    }
}

export const getters = {
    getTables(state){
        return state.tables
    }
}