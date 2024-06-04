<script setup>
import store from "@/store";
import { computed } from "vue";

store.dispatch("getMembers")

const members = computed(() => store.getters.getMembers)

const updateStatus = (id, status) => {
    const statuses = { 'ACTIVE': 1, 'INACTIVE': 2 }
    status = statuses[status]
    store.dispatch("updateStatus", { userID: id, status: status })
}

const updateRole = (id, role) => {
    const roles = { 'MEMBER': 1, 'STAFF': 3, "MANAGER": 2 }
    role = roles[role]
    store.dispatch("updateRole", { userID: id, role: role })
}

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';

DataTable.use(DataTablesCore);

const columns = [
    { data: 'phoneNumber' },
    { data: 'memberName' },
    { data: 'email' },
    { data: 'points' },
    { data: 'status' },
    { data: 'role' },
]

const options = {
    createdRow: (row, data, dataIndex) => {
        // Example: Add a class based on the status
        row.classList.add('bg-neutral-700/50')
        row.classList.add('border-neutral-700')
        row.classList.add('hover:bg-neutral-700/40')

        let transition = document.createElement("transition")
        transition.setAttribute("name", 'slide')
        // Example: Modify cell content
        const status = row.getElementsByTagName('td')[4]; // Assuming points are in the 5th column

        let select_ele1 = document.createElement("select")
        let inactive = document.createElement("option")
        let active = document.createElement("option")
        status.innerHTML = ""

        select_ele1.classList.add("bg-neutral-800/70")
        select_ele1.classList.add("text-neutral-400")

        select_ele1.addEventListener("change", () => {
            updateStatus(data.userID, select_ele1.value)
        })

        if (status) {
            inactive.innerHTML = 'INACTIVE'
            active.innerHTML = 'ACTIVE'

            if (data.status == "ACTIVE") { active.setAttribute('selected', '') }
            else { inactive.setAttribute('selected', '') }

            select_ele1.appendChild(active)
            select_ele1.appendChild(inactive)
            status.appendChild(select_ele1)
        }

        const role = row.getElementsByTagName('td')[5]; // Assuming points are in the 4th column

        let select_ele = document.createElement("select")
        let option = document.createElement("option")
        role.innerHTML = ""

        select_ele.classList.add("bg-neutral-800/70")
        select_ele.classList.add("text-neutral-400")

        select_ele.addEventListener("change", () => {
            updateRole(data.userID, select_ele.value)
        })
        if (role) {
            option.innerHTML = "MEMBER"
            if (data.role == 'MEMBER') { option.setAttribute("selected", "") }
            select_ele.appendChild(option)

            option = document.createElement("option")
            option.innerHTML = "STAFF"
            if (data.role == 'STAFF') { option.setAttribute("selected", "") }
            select_ele.appendChild(option)

            option = document.createElement("option")
            option.innerHTML = "MANAGER"
            if (data.role == 'MANAGER') { option.setAttribute("selected", "") }
            select_ele.appendChild(option)

            role.appendChild(select_ele)
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
    pageLength: 10,
    responsive: {
        breakpoints: [
            { name: 'phoneNumber', width: Infinity },
            { name: 'memberName', width: 1480 },
            { name: 'email', width: 480 },
            { name: 'points', width: 1188 },
            { name: 'status', width: Infinity },
            { name: 'role', width: Infinity },
        ]
    },
}

</script>

<template>
    <section class="md:my-16 md:mx-16 my-8 mx-8">
        <div class="flex flex-col gap-3">
            <h1 class="text-3xl">Members ({{ members.length }})</h1>
            <DataTable :columns="columns" :options="options" :data="members" class="text-sm text-neutral-500">
                <thead class="sticky z-10 top-0 text-base uppercase bg-neutral-700/20 text-neutral-400">
                    <tr>
                        <th>Phone number</th>
                        <th>Membername</th>
                        <th>Email</th>
                        <th>Points</th>
                        <th>Status</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody class="bg-neutral-700/40"></tbody>
            </DataTable>
        </div>

    </section>
</template>