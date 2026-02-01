<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    compra: Object,
});

const formatearFecha = (fechaString) => {
    if (!fechaString) return 'Sin fecha';
    const fecha = new Date(fechaString);

    return fecha.toLocaleString('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
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
    <Head title="Detalle de Compra" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">Detalle de Compra</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Resumen de la compra</h3>
                        <p class="text-sm text-gray-600">Detalle completo de tu pedido</p>
                    </div>
                    <Link
                        :href="route('cliente.compras.index')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                    >
                        Volver
                    </Link>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Código</p>
                            <p class="mt-1 text-sm font-semibold text-blue-800 bg-blue-100 inline-flex px-3 py-1 rounded-full">
                                {{ compra?.codigo_venta ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Vendedor</p>
                            <p class="mt-1 text-sm text-gray-900">{{ compra?.vendedor ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Fecha de venta</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatearFecha(compra?.fecha_venta) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Monto total</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ compra?.monto_total ?? '0.00' }} Bs.</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Tipo de pago</p>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getPagoClass(compra?.tipo_pago)">
                                {{ compra?.tipo_pago ?? '—' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Estado</p>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getEstadoClass(compra?.estado_venta)">
                                {{ compra?.estado_venta ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Detalle de productos</h4>
                    </div>

                    <div class="overflow-x-auto">
                        
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Código</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider">Precio Unit.</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in compra?.detalles ?? []" :key="item.id" class="px-6 py-4 whitespace-nowrap">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.producto }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.producto_relacion.nombre_producto }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ item.cantidad }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ item.precio_unitario }} Bs.</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">{{ item.subtotal }} Bs.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-t-2 border-blue-600">
                                    <td colspan="4" class="px-6 py-5 text-right text-lg font-bold text-gray-700 uppercase">
                                        Total:
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xl font-bold text-green-600">
                                            {{ compra?.monto_total ?? '0.00' }} Bs.
                                        </span>
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