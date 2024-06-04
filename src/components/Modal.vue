<script setup>
defineEmits(['showLogin'])
</script>

<template>
    <div class="fixed bg-black/50 w-[100%] h-[100%] z-40 top-0" @click="$emit('showLogin')">
        <div class="w-2/3 my-60 bg-white text-black m-auto rounded-md min-w-[350px] max-w-[600px]" @click.stop="">
            <slot><!-- optional fallback --></slot>
        </div>
    </div>
</template>