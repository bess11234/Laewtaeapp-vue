export const state = {
    members: [],
}

export const mutations = {
    SET_MEMBERS(state, members=[]){
        state.members = members
    },
}

export const getters = {
    getMembers(state){
        return state.members
    },
}