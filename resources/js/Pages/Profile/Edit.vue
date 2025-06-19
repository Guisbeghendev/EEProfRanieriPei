<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue'; // <-- Importe o TextArea
import { Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    user: Object, // O objeto User com o relacionamento 'profile' carregado, que agora terá 'profile.avatarRelation'
    mustVerifyEmail: Boolean,
    status: String,
});

// Função para formatar a data para exibição (para input type="date")
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

// Estado para a URL de pré-visualização do avatar
// Inicializa com a URL do avatar existente (se houver, através da nova relação)
// Nota: 'avatar_relation' é o nome que você usou no seu `Profile/Show.vue` e aqui.
// No seu User model, o relacionamento para o avatar provavelmente se chama 'avatar'.
// É importante que o nome usado aqui ('avatar_relation') reflita o nome da prop real.
// Se no User model o relacionamento for `public function avatar()`, então a prop `user.avatar.url` já estaria disponível.
// Ajustei a inicialização para usar `user.avatar?.url` para consistência com `AppLayout.vue` e o que o Controller carrega.
const previewAvatarUrl = ref(props.user.avatar?.url);

// Inicializa o formulário
const form = useForm({
    _method: 'post', // Inertia.js com _method: 'patch' exige um POST real e o Laravel interpreta como PATCH.
    // O route('profile.update') usa PATCH, então manter '_method: patch' é o correto.
    // No entanto, para upload de arquivos, Inertia usa POST e o Laravel lida com _method.
    // Vamos manter '_method: patch' e confiar no Inertia.
    name: props.user.name,
    email: props.user.email,

    // Avatar será um objeto File ou null
    avatar: null, // Campo para o arquivo de upload
    remove_avatar: false, // Flag para indicar se o avatar deve ser removido

    // Campos da tabela 'profiles'
    // Assumindo que user.profile é a relação 'profile' carregada
    birth_date: props.user.profile?.birth_date ? formatDateForInput(props.user.profile.birth_date) : '',
    address: props.user.profile?.address ?? '',
    city: props.user.profile?.city ?? '',
    state: props.user.profile?.state ?? '',
    whatsapp: props.user.profile?.whatsapp ?? '',
    other_contact: props.user.profile?.other_contact ?? '',
    ranieri_text: props.user.profile?.ranieri_text ?? '',
    biography: props.user.profile?.biography ?? '',
});

// Observa mudanças no arquivo de avatar selecionado para criar a pré-visualização
watch(() => form.avatar, (newAvatar) => {
    if (newAvatar instanceof File) {
        previewAvatarUrl.value = URL.createObjectURL(newAvatar);
        form.remove_avatar = false; // Desmarca a remoção se um novo avatar for selecionado
    } else if (newAvatar === null && !form.remove_avatar) {
        // Se avatar for nulo e não for para remover, mostra o avatar existente do usuário
        // (que virá de props.user.avatar.url, carregado pelo Controller)
        previewAvatarUrl.value = props.user.avatar?.url;
    }
});

// Função para lidar com a seleção do arquivo de avatar
const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
    } else {
        form.avatar = null;
        // Se o usuário desmarcar o arquivo, a pré-visualização volta para a URL original ou nulo
        previewAvatarUrl.value = props.user.avatar?.url;
    }
};

// Função para remover o avatar
const removeAvatar = () => {
    form.avatar = null; // Limpa o arquivo selecionado
    form.remove_avatar = true; // Define a flag para remover no backend
    previewAvatarUrl.value = null; // Limpa a pré-visualização
};

