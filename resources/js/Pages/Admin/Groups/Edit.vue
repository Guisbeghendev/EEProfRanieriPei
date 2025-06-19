<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const props = defineProps({
    group: Object, // Espera um objeto 'group' com os dados do grupo
});

// Inicializa o formulário com os dados do grupo existente
const form = useForm({
    name: props.group.name,
    description: props.group.description,
});

// Função para submeter o formulário de edição
const submit = () => {
    // Usa form.put ou form.patch para requisições de atualização
    // Utilizando URL direta para a rota de atualização
    form.put(`/admin/groups/${props.group.id}`, { // <<<<<<<<<< CORRIGIDO AQUI
        onSuccess: () => {
            // O controller já fará o redirecionamento para a lista de grupos
            // e exibirá a mensagem de sucesso.
        },
        onError: (errors) => {
            console.error('Erro ao atualizar grupo:', errors);
            // Os erros de validação serão automaticamente vinculados a `form.errors`
            // e exibidos na interface, como você já configurou.
        },
    });
};
</script>

<template>
    <Head :title="`Editar Grupo: ${group.name}`"/>

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Grupo: {{ group.name }}
            </h2>
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
                                        Atualizar Grupo
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
