<script setup>

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    icon: {
        type: String
    }
})
</script>

<template>
    <div
        class="p-10 px-8 bg-neutral-700/50 rounded-[15px] shadow-md shadow-neutral-800 border-b-[1px] border-b-white/5">
        <div class="flex flex-row gap-1 items-center">
            <h1 class="text-3xl">{{ props.title }}</h1>
            <img class="w-8" :src="icon" alt="">
        </div>
        <span class="opacity-50">{{ props.description }}</span>
    </div>
</template>

<style></style>