<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: String, // O valor do v-model
    id: String,         // O ID do textarea
    rows: {             // Número de linhas padrão
        type: [String, Number],
        default: 4,
    },
});

const emit = defineEmits(['update:modelValue']);

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

// Função para emitir o evento de atualização do v-model
const onInput = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <textarea
        :id="id"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue"
        @input="onInput"
        :rows="rows"
        ref="textarea"
    ></textarea>
</template>
