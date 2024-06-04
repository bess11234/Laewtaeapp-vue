<script setup>
const discount_display = { 0.5: '50%', 0.4: '40%', 0.3: '30%', 0.2: '20%', 0.1: '10%', 0.05: '5%', 0.01: '1%' }

import store from '@/store';
import { computed, ref, reactive } from 'vue';
import apiClient from '../services/api';
import router from '@/router';

import ModalGachaItem from '../manager/ModalGachaItem.vue';

store.dispatch("getTablesData")

const tableData = computed(() => store.getters.getTables)

const userDis = ref([])
const select_discount = computed(() => {
    const data = []

    userDis.value.forEach(item => {
        data.push(item['menuID'])
    })

    const data2 = []
    billInfo.orders.forEach(item2 => {
        if (data.includes(item2['menuID'])) data2.push(item2['menuID'])
    })

    const data3 = userDis.value.filter(item => data2.includes(item['menuID']))

    return data3
})

const discount_price = computed(() => {
    let discount_price = 0
    if (billInfo.discountCode != "") {
        const discount = select_discount.value.filter(item => item['code'] == billInfo.discountCode)[0]
        if (billInfo.orders.length > 0) {
            const menu_discount = billInfo.orders.filter(item => item['menuID'] == discount.menuID)[0]
            discount_price = -(menu_discount['price'] * menu_discount['amount']) * discount.discount
        }
    }
    return discount_price
})

const billModal = ref(false)
const billInfo = reactive({
    id: '',
    orders: [],
    total: computed(() => {
        let total = 0
        billInfo.orders.forEach(item => {
            total += item['amount'] * item['price']
        })
        total += discount_price.value
        return total
    }),
    discountCode: '',
    paymentMethod: ''
})

const randomCodeTable = (id) => {
    store.dispatch('randomCodeTable', id)
    setTimeout(() => store.dispatch("getTablesData"), 500)
}

const checkBill = () => {
    store.dispatch("checkBill", { tableID: billInfo.id, codeDiscount: billInfo.discountCode, paymentMethod: billInfo.paymentMethod, total: billInfo.total })
    billModal.value = true
    setTimeout(() => store.dispatch("getTablesData"), 500)
}

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);

const columns = [
    { data: 'tableID' },
    { data: 'code' },
    { data: 'memberName' },
    { data: 'capacity' },
    { data: 'status' },
]

const options = {
    createdRow: (row, data, dataIndex) => {

        const code = row.getElementsByTagName('td')[1]
        const button = document.createElement("button")
        code.classList.add('text-center')
        if (code && data['code'] == null) {
            code.innerHTML = ""

            button.innerHTML = 'Random code'
            button.classList.add('transition-transform')
            button.classList.add('bg-green-700/50')
            button.classList.add('hover:bg-green-600/50')
            button.classList.add('hover:scale-105')
            button.classList.add('text-green-50')
            button.classList.add('rounded-lg')
            button.classList.add('px-3')
            button.classList.add('py-1')

            button.addEventListener('click', () => {
                randomCodeTable(data['tableID'])
            })
            code.appendChild(button)
        } else if (code && data['code'] != null && data['status'] == 'RESERVED') {
            code.innerHTML = ""

            button.innerHTML = 'Check bill'
            button.classList.add('transition-transform')
            button.classList.add('bg-sky-700/50')
            button.classList.add('hover:bg-sky-600/50')
            button.classList.add('hover:scale-105')
            button.classList.add('text-sky-50')
            button.classList.add('rounded-lg')
            button.classList.add('px-3')
            button.classList.add('py-1')

            button.addEventListener('click', () => {
                billModal.value = true
                billInfo.id = data['tableID']

                apiClient.post('/api/staff/getOrderTable.php', { token: store.state.token, id: billInfo.id }).then(response => {
                    if (response.data.status == 0) {
                        billInfo.orders = response.data.result.orders
                        userDis.value = response.data.result.user_discount
                    }

                }).catch(error => {
                    router.push({ name: 'errorPage', params: { error } })
                })
            })
            code.appendChild(button)
        }

        const memberName = row.getElementsByTagName('td')[2]
        if (data['memberName'] == null) {
            memberName.innerHTML = 'NULL'
            memberName.classList.add('opacity-50')
        }

    },
    pageLength: 20,

    layout: {
        topStart: false,
        pagingType: 'simple_numbers',
    },
    responsive: true,
}

</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">

        <h1 class="text-3xl">Tables ({{ tableData.length }})</h1>
        <DataTable :columns="columns" :data="tableData" :options="options" class="text-sm text-neutral-500">
            <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-700/20 text-neutral-400">
                <tr>
                    <th>Table number</th>
                    <th>Code</th>
                    <th>Member</th>
                    <th>Capacity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="bg-neutral-700/40"></tbody>
        </DataTable>

        <transition name="slide">
            <ModalGachaItem v-show="billModal" @showModal="billModal = false">
                <form @submit.prevent="checkBill">
                    <div class="p-6">
                        <h1 class="text-3xl">Table: {{ billInfo.id }}</h1>
                        <p>Menu</p>
                        <div class="flex flex-col gap-1">
                            <div
                                class="flex flex-col gap-1 bg-neutral-200 px-3 py-2 rounded min-w-[300px] max-h-[300px] overflow-x-auto overflow-y-auto scrollbar-thin lg:text-base md:text-sm text-xs">
                                <div class="grid grid-cols-3 justify-between" v-for="(order, index) in billInfo.orders"
                                    :key="index">
                                    <p class="place-self-start">{{ order.menuName }}</p>
                                    <p class="place-self-center">{{ order.amount + ' x ' + order.price }}</p>
                                    <p class="place-self-end">{{ order.price * order.amount }}</p>
                                </div>

                                <div class="grid grid-cols-3 justify-between text-red-800"
                                    v-for="(discount, index) in select_discount.filter(item => item['code'] == billInfo.discountCode)"
                                    :key="index">
                                    <p class="place-self-start">{{ discount.menuName }} {{
                                        discount_display[discount.discount]
                                        }}</p>
                                    <p class="place-self-center"></p>
                                    <p class="place-self-end" id="discount_price">{{ discount_price }}</p>
                                </div>

                                <hr class="border-neutral-300" />
                                <div class="flex flex-row justify-between">
                                    <p>Total</p>
                                    <p>{{ billInfo.total }}</p>
                                </div>
                            </div>

                            <div class="flex md:flex-row flex-col md:gap-3 gap-1">
                                <div class="grow relative">
                                    <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" name="" id=""
                                        v-model="billInfo.discountCode">
                                        <option value="" selected>Select discount</option>
                                        <option v-for="(discount, index) in select_discount" :key="index"
                                            :value="discount.code">{{
                                                discount.menuName }} {{ discount_display[discount.discount] }}</option>
                                    </select>
                                    <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Discount</span>
                                </div>

                                <div class="grow relative">
                                    <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full uppercase" name=""
                                        id="" placeholder="Methods" v-model="billInfo.paymentMethod" required>
                                        <option value="1" selected>CASH</option>
                                        <option value="2">BANKING</option>
                                    </select>
                                    <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Payment
                                        Methods</span>
                                </div>
                            </div>

                            <button
                                class="bg-sky-700 hover:bg-sky-700/90 text-sky-50 px-3 py-1 rounded-lg mt-2 disabled:opacity-50" :disabled="billInfo.orders.length == 0">Check
                                Bill</button>
                        </div>

                    </div>
                </form>
            </ModalGachaItem>
        </transition>
    </section>
</template>

<style></style>