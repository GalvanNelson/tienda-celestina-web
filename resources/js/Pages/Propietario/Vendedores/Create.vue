<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    // Datos Usuario
    name: '',
    email: '',
    password: '',
    // Datos Vendedor
    carnet_identidad: '',
    telefono: '',
    sueldo_basico: '',
    porcentaje_comision: '',
    fecha_contratacion: '',
});

const submit = () => {
    form.post(route('vendedores.store'));
};
</script>

<template>

    <Head title="Registrar Vendedor" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrar Nuevo Vendedor</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
                <form @submit.prevent="submit">

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Datos de Acceso (Usuario)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Nombre Completo</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Correo Electrónico</label>
                            <input v-model="form.email" type="email" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700">Contraseña Inicial</label>
                            <input v-model="form.password" type="password" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2 mt-6">Datos Laborales</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Carnet de Identidad</label>
                            <input v-model="form.carnet_identidad" type="text"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.carnet_identidad" class="text-red-500 text-sm">{{
                                form.errors.carnet_identidad }}</div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Teléfono / Celular</label>
                            <input v-model="form.telefono" type="text" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.telefono" class="text-red-500 text-sm">{{ form.errors.telefono }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Sueldo Básico (Bs)</label>
                            <input v-model="form.sueldo_basico" type="number" step="0.01"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.sueldo_basico" class="text-red-500 text-sm">{{
                                form.errors.sueldo_basico }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700">% Comisión Ventas</label>
                            <input v-model="form.porcentaje_comision" type="number" step="0.01"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.porcentaje_comision" class="text-red-500 text-sm">{{
                                form.errors.porcentaje_comision }}</div>
                        </div>
                        <div>
                            <label class="block text-gray-700">Fecha Contratación</label>
                            <input v-model="form.fecha_contratacion" type="date"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.fecha_contratacion" class="text-red-500 text-sm">{{
                                form.errors.fecha_contratacion }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <Link :href="route('vendedores.index')"
                            class="mr-4 px-4 py-2 text-gray-600 hover:text-gray-900">
                            Cancelar</Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Registrar Vendedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>