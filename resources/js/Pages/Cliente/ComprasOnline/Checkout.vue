<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const carrito = ref([]);
const tipoPago = ref('contado');
const metodoPago = ref('efectivo');

const totalCarrito = computed(() => {
    return carrito.value.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
});

const form = useForm({
    carrito: [],
    total: 0,
    tipo_pago: 'contado',
    metodo_pago: 'efectivo'
});

onMounted(() => {
    const guardado = localStorage.getItem('mi_carrito');
    if (guardado) {
        carrito.value = JSON.parse(guardado);
    }
});

const procederAlPago = () => {
    if (carrito.value.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    form.carrito = carrito.value;
    form.total = totalCarrito.value;
    form.tipo_pago = tipoPago.value;
    form.metodo_pago = metodoPago.value;

    form.post(route('cliente.compras.store'), {
        onSuccess: () => {
            localStorage.removeItem('mi_carrito');
            router.visit(route('cliente.compras.index'));
        }
    });
};

const volver = () => {
    router.visit(route('cliente.tienda'));
};
</script>

<template>
    <Head title="Checkout" />
    <AuthenticatedLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Confirmar Compra</h1>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Resumen del Carrito -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4">Resumen de tu Carrito</h2>

                        <div v-if="carrito.length === 0" class="text-center text-gray-500 py-8">
                            <p>El carrito está vacío.</p>
                            <button @click="volver" class="mt-4 text-blue-600 hover:underline">
                                Volver al catálogo
                            </button>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(item, index) in carrito" :key="item.id" class="flex justify-between items-center border-b pb-4">
                                <div>
                                    <h3 class="font-bold text-lg">{{ item.nombre }}</h3>
                                    <p class="text-sm text-gray-600">Código: {{ item.id }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600">{{ item.cantidad }} x {{ item.precio }} Bs</p>
                                    <p class="font-bold text-lg">{{ (item.cantidad * item.precio).toFixed(2) }} Bs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones de Pago y Resumen -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4">Opciones de Pago</h2>

                        <div class="mb-6">
                            <label class="block text-sm font-bold mb-2">Tipo de Pago:</label>
                            <select v-model="tipoPago" class="w-full border rounded px-3 py-2">
                                <option value="contado">Contado</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold mb-2">Método de Pago:</label>
                            <select v-model="metodoPago" class="w-full border rounded px-3 py-2">
                                <option value="efectivo">Efectivo</option>
                                <option value="qr">QR</option>
                            </select>
                        </div>

                        <div class="bg-gray-50 p-4 rounded mb-6">
                            <div class="flex justify-between text-lg font-bold mb-2">
                                <span>Subtotal:</span>
                                <span>{{ totalCarrito }} Bs</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-green-600">{{ totalCarrito }} Bs</span>
                            </div>
                        </div>

                        <button 
                            @click="procederAlPago"
                            :disabled="carrito.length === 0 || form.processing"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 disabled:bg-gray-400 transition"
                        >
                            {{ form.processing ? 'Procesando...' : 'Confirmar Compra' }}
                        </button>

                        <button 
                            @click="volver"
                            class="w-full mt-3 bg-gray-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-gray-700 transition"
                        >
                            Volver al Catálogo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
