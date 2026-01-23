<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'; 
import { ref, watch } from 'vue';
import ShowProductModal from './Show.vue';

const props = defineProps({
    productos: Object,
    filters: Object
});

const showModal = ref(false);
const selectedProduct = ref(null);
const search = ref(props.filters?.search || '');
let searchTimeout;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('productos.index'), { search: value }, { preserveState: true, replace: true });
    }, 500);
});

const openShowModal = (producto) => {
    selectedProduct.value = producto;
    showModal.value = true;
};

const closeShowModal = () => {
    showModal.value = false;
    selectedProduct.value = null;
};

const deleteProduct = (id) => {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        router.delete(route('productos.destroy', id));
    }
};
</script>

<template>
    <Head title="Inventario" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Productos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4 px-4 sm:px-0">
                    <div class="w-full sm:flex-1">
                        <input v-model="search" type="text" placeholder="Buscar producto..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none text-sm" />
                    </div>
                    <Link :href="route('productos.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-center text-sm font-medium whitespace-nowrap">
                        + Nuevo
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Vista de tabla para pantallas medianas y grandes -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>                                    
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="prod in productos.data" :key="prod.codigo_producto">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img v-if="prod.imagen_completa_url" :src="prod.imagen_completa_url" class="w-12 h-12 object-cover rounded">
                                        <span v-else class="text-xs text-gray-400">Sin img</span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ prod.nombre_producto }}</td>
                                    <td class="px-6 py-4">{{ prod.precio_unitario }} Bs</td>                                    
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <span v-if="prod.unidad_medida" class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                            {{ prod.unidad_medida.nombre }}
                                        </span>
                                        <span v-else class="text-gray-400 text-xs">N/A</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button @click="openShowModal(prod)" class="text-green-600 hover:text-green-900">Ver</button>
                                        <Link :href="route('productos.edit', prod.codigo_producto)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                        <button @click="deleteProduct(prod.codigo_producto)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista de cards para dispositivos móviles y tablets -->
                    <div class="md:hidden">
                        <div class="divide-y divide-gray-200">
                            <div v-for="prod in productos.data" :key="prod.codigo_producto" class="p-4">
                                <div class="flex gap-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <img v-if="prod.imagen_completa_url" :src="prod.imagen_completa_url" class="w-16 h-16 object-cover rounded">
                                        <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-xs text-gray-400">Sin img</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <h3 class="font-semibold text-gray-900">{{ prod.nombre_producto }}</h3>
                                        <p class="text-lg font-bold text-indigo-600">{{ prod.precio_unitario }} Bs</p>
                                        <div v-if="prod.unidad_medida" class="mt-1">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                {{ prod.unidad_medida.nombre }}
                                            </span>
                                        </div>
                                        <div v-else class="text-gray-400 text-xs mt-1">N/A</div>
                                    </div>
                                </div>
                                <div class="flex gap-2 justify-end flex-wrap">
                                    <button @click="openShowModal(prod)" class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">Ver</button>
                                    <Link :href="route('productos.edit', prod.codigo_producto)" class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">Editar</Link>
                                    <button @click="deleteProduct(prod.codigo_producto)" class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border-t" v-if="productos.links">
                         <Pagination :links="productos.links" />
                    </div>
                </div>
            </div>
        </div>
        <ShowProductModal v-if="selectedProduct" :show="showModal" :producto="selectedProduct" @close="closeShowModal" />
    </AuthenticatedLayout>
</template>