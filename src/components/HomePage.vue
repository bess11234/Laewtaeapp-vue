<script setup>
import { computed } from 'vue';
import store from '@/store';

import AboutComponent from './AboutComponent.vue'

const abouts = [
  {
    title: 'Discounts',
    description: 'Ordering foods for collecting points to spends on Discounts.',
    icon: "https://cdn-icons-png.flaticon.com/512/5735/5735227.png"
  },
  {
    title: 'Coupons',
    description: 'Spends your coupons to make a discounts on ordering.',
    icon: "https://cdn-icons-png.freepik.com/512/290/290919.png"
  },
  {
    title: "Gacha Discount",
    description: "Likes in the games when you want the strongest character you have to gacha for it, but Discount!!",
    icon: "https://cdn-icons-png.flaticon.com/512/679/679821.png"
  }
]

const aboutsManager = [
  {
    title: "Manage Tables",
    description: "Create your tables in the system to match with your actual store. You can see available table and more.",
    icon: "https://cdn-icons-png.flaticon.com/512/1663/1663945.png"
  },
  {
    title: "Manage Menus",
    description: "Create menu in system to match with in actual menus in your store. Manage menus that have in system like price, description, etc.",
    icon: "https://cdn-icons-png.flaticon.com/512/737/737967.png"
  },
  {
    title: "Manage Members",
    description: "Let's your Customer be your Member!! This will help manages Members easier.",
    icon: "https://cdn-icons-png.flaticon.com/512/476/476863.png"
  },
  {
    title: "Manage Gacha box",
    description: "Add Gacha box to the system to have your Member spent their points on.",
    icon: "https://cdn-icons-png.flaticon.com/512/679/679821.png"
  }
]

const aboutsStaff = [
  {
    title: "Manage Preservation",
    description: "Make tables could be preservation and make that table unavailable.",
    icon: "https://cdn-icons-png.flaticon.com/512/1850/1850339.png"
  },
  {
    title: "Manage Queues",
    description: "Preservation for Customers when your store tables unavailable.",
    icon: "https://cdn-icons-png.flaticon.com/512/6009/6009580.png"
  },
  {
    title: "Manage Orders Foods",
    description: "Make chef in kitchen completed orders in system.",
    icon: "https://cdn-icons-png.flaticon.com/512/737/737967.png"
  },
  {
    title: "Manage Orders Serve",
    description: "Make waiter in store completed orders in system.",
    icon: "https://cdn-icons-png.flaticon.com/512/476/476863.png"
  }
]

const userInfo = computed(()=> store.getters.getUserInfo)
const isCredential = computed(() => store.getters.isCredential)
</script>

<template>
  <section>
    <!-- Welcome -->
    <div class="size-3/4 m-auto">
      <div class="my-72">
        <div class="flex flex-col text-center break-keep">
          <p class="text-4xl">Welcome to <span v-if="userInfo.role == 'MANAGER' || userInfo.role == 'STAFF'">Backside of </span>
            <span class="text-4xl bg-clip-text text-transparent bg-gradient-to-r from-sky-400 to-blue-400 animate-gradient">Laewtaeapp</span>
          </p>
          <span class="text-xl opacity-[75%]">Pleasure to see you <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#8af43f] to-[#16A085]">{{ userInfo.memberName == '' ? isCredential ? "..." : '' : userInfo.memberName }}</span></span>

          <span v-if="userInfo.role == 'MANAGER'" class="opacity-50">Manage your store with web-app!</span>
          <span v-else-if="userInfo.role == 'STAFF'" class="opacity-50">Manage queues and completed orders and serves orders</span>
          <span v-else class="opacity-50">Points, Coupon, And Special Discounts!</span>
        </div>
      </div>
    </div>

    <!-- About -->
    <div v-if="userInfo.role == 'MANAGER'" class="parent gap-5 m-10 mx-[10%]">
      <AboutComponent style="flex: 1 0 49%;" v-for="item in aboutsManager" :key="item" :title="item.title" :icon="item.icon"
        :description="item.description" />
    </div>
    <div v-else-if="userInfo.role == 'STAFF'" class="parent gap-5 m-10 mx-[10%]">
      <AboutComponent style="flex: 1 0 49%;" v-for="item in aboutsStaff" :key="item" :title="item.title" :icon="item.icon"
        :description="item.description" />
    </div>
    <div v-else class="parent gap-5 m-10 mx-[10%]">
      <AboutComponent style="flex: 1 0 49%;" v-for="item in abouts" :key="item" :title="item.title" :icon="item.icon"
        :description="item.description" />

    </div>

  </section>
</template>

<style scoped>
.parent {
  display: flex;
  flex-wrap: wrap;
}

.child {
  flex: 1 0 21%;
  /* explanation below */
  margin: 5px;
  height: 100px;
  background-color: blue;
}
</style>