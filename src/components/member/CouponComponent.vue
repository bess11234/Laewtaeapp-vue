<script setup>
const discount_display = { 0.5: '50%', 0.4: '40%', 0.3: '30%', 0.2: '20%', 0.1: '10%', 0.05: '5%', 0.01: '1%' }
defineProps({
    coupon: {
        type: Object,
        required: true
    },
    status:{
        type: String,
        default: '0'
    }
})
const emits = defineEmits(['buyCoupon'])
</script>

<template>
    <div class="flex flex-col gap-1 items-center bg-neutral-700/50 rounded-lg min-w-[300px]">
        <img class="max-w-[300px] rounded-t-lg" :src="'/src/assets/images/menus/' + coupon.menu.image" alt="">

        <div class="flex flex-col items-center gap-1 py-1">
            <h1 class="text-lg">{{ coupon.menu.menuName + " " + discount_display[coupon.discount] }}</h1>
        </div>

        <button v-if="status != '1'" class="bg-blue-500 hover:bg-blue-500/60 py-2 w-full rounded-b-lg"
            @click="emits('buyCoupon', coupon)">{{
                coupon.cost }} Points</button>
        <div class="bg-gray-600 py-2 w-full rounded-b-lg text-center">{{ coupon.code }}</div>
    </div>
</template>

<style></style>