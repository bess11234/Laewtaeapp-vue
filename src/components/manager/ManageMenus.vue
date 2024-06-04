<script setup>
import store from '@/store';
import { computed, reactive, ref } from 'vue';

import ModalMenu from './ModalMenu.vue';

const menus = computed(() => store.getters.getMenus)
const categories = computed(() => store.getters.getCategories)
const categoryData = computed(() => categories.value.map(item => {
    return item.name
}))

let categoryMenu = computed(() =>
    menus.value != undefined ? Object.keys(menus.value[0]) : []
)

let previewImage = ref("")

let menuModal = reactive({});

const fileError = ref(false)
const modalMeunShow = ref(false)

store.dispatch("getMenuData")

const menuInput = reactive({
    name: '',
    category: '',
    price: '',
    description: '',
    file: ''
})

const categoryInput = ref("")

const handleFile = (event) => {
    const file = event.target.files[0]
    if (file == undefined || file.type == 'image/webp') {

        fileError.value = false
        imageUploaded(file)
    } else {
        fileError.value = true
    }
}

const getCategoryID = (categoryName) => {
    const category = categories.value.filter(item => {
        return item['name'] == categoryName
    })
    return category[0]['categoryID']
}

const imageUploaded = (file) => {
    if (file != undefined) {
        let reader = new FileReader();

        reader.onload = function () {
            menuInput.file = reader.result.replace("data:", "")
                .replace(/^.+,/, "");
            previewImage.value = URL.createObjectURL(file)
        }
        reader.readAsDataURL(file);
    }
}

const addMenu = (form) => {
    const data = { name: menuInput.name, category: getCategoryID(menuInput.category), price: menuInput.price, description: menuInput.description, file: menuInput.file }
    store.dispatch("addMenu", data)

    setTimeout(() => {
        store.dispatch("getMenuData")
        menuInput.name = ''
        menuInput.category = ''
        menuInput.price = ''
        menuInput.description = ''
        menuInput.file = ''
        form.target.reset()
    }, 500)
}

const addCategory = () => {
    store.dispatch("addCategory", categoryInput.value)

    setTimeout(() => {
        store.dispatch("getMenuData")
        categoryInput.value = ''
    }, 500)
}

const closeModal = () => {
    modalMeunShow.value = false
    setTimeout(() => previewImage.value = '', 500)
}

const setMenuModal = (menu) => {
    const data = JSON.parse(JSON.stringify(menu))
    menuModal = reactive(data)
}

const updateCategoryName = (event) => {
    menuModal.categoryName = event.target.value
}

const updateMenu = () => {
    const data = {
        menuID: menuModal.menuID,
        menuName: menuModal.menuName,
        file: previewImage.value != '' ? menuInput.file : menuModal.image,
        price: menuModal.price,
        description: menuModal.description,
        categoryID: getCategoryID(menuModal.categoryName)
    }
    store.dispatch("updateMenu", data)
    setTimeout(() => {
        store.dispatch("getMenuData")
    }, 500)
    modalMeunShow.value = false;
}

const deleteMenu = () => {
    if (confirm('Are you sure you want to Delete the menu?')) {
        store.dispatch("deleteMenu", menuModal.menuID)
        setTimeout(() => {
            store.dispatch("getMenuData")
        }, 500)
        modalMeunShow.value = false;
    }
}
</script>

