<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const currentPage = ref(0);
const chapters = ref([]); // Inicializado como um array vazio, será preenchido após a montagem.

// Propriedade computada para obter o capítulo atual.
// Inclui um fallback robusto para garantir que sempre haja um objeto válido.
const currentChapter = computed(() => {
    // Garante que chapters.value é um array e tem elementos antes de acessar o índice.
    if (chapters.value && chapters.value.length > 0 && currentPage.value < chapters.value.length) {
        return chapters.value[currentPage.value];
    }
    // Fallback para quando os capítulos ainda estão sendo carregados ou não existem.
    return { title: 'Carregando História...', content: '<p>Aguarde enquanto carregamos a história do Coral Ranieri...</p>' };
});

// Propriedade computada para a contagem total de capítulos, mais segura.
const totalChapters = computed(() => chapters.value?.length || 0);

// Propriedades computadas para controlar a visibilidade dos botões de navegação.
const hasPrevious = computed(() => currentPage.value > 0);
const hasNext = computed(() => currentPage.value < totalChapters.value - 1);

// Função para carregar os capítulos dinamicamente
const loadChapters = async () => {
    console.log('[CoralRanieri.vue] Iniciando carregamento de capítulos dinamicamente...');
    // ATENÇÃO: O caminho para import.meta.glob busca módulos JS dentro da pasta 'Capitulos'
    // que deve estar DENTRO de 'resources/js/Pages/Coral/'.
    const modules = import.meta.glob('./Capitulos/*.js');

    const loaded = [];
    // Itera sobre os módulos importados dinamicamente
    for (const path in modules) {
        try {
            // Aguarda a importação de cada módulo para obter seu conteúdo real.
            const module = await modules[path]();
            if (module && typeof module.default === 'object' && module.default !== null) {
                loaded.push(module.default);
                console.log(`[CoralRanieri.vue] Capítulo carregado dinamicamente: ${path}`);
            } else {
                console.warn(`[CoralRanieri.vue] Pulando módulo de capítulo malformado ou vazio: ${path}`);
            }
        } catch (e) {
            console.error(`[CoralRanieri.vue] Erro ao carregar o módulo ${path}:`, e);
        }
    }

    if (loaded.length > 0) {
        // Ordena os capítulos com base no número extraído do título.
        // Adapte esta expressão regular se seus títulos de capítulo não começarem com "Capítulo X:"
        loaded.sort((a, b) => {
            const numA = parseInt(a.title.match(/Capítulo (\d+)/)?.[1] || 0);
            const numB = parseInt(b.title.match(/Capítulo (\d+)/)?.[1] || 0);
            return numA - numB;
        });
    }

    chapters.value = loaded; // ATUALIZA A REF REATIVA com os capítulos carregados.
    console.log('[CoralRanieri.vue] Número total de capítulos carregados e ordenados:', chapters.value.length);
};

// Quando o componente é montado no DOM, inicia o carregamento dos capítulos.
onMounted(() => {
    loadChapters();
});

// Funções de navegação (inalteradas)
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
        <Head title="Coral Ranieri" />

        <template #title>
            <h1 class="text-roxo2 dark:text-roxo2-hover font-extrabold tracking-tight text-4xl lg:text-5xl drop-shadow-md">
                Coral Ranieri
            </h1>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-2xl shadow-xl p-8 md:p-10 lg:p-12 border border-gray-100 dark:border-gray-700">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-roxo2 dark:text-roxo2-hover mb-6 leading-tight text-center drop-shadow-sm">
                    {{ currentChapter.title }}
                </h2>

                <div class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg mb-8" v-html="currentChapter.content"></div>

                <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        @click="goToPrevious"
                        :disabled="!hasPrevious"
                        class="px-6 py-2 bg-laranja1 text-prata1 font-semibold rounded-lg shadow-md hover:bg-laranja1-hover disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 ease-in-out transform hover:scale-105"
                    >
                        Anterior
                    </button>

                    <span class="text-gray-600 dark:text-gray-400 text-sm">
                        Capítulo {{ currentPage + 1 }} de {{ totalChapters }}
                    </span>

                    <button
                        @click="goToNext"
                        :disabled="!hasNext"
                        class="px-6 py-2 bg-laranja1 text-prata1 font-semibold rounded-lg shadow-md hover:bg-laranja1-hover disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 ease-in-out transform hover:scale-105"
                    >
                        Próximo
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Adicione estilos específicos para esta página se necessário */
</style>
