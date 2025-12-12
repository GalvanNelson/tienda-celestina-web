<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    productos: Object
});

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
                
                <div class="flex justify-end mb-4">
                    <Link :href="route('productos.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        + Nuevo Producto
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad de Medida</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="prod in productos.data" :key="prod.codigo_producto">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img v-if="prod.imagen" :src="`/storage/${prod.imagen}`" class="h-10 w-10 rounded-full object-cover">
                                    <span v-else class="text-gray-400">Sin img</span>
                                </td>
                                <td class="px-6 py-4">{{ prod.nombre_producto }}</td>
                                <td class="px-6 py-4">{{ prod.precio_unitario }} BOB</td>
                                <td class="px-6 py-4">
                                    {{ prod.unidad_medida_relacion?.nombre }}
                                </td>
                                <td class="px-6 py-4">{{ prod.categoria_relacion?.nombre }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <Link :href="route('productos.edit', prod.codigo_producto)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                    <button @click="deleteProduct(prod.codigo_producto)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="p-4" v-if="productos.links">
                         </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>