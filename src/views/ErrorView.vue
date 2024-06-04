<script setup>
defineProps({
    error:{
        type: String,
        required: true
    }
})
</script>

<template>
    <section>
        <!-- 404 -->
        <div class="size-3/4 m-auto">
            <div class="my-72">
                <div class="flex flex-col text-center break-keep">
                    <span
                        class="text-4xl bg-clip-text text-transparent bg-gradient-to-r from-sky-400 to-blue-400 animate-gradient">Oops!
                        {{ error }}</span>
                    
                    <span class="text-xl opacity-[75%]">Please contacts administrator</span>
                    <RouterLink :to="{ name: 'home' }" class="opacity-50 underline decoration-white">Return to page Home</RouterLink>
                </div>
            </div>
        </div>

    </section>
</template>