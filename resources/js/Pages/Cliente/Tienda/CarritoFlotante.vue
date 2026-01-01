<script setup>
import { computed } from 'vue';

const props = defineProps({
    carrito: Array,
    abierto: Boolean
});

const emit = defineEmits(['cerrar', 'actualizar', 'quitar', 'pagar']);

const totalCarrito = computed(() => {
    return props.carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
});

const modificarCantidad = (index, delta) => {
    emit('actualizar', { index, delta });
};
</script>

<template>
    <div v-if="abierto" class="fixed inset-0 bg-black/40 flex justify-end z-50 backdrop-blur-[1px]" @click.self="emit('cerrar')">
        <div class="bg-white w-full max-w-md h-full p-6 shadow-xl flex flex-col animate-slide-in">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="text-2xl font-bold">Tu Carrito</h2>
                <button @click="emit('cerrar')" class="text-red-500 font-bold text-xl">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div v-if="carrito.length === 0" class="text-center text-gray-500 mt-10">El carrito está vacío.</div>
                
                <div v-for="(item, index) in carrito" :key="item.id" class="flex justify-between items-center mb-4 border-b pb-2">
                    <div class="min-w-0">
                        <p class="font-bold truncate">{{ item.nombre }}</p>
                        <div class="mt-1 flex items-center gap-2">
                            <button @click="modificarCantidad(index, -1)" class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-100">−</button>
                            <span class="w-8 text-center font-medium">{{ item.cantidad }}</span>
                            <button @click="modificarCantidad(index, 1)" class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-100">+</button>
                            <span class="text-sm text-gray-600">x {{ item.precio }} Bs</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-lg">{{ (item.cantidad * item.precio).toFixed(2) }} Bs</span>
                        <button @click="emit('quitar', index)" class="text-red-500">🗑️</button>
                    </div>
                </div>
            </div>

            <div class="border-t pt-4 mt-2">
                <div class="flex justify-between text-xl font-bold mb-4">
                    <span>Total:</span>
                    <span>{{ totalCarrito }} Bs</span>
                </div>
                <button @click="emit('pagar')" :disabled="carrito.length === 0"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 disabled:bg-gray-400 transition">
                    Proceder al Pago
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
</style>