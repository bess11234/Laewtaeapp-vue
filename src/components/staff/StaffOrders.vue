<script setup>
import { RouterLink, RouterView } from 'vue-router';
import { useRoute } from 'vue-router';

const $route = useRoute()
// console.log($route.fullPath)
</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">
        <transition name="slide" mode="out-in">
            <div class="flex md:flex-row flex-col gap-12 m-auto justify-center items-center"
                v-if="$route.fullPath == '/staff/orders'">

                <RouterLink
                    class="grow bg-neutral-700/40 w-[50%] max-w-[500px] min-h-[300px] rounded-lg p-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity opacity-50 hover:opacity-100"
                    :to="{ name: 'orderChef' }">
                    <div>
                        <div class="flex flex-col gap-3 items-center">
                            <h1 class="text-[3rem] text-center text-neutral-100">Chef</h1>
                            <img class="w-[296px]" src="https://cdn-icons-png.flaticon.com/512/3461/3461974.png" alt="">
                        </div>
                    </div>
                </RouterLink>

                <RouterLink
                    class="grow bg-neutral-700/40 w-[50%] max-w-[500px] min-h-[300px] rounded-lg p-6 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity opacity-50 hover:opacity-100"
                    :to="{ name: 'orderWaiter' }">
                    <div>
                        <div class="flex flex-col gap-3 items-center">
                            <h1 class="text-[3rem] text-center text-neutral-100">Waiter</h1>
                            <img class="w-[296px]" src="https://cdn-icons-png.flaticon.com/512/3462/3462049.png" alt="">
                        </div>
                    </div>
                </RouterLink>

            </div>

            <div v-else>
                <div class="flex md:flex-row flex-col gap-6 m-auto justify-center items-center">

                    <RouterLink
                        class="grow bg-neutral-700/40 w-[50%] max-w-[200px] rounded-lg py-3 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                        :class="{'opacity-50': $route.fullPath != '/staff/orders/chef', 'hover:opacity-100' : $route.fullPath != '/staff/orders/chef'}"
                        :to="{ name: 'orderChef' }">
                        <div class="flex flex-col">
                            <div
                                class="flex flex-row gap-1 items-center size-fit m-auto">
                                <h1 class="text-[1.25rem] text-center text-neutral-100">Chef</h1>
                                <img class="w-[40px]" src="https://cdn-icons-png.flaticon.com/512/3461/3461974.png" alt="">
                            </div>
                        </div>
                    </RouterLink>

                    <RouterLink
                        class="grow bg-neutral-700/40 w-[50%] max-w-[200px] rounded-lg p-3 cursor-pointer shadow-lg hover:shadow-neutral-500/20 transition-opacity"
                        :class="{'opacity-50': $route.fullPath != '/staff/orders/waiter', 'hover:opacity-100' : $route.fullPath != '/staff/orders/waiter'}"
                        :to="{ name: 'orderWaiter' }">
                        <div class="flex flex-col">
                            <div
                                class="flex flex-row gap-1 items-center size-fit m-auto">
                                <h1 class="text-[1.25rem] text-center text-neutral-100">Waiter</h1>
                                <img class="w-[40px]" src="https://cdn-icons-png.flaticon.com/512/3462/3462049.png" alt="">
                            </div>
                        </div>
                    </RouterLink>

                </div>

                <RouterView v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </RouterView>
            </div>

        </transition>

    </section>
</template>

<style></style>