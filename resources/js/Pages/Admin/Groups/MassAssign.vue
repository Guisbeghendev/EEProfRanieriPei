<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Array,
    groups: Array,
    filters: Object,
});

const form = useForm({
    selected_user_ids: [],
    selected_group_ids: [],
});

const filterForm = useForm({
    created_at_start: props.filters.created_at_start || '',
    created_at_end: props.filters.created_at_end || '',
});

watch(
    () => filterForm.data(),
    (newVal) => {
        // CORREÇÃO: Substituindo route() por URL direta para a rota de índice de associação em massa de grupos
        router.get('/admin/users/mass-assign-groups', newVal, {
            preserveState: true,
            replace: true,
            only: ['users', 'filters'], // Apenas atualiza 'users' e 'filters' na página atual
        });
    },
    { deep: true }
);

const clearFilters = () => {
    filterForm.created_at_start = '';
    filterForm.created_at_end = '';
    // A mudança nos valores do filterForm disparará o watch e atualizará a lista
};

const submit = () => {
    // CORREÇÃO: Substituindo route() por URL direta para a rota de store de associação em massa de grupos
    form.post('/admin/users/mass-assign-groups', {
        onSuccess: () => {
            form.reset('selected_user_ids', 'selected_group_ids');
            alert('Associação de grupos em massa realizada com sucesso!');
            // O Inertia automaticamente fará uma nova visita GET para a página atual
            // se o controller redirecionar com 303, o que é o comportamento esperado.
        },
        onError: (errors) => {
            console.error('Erro ao associar grupos em massa:', errors);
            // Os erros de validação do backend (se houver) serão automaticamente expostos em form.errors
            alert('Erro ao associar grupos em massa. Verifique o console para mais detalhes.');
        },
    });
};

</script>

<template>
    <Head title="Associação em Massa de Usuários a Grupos" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Associação em Massa de Usuários a Grupos
            </h2>
        </template>

        <template #default>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form @submit.prevent="submit">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Filtrar Usuários</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div>
                                        <label for="created_at_start" class="block text-sm font-medium text-gray-700">Criado Após</label>
                                        <input
                                            id="created_at_start"
                                            type="date"
                                            v-model="filterForm.created_at_start"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        />
                                    </div>
                                    <div>
                                        <label for="created_at_end" class="block text-sm font-medium text-gray-700">Criado Antes</label>
                                        <input
                                            id="created_at_end"
                                            type="date"
                                            v-model="filterForm.created_at_end"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        />
                                    </div>
                                    <div class="flex items-end space-x-2">
                                        <button
                                            type="button"
                                            @click="clearFilters"
                                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition ease-in-out duration-150"
                                        >
                                            Limpar Filtros
                                        </button>
                                    </div>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Selecionar Usuários</h3>
                                <div v-if="form.errors.selected_user_ids" class="text-red-600 text-sm mb-4">{{ form.errors.selected_user_ids }}</div>
                                <div class="border rounded-md p-4 max-h-96 overflow-y-auto mb-6">
                                    <div v-if="users.length === 0" class="text-gray-500 text-center">Nenhum usuário encontrado com os filtros aplicados.</div>
                                    <div v-for="user in users" :key="user.id" class="flex items-center mb-2">
                                        <input
                                            :id="`user-${user.id}`"
                                            type="checkbox"
                                            :value="user.id"
                                            v-model="form.selected_user_ids"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        />
                                        <label :for="`user-${user.id}`" class="ml-2 text-sm text-gray-700">
                                            {{ user.name }} ({{ user.email }}) - Ranieri: **{{ user.ranieri_text }}**
                                            <span class="text-gray-500 text-xs">(Criado em: {{ user.created_at }})</span>
                                        </label>
                                    </div>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-b pb-2">Selecionar Grupos</h3>
                                <div v-if="form.errors.selected_group_ids" class="text-red-600 text-sm mb-4">{{ form.errors.selected_group_ids }}</div>
                                <div class="border rounded-md p-4 max-h-60 overflow-y-auto mb-6">
                                    <div v-if="groups.length === 0" class="text-gray-500 text-center">Nenhum grupo disponível.</div>
                                    <div v-for="group in groups" :key="group.id" class="flex items-center mb-2">
                                        <input
                                            :id="`group-${group.id}`"
                                            type="checkbox"
                                            :value="group.id"
                                            v-model="form.selected_group_ids"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        />
                                        <label :for="`group-${group.id}`" class="ml-2 text-sm text-gray-700">{{ group.name }}</label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4 space-x-4">
                                    <!-- CORREÇÃO: Link para Voltar ao Painel Admin - SEM ZIGGY -->
                                    <Link href="/admin/dashboard" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Voltar para o Painel Admin
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Associar Grupos em Massa
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
