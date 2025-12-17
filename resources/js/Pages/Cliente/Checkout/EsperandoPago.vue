// Pages/Checkout/EsperandoPago.vue
<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    venta_id: Number,
    qr_image: String,
    monto: Number
});

let intervaloPolling = null;
const estado = ref('Esperando pago...');

// Función que consulta el estado
const verificarPago = async () => {
    try {
        const response = await fetch(route('venta.status', props.venta_id));
        const data = await response.json();

        if (data.status === 'pagado') {
            estado.value = '¡Pago Exitoso!';
            clearInterval(intervaloPolling);
            
            // Redirigir a la página final de recibo
            router.visit(route('venta.exito', props.venta_id));
        }
    } catch (error) {
        console.error("Error verificando pago", error);
    }
};

onMounted(() => {
    // Consultar cada 3 segundos
    intervaloPolling = setInterval(verificarPago, 3000);
});

onUnmounted(() => {
    clearInterval(intervaloPolling);
});
</script>

<template>
    <div class="text-center">
        <h1>Escanea para pagar</h1>
        <p>Total: {{ monto }} Bs</p>
        
        <img :src="'data:image/png;base64,' + qr_image" alt="QR Code" class="mx-auto"/>
        
        <p class="mt-4 text-lg font-bold animate-pulse">
            {{ estado }}
        </p>
        
        <p class="text-sm text-gray-500 mt-2">
            La página se actualizará automáticamente cuando pagues.
        </p>
    </div>
</template>