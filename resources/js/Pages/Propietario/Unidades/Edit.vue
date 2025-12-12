<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// CORRECCIÓN 1: Recibimos 'unidad' en lugar de 'categoria'
const props = defineProps({
    unidad: Object
});

// CORRECCIÓN 2: Usamos 'props.unidad'
const form = useForm({
    nombre: props.unidad.nombre,
});

const submit = () => {
    // CORRECCIÓN 3: La ruta debe ser 'unidades.update' y usar el ID correcto
    form.put(route('unidades.update', props.unidad.codigo_unidad_medida));
};
</script>

<template>
    <Head title="Editar Unidad de medida" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Unidad de Medida</h2>
        </template>

        <div class="py-12">
            <div class="max-w-md mx-auto bg-white p-8 rounded shadow-sm">
                <form @submit.prevent="submit">
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Nombre de la Unidad de Medida</label>
                        <input 
                            v-model="form.nombre" 
                            type="text" 
                            class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
                        >
                        <div v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">
                            {{ form.errors.nombre }}
                        </div>
                    </div>

                    <div class="flex justify-end items-center">
                        <Link :href="route('unidades.index')" class="mr-4 text-gray-600 hover:text-gray-900 text-sm">
                            Cancelar
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Actualizar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>