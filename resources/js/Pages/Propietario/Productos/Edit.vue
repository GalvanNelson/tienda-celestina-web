<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Recibimos el producto con sus relaciones cargadas (categorias, unidadMedida)
const props = defineProps({
    producto: Object,
    categorias: Array,
    unidades: Array
});


// Inicializamos el formulario
const form = useForm({
    // IMPORTANTE: Laravel no soporta PUT/PATCH nativo con archivos (FormData),
    // por eso usamos POST con el campo _method: 'PUT'.
    _method: 'PUT', 
    
    nombre_producto: props.producto.nombre_producto,
    precio_unitario: props.producto.precio_unitario,
    grupo: props.producto.grupo, // 1: Tienda, 2: Bebida
    
    // Para Checkboxes: Convertimos el array de objetos categorías a un array de solo IDs [1, 5, ...]
    categorias: props.producto.categorias ? props.producto.categorias.map(c => c.codigo_categoria) : [],
    
    // Para Select: El ID de la unidad actual (campo directo de la tabla productos)
    unidad_medida: props.producto.unidad_medida, 
    
    imagen_url: null, // Solo se llena si sube nueva foto
});

const handleFileUpload = (event) => {
    form.imagen_url = event.target.files[0];
};

const submit = () => {
    form.post(route('productos.update', props.producto.codigo_producto), {
        forceFormData: true, // Necesario para enviar archivos
    });
};
</script>

<template>
    <Head title="Editar Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h2>
        </template>

        <div class="py-12 px-4 sm:px-0">
            <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded shadow">
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

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Grupo</label>
                            <div class="flex gap-4 items-center h-10">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" :value="1" v-model="form.grupo" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span>Tienda</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" :value="2" v-model="form.grupo" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span>Bebidas</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" :value="3" v-model="form.grupo" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" />
                                    <span>Librería</span>
                                </label>
                            </div>
                            <div v-if="form.errors.grupo" class="text-red-500 text-sm">{{ form.errors.grupo }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Categorías</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 bg-gray-50 p-4 rounded border border-gray-200 max-h-64 overflow-y-auto">
                            <label v-for="cat in categorias" :key="cat.codigo_categoria" class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition">
                                <input 
                                    type="checkbox" 
                                    :value="cat.codigo_categoria" 
                                    v-model="form.categorias" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ cat.nombre }}</span>
                            </label>
                        </div>
                        <div v-if="form.errors.categorias" class="text-red-500 text-sm mt-1">{{ form.errors.categorias }}</div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Unidad de Medida</label>                    
                        <select v-model.number="form.unidad_medida" class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                            <option :value="props.producto.unidad_medida ? props.producto.unidad_medida.codigo_unidad_medida : null" disabled>Seleccionar...</option>
                            <option v-for="uni in unidades" :key="uni.codigo_unidad_medida" :value="uni.codigo_unidad_medida">
                                {{ uni.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.unidad_medida" class="text-red-500 text-sm mt-1">{{ form.errors.unidad_medida }}</div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Imagen del Producto</label>
                        
                        <div v-if="producto.imagen_completa_url && !form.imagen_url" class="mb-3 p-3 bg-gray-50 rounded border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Imagen actual:</p>
                            <img :src="producto.imagen_completa_url" class="h-32 sm:h-40 w-auto object-cover rounded shadow-sm">
                        </div>

                        <input type="file" accept="image/*" @change="handleFileUpload" class="w-full mt-1 border border-gray-300 p-2 rounded text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Solo sube un archivo si deseas cambiar la imagen actual.</p>
                        
                        <div v-if="form.errors.imagen_url" class="text-red-500 text-sm mt-1">{{ form.errors.imagen_url }}</div>
                        
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2 h-1 bg-gray-200 rounded">
                            <div class="bg-indigo-600 h-1 rounded" :style="{ width: form.progress.percentage + '%' }"></div>
                        </progress>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3">
                        <Link :href="route('productos.index')" class="text-center text-gray-600 hover:text-gray-900 text-sm border border-gray-200 rounded px-4 py-2 hover:bg-gray-50">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 shadow-sm transition ease-in-out duration-150 disabled:opacity-50">
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>