<template>
    <div class="flex flex-col min-h-screen animated-bg">
        <div class="hidden md:block">
            <Header />
        </div>

        <div>
            <Navbar />
        </div>

        <main class="flex-grow flex justify-center px-4">
            <div class="w-full bg-white bg-opacity-90 rounded-2xl shadow-2xl m-5 p-6 max-w-6xl dark:bg-gray-800 dark:bg-opacity-90">
                <div v-if="$slots.title" class="mb-6 border-b pb-4 text-3xl font-bold text-center text-laranja2 dark:text-gray-200">
                    <slot name="title" />
                </div>

                <slot />
            </div>
        </main>

        <div>
            <Footer />
        </div>
    </div>
</template>

<script setup>
// Importa os componentes de layout que você criou
import Header from '@/Components/Layout/Header.vue';
import Navbar from '@/Components/Layout/Navbar.vue';
import Footer from '@/Components/Layout/Footer.vue';
</script>

<style scoped>
/* Estilo para o fundo animado com gradiente */
.animated-bg {
    background: linear-gradient(
        270deg,
        #c89c20, /* laranja 1 */
        #cd862a, /* laranja 2 */
        #c9583e, /* laranja 3 */
        #61152d, /* roxo 1 */
        #681630  /* roxo 2 */
    );
    background-size: 1000% 1000%;
    animation: bgGradientMove 30s ease infinite;
}

/* Definição da animação do gradiente */
@keyframes bgGradientMove {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}
</style>
