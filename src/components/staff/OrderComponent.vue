<script setup>
const emits = defineEmits(['submit'])

const props = defineProps({
    tableID: {
        type: Number,
        require: true
    },
    orderAt: {
        type: String,
        require: true
    },
    menus: {
        type: Array,
        require: true
    },
    role:{
        type: String,
        require: true
    }
})

const menus = props.menus.filter(item => {
    return props.tableID == item.tableID && props.orderAt == item.orderAt
})

</script>

<template>
    <div class="flex flex-col gap-1 bg-neutral-900/30 p-6 rounded-lg min-w-[320px]">
        <div class="relative mt-2">
            <h1 class="text-lg">Table: {{ tableID }}</h1>
            <span class="absolute top-0 text-xs -mt-4">{{ orderAt }}</span>
        </div>
        <div class="flex flex-col pl-3 py-1 pr-2 text-lg overflow-y-auto max-h-[300px] scrollbar-thin">
            <div class="flex flex-row flex-wrap justify-between" v-for="(menu, index) in menus" :key="index">
                <p>● {{ menu.menuName }}</p>
                <p>x{{ menu.amount }}</p>
            </div>
        </div>
        <div class="flex flex-row gap-3 text-2xl pt-2" v-if="role == 'Chef'">
            <button class="grow bg-teal-500/90 hover:opacity-90 p-1 px-2 rounded-lg uppercase" @click="emits('submit', tableID, orderAt, 2)">Complete</button>
            <button class="grow bg-rose-800/90 hover:opacity-90 p-1 px-2 rounded-lg uppercase" @click="emits('submit', tableID, orderAt, 3)">Cancel</button>
        </div>
        <div class="flex flex-row gap-3 text-2xl pt-2" v-else-if="role == 'Waiter'">
            <button class="grow bg-sky-500/90 hover:opacity-90 p-1 px-2 rounded-lg uppercase" @click="emits('submit', tableID, orderAt, 4)">Serve</button>
        </div>
    </div>
</template>