const submit = () => {
    // Quando há upload de arquivo, Inertia precisa enviar como FormData
    // A rota para update do perfil deve ser POST com _method=patch.
    form.post('/profile', { // <-- URL literal para profile.update (geralmente /profile)
        forceFormData: true, // Garante que o formulário seja enviado como FormData
        preserveScroll: true,
        onSuccess: () => {
            // Após o sucesso, as props são recarregadas, então o previewAvatarUrl será atualizado
            // automaticamente pelo watch ou pelas novas props do Inertia.
            form.avatar = null; // Limpa o campo de arquivo para permitir novo upload
            form.remove_avatar = false; // Reseta a flag de remoção
        },
        onError: (errors) => {
            console.error('Erro ao atualizar perfil:', errors);
            // Se houver erro de validação no avatar, limpa o input file para permitir novo upload
            if (errors.avatar) {
                form.avatar = null;
                // Mantém a pré-visualização existente se houver, ou volta para a original
                previewAvatarUrl.value = props.user.avatar?.url;
            }
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Editar Perfil" />

        <template #title>
            <h1>Editar Meu Perfil</h1>
        </template>

        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8 bg-prata1 dark:bg-gray-900 rounded-lg space-y-8">
            <form @submit.prevent="submit">
                <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Dados Pessoais</h3>

                    <div class="mb-6 flex flex-col items-center">
                        <InputLabel for="avatar" value="Avatar" class="mb-2" />
                        <img
                            v-if="previewAvatarUrl"
                            :src="previewAvatarUrl"
                            alt="Avatar Preview"
                            class="w-24 h-24 rounded-full object-cover border-2 border-laranja2 mb-4"
                        />
                        <div
                            v-else
                            class="w-24 h-24 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-sm text-gray-500 mb-4"
                        >
                            Sem Avatar
                        </div>

                        <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 bg-laranja2 text-preto1 font-semibold text-xs rounded-md shadow-sm hover:bg-laranja1-hover focus:ring-4 focus:ring-laranja2 focus:ring-offset-2 dark:bg-laranja-dark dark:hover:bg-laranja-dark-hover dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Selecionar Imagem
                            <input
                                id="avatar"
                                type="file"
                                class="hidden"
                                @change="handleAvatarChange"
                                accept="image/jpeg,image/png,image/gif"
                            />
                        </label>
                        <InputError :message="form.errors.avatar" class="mt-2" />
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Selecione uma imagem para seu avatar (JPG, PNG, GIF, máx. 2MB).
                        </p>

                        <div v-if="props.user.avatar?.url" class="mt-4 flex items-center">
                            <input
                                type="checkbox"
                                id="remove_avatar_checkbox"
                                v-model="form.remove_avatar"
                                @change="removeAvatar"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <label for="remove_avatar_checkbox" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                Remover avatar atual
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Nome" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="email" value="E-mail" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :class="{ 'border-red-500': form.errors.email }"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />

                            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                    Seu endereço de e-mail não foi verificado.
                                    <Link
                                        href="/email/verification-notification" method="post"
                                        as="button"
                                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    >
                                        Clique aqui para reenviar o e-mail de verificação.
                                    </Link>
                                </p>

                                <div
                                    v-show="status === 'verification-link-sent'"
                                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                                >
                                    Um novo link de verificação foi enviado para o seu endereço de e-mail.
                                </div>
                            </div>
                        </div>

                        <div>
                            <InputLabel for="birth_date" value="Data de Nascimento" />
                            <TextInput
                                id="birth_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.birth_date"
                                :class="{ 'border-red-500': form.errors.birth_date }"
                            />
                            <InputError :message="form.errors.birth_date" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8 space-y-6">
                    <div>
                        <InputLabel for="biography" value="Biografia" />
                        <TextArea
                            id="biography"
                            class="mt-1 block w-full"
                            v-model="form.biography"
                            :class="{ 'border-red-500': form.errors.biography }"
                            :rows="4" />
                        <InputError :message="form.errors.biography" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="ranieri_text" value="O que eu sou para a Escola Ranieri?" />
                        <TextArea
                            id="ranieri_text"
                            class="mt-1 block w-full"
                            v-model="form.ranieri_text"
                            :class="{ 'border-red-500': form.errors.ranieri_text }"
                            :rows="4" />
                        <InputError :message="form.errors.ranieri_text" class="mt-2" />
                    </div>
                </section>

                <div class="flex gap-4 justify-end">
                    <Link
                        href="/profile" class="px-4 py-2 bg-laranja2 text-preto1 rounded-md hover:bg-laranja1-hover flex items-center justify-center"
                    >
                        Cancelar
                    </Link>
                    <PrimaryButton :disabled="form.processing">
                        Salvar Alterações
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
