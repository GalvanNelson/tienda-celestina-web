<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recibimos el producto a editar y las listas para los selects
const props = defineProps({
    producto: Object,
    categorias: Array,
    unidades: Array
});

// Inicializamos el formulario con los datos existentes del producto
const form = useForm({
    // IMPORTANTE: Para subir archivos en edición, usamos POST con _method: 'PUT'
    _method: 'PUT', 
    nombre_producto: props.producto.nombre_producto,
    precio_unitario: props.producto.precio_unitario,
    stock: props.producto.stock,
    categoria: props.producto.categoria, // ID de la categoría actual
    unidad_medida: props.producto.unidad_medida, // ID de la unidad actual
    imagen: null, // Iniciamos en null, solo se llena si el usuario sube una nueva
});

const submit = () => {
    // Usamos POST apuntando a la ruta update, gracias al _method: 'PUT' Laravel lo tratará como update
    form.post(route('productos.update', props.producto.codigo_producto));
};
</script>

<template>
    <Head title="Editar Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h2>
        </template>

        <div class="py-12 px-4 sm:px-0">
            <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded shadow">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nombre del Producto</label>
                        <input v-model="form.nombre_producto" type="text" class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        <div v-if="form.errors.nombre_producto" class="text-red-500 text-sm mt-1">{{ form.errors.nombre_producto }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Precio Unitario</label>
                            <input v-model="form.precio_unitario" type="number" step="0.01" class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                            <div v-if="form.errors.precio_unitario" class="text-red-500 text-sm mt-1">{{ form.errors.precio_unitario }}</div>
                        </div>                    
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Categoría</label>
                            <select v-model="form.categoria" class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled>Seleccionar...</option>
                                <option v-for="cat in categorias" :key="cat.codigo_categoria" :value="cat.codigo_categoria">
                                    {{ cat.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.categoria" class="text-red-500 text-sm mt-1">{{ form.errors.categoria }}</div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Unidad de Medida</label>
                            <select v-model="form.unidad_medida" class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled>Seleccionar...</option>
                                <option v-for="uni in unidades" :key="uni.codigo_unidad_medida" :value="uni.codigo_unidad_medida">
                                    {{ uni.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.unidad_medida" class="text-red-500 text-sm mt-1">{{ form.errors.unidad_medida }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Imagen del Producto</label>
                        
                        <div v-if="producto.imagen" class="mb-2">
                            <p class="text-xs text-gray-500 mb-1">Imagen actual:</p>
                            <img :src="`/storage/${producto.imagen}`" class="h-20 w-20 object-cover rounded border border-gray-200">
                        </div>

                        <input type="file" @input="form.imagen = $event.target.files[0]" class="w-full mt-1 border border-gray-300 p-2 rounded text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Deja esto vacío si no quieres cambiar la imagen.</p>
                        
                        <div v-if="form.errors.imagen" class="text-red-500 text-sm mt-1">{{ form.errors.imagen }}</div>
                        
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3">
                        <Link :href="route('productos.index')" class="text-center text-gray-600 hover:text-gray-900 text-sm border border-gray-200 rounded px-4 py-2">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 shadow-sm transition ease-in-out duration-150 disabled:opacity-50">
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>