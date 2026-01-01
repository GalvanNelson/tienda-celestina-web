<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted } from 'vue';

const props = defineProps({
    compras: Object
});

// Helper para moneda (Bolivianos)
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB'
    }).format(amount);
};

// Helper para fecha: Convierte ISO a DD-MM-AAAA
const formatDate = (dateString) => {
    if (!dateString) return 'S/F';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\//g, '-');
};
</script>

<template>

    <Head title="Mis Compras Online" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Compras Online</h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash.success"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <strong class="font-bold">¡Éxito! </strong>
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Codigo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Total
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="compra in compras.data" :key="compra.codigo_venta" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ compra.codigo_venta }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(compra.fecha_venta) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="[
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        compra.estado_venta === 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    ]">
                                        {{ compra.estado_venta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    {{ formatCurrency(compra.monto_total) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('compras.show', compra.codigo_venta)"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Ver Detalles
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="compras.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No hay compras registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="compras.links.length > 3" class="p-4 border-t flex justify-center">
                        <div class="flex flex-wrap">
                            <Link v-for="(link, k) in compras.links" :key="k" :href="link.url || '#'"
                                v-html="link.label" class="mx-1 px-3 py-1 border rounded text-sm"
                                :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-500': !link.url }" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>