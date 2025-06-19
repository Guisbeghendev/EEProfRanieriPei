<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    role: Object, // Espera um objeto 'role' com os dados do papel
});

const form = useForm({
    name: props.role.name,
    description: props.role.description,
});

const submit = () => {
    form.put(route('admin.roles.update', props.role.id), {
        onSuccess: () => {
            // Redireciona para a lista de papéis após a atualização
        },
        onError: (errors) => {
            console.error('Erro ao atualizar papel:', errors);
        },
    });
};
</script>

<template>
    <Head :title="`Editar Papel: ${role.name}`" />

    <AppLayout>
        <template #title>
            <h1>
                Editar Papel: {{ role.name }}
            </h1>
        </template>

        <template #default>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome do Papel</label>
                                    <input
                                        id="name"
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        required
                                        autofocus
                                    />
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <Link :href="route('admin.roles.index')" class="text-gray-600 hover:text-gray-900 mr-4">
                                        Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Atualizar Papel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
