<script setup>
import store from "@/store";
import { computed } from 'vue';

store.dispatch('getBillsData')

const bills = computed(() => store.getters.getBills)

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);

const columns = [
  { data: 'memberName' },
  { data: 'total' },
  { data: 'paymentMethod' },
  { data: 'codeDiscount' },
  { data: 'billAt' },
]

const opitons = {
  layout: {
    topStart: false,
    topEnd: false,
    bottomEnd: {
      paging: {
        numbers: 3
      }
    },
    pagingType: 'simple_numbers',
    pageLength: 10
  },
  order: [[3, 'desc']],
}

</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">
        <div class="bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50">
            <h1 class="text-3xl">Bills</h1>
            <DataTable class="text-sm text-neutral-500" :columns="columns" :options="opitons" :data="bills">
                <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-700/20 text-neutral-400">
                    <tr>
                        <th>Member</th>
                        <th>Payment total</th>
                        <th>Payments (Method)</th>
                        <th>Code discount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody class="bg-neutral-700/40"></tbody>
            </DataTable>
        </div>
    </section>
</template>

<style></style>