<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CarritoFlotante from '@/Pages/Cliente/Tienda/CarritoFlotante.vue'; // Asegura la ruta correcta

const props = defineProps({ productos: Array });
const placeholderImg = 'https://via.placeholder.com/400x300?text=Sin+imagen';
const carrito = ref([]);
const mostrarCarrito = ref(false);

onMounted(() => {
    const guardado = localStorage.getItem('mi_carrito');
    if (guardado) carrito.value = JSON.parse(guardado);
});

const guardarCarrito = () => localStorage.setItem('mi_carrito', JSON.stringify(carrito.value));

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
        });
    }
    guardarCarrito();
    // Ya NO ponemos mostrarCarrito.value = true; para que sea manual.
};

// Lógica para manejar los eventos que vienen del componente Carrito
const handleActualizarCantidad = ({ index, delta }) => {
    const item = carrito.value[index];
    item.cantidad += delta;
    if (item.cantidad <= 0) carrito.value.splice(index, 1);
    guardarCarrito();
};

const handleRemover = (index) => {
    carrito.value.splice(index, 1);
    guardarCarrito();
};

const irAPagar = () => router.visit(route('cliente.checkout'));
</script>

<template>
    <Head title="Catálogo" />
    <AuthenticatedLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Nuestros Productos</h1>
                <button @click="mostrarCarrito = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center shadow-lg hover:bg-blue-700">
                    🛒 Ver Carrito ({{ carrito.reduce((acc, i) => acc + i.cantidad, 0) }})
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div
                    v-for="prod in productos"
                    :key="prod.codigo_producto"
                    class="bg-white rounded-lg shadow p-4 flex flex-col justify-between"
                >
                    <div>
                        <div class="mb-3 relative overflow-hidden rounded-lg bg-gray-50 h-40 flex items-center justify-center">
                            <img
                                v-if="prod.imagen_completa_url"
                                :src="prod.imagen_completa_url"
                                :alt="prod.nombre_producto"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                            <img
                                v-else
                                :src="placeholderImg"
                                alt="Sin imagen"
                                class="w-full h-full object-cover opacity-70"
                                loading="lazy"
                            />
                        </div>
                        <h3 class="font-bold text-lg">{{ prod.nombre_producto }}</h3>                        
                        <p class="text-green-600 font-bold text-xl mt-1">{{ prod.precio_unitario }} Bs</p>
                    </div>
                    <button @click="agregarAlCarrito(prod)" class="mt-4 w-full bg-gray-800 text-white py-2 rounded hover:bg-black transition">
                        Agregar +
                    </button>
                </div>
            </div>

            <CarritoFlotante 
                :carrito="carrito" 
                :abierto="mostrarCarrito" 
                @cerrar="mostrarCarrito = false"
                @actualizar="handleActualizarCantidad"
                @quitar="handleRemover"
                @pagar="irAPagar"
            />
        </div>
    </AuthenticatedLayout>
</template>