<template>
    <div>
        <section class="md:my-16 md:mx-16 my-8 mx-8">

            <div class="flex flex-col gap-3">

                <!-- 1 -->
                <div class="flex flex-col gap-12 content-start size-1/2 m-auto">
                    <!-- Create Menu -->
                    <form class="grow flex flex-col gap-3 bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50" @submit.prevent="addMenu">
                        <p class="text-3xl">Create menu</p>
                        <div class="flex flex-col gap-3">
                            <input v-model.trim="menuInput.name" class="bg-neutral-700/50 p-1 px-2 rounded-md" id="menuName"
                                type="text" placeholder="Name" required />

                            <select v-model.trim="menuInput.category" class="bg-neutral-700/50 p-1 px-1 rounded-md"
                                id="menuCategory" required>
                                <option class="bg-neutral-600" value="">--- Select category ----</option>
                                <option class="bg-neutral-600" v-for="(item, index) in categoryData" :key="index"
                                    :value="item">{{ item }}</option>
                            </select>

                            <input v-model.number="menuInput.price" class="bg-neutral-700/50 p-1 px-2 rounded-md"
                                id="menuPrice" type="number" min="0" placeholder="Price" required />

                            <textarea v-model.trim="menuInput.description" class="bg-neutral-700/50 p-1 px-2 rounded-md"
                                id="menuPrice" type="text" rows="5" placeholder="Description (Optional)"></textarea>

                            <input @change="handleFile($event)"
                                class="block w-full bg-neutral-700/50 file:bg-neutral-300 hover:file:bg-neutral-300/80 file:border-0 file:me-5 file:py-3 file:px-4 rounded-l-lg cursor-pointer hover:file:cursor-pointer rounded-md"
                                id="menuPrice" type="file" required />
                            <p class="px-3 text-sm " id="file_input_help">Webp. (900x600px) <span v-show="fileError"
                                    class="text-red-400">{{ 'file name error' }}</span> </p>
                        </div>

                        <div class="flex flex-row gap-1">
                            <button type="submit"
                                class="bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-2 px-4 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="fileError">Submit</button>
                        </div>

                    </form>

                    <!-- Menu Category -->
                    <div class="flex flex-col gap-3 bg-neutral-900/25 p-6 rounded-lg shadow-md shadow-neutral-900/50">
                        <p class="text-3xl" for="table">Add menu category</p>
                        <div class="grid lg:grid-cols-2 grid-cols-1 grow gap-12">
                            <!-- Create -->
                            <form class="flex flex-col gap-3" @submit.prevent="addCategory">
                                <div class="flex flex-col gap-1">
                                    <input v-model.trim="categoryInput" class="bg-neutral-700/50 p-1 px-2 rounded-md"
                                        id="menuNameCategory" type="text" placeholder="Name" required>
                                </div>

                                <div class="flex flex-row gap-1">
                                    <button type="submit"
                                        class="bg-gradient-to-r from-sky-700 to-blue-400 rounded-[10px] py-2 px-4 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">Submit</button>
                                </div>

                            </form>

                            <!-- Info Category -->
                            <div
                                class="grow relative overflow-y-auto h-[250px] rounded-lg scrollbar-thin scrollbar-thumb-neutral-300 scrollbar-track-neutral-700">
                                <table class="w-full text-sm text-left rtl:text-right text-neutral-500">
                                    <thead class="text-xs uppercase bg-neutral-700 text-neutral-400">
                                        <tr>
                                            <th class="text-center px-6 py-3">name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b bg-neutral-700/50 border-neutral-700"
                                            v-for="(item, index) in categoryData" :key='index'>
                                            <td class="text-center py-1">{{ item == null ?
                                                "NULL" :
                                                item }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- 2 -->
                <h1 class="text-3xl">Menus ({{ menus.length }})</h1>
                <div
                    class="relative overflow-x-auto shadow-md rounded-lg h-[800px] scrollbar-thin scrollbar-thumb-neutral-300 scrollbar-track-neutral-700">
                    <table class="table-auto w-[100%] text-sm text-left rtl:text-right text-neutral-500 ">
                        <thead class="sticky z-10 top-0 text-xs uppercase bg-neutral-700 text-neutral-400">
                            <tr>
                                <th class="text-center px-6 py-3" v-for="index in categoryMenu.length - 1" :key="index">
                                    {{
                                        categoryMenu[index - 1] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b bg-neutral-700/50 border-neutral-700 hover:bg-neutral-700/70 cursor-pointer"
                                v-for="(item, index) in menus" :key='index'
                                @click="modalMeunShow = true; setMenuModal(item)">
                                <td class="text-center" v-for="index in categoryMenu.length - 1" :key="index">{{
                                    item[categoryMenu[index - 1]] == null ? "NULL" : categoryMenu[index - 1] != 'image'
                                        ?
                                        item[categoryMenu[index - 1]] : null }}
                                    <img v-if="categoryMenu[index - 1] == 'image'" class="w-[200px] m-auto rounded my-2"
                                        :src="'/src/assets/images/menus/' + item[categoryMenu[index - 1]]">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </section>

        <transition name="slide">
            <ModalMenu v-show="modalMeunShow" @showModal="closeModal">
                <form class="p-3" @submit.prevent="updateMenu">
                    <h1 class="text-3xl py-6 text-center font-extrabold">Update menu</h1>
                    <div class="flex flex-col m-1 gap-1">

                        <div class="flex flex-col m-2">
                            <label class="ml-2" for="name">Name:</label>
                            <input
                                class="border-2 px-5 py-2 rounded-full outline-none focus:border-sky-300 bg-neutral-50"
                                type="text" id="name" placeholder="Name" v-model="menuModal.menuName" required>
                        </div>

                        <div class="flex flex-col m-2">
                            <label class="ml-2" for="name">Category:</label>
                            <select @change="updateCategoryName($event)"
                                class="border-2 px-4 py-2 rounded-full outline-none focus:border-sky-300 bg-neutral-50"
                                required>
                                <option class="" v-for="(item, index) in categoryData" :key="index" :value="item"
                                    :selected="menuModal.categoryName == item">{{ item }}</option>
                            </select>
                        </div>

                        <div class="flex flex-col m-2">
                            <label class="ml-3" for="name">Price:</label>
                            <input
                                class="border-2 px-5 py-2 rounded-full outline-none focus:border-sky-300 bg-neutral-50"
                                type="number" placeholder="Price" v-model="menuModal.price" required>
                        </div>

                        <div class="flex flex-col m-2">
                            <label class="ml-3" for="name">Description:</label>
                            <input
                                class="border-2 px-5 py-2 rounded-full outline-none focus:border-sky-300 bg-neutral-50"
                                type="text" placeholder="Description (Optional)" v-model="menuModal.description">
                        </div>

                        <div class="flex flex-col m-2">
                            <img v-if="previewImage == ''" class="rounded lg:w-[350px] w-[200px] m-auto"
                                :src="'/src/assets/images/menus/' + menuModal.image" alt="">
                            <img v-else class="rounded lg:w-[350px] w-[200px] m-auto" :src="previewImage" alt="">
                        </div>

                        <div class="flex flex-col m-2">
                            <input @change="handleFile($event)"
                                class="border-2 rounded-full outline-none bg-neutral-300/50 file:bg-neutral-50 hover:file:bg-neutral-200/80 file:border-0 file:me-5 file:py-3 file:px-4 rounded-l-lg cursor-pointer hover:file:cursor-pointer"
                                id="menuPrice" type="file" />
                            <p class="pt-3 pl-3 text-sm " id="file_input_help">Webp. (900x600px) <span
                                    v-show="fileError" class="text-red-400">{{ 'file name error' }}</span> </p>
                        </div>

                        <div class="grid lg:grid-cols-2 grid-cols-1">
                            <button type="submit"
                                class="m-3 p-3 bg-gradient-to-r from-indigo-300 to-violet-400 rounded-full font-bold hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="fileError">Update</button>

                            <button type="button" @click="deleteMenu"
                                class="m-3 p-3 bg-gradient-to-r from-red-400 to-rose-400 rounded-full font-bold hover:opacity-90 text-white disabled:opacity-50 disabled:cursor-not-allowed">Delete</button>
                        </div>

                        <div class="m-1"></div>
                    </div>
                </form>
            </ModalMenu>
        </transition>
    </div>
</template>
