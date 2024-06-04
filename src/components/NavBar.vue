<script setup>
import { ref, reactive, computed } from 'vue';
import { RouterLink, useRoute } from 'vue-router'

import iconFavicon from '../assets/icons/favicon.svg'

import Modal from './Modal.vue';
import validateEmail from './services/validEmail';
import store from '@/store';

const $route = useRoute()
let loginShow = ref(false)
let navbarShow = ref(false)

let validEmail = ref(true)
let wrongPassword = ref(false)

let profileShow = ref(false)

const isCredential = computed(() => {
    return store.getters.isCredential
});

const userInfo = computed(() => {
    return store.getters.getUserInfo
})

const showLogin = () => {
    loginShow.value = !loginShow.value
}
const userLogin = reactive({
    email: '',
    password: '',
})
const resetUserLogin = () => {
    userLogin.email = ''
    userLogin.password = ''
}
const login = () => {
    if (!validateEmail(userLogin.email)) {
        validEmail.value = false
        resetUserLogin()
        return
    }
    validEmail.value = true
    store.dispatch('credential', userLogin)

    setTimeout(() => {
        store.dispatch("isValidToken")
    }, 750)

    // // เมื่อใส่รหัสผ่านผิดจะให้ใส่ใหม่
    // const noti = store.getters.getNotification
    // if (message[0]['message'] == 'Wrong Password') {
    //     userLogin.password = ''
    //     wrongPassword.value = true
    // } else {
    //     wrongPassword.value = false
    // }
    loginShow.value = false
}

const logout = () => {
    userLogin.email = ""
    userLogin.password = ""
    store.dispatch("userLogout")
}

const memberLink = [
    {
        link: "/coupons",
        name: "Coupons"
    },
    {
        link: '/gachabox',
        name: "Gacha box"
    }
]

const managerLink = [
    {
        link: "/manage/tables",
        name: "Tables"
    },
    {
        link: '/manage/menus',
        name: "Menus"
    },
    {
        link: '/manage/members',
        name: "Members"
    },
    {
        link: '/manage/coupons',
        name: "Coupons"
    },
    {
        link: '/manage/gacha',
        name: "Gacha"
    },
    {
        link: '/manage/bills',
        name: "Bills"
    }
]

const staffLink = [
    {
        link: "/staff/tables",
        name: "Tables"
    },
    {
        link: '/staff/orders',
        name: "Orders"
    },
]

const resetNotify = () => {
    store.commit("SET_NOTIFY")
}
</script>

