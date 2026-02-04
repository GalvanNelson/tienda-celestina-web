<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

// Estado del menú (inicia cerrado para móviles, puedes cambiar a true si prefieres)
const isSidebarOpen = ref(false);

const user = computed(() => usePage().props.auth.user);
// Ajusta 'role_type' al nombre real de tu columna de rol en la base de datos
const userRole = computed(() => user.value.role_type || 'cliente');

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};
</script>

<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden text-gray-800">
        
        <div 
            v-if="isSidebarOpen" 
            @click="closeSidebar" 
            class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm transition-opacity md:hidden"
        ></div>

        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transition-all duration-300 flex flex-col md:relative',
                isSidebarOpen ? 'w-56 translate-x-0' : 'w-20 -translate-x-full md:translate-x-0'
            ]"
        >
            <div class="h-16 flex items-center justify-center border-b border-gray-100 shrink-0">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-8 w-auto fill-current" />
                </Link>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span v-show="isSidebarOpen" class="ms-3 font-medium">Dashboard</span>
                    </div>
                </NavLink>

                <template v-if="userRole === 'propietario'">
                    <p v-show="isSidebarOpen" class="text-[10px] uppercase font-bold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Almacén</p>
                    <NavLink :href="route('productos.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Productos</span>
                        </div>
                    </NavLink>
                    <NavLink :href="route('categorias.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Categoria</span>
                        </div>
                    </NavLink>
                    <NavLink :href="route('unidades.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1zm4 0v4m4-4v2m4-2v4m-8 6v2m4-2v2"/>
                            </svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Unidad de medida</span>
                        </div>
                    </NavLink>
                    <NavLink :href="route('clientes.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6v-2a4 4 0 00-4-4H4a4 4 0 00-4 4v2m11-10a4 4 0 11-8 0 4 4 0 018 0m6-3a4 4 0 11-8 0 4 4 0 018 0"/>
                            </svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Clientes</span>
                        </div>
                    </NavLink>
                    <NavLink :href="route('vendedores.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6a2 2 0 114 0v2h5a1 1 0 011 1v3H4V9a1 1 0 011-1h5V6zm10 6v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6"/>
                            </svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Vendedores</span>
                        </div>
                    </NavLink>
                </template>

                 <template v-else-if="userRole === 'vendedor'">
                    <p v-show="isSidebarOpen" class="text-[10px] uppercase font-bold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Productos</p>  
                    <NavLink :href="route('productos.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Productos</span>
                        </div>
                    </NavLink>                
                </template>

                 <template v-else-if="userRole === 'cliente'">
                    <p v-show="isSidebarOpen" class="text-[10px] uppercase font-bold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Compras</p>  
                    <NavLink :href="route('cliente.tienda')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Catalogo</span>
                        </div>
                    </NavLink>                
                    <NavLink :href="route('cliente.compras.index')" class="w-full">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span v-show="isSidebarOpen" class="ms-3 font-medium">Compras</span>
                        </div>
                    </NavLink>                
                </template>
            </nav>

            <div class="p-4 border-t border-gray-100 shrink-0">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shrink-0">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div v-show="isSidebarOpen" class="min-w-0">
                        <p class="text-xs font-bold truncate">{{ user.name }}</p>
                        <p class="text-[10px] text-gray-500 truncate capitalize">{{ userRole }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden" @click="isSidebarOpen ? closeSidebar() : null">
            
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 shrink-0 z-30">
                <div class="flex items-center gap-4">
                    <button 
                        @click.stop="toggleSidebar" 
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    >
                        <svg v-if="!isSidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    </button>

                    <h2 v-if="$slots.header" class="text-lg font-bold text-gray-800 hidden sm:block">
                        <slot name="header" />
                    </h2>
                </div>

                <div class="flex items-center">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div class="flex flex-col text-right mr-2 hidden md:flex">
                                        <span class="text-xs font-bold text-gray-900 leading-none mb-1">{{ user.name }}</span>
                                        <span class="text-[10px] text-gray-500 leading-none capitalize">{{ userRole }}</span>
                                    </div>
                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <div class="block px-4 py-2 text-xs text-gray-400 border-b border-gray-100">
                                {{ user.email }}
                            </div>
                            <DropdownLink :href="route('profile.edit')"> Mi Perfil </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Cerrar Sesión
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>