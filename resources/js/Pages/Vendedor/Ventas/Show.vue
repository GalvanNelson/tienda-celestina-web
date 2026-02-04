<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    venta: Object,
    cliente: Object
});

const mostrarFormulario = ref(false);

const form = useForm({
    estado_venta: props.venta?.estado_venta || 'pendiente'
});

const actualizarEstado = () => {
    form.put(route('ventas.update', props.venta.codigo_venta), {
        onSuccess: () => {
            mostrarFormulario.value = false;
        }
    });
};

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
    <Head title="Detalle de Venta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de Venta</h2>            
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">detalle de venta</h3>                        
                    </div>
                    <Link
                        :href="route('ventas.index')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                    >
                        Volver
                    </Link>            
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Código de venta</p>
                            <p class="mt-1 text-sm font-semibold text-blue-800 bg-blue-100 inline-flex px-3 py-1 rounded-full">
                                {{ venta?.codigo_venta ?? '—' }}                               
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Nombre del cliente</p>
                            <p class="mt-1 text-sm font-semibold inline-flex px-3 py-1 rounded-full">
                                {{ cliente.nombre_cliente }}                               
                            </p>
                        </div>                        
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Fecha de venta</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatearFecha(venta?.fecha_venta) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Monto total</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ venta?.monto_total ?? '0.00' }} Bs.</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Tipo de pago</p>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getPagoClass(venta?.tipo_pago)">
                                {{ venta?.tipo_pago ?? '—' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500">Estado</p>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="getEstadoClass(venta?.estado_venta)">
                                {{ venta?.estado_venta ?? '—' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Botón para actualizar estado solo si está pendiente -->
                    <div v-if="venta?.estado_venta === 'pendiente'" class="mt-6">
                        <button
                            v-if="!mostrarFormulario"
                            @click="mostrarFormulario = true"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700"
                        >
                            Actualizar Estado
                        </button>
                        
                        <div v-else class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Cambiar estado de la venta</h5>
                            <form @submit.prevent="actualizarEstado" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo Estado</label>
                                    <select
                                        v-model="form.estado_venta"
                                        class="block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="pendiente">Pendiente</option>
                                        <option value="pagado">Pagado</option>
                                        <option value="enviado">Enviado</option>
                                        <option value="entregado">Entregado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                
                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 disabled:opacity-50"
                                    >
                                        Guardar
                                    </button>
                                    <button
                                        type="button"
                                        @click="mostrarFormulario = false"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Detalle de la venta</h4>
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
                                <tr v-for="detalle in venta?.detalles || []" :key="detalle.id">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ detalle.producto_relacion.codigo_producto }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ detalle.producto_relacion.nombre_producto }}</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ detalle.cantidad }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ detalle.precio_unitario }} Bs.</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ detalle.subtotal }} Bs.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-t-2 border-blue-600">
                                    <td colspan="4" class="px-6 py-5 text-right text-lg font-bold text-gray-700 uppercase">
                                        Total:
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xl font-bold text-green-600">
                                            {{ venta?.monto_total ?? '0.00' }} Bs.
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