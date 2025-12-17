<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  venta_id: Number,
  qr_image: String,
  monto: Number,
});

let poller = null;
const estado = ref('Esperando pago...');

const verificarPago = async () => {
  try {
    const response = await fetch(route('venta.status', props.venta_id));
    const data = await response.json();
    if (data.status === 'pagado') {
      estado.value = '¡Pago aprobado!';
      clearInterval(poller);
      router.visit(route('venta.exito', props.venta_id));
    }
  } catch (error) {
    console.error('Error verificando pago', error);
  }
};

onMounted(() => {
  poller = setInterval(verificarPago, 3000);
});

onUnmounted(() => {
  if (poller) clearInterval(poller);
});
</script>

<template>
  <div class="max-w-xl mx-auto p-6 text-center space-y-4">
    <h1 class="text-2xl font-bold">Escanea el QR para pagar</h1>
    <p class="text-lg">Total: {{ monto }} Bs</p>
    <div class="border rounded-lg p-4 inline-block bg-white shadow">
      <img :src="`data:image/png;base64,${qr_image}`" alt="QR PagoFacil" class="mx-auto" />
    </div>
    <p class="text-lg font-semibold animate-pulse">{{ estado }}</p>
    <p class="text-sm text-gray-500">La página se actualizará automáticamente cuando el pago sea aprobado.</p>
  </div>
</template>
