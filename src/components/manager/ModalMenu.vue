<script setup>
defineEmits(['showModal'])
</script>

<template>
    <div class="fixed bg-black/50 w-[100%] h-[100%] z-40 top-0 left-0" @click="$emit('showModal')">
        <div class="w-2/3 my-24 bg-white text-black m-auto rounded-md  max-h-[75%] overflow-y-auto" @click.stop="">
            <slot><!-- optional fallback --></slot>
        </div>
    </div>
</template>

<style></style>