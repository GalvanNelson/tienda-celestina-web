<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    // Datos Usuario
    name: '',
    email: '',
    password: '',
    // Datos Cliente
    carnet_identidad: '',
});

const submit = () => {
    form.post(route('clientes.store'));
};
</script>

<template>
    <Head title="Registrar Cliente"></Head>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrar Nuevo Cliente</h2>
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

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2 mt-6">Datos Personales</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Carnet de Identidad</label>
                            <input v-model="form.carnet_identidad" type="text" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.carnet_identidad" class="text-red-500 text-sm">{{ form.errors.carnet_identidad }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <Link :href="route('clientes.index')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancelar</Link>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Registrar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>