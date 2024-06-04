<script setup>
import { ref } from "vue"
import store from "@/store";

defineProps({
    message: {
        type: String,
        required: true
    },
    status: {
        type: Number,
        required: true
    }
})

const show = ref(true)

setTimeout(() => {
    show.value = !show.value
}, 2500)

</script>

<template>
    <transition name="slide" appear>
        <li v-if="show" class="bg-white/50 p-3 m-3 rounded-[10px]">
            <div class="flex flex-row gap-2 items-center m-auto">
                <img class="size-5"
                    :src="status == 0 ? 'https://cdn-icons-png.flaticon.com/512/190/190411.png' : 'https://cdn-icons-png.flaticon.com/512/190/190406.png'"
                    alt="">
                <span class="">{{ message }}</span>
            </div>
        </li>
    </transition>
</template>