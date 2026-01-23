<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    categorias: Array,
    unidades: Array
});

const form = useForm({
    nombre_producto: '',
    precio_unitario: '',
    grupo: 1, // Valor por defecto (ej. 1: Tienda)
    categorias: [], // Array para múltiples IDs
    unidad_medida: null,   // Selección única
    imagen_url: null,
});

// Seleccionar "Unidad" por defecto cuando se carguen las unidades
watch(() => props.unidades, (newUnidades) => {
    if (newUnidades && newUnidades.length > 0) {
        const unidadDefault = newUnidades.find(u => u.nombre === 'Unidad');
        if (unidadDefault) {
            form.unidad_medida = unidadDefault.codigo_unidad_medida;
        }
    }
}, { immediate: true });

const handleFileUpload = (event) => {
    form.imagen_url = event.target.files[0];
};

const submit = () => {
    form.post(route('productos.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Crear Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Producto</h2>
        </template>

        <div class="py-12 px-4 sm:px-0">
            <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded shadow">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <label class="block text-gray-700 font-medium">Nombre del Producto</label>
                        <input v-model="form.nombre_producto" type="text" class="w-full border-gray-300 rounded mt-1">
                        <div v-if="form.errors.nombre_producto" class="text-red-500 text-sm">{{ form.errors.nombre_producto }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium">Precio Unitario</label>
                            <input v-model="form.precio_unitario" type="number" step="0.01" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.precio_unitario" class="text-red-500 text-sm">{{ form.errors.precio_unitario }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium">Grupo</label>
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" :value="1" v-model="form.grupo" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span>Tienda</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" :value="2" v-model="form.grupo" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span>Bebidas</span>
                                </label>
                            </div>
                            <div v-if="form.errors.grupo" class="text-red-500 text-sm">{{ form.errors.grupo }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Categorías (Selección Múltiple)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-gray-50 p-3 rounded border border-gray-200">
                            <div v-for="cat in categorias" :key="cat.codigo_categoria" class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    :value="cat.codigo_categoria" 
                                    v-model="form.categorias" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ cat.nombre }}</span>
                            </div>
                        </div>
                        <div v-if="form.errors.categorias" class="text-red-500 text-sm mt-1">{{ form.errors.categorias }}</div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Unidad de Medida</label>
                        <select v-model="form.unidad_medida" class="w-full border border-gray-300 rounded px-3 py-2 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Seleccionar una unidad --</option>
                            <option v-for="uni in unidades" :key="uni.codigo_unidad_medida" :value="uni.codigo_unidad_medida">
                                {{ uni.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.unidad_medida" class="text-red-500 text-sm mt-1">{{ form.errors.unidad_medida }}</div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium">Imagen del Producto</label>
                        <input type="file" accept="image/*" @change="handleFileUpload" class="w-full mt-1 border border-gray-300 p-2 rounded">
                        <div v-if="form.progress" class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                        </div>
                        <div v-if="form.errors.imagen_url" class="text-red-500 text-sm">{{ form.errors.imagen_url }}</div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link :href="route('productos.index')" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>