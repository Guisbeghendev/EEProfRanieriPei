<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false, // Adicione um default para segurança
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    // AQUI: Alterado de `route('login')` para a URL literal `'/login'`
    // A Inertia.js é inteligente o suficiente para incluir o token CSRF
    // automaticamente em requisições POST se a meta tag csrf-token estiver presente no HTML.
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Login"/>

        <template #title>
            <h1 class="text-roxo2 dark:text-roxo2-hover font-extrabold tracking-tight text-4xl lg:text-5xl drop-shadow-md">
                Login no Sistema
            </h1>
        </template>

        <div class="min-h-screen flex items-center justify-center p-4">
            <div
                class="w-full max-w-5xl flex flex-col lg:flex-row bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">

                <div
                    class="w-full lg:w-1/2 flex items-center justify-center bg-gradient-to-br from-laranja1 via-laranja2 to-roxo2 text-white p-10 py-12 lg:py-0">
                    <div class="text-center space-y-6">
                        <h2 class="text-4xl lg:text-5xl font-bold mb-4 drop-shadow-md">Bem-vindo(a) de volta!</h2>
                        <p class="text-lg lg:text-xl text-prata1 opacity-90">
                            Acesse sua conta para continuar gerenciando o conteúdo.
                        </p>
                        <div class="mt-8">
                            <img src="/images/logo/prof_ranieri - fundo branco.png" alt="Logo Ranieri"
                                 class="h-28 w-auto mx-auto drop-shadow-lg filter brightness-110"/>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 flex items-center justify-center p-8 md:p-12 lg:p-16">
                    <div class="w-full max-w-md space-y-6">
                        <div v-if="status"
                             class="mb-6 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-medium border border-green-200 dark:border-green-800">
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="email" value="Endereço de E-mail"
                                            class="text-gray-800 dark:text-gray-200"/>
                                <TextInput
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="mt-2 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-roxo1 focus:ring-roxo1 rounded-md shadow-sm"
                                />
                                <InputError class="mt-2 text-sm text-red-600 dark:text-red-400"
                                            :message="form.errors.email"/>
                            </div>

                            <div>
                                <InputLabel for="password" value="Senha" class="text-gray-800 dark:text-gray-200"/>
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    class="mt-2 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-roxo1 focus:ring-roxo1 rounded-md shadow-sm"
                                />
                                <InputError class="mt-2 text-sm text-red-600 dark:text-red-400"
                                            :message="form.errors.password"/>
                            </div>

                            <div class="block">
                                <label class="flex items-center">
                                    <Checkbox name="remember" v-model:checked="form.remember"/>
                                    <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">Lembrar-me</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <Link
                                    v-if="canResetPassword"
                                    href="/forgot-password"
                                    class="text-sm text-gray-600 dark:text-gray-300 underline hover:text-roxo2 dark:hover:text-laranja2 transition-colors duration-200"
                                >
                                    Esqueceu sua senha?
                                </Link>

                                <PrimaryButton
                                    class="ms-4 px-6 py-3 bg-roxo1 hover:bg-roxo1-hover text-prata1 font-semibold rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Entrar
                                </PrimaryButton>
                            </div>
                            <div class="mt-4 text-center">
                                <Link
                                    href="/register"
                                    class="text-sm text-gray-600 dark:text-gray-300 underline hover:text-roxo2 dark:hover:text-laranja2 transition-colors duration-200"
                                >
                                    Ainda não tem uma conta? Cadastre-se!
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
