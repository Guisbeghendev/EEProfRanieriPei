<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout>
        <Head title="Grêmio Estudantil" />

        <template #title>
            <h1 class="text-roxo2 dark:text-roxo2-hover font-extrabold tracking-tight text-4xl lg:text-5xl drop-shadow-md">
                Grêmio Estudantil
            </h1>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-2xl shadow-xl p-8 md:p-10 lg:p-12 border border-gray-100 dark:border-gray-700 text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-roxo2 dark:text-roxo2-hover mb-6 leading-tight drop-shadow-sm">
                    Em Breve: Grêmio Estudantil Ranieri!
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg mb-8">
                    Estamos trabalhando para construir a página do Grêmio Estudantil,
                    um espaço dedicado aos alunos da nossa escola.
                </p>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                    Fique atento para novidades sobre eventos, notícias e como se engajar!
                </p>

                <div class="mt-8">
                    <img src="https://placehold.co/600x350/AED6F1/2E86C1?text=PAGINA+EM+CONSTRUCAO"
                         alt="Página em construção"
                         class="mx-auto rounded-2xl shadow-md max-w-full h-auto"
                         onerror="this.onerror=null;this.src='https://placehold.co/600x350/cccccc/000000?text=Imagem+Não+Disponível';">
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Adicione estilos específicos para esta página se necessário */
</style>
