<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue'; // Importar TextArea para os campos biography e ranieri_text
import { ref, watch } from 'vue'; // Importar ref e watch para a lógica do avatar

const props = defineProps({
    roles: Array, // Papéis disponíveis
    groups: Array, // Grupos disponíveis
});

// Estado para a URL de pré-visualização do avatar
// Inicializado como nulo para um novo usuário
const previewAvatarUrl = ref(null);

// Inicializa o formulário
const form = useForm({
    // Campos do User
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
    groups: [],
    // Campos do Profile
    avatar: null, // Agora será um objeto File ou null para upload
    remove_avatar: false, // Flag para indicar se o avatar deve ser removido (útil em edições, mas bom ter a lógica)
    birth_date: '',
    address: '',
    city: '',
    state: '',
    whatsapp: '',
    other_contact: '',
    ranieri_text: '',
    biography: '',
});

// Observa mudanças no arquivo de avatar selecionado para criar a pré-visualização
watch(() => form.avatar, (newAvatar) => {
    if (newAvatar instanceof File) {
        previewAvatarUrl.value = URL.createObjectURL(newAvatar);
        form.remove_avatar = false; // Desmarca a remoção se um novo avatar for selecionado
    } else if (newAvatar === null && !form.remove_avatar) {
        // Se avatar for nulo e não for para remover, limpa a pré-visualização
        previewAvatarUrl.value = null;
    }
});

// Função para lidar com a seleção do arquivo de avatar
const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
    } else {
        form.avatar = null;
        previewAvatarUrl.value = null; // Limpa a pré-visualização
    }
};

// Função para remover o avatar (mais relevante para edição, mas incluída para completar a lógica)
const removeAvatar = () => {
    form.avatar = null; // Limpa o arquivo selecionado
    form.remove_avatar = true; // Define a flag para remover no backend
    previewAvatarUrl.value = null; // Limpa a pré-visualização
};

const submit = () => {
    // É CRUCIAL usar forceFormData: true para enviar arquivos com Inertia
    form.post(route('admin.users.store'), {
        forceFormData: true, // Garante que o formulário seja enviado como FormData
        onSuccess: () => {
            alert('Usuário criado com sucesso!'); // Usar alert para feedback rápido
            form.reset(
                'name', 'email', 'password', 'password_confirmation', 'roles', 'groups',
                'avatar', 'remove_avatar', 'birth_date', 'address', 'city', 'state',
                'whatsapp', 'other_contact', 'ranieri_text', 'biography'
            );
            previewAvatarUrl.value = null; // Garante que o preview seja limpo também
        },
        onError: (errors) => {
            console.error('Erro ao criar usuário:', errors);
            // Manter o alert original para feedback direto ao usuário
            alert('Erro ao criar usuário. Verifique o console para mais detalhes.');
            // Se houver erro de validação no avatar, limpa o input file para permitir novo upload
            if (errors.avatar) {
                form.avatar = null;
                previewAvatarUrl.value = null;
            }
        },
    });
};
</script>

