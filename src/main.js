import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

const app = createApp(App)

if (localStorage.getItem('token') != undefined && localStorage.getItem('token') != ''){
    store.commit("SET_TOKEN", {jwt: localStorage.getItem('token'), key: localStorage.getItem('key')})
    setTimeout(()=>{
        store.dispatch("isValidToken")
    }, 250)
}
app.use(store).use(router)

app.mount('#app')
