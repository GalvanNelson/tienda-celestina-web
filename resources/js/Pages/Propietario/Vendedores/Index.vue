<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ vendedores: Object });

const deleteVendedor = (id) => {
    if (confirm('¿Eliminar este vendedor? Esto borrará su acceso al sistema.')) {
        router.delete(route('vendedores.destroy', id));
    }
};
</script>

<template>

    <Head title="Vendedores" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Equipo de Ventas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <div class="flex justify-end mb-4">
                    <Link :href="route('vendedores.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        + Registrar Vendedor
                    </Link>
                </div>

                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre /
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="vend in vendedores.data" :key="vend.codigo_vendedor">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ vend.user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ vend.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ vend.telefono }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ vend.porcentaje_comision }}%</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="vend.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ vend.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <Link :href="route('vendedores.edit', vend.codigo_vendedor)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                    <button @click="deleteVendedor(vend.codigo_vendedor)"
                                        class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>