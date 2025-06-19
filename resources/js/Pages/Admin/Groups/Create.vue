<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'; // Importe useForm para lidar com o formulário

// Define um formulário usando useForm
const form = useForm({
    name: '',        // Nome do grupo
    description: '', // Descrição do grupo
});

// Função para submeter o formulário
const submit = () => {
    // Usando URL direta para a rota POST (store)
    form.post('/admin/groups', { // <<<<<<<<<< CORRIGIDO AQUI
        onSuccess: () => {
            form.reset(); // Limpa o formulário após o sucesso
            // O Inertia já vai redirecionar para a rota 'admin.groups.index'
            // graças ao `return redirect()->route('admin.groups.index')->withStatus(303);` no controller.
        },
        onError: (errors) => {
            console.error('Erro ao criar grupo:', errors);
            // Os erros de validação serão automaticamente vinculados a `form.errors`
            // e exibidos na interface, como você já configurou com `v-if="form.errors.name"`.
        },
    });
};
</script>

<template>
    <Head title="Criar Novo Grupo"/>

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Criar Novo Grupo</h2>
        </template>

        <template #default>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome do
                                        Grupo</label>
                                    <input
                                        id="name"
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        required
                                        autofocus
                                    />
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{
                                            form.errors.name
                                        }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="description"
                                           class="block text-sm font-medium text-gray-700">Descrição</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.description }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <Link href="/admin/groups" class="text-gray-600 hover:text-gray-900 mr-4"> Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Criar Grupo
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
