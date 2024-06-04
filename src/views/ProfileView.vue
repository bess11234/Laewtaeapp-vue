<script setup>
import { computed, ref, reactive } from 'vue';
import router from '@/router';
import store from '@/store';
import apiClient from '@/components/services/api';


const userInfo = computed(() => store.state.user)

const nameUpdate = ref("")

const passwordInput = reactive({
  old: "",
  new: "",
  cfnew: "",
})

const isSyncPassword = computed(()=> passwordInput.new == passwordInput.cfnew)

const updateName = () => {
  userInfo.value.memberName = nameUpdate
  apiClient.post('/api/member/updateName.php', { token: store.state.token, key: store.state.key, name: nameUpdate.value }).then(response => {

    if (response.data.status == 0){
      store.commit("SET_TOKEN", { jwt: response.data.result, key: store.state.key })
    }
    store.commit("SET_NOTIFY", { message: response.data.message, status: response.data.status })
  }).catch(error => {

    router.push({name: 'errorPage', params: {error: error}})
  })
}

const updatePassword = () => {
  apiClient.post('/api/member/updatePassword.php', { token: store.state.token, old: passwordInput.old, new: passwordInput.new }).then(response => {

    store.commit("SET_NOTIFY", { message: response.data.message, status: response.data.status })
  }).catch(error => {

    router.push({name: 'errorPage', params: {error: error}})
  })
}
</script>

<template>
  <section class="m-auto size-fit">
    <div class="grid md:grid-cols-2 grid-cols-1 md:gap-24 gap-12 md:my-16 md:mx-16 my-8 mx-8">
      <div class="flex flex-col place-items-center gap-1">
        <img class="w-40" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
        <h1 class="text-3xl font-bold">{{ userInfo.memberName }}</h1>
        <p class="text-xl">{{ userInfo.email }}</p>
      </div>

      <!-- Profile -->
      <div class="flex flex-col m-3 gap-3 font-light">
        <h1 class="text-2xl font-bold">User Information</h1>

        <div class="flex flex-col gap-1 opacity-45">
          <label for="UIPhone">Phone Number:</label>
          <input class="bg-neutral-700/50 p-1 px-2" id="UIPhone" type="text" :value="userInfo.phoneNumber" readonly
            disabled>
        </div>

        <div class="flex flex-col gap-1 opacity-45">
          <label for="UIMember">Member Name:</label>
          <input class="bg-neutral-700/50 p-1 px-2" id="UIMember" type="text" :value="userInfo.memberName" readonly
            disabled>
        </div>

        <div class="flex flex-col gap-1 opacity-45">
          <label for="UIEmail">Email:</label>
          <input class="bg-neutral-700/50 p-1 px-2" id="UIEmail" type="text" :value="userInfo.email" readonly disabled>
        </div>

        <div class="flex flex-col gap-1 opacity-45">
          <label for="UIPoint">Points:</label>
          <input class="bg-neutral-700/50 p-1 px-2" id="UIPoint" type="text" :value="userInfo.points" readonly disabled>
        </div>

      </div>

      <hr class="md:hidden" />

      <form @submit.prevent="">
        <div class="flex flex-col m-3 gap-3">
          <h1 class="text-2xl font-bold">Update name</h1>

          <div class="flex flex-col gap-1">
            <label for="UNMember">Member Name:</label>
            <input v-model.trim="nameUpdate" class="bg-neutral-700/50 p-1 px-2" id="UNMember" type="text"
              :placeholder="userInfo.memberName">
          </div>

          <div class="flex flex-row gap-3 px-1">
            <button @click="updateName" type="submit"
              class="grow bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] hover:opacity-90 py-1">Submit</button>
            <button type="reset"
              class="grow bg-gradient-to-r from-yellow-700 to-orange-400 rounded-[10px] hover:opacity-90 py-1">Reset</button>
          </div>

        </div>
      </form>

      <hr class="md:hidden" />

      <form @submit.prevent="">
        <div class="flex flex-col m-3 gap-3">
          <h1 class="text-2xl font-bold">Change password</h1>

          <div class="flex flex-col gap-1">
            <label for="CPold">Old password:</label>
            <input v-model="passwordInput.old" class="bg-neutral-700/50 p-1 px-2" id="CPold" type="password" placeholder="****">
          </div>

          <div class="flex flex-col gap-1">
            <label for="CPnew">New password:</label>
            <input v-model="passwordInput.new" class="bg-neutral-700/50 p-1 px-2" id="CPnew" type="password" placeholder="****">
          </div>

          <div class="flex flex-col gap-1">
            <label for="CPconfirm">Confirm password:</label>
            <input v-model="passwordInput.cfnew" class="bg-neutral-700/50 p-1 px-2" id="CPconfirm" type="password" placeholder="****">
          </div>

          <span v-show="!isSyncPassword" class="text-sm font-mono font-light text-red-400">Password not match.</span>

          <div class="flex flex-row gap-3 px-1">
            <button type="submit" @click="updatePassword"
              class="grow bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-1 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="!isSyncPassword">Submit</button>
            <button type="reset"
              class="grow bg-gradient-to-r from-yellow-700 to-orange-400 rounded-[10px] hover:opacity-90 py-1">Reset</button>
          </div>

        </div>
      </form>

      <hr class="md:hidden" />

    </div>
  </section>
</template>

<style></style>