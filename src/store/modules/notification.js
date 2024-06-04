export const state = {
    notify: [
        //{ message: "",status: null }
    ]
}

export const mutations = {
    SET_NOTIFY(state, notify=[]) {
        state.notify = notify
    },

    ADD_NOTIFY(state, noti = { message: "", status: null }) {
        state.notify.push(noti)
    },
}

export const getters = {
    getNotification(state) {
        return state.notify
    }
}