<template>
    <div class="card p-5">
        <Menubar :model="items" class="!text-xs !font-medium !bg-white-500 !shadow-md !shadow-white-500/50">
            <template #end>
                <Button v-if="!currentUser" label="Login" icon="pi pi-user" @click="showDialog = true" />
                <SplitButton v-else :model="itemsSplitBtn" severity="secondary" size="small" outlined>
                    <span class="flex items-center cursor-pointer">
                        <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" class="mr-2"
                            shape="circle" style="width: 22px; height: 22px;" />
                        <span class="!text-xs">{{ currentUser.user_name }}</span>
                    </span>
                </SplitButton>
            </template>
        </Menubar>
    </div>

    <!-- dialog login start-->
    <AuthFrm v-model:visible="showDialog" />
    <!-- dialog login end -->
</template>

<script setup>
import { onMounted, computed } from "vue";
import Menubar from 'primevue/menubar';
import AuthFrm from "@/components/Auth/AuthFrm.vue";

// pinia
import { storeToRefs } from "pinia";
import { useAuthenticationStore } from "@/stores/authStore.js";
import router from "@/router";

const authStore = useAuthenticationStore();

// state pinia
const { currentUser, showDialog } = storeToRefs(authStore);
// action pinia
const { logoutStore } = authStore;

const baseItems = [
    {
        label: 'Home',
        icon: 'pi pi-home',
        command: () => {
            router.push({ name: 'home' });
        }
    },
];

const items = computed(() => {
    const menuItems = [...baseItems];
    
    // Jika user sudah login, tambahkan menu Products
    if (currentUser.value) {
        menuItems.push({
            label: 'Products',
            icon: 'pi pi-objects-column',
            command: () => {
                router.push({ name: 'products' });
            }
        });
    }
    
    return menuItems;
});

const itemsSplitBtn = [
    {
        label: 'Dashboard',
        class: '!text-xs',
        command: () => {
            console.log('Dashboard clicked');
        }
    },
    {
        separator: true
    },
    {
        label: 'Sign Out',
        class: '!text-xs',
        command: () => {
            logoutStore();
        }
    }
];

onMounted(() => {
    authStore.loadUserFromLocalStorage();
});
</script>