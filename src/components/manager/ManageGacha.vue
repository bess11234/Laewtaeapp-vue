<script setup>
import store from '@/store'
import { reactive, computed, ref } from 'vue'

import ModalGacha from './ModalGacha.vue'
import ModalGachaItem from './ModalGachaItem.vue';

store.dispatch('getMenuData')
store.dispatch('getGachaData')

const gachaInput = reactive({
  name: '',
  cost: '',
  description: '',
  expire: '',
})
const gachaModal = reactive({
  id: '',
  name: '',
  cost: '',
  description: '',
  expire: '',
})

const currentTime = new Date()

const min_date = () => {
  let year = currentTime.getFullYear();
  let month = String(currentTime.getMonth() + 1).padStart(2, '0');
  let day = String(currentTime.getDate()).padStart(2, '0');
  let hours = String(currentTime.getHours()).padStart(2, '0');
  let minutes = String(currentTime.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

const convertDate = (datetime) => {
  const date = new Date(datetime)
  return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`
}

const modal = ref(false)
const modalAddItem = ref(false)

const modalData = ref({})

const gachaData = computed(() => store.getters.getGacha)
const menuData = computed(() => store.getters.getMenus)

const modalItemStrict = ref({})
const modalItem = reactive({
  gachaID: '',
  menuID: '',
  rarity: '',
  discount: '',
})

const discounts = { '50%': 0.5, '40%': 0.4, '30%': 0.3, '20%': 0.2, '10%': 0.1, '5%': 0.05, '1%': 0.01 }
const rank_discount = { '50%': 1, '40%': 2, '30%': 3, '20%': 4, '10%': 5, '5%': 6, '1%': 7 }
const rank_rarity = { 'MYTHIC': 6, 'LEGENDARY': 5, 'EPIC': 4, 'RARE': 3, 'UNCOMMON': 2, 'COMMON': 1 }

const showModal = () => {
  modal.value = false
}

const showModalItem = () => {
  modalAddItem.value = false
}

const updateRarity = (data) => {
  store.dispatch('updateGachaRarity', data)
}

const updateDiscount = (data) => {
  store.dispatch('updateGachaDiscount', data)
}

const addGacha = () => {
  gachaInput.expire = convertDate(gachaInput.expire)

  store.dispatch('addGacha', gachaInput)

  gachaInput.name = ''
  gachaInput.cost = ''
  gachaInput.description = ''
  gachaInput.expire = ''
  store.dispatch('getGachaData')
}

const updateGacha = () => {
  gachaModal.expire = convertDate(gachaModal.expire)
  store.dispatch('updateGacha', gachaModal)
  store.dispatch('getGachaData')
  showModal()
}

const deleteGacha = () => {
  store.dispatch('deleteGacha', gachaModal.id)
  store.dispatch('getGachaData')
  showModal()
}

const addItemGacha = () => {
  store.dispatch('addGachaItem', modalItem)
  store.dispatch('getGachaData')
  showModalItem()
}

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);

const columns = [
  { data: 'name' },
  { data: 'cost' },
  { data: 'description' },
  { data: 'expire' },
]

const opitons = {
  createdRow: (row, data, dataIndex) => {
    row.classList.add('bg-neutral-700/50')
    row.classList.add('border-neutral-700')
    row.classList.add('hover:bg-neutral-700/40')
    row.classList.add('cursor-pointer')
    row.addEventListener('click', () => {
      modal.value = true
      modalData.value = data

      gachaModal.id = modalData.value.gachaID
      gachaModal.name = modalData.value.name
      gachaModal.cost = modalData.value.cost
      gachaModal.description = modalData.value.description
      gachaModal.expire = modalData.value.expire
      gachaModal.menus = modalData.value.menus

      const menu = new Set()
      gachaModal.menus.forEach(item => {
        menu.add(item['menuID'])
      })

      menuData.value.forEach(item => {
        item = JSON.parse(JSON.stringify(item))
        item['rarity_use'] = ['MYTHIC', 'LEGENDARY', 'EPIC', 'RARE', 'UNCOMMON', 'COMMON']
        modalItemStrict.value[item.menuID] = item
      })

      menu.forEach(menuID => {
        let rarity = new Set(modalItemStrict.value[menuID].rarity_use)
        gachaModal.menus.filter(item => item.menuID == menuID).forEach(item => {
          rarity.delete(item.rarity)
        })
        rarity = Array.from(rarity)

        if (rarity.length == 0) {
          delete modalItemStrict.value[menuID]
        } else {
          modalItemStrict.value[menuID]['rarity_use'] = rarity
        }
      })
      // console.log(modalItemStrict.value) //ดูข้อมูล Menus ที่เลือกได้ใน Gacha

    })
  },
  layout: {
    topStart: false,
    bottomEnd: {
      paging: {
        numbers: 3
      }
    },
    pagingType: 'simple_numbers',
    pageLength: 10
  },
  responsive: {
    breakpoints: [
      { name: 'name', width: Infinity },
      { name: 'cost', width: 1480 },
      { name: 'description', width: 480 },
      { name: 'expire', width: 1188 },
    ]
  },
}

const columns2 = [
  { data: 'rarity' },
  { data: 'menu.menuName' },
  { data: 'menu.price' },
  { data: 'discount' },
  { data: 'discount_price' },
  { data: 'rank_rarity', visible: false },
]

const options2 = {
  createdRow: (row, data, dataIndex) => {

    row.classList.add('hover:bg-neutral-200/50')

    let select1 = document.createElement("select")

    // Rarity
    const rarity = row.getElementsByTagName('td')[0]
    select1.classList.add("bg-neutral-300/50")
    select1.classList.add("rounded-lg")
    select1.classList.add("px-1")
    rarity.innerHTML = ''
    rarity.appendChild(select1)

    let element = ''
    if (rarity) {
      element = ''
      element += data.rarity == 'MYTHIC'
        ? '<option value="MYTHIC" selected>MYTHIC (0.01%)</option>'
        : '<option value="MYTHIC">MYTHIC (0.01%)</option>'
      element += data.rarity == 'LEGENDARY'
        ? '<option value="LEGENDARY" selected>LEGENDARY (0.5%)</option>'
        : '<option value="LEGENDARY">LEGENDARY (0.5%)</option>'
      element += data.rarity == 'EPIC'
        ? '<option value="EPIC" selected>EPIC (3.4%)</option>'
        : '<option value="EPIC">EPIC (3.4%)</option>'
      element += data.rarity == 'RARE'
        ? '<option value="RARE" selected>RARE (11%)</option>'
        : '<option value="RARE">RARE (11%)</option>'
      element += data.rarity == 'UNCOMMON'
        ? '<option value="UNCOMMON" selected>UNCOMMON (15%)</option>'
        : '<option value="UNCOMMON">UNCOMMON (15%)</option>'
      element += data.rarity == 'COMMON'
        ? '<option value="COMMON" selected>COMMON (70%)</option>'
        : '<option value="COMMON">COMMON (70%)</option>'
      select1.innerHTML = element
    }
    select1.addEventListener('change', () => {

      updateRarity({
        gachaID: data.gachaID,
        menuID: data.menuID,
        discount: data.discount,
        rarity: rank_rarity[select1.value]
      })
    })

    // Menu
    const menu = row.getElementsByTagName('td')[1];
    menu.classList.add('opacity-40')

    // Price
    const price = row.getElementsByTagName('td')[2];
    price.classList.add('opacity-40')

    // Discount
    const discount = row.getElementsByTagName('td')[3];
    let select2 = document.createElement("select")
    select2.classList.add("bg-neutral-300/50")
    select2.classList.add("rounded-lg")
    select2.classList.add("px-1")
    discount.innerHTML = ''
    discount.appendChild(select2)

    if (discount) {
      element = ''
      element += data.discount == 0.5 ? '<option selected>50%</option>' : '<option>50%</option>'
      element += data.discount == 0.4 ? '<option selected>40%</option>' : '<option>40%</option>'
      element += data.discount == 0.3 ? '<option selected>30%</option>' : '<option>30%</option>'
      element += data.discount == 0.2 ? '<option selected>20%</option>' : '<option>20%</option>'
      element += data.discount == 0.1 ? '<option selected>10%</option>' : '<option>10%</option>'
      element += data.discount == 0.05 ? '<option selected>5%</option>' : '<option>5%</option>'
      element += data.discount == 0.01 ? '<option selected>1%</option>' : '<option>1%</option>'
      select2.innerHTML = element
      // discount.innerHTML = discount.innerHTML * 100 + "%"
    }

    const discount_price = row.getElementsByTagName('td')[4];
    select2.addEventListener('change', () => {
      discount_price.innerHTML = data.menu.price - (data.menu.price * discounts[select2.value])

      updateDiscount({
        gachaID: data.gachaID,
        menuID: data.menuID,
        rarity: data.rarity,
        discount: rank_discount[select2.value]
      })
    })

  },
  order: [[5, 'desc'], [3, 'desc']],
  columnDefs: [
    {
      targets: [0],
      orderData: [5, 0]
    }
  ]
}
</script>

<template>
  <section class="md:my-16 md:mx-16 my-8 mx-8">

    <div class="flex flex-col gap-12">
      <!-- 1 Create gacha -->
      <div class="flex flex-col gap-12 content-start m-auto min-w-[40%]">
        <form class="grow flex flex-col gap-3 bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50"
          @submit.prevent="addGacha">
          <p class="text-3xl">Create gacha</p>
          <div class="flex flex-col gap-4">

            <div class="relative -mt-3">
              <input v-model.trim="gachaInput.name" class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full"
                id="gachaName" type="text" placeholder="Name" required />
              <transition name="fade">
                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded"
                  v-show="gachaInput.name != ''">Name</span>
              </transition>
            </div>

            <div class="relative -mt-3">
              <input v-model.number="gachaInput.cost" class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full"
                id="gachaPrice" type="number" min="0" placeholder="Cost (points)" required />
              <transition name="fade">
                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded" v-show="gachaInput.cost != ''">Cost
                  (points)</span>
              </transition>
            </div>

            <div class="relative -mt-3">
              <textarea v-model.trim="gachaInput.description" class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full"
                id="gachaDescription" type="text" rows="5" placeholder="Description (Optional)"></textarea>
              <transition name="fade">
                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded"
                  v-show="gachaInput.description != ''">Description</span>
              </transition>
            </div>

            <div class="flex flex-row gap-3 flex-wrap">

              <div class="grow relative -mt-3">
                <input v-model.trim="gachaInput.expire" type="datetime-local" id="gachaDate"
                  class="bg-neutral-700/50 p-1 px-2 rounded-md mt-3 w-full" :min="min_date()" required>
                <span class="absolute left-0 text-xs bg-neutral-800 px-1 rounded">Expired date</span>
              </div>

            </div>
          </div>

          <div class="flex flex-row gap-1">
            <button type="submit"
              class="bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-2 px-4 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">Submit</button>
          </div>

        </form>
      </div>

      <!-- 2 Manage gacha box -->
      <div class="bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50">
        <h1 class="text-3xl">Manage Gacha box</h1>
        <DataTable :columns="columns" :data="gachaData" :options="opitons" class="text-sm text-neutral-500">
          <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-700/20 text-neutral-400">
            <tr>
              <th>Name</th>
              <th>Cost (Points)</th>
              <th>Description</th>
              <th>Expired</th>
            </tr>
          </thead>
          <tbody class="bg-neutral-700/40"></tbody>

        </DataTable>
      </div>

    </div>

    <!-- Modal -->
    <transition name="slide">
      <ModalGacha @showModal="showModal" v-show="modal">
        <form @submit.prevent="updateGacha">
          <div class="m-3 p-6 flex flex-col gap-3">
            <h1 class="text-3xl text-center">Gacha: {{ modalData.name }}</h1>
            <div class="flex flex-col gap-4">

              <!-- Update -->
              <div class="relative -mt-3">
                <input v-model.trim="gachaModal.name" class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full"
                  id="gachaName" type="text" placeholder="Name" required />
                <transition name="fade">
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded"
                    v-show="gachaModal.name != ''">Name</span>
                </transition>
              </div>

              <div class="relative -mt-3">
                <input v-model.number="gachaModal.cost" class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full"
                  id="gachaPrice" type="number" min="0" placeholder="Cost (points)" required />
                <transition name="fade">
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded" v-show="gachaModal.cost != ''">Cost
                    (points)</span>
                </transition>
              </div>

              <div class="relative -mt-3">
                <textarea v-model.trim="gachaModal.description"
                  class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" id="menuPrice" type="text" rows="5"
                  placeholder="Description (Optional)"></textarea>
                <transition name="fade">
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded"
                    v-show="gachaModal.description != ''">Description</span>
                </transition>
              </div>

              <div class="flex flex-row gap-3 flex-wrap">

                <div class="grow relative -mt-3">
                  <input v-model.trim="gachaModal.expire" type="datetime-local"
                    class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" :min="min_date()" required>
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Expired date</span>
                </div>

              </div>

              <div class="flex flex-row gap-1">
                <button type="submit"
                  class="bg-gradient-to-r from-indigo-400 to-violet-400 rounded-[10px] py-2 px-4 hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="false">Update</button>

                <button type="button"
                  class="bg-gradient-to-r from-red-400 to-rose-400 rounded-[10px] py-2 px-4 hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="false" @click="deleteGacha">Delete</button>
              </div>

            </div>

            <!-- Discount -->
            <h1 class="text-2xl text-center">Menus discounts</h1>

            <div class="flex flex-row gap-3">
              <button type="button" @click="modalAddItem = true; modalItem.gachaID = gachaModal.id"
                class="bg-gradient-to-r from-teal-500 to-green-400 rounded-[10px] py-2 px-4 hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="false">Add item</button>
            </div>

            <DataTable :columns="columns2" :data="gachaModal.menus" :options="options2">
              <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-200 border border-neutral-200">
                <tr>
                  <th>Ranks</th>
                  <th>Menu</th>
                  <th>Price</th>
                  <th>Discounts</th>
                  <th>Discount (Price)</th>
                </tr>
              </thead>
              <tbody class="bg-neutral-200/30"></tbody>
            </DataTable>

          </div>
        </form>
      </ModalGacha>
    </transition>

    <!-- Modal Add Item -->
    <transition name="slide">
      <ModalGachaItem @showModal="showModalItem" v-show="modalAddItem">

        <form @submit.prevent="addItemGacha">
          <div class="flex flex-col gap-4 p-6">
            <h1 class="text-center text-3xl">Add item</h1>

            <div class="relative -mt-3">
              <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" name="" id="" v-model="modalItem.menuID"
                required>
                <option value="" selected>Select menu</option>
                <option v-for="(option, index) in modalItemStrict" :value="option.menuID" :key="index">{{
                  option.menuName
                }}</option>
              </select>
              <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Menu</span>
            </div>

            <div v-if="modalItem.menuID != ''" class="flex flex-col gap-4">
              <div class="relative -mt-3">
                <input class="bg-neutral-400/50 p-1 px-2 rounded-md mt-3 w-full opacity-50" type="text" name="" id=""
                  :value="modalItemStrict[modalItem.menuID].categoryName" readonly>
                <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Menu category</span>
              </div>

              <div class="relative -mt-3">
                <input class="bg-neutral-400/50 p-1 px-2 rounded-md mt-3 w-full opacity-50" type="text" name="" id=""
                  :value="modalItemStrict[modalItem.menuID].price" readonly>
                <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Price</span>
              </div>

              <div class="flex md:flex-row flex-col gap-4">
                <div class="grow relative -mt-3">
                  <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" name="" id=""
                    v-model="modalItem.rarity" required>
                    <option value="" selected>Select rarity</option>
                    <option v-for="(option, index) in modalItemStrict[modalItem.menuID].rarity_use"
                      :value="rank_rarity[option]" :key="index">{{
                        option
                      }}</option>
                  </select>
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Rarity</span>
                </div>

                <div class="grow relative -mt-3">
                  <select class="bg-neutral-400/10 p-1 px-2 rounded-md mt-3 w-full" name="" id=""
                    v-model="modalItem.discount" required>
                    <option value="" selected>Select discounts</option>
                    <option :value="rank_discount['50%']">50%</option>
                    <option :value="rank_discount['40%']">40%</option>
                    <option :value="rank_discount['30%']">30%</option>
                    <option :value="rank_discount['20%']">20%</option>
                    <option :value="rank_discount['10%']">10%</option>
                    <option :value="rank_discount['5%']">5%</option>
                    <option :value="rank_discount['1%']">1%</option>
                  </select>
                  <span class="absolute left-0 text-xs bg-neutral-100 px-1 rounded">Discount</span>
                </div>
              </div>

              <button
                class="bg-gradient-to-r from-indigo-400 to-violet-400 rounded-[10px] py-2 px-4 hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed size-fit"
                type="submit">Submit</button>
            </div>

          </div>
        </form>
      </ModalGachaItem>
    </transition>

  </section>
</template>

<style scoped>
:root {
  --select-border: #777;
  --select-focus: blue;
  --select-arrow: var(--select-border);
}
</style>