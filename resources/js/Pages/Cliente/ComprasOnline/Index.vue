<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'; 

const props = defineProps({
    compras: Object,    
});

const formatearFecha = (fechaString) => {
    if (!fechaString) return 'Sin fecha';
    const fecha = new Date(fechaString);
    
    // Configuración para mostrar fecha y hora (ej: 30/01/2026 04:51)
    return fecha.toLocaleString('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false // Cambia a true si prefieres formato AM/PM
    });
};

const getEstadoClass = (estado) => {
    const estadoLower = estado?.toLowerCase() || '';
    
    if (estadoLower.includes('completad') || estadoLower.includes('entregad')) {
        return 'bg-green-100 text-green-800';
    } else if (estadoLower.includes('pendiente') || estadoLower.includes('procesando')) {
        return 'bg-yellow-100 text-yellow-800';
    } else if (estadoLower.includes('cancelad') || estadoLower.includes('rechazad')) {
        return 'bg-red-100 text-red-800';
    }
    return 'bg-gray-100 text-gray-800';
};

const getPagoClass = (tipo) => {
    const tipoLower = tipo?.toLowerCase() || '';
    
    if (tipoLower.includes('efectivo') || tipoLower.includes('cash')) {
        return 'bg-purple-100 text-purple-800';
    } else if (tipoLower.includes('tarjeta') || tipoLower.includes('card')) {
        return 'bg-indigo-100 text-indigo-800';
    } else if (tipoLower.includes('transferencia') || tipoLower.includes('banco')) {
        return 'bg-cyan-100 text-cyan-800';
    }
    return 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Compras Realizadas" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">Mis Compras Realizadas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Card Container -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Table Responsive -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Código</th>                                    
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha de Venta</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Monto Total</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tipo de Pago</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Accion</th>
                                </tr>                
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="compra in compras.data" :key="compra.id" class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold text-blue-800">{{ compra.codigo_venta }}</span>
                                    </td>                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatearFecha(compra.fecha_venta) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900">{{ compra.monto_total }} Bs.</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getPagoClass(compra.tipo_pago)">
                                            {{ compra.tipo_pago }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getEstadoClass(compra.estado_venta)">
                                            {{ compra.estado_venta }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link 
                                            :href="route('cliente.compras.show', compra.codigo_venta)" 
                                            class="text-indigo-600 hover:text-indigo-900 font-bold flex items-center gap-1"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver Detalle
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!compras.data || compras.data.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay compras registradas</h3>
                        <p class="mt-2 text-sm text-gray-600">Aún no has realizado ninguna compra en línea</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <Pagination :links="compras.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>