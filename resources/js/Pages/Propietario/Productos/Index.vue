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
    // Limpiar timeout anterior
    clearTimeout(searchTimeout);
    
    // Esperar 500ms después de que el usuario deje de escribir
    searchTimeout = setTimeout(() => {
        router.get(
            route('productos.index'),
            { search: value }, // Enviamos el término como parámetro ?search=...
            {
                preserveState: true, // Evita que el componente se reinicie por completo
                replace: true        // Reemplaza la entrada en el historial del navegador
            }
        );
    }, 500); // 500ms de debounce
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

                <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
                    <div class="w-full sm:w-1/3">
                        <input v-model="search" type="text" placeholder="Buscar producto por nombre..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <Link :href="route('productos.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-center">
                        + Nuevo Producto
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Desktop table -->
                    <div class="hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Imagen</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unidad de Medida</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="prod in productos.data" :key="prod.codigo_producto">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img v-if="prod.imagen_url" :src="prod.imagen_url"
                                                class="w-16 h-16 object-cover rounded-md">
                                            <span v-else class="text-gray-400">Sin img</span>
                                        </td>
                                        <td class="px-6 py-4">{{ prod.nombre_producto }}</td>
                                        <td class="px-6 py-4">{{ prod.precio_unitario }} Bs</td>
                                        <td class="px-6 py-4">
                                            {{ prod.unidad_medida_relacion?.nombre }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                            <button @click="openShowModal(prod)"
                                                class="text-green-600 hover:text-green-900">
                                                Ver
                                            </button>
                                            <Link :href="route('productos.edit', prod.codigo_producto)"
                                                class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                            <button @click="deleteProduct(prod.codigo_producto)"
                                                class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile cards -->
                    <div class="grid gap-4 p-4 md:hidden">
                        <div v-for="prod in productos.data" :key="prod.codigo_producto"
                            class="border rounded-lg p-4 shadow-sm space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden">
                                    <img v-if="prod.imagen_url" :src="prod.imagen_url"
                                        class="w-full h-full object-cover">
                                    <span v-else
                                        class="text-xs text-gray-400 flex items-center justify-center h-full">Sin
                                        img</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Producto</p>
                                    <p class="font-semibold text-gray-800">{{ prod.nombre_producto }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between text-sm text-gray-700">
                                <div>
                                    <p class="text-gray-500">Precio</p>
                                    <p class="font-medium">{{ prod.precio_unitario }} Bs</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-500">Unidad</p>
                                    <p class="font-medium">{{ prod.unidad_medida_relacion?.nombre }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4 text-sm font-medium">
                                <button @click="openShowModal(prod)"
                                    class="text-green-600 hover:text-green-900">Ver</button>
                                <Link :href="route('productos.edit', prod.codigo_producto)"
                                    class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                <button @click="deleteProduct(prod.codigo_producto)"
                                    class="text-red-600 hover:text-red-900">Eliminar</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border-t">
                        <Pagination :links="productos.links" />
                    </div>
                </div>
            </div>
        </div>
        <ShowProductModal v-if="selectedProduct" :show="showModal" :producto="selectedProduct"
            @close="closeShowModal" />
    </AuthenticatedLayout>
</template>