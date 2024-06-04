<script setup>
const rank_discount_coupon = { '50%': 1, '40%': 2, '30%': 3, '20%': 4, '15%': 5,'10%': 6, '5%': 7, '1%': 8 }
const discount_display = { 0.5: '50%', 0.4: '40%', 0.3: '30%', 0.2: '20%', 0.1: '10%', 0.05: '5%', 0.01: '1%' }

import store from '@/store';
import { computed, ref } from 'vue';

import CouponComponent from '@/components/member/CouponComponent.vue'

store.dispatch('getMenuData')
store.dispatch("getCoupons")

const coupons = computed(() => store.getters.getCoupons)
const category_select = ref(0)

const coupon_categories = computed(() => {
    let data = new Set()
    if ((typeof coupons.value) == 'object') {
        coupons.value.forEach(item => data.add(item['menu']['categoryName']))
    }
    return Array.from(data)
})

const buyCoupon = (coupon) =>{
    store.dispatch('userBuyCoupon', {couponID: coupon.couponID, discount: rank_discount_coupon[discount_display[coupon.discount]], menuID: coupon.menuID, cost: coupon.cost})
    setTimeout(()=>store.dispatch('getUserInfo'), 500)
}
</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">

        <div class="flex flex-col gap-12">
            <!-- 1 -->
            <div
                class="md:flex md:flex-row md:flex-wrap grid sm:grid-cols-3 grid-cols-2 gap-3 m-auto justify-center items-center ">
                <div class="bg-neutral-700/40 rounded-lg py-2 px-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                    @click="category_select = 0" :class="{ 'opacity-50': category_select != 0 }">
                    <div class="flex flex-col">
                        <div class="flex flex-row gap-1 items-center size-fit m-auto">
                            <h1 class="text-[1.25rem] text-center text-sm text-neutral-100">All</h1>
                        </div>
                    </div>
                </div>

                <div v-for="(category, index) in coupon_categories" :key="index"
                    class="bg-neutral-700/40 rounded-lg py-2 px-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                    @click="category_select = category" :class="{ 'opacity-50': category_select != category }">
                    <div class="flex flex-col">
                        <div class="flex flex-row gap-1 items-center size-fit m-auto">
                            <h1 class="text-[1.25rem] text-center text-sm text-neutral-100">{{ category }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2 -->
            <div class="flex flex-row gap-3 flex-wrap justify-center" v-if="typeof coupons == 'object'">
                <CouponComponent v-for="(coupon, index) in coupons.filter(item=>category_select != 0 ? item['menu']['categoryName'] == category_select : true)" :key="index" :coupon="coupon" @buyCoupon="buyCoupon" />

            </div>
            
        </div>
    </section>
</template>

<style></style>