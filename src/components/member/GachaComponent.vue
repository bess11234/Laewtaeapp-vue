<script setup>
const rank_discount = { '50%': 1, '40%': 2, '30%': 3, '20%': 4, '15%': 5,'10%': 6, '5%': 7, '1%': 8 }
const discount_display = { 0.5: '50%', 0.4: '40%', 0.3: '30%', 0.2: '20%', 0.15: '15%', 0.1: '10%', 0.05: '5%', 0.01: '1%' }

import { ref, computed } from 'vue'
import store from '@/store';

const showGachaInfo = ref(false)

const props = defineProps({
    gacha: {
        type: Object,
        required: true,
    }
})

const posibility = computed(() => {
    const data = new Set()
    props.gacha.menus.forEach(item => data.add(item['rarity']))
    return Array.from(data)
})

const buyGacha = () => {
    const random = Math.floor(Math.random() * posibility.value.length)
    const rarity = posibility.value[random]

    const menus_discount = props.gacha.menus.filter(item=>item['rarity'] == rarity)
    
    const menu = menus_discount[Math.floor(Math.random() * menus_discount.length)]
    store.dispatch('buyGacha', {menuID: menu.menuID, discount: rank_discount[discount_display[menu.discount]], menu: menu.menu.menuName, rarity: rarity, cost: props.gacha.cost})
    setTimeout(()=>store.dispatch('getUserInfo'), 500)
}

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);
// 0.01%, 0.5%, 3.4%, 15%, 70%
const columns = [
    { data: 'menu.menuID', visible: false },
    { data: 'menu.menuName' },
    { data: 'menu.categoryName' },
    { data: 'rarity' },
    { data: 'discount' },
]

const options = {
    createdRow: (row, data, dataIndex) => {
        const discount = row.getElementsByTagName('td')[3]
        if (discount) {
            discount.innerHTML = discount_display[data['discount']]
        }
    },
    layout: {
        topStart: false,
        bottomEnd: {
            paging: {
                numbers: 3
            }
        },
        pagingType: 'simple_numbers',
    },
    order: [[4, 'desc']]
}
</script>

<template>
    <div class="flex flex-col gap-1 items-center bg-neutral-700/50 rounded-lg min-w-[300px]">

        <div class="flex flex-col items-center gap-1 py-1 my-1 mt-2">
            <h1 class="text-lg">{{ gacha.name }}</h1>
            <h1 class="text-sm">{{ gacha.description }}</h1>
            <button class="bg-gray-500/60 py-1 px-3 text-sm mb-1 rounded-lg"
                @click="showGachaInfo = !showGachaInfo">ดูรายละเอียด</button>
        </div>

        <button class="bg-blue-500 hover:bg-blue-500/60 py-2 w-full rounded-b-lg" @click="buyGacha">{{ gacha.cost }}
            Points</button>

        <div v-show="showGachaInfo">
            <div class="fixed bg-black/50 w-[100%] h-[100%] z-40 top-0 left-0" @click="showGachaInfo = false">
                <div class="w-2/3 my-36 bg-neutral-100 text-black m-auto rounded-md max-h-[75%] overflow-y-auto scrollbar-thin"
                    @click.stop="">
                    <div class="py-3 px-6 m-3">
                        <h1 class="text-3xl text-center">Gacha: {{ gacha.name }}</h1>
                        <DataTable :columns="columns" :options="options" :data="gacha.menus">
                            <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-200 text-neutral-800">
                                <tr>
                                    <th>ID</th>
                                    <th>Menu</th>
                                    <th>Catgory</th>
                                    <th>Rarity</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style></style>