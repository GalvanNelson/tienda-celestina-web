<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    vendedor: Number,
    clientes: Array,
    productos: Array
});

// --- ESTADO DE LA APLICACIÓN ---
const vistaActual = ref('catalogo'); // 'catalogo' o 'carrito'
const busqueda = ref('');
const carrito = ref([]);

// --- FORMULARIO INERTIA ---
const form = useForm({
    cliente_id: '',
    carrito: [],
    total: 0,
    tipo_pago: 'contado',
    metodo_pago: 'efectivo',
});

// --- LÓGICA DEL CARRITO ---

// Filtrar productos por buscador en tiempo real
const productosFiltrados = computed(() => {
    if (!busqueda.value) return props.productos;
    return props.productos.filter(p =>
        p.nombre_producto.toLowerCase().includes(busqueda.value.toLowerCase())
    );
});

// Agregar producto (desde el catálogo)
const agregarAlCarrito = (producto) => {
    // Verificamos si ya existe en el carrito
    const itemExistente = carrito.value.find(item => item.codigo_producto === producto.codigo_producto);

    if (itemExistente) {
        // Si existe, validamos stock y sumamos 1
        if (itemExistente.cantidad < producto.stock) {
            itemExistente.cantidad++;
        } else {
            alert("Has alcanzado el stock máximo disponible para este producto.");
        }
    } else {
        // Si no existe, lo agregamos
        carrito.value.push({
            codigo_producto: producto.codigo_producto,
            nombre: producto.nombre_producto,
            precio: parseFloat(producto.precio_unitario),
            imagen: producto.imagen,
            cantidad: 1,
            stock_max: producto.stock
        });
    }
};

// Modificar cantidad (desde el carrito)
const cambiarCantidad = (index, delta) => {
    const item = carrito.value[index];
    const nuevaCantidad = item.cantidad + delta;

    // Solo permitimos cambiar si es mayor a 0 y no supera el stock real
    if (nuevaCantidad > 0 && nuevaCantidad <= item.stock_max) {
        item.cantidad = nuevaCantidad;
    }
};

const eliminarDelCarrito = (index) => {
    carrito.value.splice(index, 1);
};

// Cálculos automáticos
const cantidadTotalProductos = computed(() => {
    return carrito.value.reduce((acc, item) => acc + item.cantidad, 0);
});

const totalPagar = computed(() => {
    return carrito.value.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
});

// --- FINALIZAR VENTA ---
const procesarVenta = () => {
    if (!form.cliente_id) {
        alert("Por favor selecciona un cliente para la factura/recibo.");
        return;
    }
    // Pasamos los datos reactivos al formulario de Inertia
    form.carrito = carrito.value;
    form.total = totalPagar.value;

    form.post(route('ventas.store'), {
        onSuccess: () => {
            carrito.value = [];
            vistaActual.value = 'catalogo';
            form.reset();
            form.tipo_pago = 'contado'; // Resetear a contado
        }
    });
};

const clienteInfo = computed(() => {
    if (!form.cliente_id) return null;
    return props.clientes.find(c => c.codigo_cliente === form.cliente_id);
});

const creditoDisponible = computed(() => {
    if (!clienteInfo.value) return 0;
    return clienteInfo.value.limite_credito - clienteInfo.value.saldo_actual;
});

const excedeCredito = computed(() => {
    if (form.tipo_pago === 'contado') return false;
    // Si es crédito, verificamos si el total supera lo disponible
    return parseFloat(totalPagar.value) > creditoDisponible.value;
});
</script>