<template>
    <Head title="Criar Novo Usuário" />

    <AppLayout>
        <template #title>
            <h1>
                Criar Novo Usuário
            </h1>
        </template>

        <template #default>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form @submit.prevent="submit">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Dados do Usuário</h3>

                                <!-- Avatar Upload (replicado do Profile/Edit.vue) -->
                                <div class="mb-6 flex flex-col items-center">
                                    <InputLabel for="avatar" value="Avatar" class="mb-2" />
                                    <!-- Pré-visualização do avatar -->
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
                                        Selecione uma imagem para o avatar (JPG, PNG, GIF, máx. 2MB).
                                    </p>
                                    <!-- A opção de "remover avatar" para criação de usuário não faz muito sentido aqui
                                         já que o usuário não tem um avatar ainda. Pode ser removida ou mantida
                                         para consistência se a edição usar o mesmo componente.
                                         Por enquanto, vou mantê-la para que você veja a lógica completa. -->
                                    <div v-if="previewAvatarUrl" class="mt-4 flex items-center">
                                        <input
                                            type="checkbox"
                                            id="remove_avatar_checkbox"
                                            v-model="form.remove_avatar"
                                            @change="removeAvatar"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        />
                                        <label for="remove_avatar_checkbox" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            Remover avatar selecionado (se aplicável)
                                        </label>
                                    </div>
                                </div>
                                <!-- Fim da seção de Avatar Upload -->

                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                                    <TextInput
                                        id="name"
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
                                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.name" />
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                                    <TextInput
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        class="mt-1 block w-full"
                                        required
                                        autocomplete="username"
                                    />
                                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.email" />
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                                    <TextInput
                                        id="password"
                                        type="password"
                                        v-model="form.password"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password" />
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        v-model="form.password_confirmation"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-b pb-2">Dados do Perfil (Opcional)</h3>

                                <!-- Removido o input type="text" para avatar, substituído pela lógica acima -->
                                <!-- <div class="mb-4">
                                    <label for="avatar" class="block text-sm font-medium text-gray-700">URL do Avatar</label>
                                    <input
                                        id="avatar"
                                        type="text"
                                        v-model="form.avatar"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <div v-if="form.errors.avatar" class="text-red-600 text-sm mt-1">{{ form.errors.avatar }}</div>
                                </div> -->

                                <div class="mb-4">
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                                    <TextInput
                                        id="birth_date"
                                        type="date"
                                        v-model="form.birth_date"
                                        class="mt-1 block w-full"
                                    />
                                    <div v-if="form.errors.birth_date" class="text-red-600 text-sm mt-1">{{ form.errors.birth_date }}</div>
                                </div>

                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                                    <TextInput
                                        id="address"
                                        type="text"
                                        v-model="form.address"
                                        class="mt-1 block w-full"
                                    />
                                    <div v-if="form.errors.address" class="text-red-600 text-sm mt-1">{{ form.errors.address }}</div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">Cidade</label>
                                        <TextInput
                                            id="city"
                                            type="text"
                                            v-model="form.city"
                                            class="mt-1 block w-full"
                                        />
                                        <div v-if="form.errors.city" class="text-red-600 text-sm mt-1">{{ form.errors.city }}</div>
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                                        <TextInput
                                            id="state"
                                            type="text"
                                            v-model="form.state"
                                            class="mt-1 block w-full"
                                        />
                                        <div v-if="form.errors.state" class="text-red-600 text-sm mt-1">{{ form.errors.state }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                                        <TextInput
                                            id="whatsapp"
                                            type="text"
                                            v-model="form.whatsapp"
                                            class="mt-1 block w-full"
                                        />
                                        <div v-if="form.errors.whatsapp" class="text-red-600 text-sm mt-1">{{ form.errors.whatsapp }}</div>
                                    </div>
                                    <div>
                                        <label for="other_contact" class="block text-sm font-medium text-gray-700">Outro Contato</label>
                                        <TextInput
                                            id="other_contact"
                                            type="text"
                                            v-model="form.other_contact"
                                            class="mt-1 block w-full"
                                        />
                                        <div v-if="form.errors.other_contact" class="text-red-600 text-sm mt-1">{{ form.errors.other_contact }}</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="ranieri_text" class="block text-sm font-medium text-gray-700">Texto Ranieri</label>
                                    <TextArea
                                        id="ranieri_text"
                                        v-model="form.ranieri_text"
                                        rows="3"
                                        class="mt-1 block w-full"
                                    ></TextArea>
                                    <div v-if="form.errors.ranieri_text" class="text-red-600 text-sm mt-1">{{ form.errors.ranieri_text }}</div>
                                </div>

                                <div class="mb-4">
                                    <label for="biography" class="block text-sm font-medium text-gray-700">Biografia</label>
                                    <TextArea
                                        id="biography"
                                        v-model="form.biography"
                                        rows="5"
                                        class="mt-1 block w-full"
                                    ></TextArea>
                                    <div v-if="form.errors.biography" class="text-red-600 text-sm mt-1">{{ form.errors.biography }}</div>
                                </div>


                                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-b pb-2">Associação de Papéis</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Papéis</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        <div v-for="role in roles" :key="role.id" class="flex items-center">
                                            <input
                                                :id="`role-${role.id}`"
                                                type="checkbox"
                                                :value="role.id"
                                                v-model="form.roles"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                            <label :for="`role-${role.id}`" class="ml-2 text-sm text-gray-700">{{ role.name }}</label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.roles" class="text-red-600 text-sm mt-1">{{ form.errors.roles }}</div>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-b pb-2">Associação de Grupos</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grupos</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        <div v-for="group in groups" :key="group.id" class="flex items-center">
                                            <input
                                                :id="`group-${group.id}`"
                                                type="checkbox"
                                                :value="group.id"
                                                v-model="form.groups"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                            <label :for="`group-${group.id}`" class="ml-2 text-sm text-gray-700">{{ group.name }}</label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.groups" class="text-red-600 text-sm mt-1">{{ form.errors.groups }}</div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <Link :href="route('admin.users.index')" class="text-gray-600 hover:text-gray-900 mr-4">
                                        Cancelar
                                    </Link>
                                    <PrimaryButton
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Criar Usuário
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
