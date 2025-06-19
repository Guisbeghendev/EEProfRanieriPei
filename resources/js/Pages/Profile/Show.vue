<template>
    <AppLayout>
        <Head title="Meu perfil" />

        <template #title>
            <h1>Meu Perfil</h1>
        </template>

        <!-- A div externa agora tem padding py-16 e px maiores para a "área branca". -->
        <div
            class="py-16 max-w-4xl mx-auto sm:px-8 lg:px-12 space-y-8"
        >
            <!-- Cada seção agora tem um fundo bg-gray-200 e padding p-4 (reduzido). -->
            <section class="bg-gray-200 dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-2xl font-bold mb-6">Dados Pessoais</h3>
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="flex-shrink-0">
                        <img
                            v-if="user.profile?.avatar_relation?.url"
                            :src="user.profile.avatar_relation.url"
                            alt="Avatar"
                            class="w-32 h-32 rounded-full object-cover border-2 border-laranja2"
                        />
                        <div
                            v-else
                            class="w-32 h-32 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-xl text-gray-500"
                        >
                            Não informado
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 flex-grow w-full">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</p>
                            <p class="text-lg font-semibold">{{ displayOrDefault(user.name) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</p>
                            <p class="text-lg font-semibold">{{ displayOrDefault(user.email) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Nascimento</p>
                            <p class="text-lg font-semibold">
                                {{
                                    user.profile?.birth_date
                                        ? new Date(user.profile.birth_date).toLocaleDateString('pt-BR')
                                        : 'não informado'
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cadastrado em</p>
                            <p class="text-lg font-semibold">
                                {{
                                    user.created_at
                                        ? new Date(user.created_at).toLocaleDateString('pt-BR')
                                        : 'não informado'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-gray-200 dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-2xl font-bold mb-6">Contatos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço</p>
                        <p class="text-lg font-semibold">{{ displayOrDefault(user.profile?.address) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade</p>
                        <p class="text-lg font-semibold">{{ displayOrDefault(user.profile?.city) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</p>
                        <p class="text-lg font-semibold">{{ displayOrDefault(user.profile?.state) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp</p>
                        <p class="text-lg font-semibold">{{ displayOrDefault(user.profile?.whatsapp) }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Outro Contato</p>
                        <p class="text-lg font-semibold">{{ displayOrDefault(user.profile?.other_contact) }}</p>
                    </div>
                </div>
            </section>

            <section class="bg-gray-200 dark:bg-gray-800 rounded-lg shadow p-4 space-y-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Biografia</h3>
                    <p
                        class="whitespace-pre-line text-gray-900 dark:text-gray-100 min-h-[80px] border border-gray-200 dark:border-gray-700 rounded p-4 bg-gray-50 dark:bg-gray-900"
                    >
                        {{ displayOrDefault(user.profile?.biography) }}
                    </p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">O que eu sou para a Escola Ranieri?</h3>
                    <p
                        class="whitespace-pre-line text-gray-900 dark:text-gray-100 min-h-[80px] border border-gray-200 dark:border-gray-700 rounded p-4 bg-gray-50 dark:bg-gray-900"
                    >
                        {{ displayOrDefault(user.profile?.ranieri_text) }}
                    </p>
                </div>
            </section>

            <div class="flex gap-4 justify-end">
                <Link
                    href="/dashboard"
                    class="px-4 py-2 bg-laranja2 text-preto1 rounded-md hover:bg-laranja1-hover"
                >
                    Voltar
                </Link>
                <Link
                    href="/profile/edit"
                    class="px-4 py-2 bg-roxo2 text-white rounded-md hover:bg-roxo2-hover"
                >
                    Editar Conta
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const displayOrDefault = (value) => {
    if (value === null || value === undefined || value === '') {
        return 'não informado';
    }
    return value;
};
</script>
