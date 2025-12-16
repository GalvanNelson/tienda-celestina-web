<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    qrImage: String,
    ventaId: Number,
    total: Number
});
</script>

<template>
    <Head title="Cobro QR" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <h2 class="text-2xl font-bold mb-4">Escanea para Pagar</h2>
                    <p class="mb-4 text-gray-600">Venta #{{ ventaId }} - Total: {{ total }} Bs</p>
                    
                    <div class="flex justify-center mb-6">
                        <img :src="'data:image/png;base64,' + qrImage" alt="Código QR de Pago" class="w-64 h-64 border" />
                    </div>

                    <p class="text-sm text-gray-500 mb-6">
                        El estado del pago se actualizará automáticamente una vez el cliente realice la transferencia.
                    </p>

                    <Link :href="route('ventas.create')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Volver al Punto de Venta
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>