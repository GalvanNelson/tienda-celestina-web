<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Recibimos las categorías desde el controlador
defineProps({
    categorias: Object
});

const deleteCategoria = (id) => {
    if (confirm('¿Estás seguro de eliminar esta categoría?')) {
        router.delete(route('categorias.destroy', id));
    }
};
</script>

<template>

    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Categorías</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="$page.props.flash.success"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">¡Éxito! </strong>
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <div v-if="$page.props.flash.error"
                    class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>
                <div class="flex justify-end mb-4">
                    <Link :href="route('categorias.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        + Nueva Categoría
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="cat in categorias.data" :key="cat.codigo_categoria">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    #{{ cat.codigo_categoria }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ cat.nombre }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <Link :href="route('categorias.edit', cat.codigo_categoria)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Editar
                                    </Link>
                                    <button @click="deleteCategoria(cat.codigo_categoria)"
                                        class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="categorias.data.length === 0">
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    No hay categorías registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="p-4 flex justify-between" v-if="categorias.links">
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>