<template>
    <header>
        <transition name="slide" appear>
            <nav v-if="true" class="z-20 py-6 px-4 bg-neutral-800/80 border-none shadow drop-shadow-lg select-none">

                <!-- hidden when sm -->
                <div class="sm:flex flex-row place-content-between hidden">
                    <!-- <Left> -->

                    <!-- if route != /orderFoods -->
                    <div class="flex flex-row gap-3 items-center" @click="resetNotify"
                        v-if="$route.fullPath != '/orderFoods'">
                        <RouterLink to="/" class="font-bold">
                            <div class="flex flex-row items-center gap-1">
                                <img class="size-[30px]" :src="iconFavicon" alt="">Home
                            </div>
                        </RouterLink>

                        <!-- Member -->
                        <div class="flex flex-row gap-3 font-light" v-if="isCredential && userInfo.role == 'MEMBER'">
                            <RouterLink v-for="(route, index) in memberLink" :key="index" :to="route.link"
                                class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                                {{ route.name }}</RouterLink>
                        </div>

                        <!-- Manager -->
                        <div class="flex flex-row gap-3 font-light" v-if="isCredential && userInfo.role == 'MANAGER'">
                            <RouterLink v-for="(route, index) in managerLink" :key="index" :to="route.link"
                                class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                                {{ route.name }}</RouterLink>
                        </div>

                        <!-- Staff -->
                        <div class="flex flex-row gap-3 font-light" v-if="isCredential && userInfo.role == 'STAFF'">
                            <RouterLink v-for="(route, index) in staffLink" :key="index" :to="route.link"
                                class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                                {{ route.name }}</RouterLink>
                        </div>

                    </div>

                    <!-- else route == /orderFoods -->
                    <div class="flex flex-row gap-3 items-center" v-else>
                        <RouterLink to="/orderFoods" class="font-bold">
                            <div class="flex flex-row items-center gap-1">
                                <img class="size-[30px]" :src="iconFavicon" alt="">Orders
                            </div>
                        </RouterLink>

                        <button @click="showLogin" v-if="!isCredential"
                            class="transition py-1 hover:scale-105 shadow hover:shadow-sky-400 bg-gradient-to-r from-sky-700 to-blue-400 px-3 rounded-full text-sm">Login</button>
                    </div>

                    <!-- <Right> Not credential -->
                    <div v-if="!isCredential && $route.fullPath != '/orderFoods'" class="flex flex-row gap-3 items-center">
                        <button @click="showLogin"
                            class="transition py-1 hover:scale-105 shadow hover:shadow-sky-400 bg-gradient-to-r from-sky-700 to-blue-400 px-3 rounded-full text-sm">Login</button>
                    </div>

                    <!-- <Right> Credential -->
                    <div v-else-if="$route.fullPath != '/orderFoods'" class="flex flex-row gap-3 items-center">

                        <div class="bg-blue-400/90 text-xs font-bold py-1 px-3 rounded-full"
                            v-if="userInfo.role == 'MEMBER'">
                            <span>Points: {{ userInfo.points }}</span>
                        </div>

                        <div class="relative">
                            <h1 @mouseover="profileShow = true" @mouseout="profileShow = false"
                                class="cursor-pointer hover:bg-neutral-700/40">{{ userInfo.email == '' ? 'Loading...' :
                                    userInfo.email }}</h1>

                            <transition name="slide">
                                <div @mouseover="profileShow = true" @mouseout="profileShow = false"
                                    @click="profileShow = false" v-show="profileShow"
                                    class="absolute float-left min-w-max z-20">
                                    <div class="flex flex-col bg-neutral-700/50 m-1 rounded text-sm font-light">
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded" to="/myCoupon">My
                                            Coupon</RouterLink>
                                        <hr class="opacity-25" />
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded"
                                            to="/profile">Profile</RouterLink>
                                        <hr class="opacity-25" />
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded"
                                            @click="logout" to="">Logout</RouterLink>
                                    </div>
                                    <h1 class="text-transparent">{{ userInfo.email == '' ? 'Loading...' :
                                        userInfo.email }}</h1>
                                </div>
                            </transition>
                        </div>
                    </div>

                </div>

                <!-- Responsive button -->
                <div class="sm:hidden flex flex-row place-content-between">

                    <!-- Left -->
                    <div class="flex flex-row gap-3 items-center" v-if="$route.fullPath != '/orderFoods'">
                        <button @click="navbarShow = !navbarShow">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-row gap-3 items-center" v-else>
                        <RouterLink to="/orderFoods" class="font-bold">
                            <div class="flex flex-row items-center gap-1">
                                <img class="size-[30px]" :src="iconFavicon" alt="">Orders
                            </div>
                        </RouterLink>

                        <button @click="showLogin" v-if="!isCredential"
                            class="transition py-1 hover:scale-105 shadow hover:shadow-sky-400 bg-gradient-to-r from-sky-700 to-blue-400 px-3 rounded-full text-sm">Login</button>
                    </div>

                    <div v-if="!isCredential && $route.fullPath != '/orderFoods'" class="flex flex-row gap-3 items-center">
                        <button @click="showLogin"
                            class="transition py-1 hover:scale-105 shadow hover:shadow-sky-400 bg-gradient-to-r from-sky-700 to-blue-400 px-3 rounded-full text-sm">Login</button>
                    </div>

                    <!-- Right -->
                    <div v-else-if="$route.fullPath != '/orderFoods'" class="flex flex-row gap-3 items-center">
                        <div class="bg-blue-400/90 text-xs font-bold py-1 px-3 rounded-full"
                            v-if="userInfo.role == 'MEMBER'">
                            <span>Points: {{ userInfo.points }}</span>
                        </div>

                        <div>
                            <h1 @click="profileShow = !profileShow" class="cursor-pointer hover:bg-neutral-700/40">{{
                                userInfo.email == '' ? 'Loading...' :
                                    userInfo.email }}</h1>

                            <transition name="slide">
                                <div @click="profileShow = !profileShow" v-show="profileShow"
                                    class="absolute float-left min-w-max">
                                    <div class="flex flex-col bg-neutral-700/50 m-1 rounded text-sm font-light">
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded" to="/myCoupon">My
                                            Coupon</RouterLink>
                                        <hr class="opacity-25" />
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded"
                                            to="/profile">Profile</RouterLink>
                                        <hr class="opacity-25" />
                                        <RouterLink class="hover:bg-neutral-700 text-right p-1 py-2 rounded"
                                            @click="logout" to="">Logout</RouterLink>
                                    </div>
                                    <h1 class="text-transparent">{{ userInfo.email == '' ? 'Loading...' :
                                        userInfo.email }}</h1>
                                </div>
                            </transition>
                        </div>
                    </div>

                </div>

            </nav>

        </transition>


        <!-- NavbarShow -->
        <transition name="slide">
            <div v-show="navbarShow" class="absolute w-full bg-neutral-900/90 z-40">
                <div class="flex flex-col gap-2 p-3" @click="navbarShow = false">

                    <RouterLink to="/" class="font-bold">Home</RouterLink>

                    <!-- Member -->
                    <div class="flex flex-col gap-2 font-light" v-if="isCredential && userInfo.role == 'MEMBER'">
                        <RouterLink v-for="(route, index) in memberLink" :key="index" :to="route.link"
                            class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                            {{ route.name }}</RouterLink>
                    </div>

                    <!-- Manager -->
                    <div class="flex flex-col gap-2 font-light" v-if="isCredential && userInfo.role == 'MANAGER'">
                        <RouterLink v-for="(route, index) in managerLink" :key="index" :to="route.link"
                            class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                            {{ route.name }}</RouterLink>
                    </div>

                    <!-- Staff -->
                    <div class="flex flex-col gap-2 font-light" v-if="isCredential && userInfo.role == 'STAFF'">
                        <RouterLink v-for="(route, index) in staffLink" :key="index" :to="route.link"
                            class="hover:text-blue-400" :class="{ 'text-blue-400': $route.fullPath == route.link }">
                            {{ route.name }}</RouterLink>
                    </div>

                </div>
            </div>
        </transition>

        <!-- Login Modal -->
        <transition name="slide">
            <Modal v-show="loginShow && !isCredential" @showLogin="showLogin">
                <form @submit.prevent="login">
                    <div class="p-3">
                        <h1 class="text-3xl py-6 text-center font-extrabold">Login</h1>
                        <div class="flex flex-col m-1 gap-3">

                            <div class="flex flex-col m-2">
                                <input class="border-2 px-5 py-2 rounded-full outline-none focus:border-sky-300"
                                    type="email" v-model.trim="userLogin.email" name="email" id="email"
                                    placeholder="Email">
                                <span v-show="!validEmail"
                                    class="text-sm opacity-50 pt-2 pl-4 font-mono text-red-800">Invalid Email</span>
                            </div>

                            <div class="flex flex-col m-2">
                                <input class="border-2 px-5 py-2 rounded-full outline-none focus:border-sky-300"
                                    v-model="userLogin.password" type="password" name="password" id="password"
                                    placeholder="Password">
                                <span v-show="wrongPassword"
                                    class="text-sm opacity-50 pt-2 pl-4 font-mono text-red-800">Wrong Password</span>
                            </div>

                            <button type="submit"
                                class="m-3 p-3 bg-gradient-to-r from-teal-400 to-green-200 rounded-full font-bold text-white">Login</button>
                            <div class="m-1"></div>
                        </div>
                    </div>
                </form>
            </Modal>
        </transition>

    </header>
</template>
