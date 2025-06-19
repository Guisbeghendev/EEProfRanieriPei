<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Importar Link para usar o link externo
</script>

<template>
    <AppLayout>
        <Head title="Simoninha na Cozinha" />

        <template #title>
            <h1 class="text-roxo2 dark:text-roxo2-hover font-extrabold tracking-tight text-4xl lg:text-5xl drop-shadow-md">
                Simoninha na Cozinha
            </h1>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-2xl shadow-xl p-8 md:p-10 lg:p-12 border border-gray-100 dark:border-gray-700 text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-roxo2 dark:text-roxo2-hover mb-6 leading-tight drop-shadow-sm">
                    Bem-vindo ao Canal Simoninha na Cozinha!
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg mb-8">
                    Descubra receitas deliciosas, dicas de culinária e muita diversão com a Simoninha!
                    Prepare-se para aventuras gastronômicas incríveis no nosso canal do YouTube.
                </p>

                <div class="mt-8 flex justify-center">
                    <a href="https://www.youtube.com/@simoninhanacozinha672"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-bold rounded-full shadow-lg
                              text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 focus:ring-opacity-50
                              transform hover:scale-105 transition-all duration-300 ease-in-out">
                        <svg class="-ml-1 mr-3 h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.016 3.167c-1.897 0-3.693.447-5.32 1.341-1.627.893-2.986 2.186-4.077 3.873-1.091 1.687-1.748 3.593-1.748 5.626 0 2.033.657 3.94 1.748 5.626 1.091 1.687 2.45 2.98 4.077 3.873 1.627.894 3.423 1.341 5.32 1.341s3.693-.447 5.32-1.341c1.627-.893 2.986-2.186 4.077-3.873 1.091-1.686 1.748-3.593 1.748-5.626 0-2.033-.657-3.94-1.748-5.626-1.091-1.687-2.45-2.98-4.077-3.873-1.627-.894-3.423-1.341-5.32-1.341zm-4.321 6.836l6.634 3.771-6.634 3.772v-7.543z" />
                        </svg>
                        Visitar o Canal no YouTube
                    </a>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Adicione estilos específicos para esta página se necessário */
</style>
