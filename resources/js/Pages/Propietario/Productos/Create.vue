<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    categorias: Array,
    unidades: Array
});

const form = useForm({
    nombre_producto: '',
    precio_unitario: '',
    stock: '',
    categoria: '',
    unidad_medida: '',
    imagen: null,
});

const handleFileUpload = (event) => {
    form.imagen = event.target.files[0];
};

const submit = () => {
    form.post(route('productos.store'), {
        forceFormData: true, // Forzamos el envío como formulario con archivos
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
            <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded shadow">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="block text-gray-700">Nombre del Producto</label>
                        <input v-model="form.nombre_producto" type="text" class="w-full border-gray-300 rounded mt-1">
                        <div v-if="form.errors.nombre_producto" class="text-red-500 text-sm">{{
                            form.errors.nombre_producto }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700">Precio Unitario</label>
                            <input v-model="form.precio_unitario" type="number" step="0.01"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.precio_unitario" class="text-red-500 text-sm">{{
                                form.errors.precio_unitario
                                }}</div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Stock Inicial</label>
                            <input v-model="form.stock" type="number" step="0.01"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.stock" class="text-red-500 text-sm">{{ form.errors.stock }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700">Categoría</label>
                            <select v-model="form.categoria" class="w-full border-gray-300 rounded mt-1">
                                <option value="" disabled>Seleccionar...</option>
                                <option v-for="cat in categorias" :key="cat.codigo_categoria"
                                    :value="cat.codigo_categoria">
                                    {{ cat.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.categoria" class="text-red-500 text-sm">{{ form.errors.categoria }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Unidad de Medida</label>
                            <select v-model="form.unidad_medida" class="w-full border-gray-300 rounded mt-1">
                                <option value="" disabled>Seleccionar...</option>
                                <option v-for="uni in unidades" :key="uni.codigo_unidad_medida"
                                    :value="uni.codigo_unidad_medida">
                                    {{ uni.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.unidad_medida" class="text-red-500 text-sm">{{
                                form.errors.unidad_medida }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700">Imagen del Producto</label>
                        <input type="file" accept="image/png, image/jpeg, image/jpg" @change="handleFileUpload"
                            class="w-full mt-1 border border-gray-300 p-2 rounded">
                        <div v-if="form.progress" class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full"
                                :style="{ width: form.progress.percentage + '%' }">
                            </div>
                        </div>

                        <div v-if="form.errors.imagen" class="text-red-500 text-sm">{{ form.errors.imagen }}</div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <Link :href="route('productos.index')" class="px-4 py-2 text-center text-gray-600 hover:text-gray-900 border border-gray-200 rounded">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>