<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ clientes: Object });

const deleteCliente = (id) => {
    if (confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.')) {
        router.delete(route('clientes.destroy', id));
    }
};
</script>

<template>

    <Head title="Clientes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Clientes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <div class="flex justify-end mb-4">
                    <Link :href="route('clientes.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        + Registrar Cliente
                    </Link>
                </div>

                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre/Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">C.I.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Limite de
                                    credito
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo actual</th>                                
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="cliente in clientes.data" :key="cliente.codigo_cliente">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ cliente.nombre_cliente }}</div>
                                    <div class="text-sm text-gray-500">{{ cliente.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ cliente.carnet_identidad }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ cliente.limite_credito }}</td>                                
                                <td class="px-6 py-4 text-sm text-gray-500">{{ cliente.saldo_actual }}</td>                                
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <Link :href="route('clientes.edit', cliente.codigo_cliente)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                    <button @click="deleteCliente(cliente.codigo_cliente)"
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