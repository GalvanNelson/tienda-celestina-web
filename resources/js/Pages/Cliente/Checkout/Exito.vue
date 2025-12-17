<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
	venta_id: Number
});

onMounted(() => {
	// Limpiar carrito al llegar a éxito
	localStorage.removeItem('mi_carrito');
});

const irATienda = () => router.visit(route('cliente.tienda'));
</script>

<template>
	<div class="p-6 max-w-xl mx-auto text-center">
		<h1 class="text-3xl font-bold mb-4">¡Gracias por tu compra!</h1>
		<p class="text-gray-700 mb-6">Tu número de venta es <strong>#{{ props.venta_id }}</strong>.</p>
		<button @click="irATienda" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Volver a la tienda</button>
	</div>
  
</template>

<style scoped>
</style>
