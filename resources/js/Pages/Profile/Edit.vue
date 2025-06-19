<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
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
const previewAvatarUrl = ref(props.user.profile?.avatar_relation?.url);

// Inicializa o formulário
const form = useForm({
    _method: 'patch',
    name: props.user.name,
    email: props.user.email,

    // Avatar será um objeto File ou null
    avatar: null, // Campo para o arquivo de upload
    remove_avatar: false, // Flag para indicar se o avatar deve ser removido

    // Campos da tabela 'profiles'
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
        // (que virá de props.user.profile.avatar_relation.url)
        previewAvatarUrl.value = props.user.profile?.avatar_relation?.url;
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
        previewAvatarUrl.value = props.user.profile?.avatar_relation?.url;
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
    form.post(route('profile.update'), {
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
                previewAvatarUrl.value = props.user.profile?.avatar_relation?.url;
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
                <!-- Seção de Dados Pessoais -->
                <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Dados Pessoais</h3>

                    <!-- Avatar Upload -->
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

                        <!-- Botão de Upload Estilizado -->
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

                        <div v-if="props.user.profile?.avatar_relation" class="mt-4 flex items-center">
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
                        <!-- Nome -->
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

                        <!-- E-mail -->
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
                                        :href="route('verification.send')"
                                        method="post"
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

                        <!-- Data de Nascimento -->
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

                <!-- Seção de Contatos -->
                <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                    <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Contatos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Endereço -->
                        <div>
                            <InputLabel for="address" value="Endereço" />
                            <TextInput
                                id="address"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.address"
                                :class="{ 'border-red-500': form.errors.address }"
                            />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>

                        <!-- Cidade -->
                        <div>
                            <InputLabel for="city" value="Cidade" />
                            <TextInput
                                id="city"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.city"
                                :class="{ 'border-red-500': form.errors.city }"
                            />
                            <InputError :message="form.errors.city" class="mt-2" />
                        </div>

                        <!-- Estado -->
                        <div>
                            <InputLabel for="state" value="Estado" />
                            <TextInput
                                id="state"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.state"
                                :class="{ 'border-red-500': form.errors.state }"
                            />
                            <InputError :message="form.errors.state" class="mt-2" />
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <InputLabel for="whatsapp" value="WhatsApp" />
                            <TextInput
                                id="whatsapp"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.whatsapp"
                                placeholder="(XX) XXXXX-XXXX"
                                :class="{ 'border-red-500': form.errors.whatsapp }"
                            />
                            <InputError :message="form.errors.whatsapp" class="mt-2" />
                        </div>

                        <!-- Outro Contato -->
                        <div class="md:col-span-2">
                            <InputLabel for="other_contact" value="Outro Contato" />
                            <TextInput
                                id="other_contact"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.other_contact"
                                :class="{ 'border-red-500': form.errors.other_contact }"
                            />
                            <InputError :message="form.errors.other_contact" class="mt-2" />
                        </div>
                    </div>
                </section>

                <!-- Seção de Biografia e Texto Ranieri -->
                <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8 space-y-6">
                    <!-- Biografia -->
                    <div>
                        <InputLabel for="biography" value="Biografia" />
                        <TextArea
                            id="biography"
                            class="mt-1 block w-full"
                            v-model="form.biography"
                            :class="{ 'border-red-500': form.errors.biography }"
                            rows="4"
                        />
                        <InputError :message="form.errors.biography" class="mt-2" />
                    </div>

                    <!-- Texto Ranieri -->
                    <div>
                        <InputLabel for="ranieri_text" value="O que eu sou para a Escola Ranieri?" />
                        <TextArea
                            id="ranieri_text"
                            class="mt-1 block w-full"
                            v-model="form.ranieri_text"
                            :class="{ 'border-red-500': form.errors.ranieri_text }"
                            rows="4"
                        />
                        <InputError :message="form.errors.ranieri_text" class="mt-2" />
                    </div>
                </section>

                <!-- Botões de Ação -->
                <div class="flex gap-4 justify-end">
                    <Link
                        :href="route('profile.show')"
                        class="px-4 py-2 bg-laranja2 text-preto1 rounded-md hover:bg-laranja1-hover flex items-center justify-center"
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
