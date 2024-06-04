<script setup>
defineEmits(['showModal'])
</script>

<template>
    <div class="fixed bg-black/50 w-[100%] h-[100%] z-40 top-0 left-0" @click="$emit('showModal')">
        <div class="w-1/2 my-56 bg-neutral-100 text-black m-auto rounded-md max-h-[75%] overflow-y-auto scrollbar-thin" @click.stop="">
            <slot><!-- optional fallback --></slot>
        </div>
    </div>
</template>

<style></style>