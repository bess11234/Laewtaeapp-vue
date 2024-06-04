<script setup>
const discounts = { '50%': 0.5, '40%': 0.4, '30%': 0.3, '20%': 0.2, '10%': 0.1, '5%': 0.05, '1%': 0.01 }
const rank_discount = { '50%': 1, '40%': 2, '30%': 3, '20%': 4, '10%': 5, '5%': 6, '1%': 7 }
const discount_display = { 0.5: '50%', 0.4: '40%', 0.3: '30%', 0.2: '20%', 0.1: '10%', 0.05: '5%', 0.01: '1%' }

import store from '@/store';
import { computed, reactive } from 'vue';

store.dispatch('getMenuData')
store.dispatch("getCoupons")

const menus = computed(() => store.getters.getMenus)
const coupons = computed(() => store.getters.getCoupons)

const couponInput = reactive({
    menu: "",
    cost: "",
    discount: "",
})

const addCoupon = () => {
    store.dispatch("addCoupon", { menuID: couponInput.menu, cost: couponInput.cost, discount: couponInput.discount })
    Object.keys(couponInput).forEach(item=>{
        couponInput[item] = ""
    })
    setTimeout(() => { store.dispatch("getCoupons") }, 500)
}

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);

const columns = [
    { data: 'couponID', visible: false },
    { data: 'menu.menuName' },
    { data: 'cost' },
    { data: 'discount' },
    { data: 'menu.price' },
    { data: 'menu.categoryName' },
    { data: 'menu.image' },
]

const options = {
    createdRow: (row, data, dataIndex) => {
        row.classList.add('hover:bg-neutral-700/40')

        const discount = row.getElementsByTagName('td')[2]
        if (discount) {
            discount.innerHTML = discount_display[data['discount']]
        }

        const price = row.getElementsByTagName('td')[3]
        if (price) {
            price.innerHTML = data['menu']['price'] + " 🠲 " + (data['menu']['price'] - data['menu']['price'] * data['discount'])
        }

        const image = row.getElementsByTagName('td')[5]
        if (image) {
            image.innerHTML = `<img class="rounded-lg min-w-[150px] max-w-[180px]" src='/src/assets/images/menus/${data.menu.image}' />`
        }
    },
    layout: {
        topStart: false,
    },
    pageLength: 5,
    responsive: true,
}
</script>

<template>
    <section class="md:m-16 m-8">
        <div class="flex flex-col gap-12">
            <div class="flex flex-col gap-12 content-start m-auto min-w-[40%]">
                <form class="grow flex flex-col gap-3 bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50"
                    @submit.prevent="addCoupon">
                    <p class="text-3xl">Create coupon</p>
                    <div class="flex flex-col gap-4">

                        <div class="relative -mt-3">
                            <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" name="" id="couponMenuID"
                                v-model="couponInput.menu" required>
                                <option class="bg-neutral-800" value="" selected>Select menu</option>
                                <option class="bg-neutral-800" v-for="(option, index) in menus" :value="option.menuID"
                                    :key="index">
                                    {{
                                        option.menuName
                                    }}</option>
                            </select>
                            <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded">Menu</span>
                        </div>

                        <div class="relative -mt-3">
                            <input v-model.number="couponInput.cost"
                                class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full" id="couponCost" type="number"
                                min="0" placeholder="Cost (Points)" required />
                            <transition name="fade">
                                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded"
                                    v-show="couponInput.cost != ''">Cost
                                    (Points)</span>
                            </transition>
                        </div>

                        <div class="relative -mt-3">
                            <div class="grow relative">
                                <select class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full" name="" id="couponDiscount"
                                    v-model="couponInput.discount" required>
                                    <option class="bg-neutral-800" value="" selected>Select discounts</option>
                                    <option class="bg-neutral-800" :value="rank_discount['50%']">50%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['40%']">40%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['30%']">30%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['20%']">20%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['10%']">10%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['5%']">5%</option>
                                    <option class="bg-neutral-800" :value="rank_discount['1%']">1%</option>
                                </select>
                                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded">Discount</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row gap-1">
                        <button type="submit"
                            class="bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-2 px-4 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">Submit</button>
                    </div>

                </form>
            </div>

            <div>
                <h1 class="text-3xl">Coupons ({{ coupons.length }})</h1>

                <DataTable :columns="columns" :options="options" :data="coupons">
                    <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-700/20 text-neutral-400">
                        <tr>
                            <th>CouponID</th>
                            <th>Menu</th>
                            <th>Cost (Points)</th>
                            <th>Discount</th>
                            <th>Price</th>
                            <th>Category Name</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody class="bg-neutral-700/40"></tbody>
                </DataTable>
            </div>
        </div>
    </section>
</template>

<style></style>