<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    users: Array, // Espera um array de usuários
});

const deleteUser = (userId) => {
    if (confirm('Tem certeza de que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        router.delete(route('admin.users.destroy', userId), {
            onSuccess: () => {
                // A página será recarregada automaticamente via Inertia
            },
            onError: (errors) => {
                console.error('Erro ao excluir usuário:', errors);
                alert('Erro ao excluir usuário. Verifique o console para mais detalhes.');
            },
        });
    }
};
</script>

<template>
    <Head title="Gerenciar Usuários" />

    <AppLayout>
        <template #title>
            <h1>Gerenciar Usuários</h1>
        </template>

        <template #default>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lista de Usuários</h3>

                            <div v-if="users.length > 0">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="user in users" :key="user.id" class="py-4 flex justify-between items-center">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ user.name }} ({{ user.email }})</div>
                                            <div class="text-xs text-gray-500">
                                                Papéis: {{ user.roles.map(role => role.name).join(', ') || 'Nenhum' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Grupos: {{ user.groups.map(group => group.name).join(', ') || 'Nenhum' }}
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                Editar
                                            </Link>
                                            <button
                                                @click="deleteUser(user.id)"
                                                class="text-red-600 hover:text-red-900 text-sm"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="text-gray-600">
                                Nenhum usuário cadastrado.
                            </div>

                            <div class="mt-6 flex items-center space-x-4">
                                <Link :href="route('admin.users.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Criar Novo Usuário
                                </Link>
                                <Link :href="route('admin.dashboard')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Voltar ao Painel
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
