<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const carrito = ref([]);
const mensaje = ref('Cargando carrito...');
const metodoPago = ref('efectivo');
const procesando = ref(false);
const error = ref('');

const totalCarrito = computed(() => {
    return carrito.value.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
});

onMounted(() => {    
    const guardado = localStorage.getItem('mi_carrito');    
    
    if (guardado) {
        try {
            carrito.value = JSON.parse(guardado);
            mensaje.value = `Carrito cargado: ${carrito.value.length} items`;
        } catch (e) {
            console.error('Error al cargar carrito:', e);
            mensaje.value = 'Error al cargar carrito';
        }
    } else {
        mensaje.value = 'No hay carrito guardado';
    }
});

const confirmarPago = () => {
    if (carrito.value.length === 0) {
        error.value = 'El carrito está vacío.';
        return;
    }

    procesando.value = true;
    error.value = '';

    router.post(route('cliente.compras.store'), {        
        metodo_pago: metodoPago.value,
        carrito: carrito.value,
        total: totalCarrito.value,
        tipo_pago: 'contado'
    }, {
        onSuccess: () => {
            localStorage.removeItem('mi_carrito');
            carrito.value = []; 
            router.visit(route('cliente.tienda'), { 
                preserveState: false,
                preserveScroll: true,
                replace: true,
                onFinish: () => {
                    alert('Pago confirmado y carrito limpiado. Gracias por su compra.');
                }
            });           
        },
        onError: () => {
            error.value = 'No se pudo confirmar el pago. Intenta nuevamente.';
        },
        onFinish: () => {
            procesando.value = false;
        }
    });
};
</script>

<template>
    <Head title="Checkout" />
    <AuthenticatedLayout>
        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="max-w-5xl mx-auto">
                <h1 class="text-4xl font-bold mb-8 text-gray-800 flex items-center gap-3">                    
                    Nota de Compra
                </h1>            
                
                <div v-if="carrito.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
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
                                <tr v-for="(item, index) in carrito" :key="item.id" 
                                    class="hover:bg-blue-50 transition duration-150"
                                    :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                                    <td class="px-6 py-4 text-sm text-gray-700 font-mono">{{ item.id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ item.nombre }}</td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <span class="inline-flex items-center justify-center w-12 h-8 bg-blue-100 text-blue-800 rounded-full font-semibold">
                                            {{ item.cantidad }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-700">{{ item.precio.toFixed(2) }} Bs</td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">
                                        {{ (item.cantidad * item.precio).toFixed(2) }} Bs
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-t-2 border-blue-600">
                                    <td colspan="4" class="px-6 py-5 text-right text-lg font-bold text-gray-700 uppercase">
                                        Total:
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-xl font-bold text-green-600">
                                            {{ carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2) }} Bs
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Método de pago</h3>
                            <span class="text-xs text-gray-500">Selecciona una opción</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label
                                class="group relative flex items-center gap-4 rounded-xl border-2 p-4 cursor-pointer transition"
                                :class="metodoPago === 'efectivo' ? 'border-blue-600 bg-blue-50 shadow-sm' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/40'"
                            >
                                <input
                                    v-model="metodoPago"
                                    type="radio"
                                    value="efectivo"
                                    class="sr-only"
                                />
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl">
                                    💵
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">Efectivo</p>
                                    <p class="text-xs text-gray-500">Pago contra entrega</p>
                                </div>
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                    :class="metodoPago === 'efectivo' ? 'border-blue-600 bg-blue-600' : 'border-gray-300'"
                                >
                                    <span class="block w-2 h-2 rounded-full bg-white" v-if="metodoPago === 'efectivo'"></span>
                                </div>
                            </label>

                            <label
                                class="group relative flex items-center gap-4 rounded-xl border-2 p-4 cursor-pointer transition"
                                :class="metodoPago === 'qr' ? 'border-emerald-600 bg-emerald-50 shadow-sm' : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/40'"
                            >
                                <input
                                    v-model="metodoPago"
                                    type="radio"
                                    value="qr"
                                    class="sr-only"
                                />
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-xl">
                                    📱
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">QR</p>
                                    <p class="text-xs text-gray-500">PagoFácil / Pago Express</p>
                                </div>
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                    :class="metodoPago === 'qr' ? 'border-emerald-600 bg-emerald-600' : 'border-gray-300'"
                                >
                                    <span class="block w-2 h-2 rounded-full bg-white" v-if="metodoPago === 'qr'"></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div v-if="error" class="px-6 pb-4">
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
                            {{ error }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <button 
                                @click="router.visit(route('cliente.tienda'))"
                                class="px-6 py-2 bg-gray-500 text-white hover:bg-gray-600 transition font-medium">
                                ← Volver al Catálogo
                            </button>
                            <button 
                                @click="confirmarPago"
                                :disabled="procesando || carrito.length === 0"
                                class="px-6 py-2 bg-green-600 text-white hover:bg-green-700 disabled:bg-gray-400 transition font-bold text-lg shadow-lg">
                                {{ procesando ? 'Procesando...' : 'Confirmar Pedido' }}
                            </button>
                        </div>
                    </div>
                </div>
                
                <div v-else class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <span class="text-4xl mr-4">⚠️</span>
                        <div>
                            <p class="font-bold text-yellow-800 text-lg">El carrito está vacío</p>
                            <p class="text-yellow-700 mt-1">Agrega productos desde el catálogo para continuar</p>
                        </div>
                    </div>
                    <button 
                        @click="router.visit(route('cliente.tienda'))"
                        class="mt-4 px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                        Ir al Catálogo
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>