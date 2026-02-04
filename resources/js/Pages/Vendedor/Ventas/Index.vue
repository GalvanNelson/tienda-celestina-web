<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Recibimos las unidades desde el controlador
defineProps({
    ventas: Object
});

// Función para formatear la fecha
const formatearFecha = (fecha) => {
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    const horas = String(date.getHours()).padStart(2, '0');
    const minutos = String(date.getMinutes()).padStart(2, '0');
    return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
};
</script>

<template>
    <Head title="Ventas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Ventas</h2>
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
                    <Link :href="route('ventas.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        + Nueva Venta
                    </Link>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="ventas && ventas.length === 0" class="p-6 text-center text-gray-500">
                        No hay ventas registradas
                    </div>                    
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="venta in ventas" :key="venta.codigo_venta" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ venta.codigo_venta }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatearFecha(venta.fecha_venta) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Bs. {{ Number(venta.monto_total).toFixed(2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="{
                                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800': venta.estado_venta === 'completada',
                                        'bg-yellow-100 text-yellow-800': venta.estado_venta === 'pendiente',
                                        'bg-red-100 text-red-800': venta.estado_venta === 'cancelada',
                                        'bg-gray-100 text-gray-800': !['completada', 'pendiente', 'cancelada'].includes(venta.estado_venta)
                                    }">
                                        {{ venta.estado_venta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <Link :href="route('ventas.show', venta.codigo_venta)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Ver
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>