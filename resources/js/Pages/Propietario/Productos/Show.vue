<script setup>
defineProps({
    producto: Object,
    show: Boolean
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="emit('close')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-4 mb-4" id="modal-title">
                                Detalle del Producto
                            </h3>
                            
                            <div class="flex flex-col gap-6">
                                
                                <div class="w-full flex justify-center bg-gray-50 rounded-lg p-2">
                                    <img v-if="producto.imagen_completa_url" 
                                         :src="producto.imagen_completa_url" 
                                         class="max-h-80 w-auto rounded-lg shadow-md object-contain border border-gray-100">
                                    
                                    <div v-else class="w-full h-48 bg-gray-100 flex flex-col items-center justify-center text-gray-400 rounded-lg border-2 border-dashed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Sin imagen disponible</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-semibold uppercase text-gray-500">Nombre del Producto</label>
                                        <p class="text-xl font-bold text-gray-800">{{ producto.nombre_producto }}</p>
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase text-gray-500">Grupo</label>
                                        <p class="text-gray-700 font-medium">
                                            {{ producto.grupo == 1 ? 'Tienda' : (producto.grupo == 2 ? 'Bebidas' : 'Librería') }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-xs font-semibold uppercase text-gray-500">Unidad de Medida</label>
                                        <p class="text-gray-700 font-medium bg-white border px-2 py-1 rounded inline-block mt-1">
                                            {{ producto.unidad_medida?.nombre || 'No asignada' }}
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-semibold uppercase text-gray-500 block mb-1">Categorías</label>
                                        <div class="flex flex-wrap gap-2">
                                            <span v-for="cat in producto.categorias" :key="cat.codigo_categoria" 
                                                  class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full border border-blue-200">
                                                {{ cat.nombre }}
                                            </span>
                                            <span v-if="!producto.categorias?.length" class="text-sm text-gray-400 italic">
                                                Sin categorías
                                            </span>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-2 p-3 bg-indigo-50 rounded border border-indigo-100 flex justify-between items-center">
                                        <div>
                                            <label class="text-xs font-semibold uppercase text-indigo-600">Precio Unitario</label>
                                            <p class="text-2xl font-black text-indigo-700">{{ producto.precio_unitario }} <span class="text-sm">Bs</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <span class="text-xs text-gray-400 italic">ID Producto: {{ producto.codigo_producto }}</span>
                    <button type="button" @click="emit('close')"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-900 focus:outline-none sm:w-auto sm:text-sm transition-colors">
                        Cerrar Ventana
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>