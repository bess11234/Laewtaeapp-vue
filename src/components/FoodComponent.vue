<script setup>
defineProps({
    menu:{
        type: Object,
        required: true
    },
})

const emits = defineEmits(['decrement', 'increment'])
</script>

<template>
    <div class="flex flex-col gap-1 items-center bg-neutral-700/50 p-3 px-6 rounded min-w-[300px]">
        <img class="max-w-[300px] rounded" :src="'/src/assets/images/menus/' + menu.image" alt="">

        <div class="flex flex-col items-center gap-1 py-2">
            <h1 class="text-lg">{{ menu.menuName }}</h1>
            <p class="text-base">{{ menu.price }}</p>

            <div class="flex flex-row gap-3">
                <button class="bg-rose-300/50 hover:bg-rose-300/70 px-3 rounded" @click="emits('decrement', menu.menuID)">-</button>
                <slot><!-- optional fallback --></slot>
                <button class="bg-teal-300/50 hover:bg-teal-300/70 px-3 rounded" @click="emits('increment', menu.menuID)">+</button>
            </div>
        </div>
    </div>
</template>

<style></style>