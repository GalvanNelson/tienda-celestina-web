<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    productos: Array
});

// Estado del carrito
const carrito = ref([]);
const mostrarCarrito = ref(false);

// 1. Cargar carrito desde LocalStorage al iniciar
onMounted(() => {
    const guardado = localStorage.getItem('mi_carrito');
    if (guardado) {
        carrito.value = JSON.parse(guardado);
    }
});

// 2. Funciones del Carrito
const agregarAlCarrito = (producto) => {
    const existente = carrito.value.find(item => item.id === producto.codigo_producto);
    
    if (existente) {
        existente.cantidad++;
    } else {
        carrito.value.push({
            id: producto.codigo_producto,
            nombre: producto.nombre_producto,
            precio: parseFloat(producto.precio_unitario),
            cantidad: 1,
            // imagen: producto.imagen
        });
    }
    guardarCarrito();
    mostrarCarrito.value = true; // Abrir carrito al agregar
};

const removerDelCarrito = (index) => {
    carrito.value.splice(index, 1);
    guardarCarrito();
};

const incrementarCantidad = (index) => {
    const item = carrito.value[index];
    if (!item) return;
    item.cantidad = parseInt(item.cantidad || 1, 10) + 1;
    guardarCarrito();
};

const decrementarCantidad = (index) => {
    const item = carrito.value[index];
    if (!item) return;
    const nueva = parseInt(item.cantidad || 1, 10) - 1;
    if (nueva <= 0) {
        carrito.value.splice(index, 1);
    } else {
        item.cantidad = nueva;
    }
    guardarCarrito();
};

const actualizarCantidad = (index, valor) => {
    const item = carrito.value[index];
    if (!item) return;
    let n = parseInt(valor, 10);
    if (isNaN(n) || n < 1) n = 1;
    item.cantidad = n;
    guardarCarrito();
};

const guardarCarrito = () => {
    localStorage.setItem('mi_carrito', JSON.stringify(carrito.value));
};

// 3. Totales
const totalCarrito = computed(() => {
    return carrito.value.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
});

// 4. Ir a Pagar (Navegación a tu Checkout existente)
const irAPagar = () => {
    // Simplemente navegamos. El componente 'Inicio.vue' leerá el localStorage
    router.visit(route('checkout.inicio'));
};
</script>

<template>
    <div class="p-6 bg-gray-100 min-h-screen">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Nuestros Productos</h1>
            <button 
                @click="mostrarCarrito = !mostrarCarrito"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center shadow-lg hover:bg-blue-700 transition"
            >
                🛒 Ver Carrito ({{ carrito.length }})
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div v-for="prod in productos" :key="prod.codigo_producto" class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
                <div class="h-40 bg-gray-200 rounded mb-4 flex items-center justify-center text-gray-500">
                    Imagen Producto
                </div>
                
                <div>
                    <h3 class="font-bold text-lg">{{ prod.nombre_producto }}</h3>
                    <p v-if="prod.descripcion" class="text-gray-600 text-sm mb-2">{{ prod.descripcion }}</p>
                    <p class="text-green-600 font-bold text-xl">{{ prod.precio_unitario }} Bs</p>
                </div>

                <button 
                    @click="agregarAlCarrito(prod)"
                    class="mt-4 w-full bg-gray-800 text-white py-2 rounded hover:bg-black transition"
                >
                    Agregar +
                </button>
            </div>
        </div>

        <div 
            v-if="mostrarCarrito" 
            class="fixed inset-0 bg-black/40 flex justify-end z-50 backdrop-blur-[1px]"
            @click.self="mostrarCarrito = false"
        >
            <div class="bg-white w-full max-w-md h-full p-6 shadow-xl flex flex-col">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h2 class="text-2xl font-bold">Tu Carrito</h2>
                    <button @click="mostrarCarrito = false" class="text-red-500 font-bold text-xl">✕</button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-if="carrito.length === 0" class="text-center text-gray-500 mt-10">
                        El carrito está vacío.
                    </div>
                    
                    <div v-for="(item, index) in carrito" :key="item.id" class="flex justify-between items-center mb-4 border-b pb-2">
                        <div class="min-w-0">
                            <p class="font-bold truncate">{{ item.nombre }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <button @click="decrementarCantidad(index)" class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-100">−</button>
                                <input 
                                    type="number" 
                                    class="w-16 border rounded px-2 py-1 text-center"
                                    :value="item.cantidad"
                                    min="1"
                                    @input="e => actualizarCantidad(index, e.target.value)"
                                />
                                <button @click="incrementarCantidad(index)" class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-100">+</button>
                                <span class="text-sm text-gray-600">x {{ item.precio }} Bs</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-lg whitespace-nowrap">{{ (item.cantidad * item.precio).toFixed(2) }} Bs</span>
                            <button @click="removerDelCarrito(index)" class="text-red-500 hover:text-red-700" title="Quitar">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mt-2">
                    <div class="flex justify-between text-xl font-bold mb-4">
                        <span>Total:</span>
                        <span>{{ totalCarrito }} Bs</span>
                    </div>
                    <button 
                        @click="irAPagar"
                        :disabled="carrito.length === 0"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 disabled:bg-gray-400 transition"
                    >
                        Proceder al Pago
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>