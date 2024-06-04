<script setup>
import { RouterView } from 'vue-router'
import { computed } from 'vue';
import store from './store';

import NavBar from './components/NavBar.vue';
import MessageComponent from './components/MessageComponent.vue';

const notification = computed(() => store.getters.getNotification)

</script>

<template>
  <section>

    <transition-group name="fade" tag="ul"
      class="fixed right-0 bottom-0 w-fit p-3 text-black z-50 text-right">
      <MessageComponent v-for="(notify, index) in notification" :key="index" :message="notify.message"
        :status="notify.status" />
    </transition-group>
    <!-- Navbar -->
    <NavBar />

    <RouterView v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component"></component>
      </transition>
    </RouterView>

  </section>
</template>

<style>
/*** TRANSITIONS ***/
/** slide **/
.slide-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-enter-to {
  transform: translateY(0px);
  opacity: 1;
}

.slide-leave-from {
  transform: translateY(0px);
  opacity: 1;
}

.slide-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-enter-active {
  transition: all 0.5s ease;
}

.slide-leave-active {
  transition: all 0.25s ease;
}

/** fade **/
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.25s ease;
}
</style>
