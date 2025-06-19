<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const currentPage = ref(0);
const chapters = ref([]);

const currentChapter = computed(() => {
    if (chapters.value && chapters.value.length > 0 && currentPage.value < chapters.value.length) {
        return chapters.value[currentPage.value];
    }
    // Fallback mais amigável para o usuário.
    return { title: 'Carregando a História da Escola...', content: '<p class="text-center text-gray-500 dark:text-gray-400 italic">Aguarde enquanto carregamos os capítulos da história. Se a página não carregar, por favor, tente recarregar.</p>' };
});

const totalChapters = computed(() => chapters.value?.length || 0);
const hasPrevious = computed(() => currentPage.value > 0);
const hasNext = computed(() => currentPage.value < totalChapters.value - 1);

const loadChapters = async () => {
    console.log('[SobreEscola.vue] Iniciando carregamento de capítulos dinamicamente...');
    const modules = import.meta.glob('./Capitulos/*.js');

    const loaded = [];
    for (const path in modules) {
        try {
            const module = await modules[path]();
            if (module && typeof module.default === 'object' && module.default !== null) {
                loaded.push(module.default);
                console.log(`[SobreEscola.vue] Capítulo carregado dinamicamente: ${path}`);
            } else {
                console.warn(`[SobreEscola.vue] Pulando módulo de capítulo malformado ou vazio: ${path}`);
            }
        } catch (e) {
            console.error(`[SobreEscola.vue] Erro ao carregar o módulo ${path}:`, e);
        }
    }

    if (loaded.length > 0) {
        loaded.sort((a, b) => {
            const numA = parseInt(a.title.match(/Capítulo (\d+)/)?.[1] || 0);
            const numB = parseInt(b.title.match(/Capítulo (\d+)/)?.[1] || 0);
            return numA - numB;
        });
    }

    chapters.value = loaded;
    console.log('[SobreEscola.vue] Número total de capítulos carregados e ordenados:', chapters.value.length);
};

onMounted(() => {
    loadChapters();
});

const goToPrevious = () => {
    if (hasPrevious.value) {
        currentPage.value--;
    }
};

const goToNext = () => {
    if (hasNext.value) {
        currentPage.value++;
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Sobre a Escola" />

        <template #title>
            <h1 class="text-laranja2 dark:text-laranja1-hover font-extrabold tracking-tight text-4xl lg:text-5xl drop-shadow-md">
                Sobre a Escola
            </h1>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-2xl shadow-xl p-8 md:p-10 lg:p-12 border border-gray-100 dark:border-gray-700">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-roxo2 dark:text-roxo2-hover mb-6 text-center leading-tight drop-shadow-sm">
                    {{ currentChapter.title }}
                </h2>

                <div class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg mb-8 prose dark:prose-invert">
                    <div v-html="currentChapter.content"></div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 gap-4">
                    <button
                        @click="goToPrevious"
                        :disabled="!hasPrevious"
                        class="px-8 py-3 w-full sm:w-auto
                               bg-roxo2 text-white font-bold rounded-full
                               shadow-lg hover:bg-roxo1-hover
                               disabled:bg-gray-300 dark:disabled:bg-gray-700
                               disabled:text-gray-500 dark:disabled:text-gray-400
                               disabled:cursor-not-allowed
                               transition duration-300 ease-in-out transform hover:scale-105 active:scale-95
                               focus:outline-none focus:ring-2 focus:ring-roxo2-focus focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Anterior
                    </button>

                    <span class="text-gray-600 dark:text-gray-400 text-base sm:text-lg font-semibold whitespace-nowrap">
                        Capítulo {{ currentPage + 1 }} de {{ totalChapters }}
                    </span>

                    <button
                        @click="goToNext"
                        :disabled="!hasNext"
                        class="px-8 py-3 w-full sm:w-auto
                               bg-laranja2 text-preto1 font-bold rounded-full
                               shadow-lg hover:bg-laranja1-hover
                               disabled:bg-gray-300 dark:disabled:bg-gray-700
                               disabled:text-gray-500 dark:disabled:text-gray-400
                               disabled:cursor-not-allowed
                               transition duration-300 ease-in-out transform hover:scale-105 active:scale-95
                               focus:outline-none focus:ring-2 focus:ring-laranja1-focus focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
