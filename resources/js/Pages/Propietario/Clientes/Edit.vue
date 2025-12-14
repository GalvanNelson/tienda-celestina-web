<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    cliente: Object
});

const form = useForm({
    // Datos de la tabla Users
    name: props.cliente.user.name,
    email: props.cliente.user.email,
    password: '',

    // Datos de la tabla Clientes
    carnet_identidad: props.cliente.carnet_identidad,
    limite_credito: props.cliente.limite_credito,
});

const submit = () => {
    form.put(route('clientes.update', props.cliente.codigo_cliente));
};
</script>

<template>

    <Head title="Editar Cliente"></Head>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Cliente: {{ cliente.user.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
                <form @submit.prevent="submit">

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Datos de Acceso</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-gray-700">Nombre Completo</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-gray-700">Correo Electrónico</label>
                            <input v-model="form.email" type="email" class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700">Nueva Contraseña <span
                                    class="text-gray-400 text-sm">(Dejar en
                                    blanco para mantener la actual)</span></label>
                            <input v-model="form.password" type="password" placeholder="********"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <!-- Aquí irían los campos específicos de Clientes -->
                    <h3>Datos del Cliente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-gray-700">Carnet de identidad <span
                                    class="text-gray-400 text-sm">(Dejar en
                                    blanco para mantener la actual)</span></label>
                            <input v-model="form.carnet_identidad" type="text"
                                class="w-full border-gray-300 rounded mt-1">
                            <div v-if="form.errors.carnet_identidad" class="text-red-500 text-sm">{{
                                form.errors.carnet_identidad }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700">Limite de credito (Max. 5000)</label>
                            <input v-model="form.limite_credito" type="number" min="0" max="5000" step="10"
                                placeholder="Ej. 1000"
                                class="w-full border-gray-300 rounded mt-1 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.limite_credito }">
                            <p class="text-xs text-gray-500 mt-1">Rango permitido: 0 a 5000 Bs.</p>

                            <div v-if="form.errors.limite_credito" class="text-red-500 text-sm">
                                {{ form.errors.limite_credito }}
                            </div>
                        </div>
                    </div>


                    <div class="flex items-center justify-end mt-6">
                        <Link :href="route('clientes.index')" class="mr-4 text-gray-600 hover:text-gray-900">Cancelar
                        </Link>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            :disabled="form.processing">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>