<template>

    <Head title="Punto de Venta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ vistaActual === 'catalogo' ? 'Catálogo de Productos' : 'Carrito de Compras' }}
                </h2>

                <button @click="vistaActual = (vistaActual === 'catalogo' ? 'carrito' : 'catalogo')"
                    class="relative bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg">
                    <span v-if="vistaActual === 'catalogo'">🛒 Ir a Pagar</span>
                    <span v-else>⬅ Seguir Comprando</span>

                    <span v-if="carrito.length > 0"
                        class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        {{ cantidadTotalProductos }}
                    </span>
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div v-if="$page.props.flash.success"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">¡Éxito! </strong>
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <div v-if="$page.props.flash.error"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="vistaActual === 'catalogo'">

                    <div class="mb-6 bg-white p-4 rounded shadow">
                        <input v-model="busqueda" type="text" placeholder="🔍 Buscar producto por nombre..."
                            class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
                            autofocus>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div v-for="prod in productosFiltrados" :key="prod.codigo_producto"
                            class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col h-full border border-gray-100 cursor-pointer"
                            @click="agregarAlCarrito(prod)">
                            <div
                                class="h-40 w-full bg-gray-50 flex items-center justify-center overflow-hidden relative">
                                <img v-if="prod.imagen" :src="`/storage/${prod.imagen}`"
                                    class="object-cover h-full w-full">
                                <span v-else class="text-gray-300 text-5xl">📦</span>

                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition duration-300">
                                </div>
                            </div>

                            <div class="p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm md:text-base mb-1">{{
                                        prod.nombre_producto }}
                                    </h3>
                                    <p class="text-xs text-gray-500">Stock: {{ prod.stock }}</p>
                                </div>
                                <div class="mt-3 flex justify-between items-end">
                                    <span class="text-lg font-bold text-indigo-600">{{ prod.precio_unitario }} Bs</span>
                                    <div class="bg-indigo-100 text-indigo-700 p-1.5 rounded-full hover:bg-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="vistaActual === 'carrito'" class="flex flex-col lg:flex-row gap-6">

                    <div class="lg:w-2/3 bg-white p-6 rounded shadow">
                        <h3 class="text-lg font-bold mb-4 text-gray-700">Detalle del Pedido</h3>

                        <div v-if="carrito.length === 0"
                            class="text-center py-10 text-gray-500 bg-gray-50 rounded border border-dashed">
                            <p class="mb-2">🛒</p>
                            Tu carrito está vacío. <br>
                            <span class="text-indigo-600 cursor-pointer hover:underline"
                                @click="vistaActual = 'catalogo'">Volver al catálogo</span> para agregar productos.
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(item, index) in carrito" :key="item.codigo_producto"
                                class="flex items-center justify-between border-b pb-4 last:border-0">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="h-16 w-16 bg-gray-100 rounded overflow-hidden flex-shrink-0 border">
                                        <img v-if="item.imagen" :src="`/storage/${item.imagen}`"
                                            class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">{{ item.nombre }}</h4>
                                        <p class="text-sm text-gray-500">{{ item.precio }} Bs / u</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center border rounded-md">
                                        <button @click="cambiarCantidad(index, -1)"
                                            class="px-3 py-1 bg-gray-50 hover:bg-gray-200 text-gray-600 font-bold border-r">-</button>
                                        <span class="px-3 py-1 font-bold min-w-[30px] text-center">{{ item.cantidad
                                            }}</span>
                                        <button @click="cambiarCantidad(index, 1)"
                                            class="px-3 py-1 bg-gray-50 hover:bg-gray-200 text-gray-600 font-bold border-l">+</button>
                                    </div>

                                    <div class="text-right w-24 hidden sm:block">
                                        <div class="font-bold text-gray-800">{{ (item.precio * item.cantidad).toFixed(2)
                                            }} Bs
                                        </div>
                                    </div>

                                    <button @click="eliminarDelCarrito(index)"
                                        class="text-red-400 hover:text-red-600 p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/3 h-fit bg-white p-6 rounded shadow sticky top-6 border border-gray-100">
                        <h3 class="text-lg font-bold mb-4 text-gray-700">Finalizar Venta</h3>

                        <div class="mb-6">
                            <label
                                class="block text-gray-700 font-medium mb-1 text-sm uppercase tracking-wide">Cliente</label>
                            <select v-model="form.cliente_id"
                                class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': !form.cliente_id }">
                                <option value="" disabled>-- Seleccionar Cliente --</option>
                                <option v-for="cli in clientes" :key="cli.codigo_cliente" :value="cli.codigo_cliente">
                                    {{ cli.nombre_cliente }}
                                </option>
                            </select>

                            <div v-if="clienteInfo" class="mt-2 text-xs p-2 bg-gray-50 rounded border">
                                <p><strong>Límite Crédito:</strong> {{ clienteInfo.limite_credito }} Bs</p>
                                <p><strong>Deuda Actual:</strong> <span class="text-red-600">{{ clienteInfo.saldo_actual
                                        }}
                                        Bs</span></p>
                                <p><strong>Disponible:</strong> <span class="text-green-600 font-bold">{{
                                    creditoDisponible.toFixed(2) }} Bs</span></p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2 text-sm uppercase tracking-wide">
                                Tipo de Venta
                            </label>

                            <div class="flex gap-4 mb-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="form.tipo_pago" value="contado"
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <span>💵 Contado</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="form.tipo_pago" value="credito"
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <span>📝 Crédito</span>
                                </label>
                            </div>

                            <div v-if="form.tipo_pago === 'contado'"
                                class="bg-gray-50 p-3 rounded border border-gray-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Método de
                                    Pago:</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" v-model="form.metodo_pago" value="efectivo"
                                            class="text-green-600 focus:ring-green-500">
                                        <span>💰 Efectivo</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" v-model="form.metodo_pago" value="qr"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span>📱 QR (PagoFácil)</span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="excedeCredito" class="mt-2 text-red-600 text-xs font-bold bg-red-50 p-2 rounded">
                                ⚠ Saldo insuficiente. Faltan {{ (parseFloat(totalPagar) - creditoDisponible).toFixed(2)
                                }} Bs de
                                crédito.
                            </div>
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Cant. Productos:</span>
                                <span>{{ cantidadTotalProductos }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-2xl font-bold text-gray-800 pt-2 border-t mt-2">
                                <span>Total:</span>
                                <span>{{ totalPagar }} Bs</span>
                            </div>

                            <button @click="procesarVenta"
                                :disabled="carrito.length === 0 || form.processing || excedeCredito"
                                class="w-full mt-6 text-white py-3 rounded-lg font-bold text-lg transition shadow-md flex justify-center items-center gap-2"
                                :class="excedeCredito ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'">
                                <span v-if="form.processing">Procesando...</span>
                                <span v-else>
                                    {{ form.tipo_pago === 'credito' ? '📝 Confirmar Crédito' : '✅ Cobrar Contado' }}
                                </span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>