<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    compra: Object
});

// Formateadores
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Detalle de Compra" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Codigo: {{ compra.codigo_venta }}
                </h2>
                <Link :href="route('compras.index')" class="text-sm text-blue-600 hover:underline">
                    &larr; Volver al historial
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-6">
                        <div>
                            <p class="text-gray-500 text-sm">Fecha de Venta</p>
                            <p class="font-bold">{{ formatDate(compra.fecha_venta) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Estado</p>
                            <span class="px-2 py-1 text-xs font-bold rounded-full" 
                                :class="compra.estado_venta === 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ compra.estado_venta.toUpperCase() }}
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Método de Pago</p>
                            <p class="font-bold capitalize">{{ compra.tipo_pago }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Productos Comprados</h3>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="detalle in compra.detalles" :key="detalle.codigo_detalle_venta">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ detalle.producto_relacion?.nombre_producto || 'Producto no encontrado' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">
                                        {{ detalle.cantidad }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-600">
                                        {{ formatCurrency(detalle.precio_unitario) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">
                                        {{ formatCurrency(detalle.subtotal) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">TOTAL PAGADO:</td>
                                    <td class="px-6 py-4 text-right font-black text-xl text-indigo-600">
                                        {{ formatCurrency(compra.monto_total) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>