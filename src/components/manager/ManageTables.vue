<script setup>
import store from '@/store';
import { computed, ref } from 'vue';

const table = ref('')

store.dispatch("getTablesData")

const tableData = computed(() => store.getters.getTables)

const addTable = ()=>{
  store.dispatch("addTable", table.value)
}
</script>

<template>
  <section class="md:my-16 md:mx-16 my-8 mx-8">

    <div class="flex flex-col gap-3">
      <!-- 1 -->
      <form class="flex flex-col gap-3 md:size-1/3 size-1/2 m-auto bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50" @submit.prevent="addTable">
        <div class="flex flex-col gap-1">
          <label class="text-3xl" for="table">Add table</label>
          <input v-model.number="table" class="bg-neutral-700/50 p-1 px-2 opacity-60" id="table" type="number" min="1" max="99" placeholder="Capacity" required>
        </div>

        <div class="flex flex-row gap-1">
          <button type="submit"
            class=" bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-1 px-3 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">Submit</button>
        </div>

      </form>

      <!-- 2 -->
      <div class="relative overflow-x-auto my-12 rounded-lg flex flex-col gap-3">
        <h1 class="text-3xl">Tables ({{ tableData.length }})</h1>
        <table class="w-full text-sm text-left rtl:text-right text-neutral-500 shadow-md">
          <thead class="text-xs uppercase bg-neutral-700 text-neutral-400">
            <tr>
              <th class="text-center px-6 py-3" v-for="(_, key) in tableData[0]" :key="key">{{ key }}</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b bg-neutral-700/50 border-neutral-700" v-for="(item, index) in tableData" :key='index'>
              <td class="text-center" v-for="(data, key) in item" :key="key">{{ data == null ? "NULL" : data }}</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

  </section>
</template>
