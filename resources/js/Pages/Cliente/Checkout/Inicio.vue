<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

// Eliminamos props.carrito porque lo leeremos localmente
const carrito = ref([]);

// Cargamos el carrito al entrar a la página
onMounted(() => {
    const guardado = localStorage.getItem('mi_carrito');
    if (guardado) {
        carrito.value = JSON.parse(guardado);

        // Sincronizamos con el formulario de Inertia
        form.carrito = carrito.value;
        form.total = calcularTotal();
    } else {
        // Si no hay carrito, devolver a la tienda
        router.visit(route('cliente.tienda'));
    }
});

const calcularTotal = () => {
    return carrito.value.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
};

// Formulario de Inertia
const form = useForm({
    metodo_pago: 'efectivo',
    carrito: [], // Se llenará en onMounted
    tipo_pago: 'contado', // Nuevo campo para el tipo de pago
    total: 0
});

const procesarCompra = () => {
    form.post(route('compras.store'), {
        onBefore: () => {
            // Confirmación opcional antes de enviar
            return confirm('¿Estás seguro de confirmar esta compra?');
        },
        onSuccess: () => {
            // Limpiamos el localStorage localmente
            localStorage.removeItem('mi_carrito');
            
            // Si usas una librería de notificaciones como SweetAlert2 o Toastr:
            // alert('Venta pendiente creada exitosamente.');
        },
        onError: (errors) => {
            console.error("Errores en el formulario:", errors);
        }
    });
};

const volverAlCatalogo = () => {
    router.visit(route('cliente.tienda'));
};
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto bg-white shadow rounded-lg mt-10">
        <h2 class="text-2xl font-bold mb-4">Resumen de Compra</h2>

        <ul class="mb-4 border-b pb-4">
            <li v-for="item in carrito" :key="item.id" class="flex justify-between py-1">
                <span>{{ item.cantidad }}x {{ item.nombre }}</span>
                <span>{{ (item.precio * item.cantidad).toFixed(2) }} Bs</span>
            </li>
        </ul>

        <div class="flex justify-between text-xl font-bold mb-6">
            <span>Total a Pagar:</span>
            <span>{{ form.total.toFixed(2) }} Bs</span>
        </div>

        <div class="mb-6">
            <label class="block font-medium mb-3 text-gray-700">Método de Pago:</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    :class="{ 'border-blue-600 bg-blue-50': form.metodo_pago === 'efectivo', 'border-gray-200': form.metodo_pago !== 'efectivo' }"
                    class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none transition-all hover:bg-gray-50">
                    <input type="radio" v-model="form.metodo_pago" value="efectivo" class="sr-only">
                    <div class="flex flex-1">
                        <div class="flex flex-col">
                            <span class="block text-sm font-bold text-gray-900">💵 Efectivo</span>
                            <span class="mt-1 flex items-center text-xs text-gray-500">Pago contra entrega</span>
                        </div>
                    </div>
                    <div v-if="form.metodo_pago === 'efectivo'" class="text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </label>

                <label
                    :class="{ 'border-blue-600 bg-blue-50': form.metodo_pago === 'qr', 'border-gray-200': form.metodo_pago !== 'qr' }"
                    class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none transition-all hover:bg-gray-50">
                    <input type="radio" v-model="form.metodo_pago" value="qr" class="sr-only">
                    <div class="flex flex-1">
                        <div class="flex flex-col">
                            <span class="block text-sm font-bold text-gray-900">📱 QR</span>
                            <span class="mt-1 flex items-center text-xs text-gray-500">Pago Fácil</span>
                        </div>
                    </div>
                    <div v-if="form.metodo_pago === 'qr'" class="text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </label>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mt-6">
            <button type="button" @click="volverAlCatalogo"
                class="w-full sm:w-1/2 bg-red-600 text-white py-3 rounded font-bold hover:bg-red-700 transition shadow-md">
                Volver al Catálogo
            </button>

            <button @click="procesarCompra" :disabled="form.processing || carrito.length === 0"
                class="w-full sm:w-1/2 bg-blue-600 text-white py-3 rounded font-bold hover:bg-blue-700 disabled:bg-gray-400 transition shadow-md">
                <span v-if="form.processing">Procesando...</span>
                <span v-else>Confirmar Compra</span>
            </button>
        </div>
    </div>
</template>