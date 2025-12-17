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
    total: 0
});

const procesarCompra = () => {
    form.post(route('venta.store'), {
        onSuccess: () => {
            // Limpiar carrito tras compra exitosa (solo si es efectivo o al finalizar proceso)
            // Nota: Si es QR, quizás quieras limpiarlo en la vista de Éxito.
            if(form.metodo_pago === 'efectivo') {
                 localStorage.removeItem('mi_carrito');
            }
        }
    });
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
          <label class="block font-medium mb-2">Método de Pago:</label>
          <select v-model="form.metodo_pago" class="w-full border-gray-300 rounded shadow-sm">
              <option value="efectivo">Pagar en Efectivo (Contra entrega)</option>
              <option value="qr">Pagar con QR (Pago Fácil)</option>
          </select>
      </div>

      <button 
        @click="procesarCompra" 
        :disabled="form.processing || carrito.length === 0"
        class="w-full bg-blue-600 text-white py-3 rounded font-bold hover:bg-blue-700 disabled:bg-gray-400"
      >
          <span v-if="form.processing">Procesando...</span>
          <span v-else>Confirmar Compra</span>
      </button>
  </div>
</template>