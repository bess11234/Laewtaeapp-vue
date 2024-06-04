<script setup>
import store from "@/store";
import { computed } from 'vue';

import OrderComponent from "./OrderComponent.vue";

store.dispatch("getOrders")

const orders = computed(()=> store.getters.getOrderFoods)

const orderFoods = computed(() => {

    const identified = Array.from(new Set(orders.value.map(item=> item.tableID))).map(id=>{
        return {
            tableID: id,
            orderAt: orders.value.find(item => item.tableID === id).orderAt
        }
    })
    return identified
})

const updateOrder = (tableID, orderAt, status) =>{
    // status = 2 mean Complete, 3 mean Incomplete
    store.dispatch('completeOrder', {tableID, orderAt, status})
    setTimeout(()=>store.dispatch("getOrders"), 500)
}

</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">

        <div class="flex flex-row gap-3 flex-wrap place-content-center max-w-[100%]" v-if="orderFoods.length > 0">
            <OrderComponent v-for="(order, index) in orderFoods" :key="index" :tableID="order.tableID" :orderAt="order.orderAt" role='Chef' :menus="orders" @submit="updateOrder" />
        </div>
        
        <div v-else>
            <h1>No orders currently</h1>
        </div>

    </section>
</template>

<style></style>