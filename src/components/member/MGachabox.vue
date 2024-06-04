<script setup>
import store from '@/store';
import { computed } from 'vue';

import GachaComponent from './GachaComponent.vue'

store.dispatch('getMenuData')
store.dispatch('getGachaData')

const gachas = computed(() => store.getters.getGacha)

</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">
        <div class="flex flex-row gap-3 flex-wrap justify-center">
            <GachaComponent v-for="(gacha, index) in gachas" :key="index" :gacha="gacha" />

        </div>

    </section>
</template>

<style></style>