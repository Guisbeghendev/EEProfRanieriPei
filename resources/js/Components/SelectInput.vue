<script setup>
import { computed, ref } from 'vue'; // <<-- ADICIONE 'ref' AQUI

const props = defineProps({
    modelValue: {
        type: [String, Number, Array],
        default: '',
    },
    options: {
        type: Array,
        required: true,
    },
    defaultOptionLabel: {
        type: String,
        default: 'Todos',
    },
    defaultOptionValue: {
        type: [String, Number],
        default: '',
    },
    id: String,
    name: String,
    autocomplete: String,
    required: Boolean,
    disabled: Boolean,
    autofocus: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const proxyModel = computed({
    get() {
        return props.modelValue;
    },
    set(val) {
        emit('update:modelValue', val);
    },
});

// Referência ao input, se precisar focar ou algo assim
const input = ref(null); // Esta é a linha que causou o erro

defineExpose({focus: () => input.value?.focus()});
</script>

<template>
    <select
        :id="id"
        :name="name"
        :autocomplete="autocomplete"
        :required="required"
        :disabled="disabled"
        :autofocus="autofocus"
        v-model="proxyModel"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    >
        <option :value="defaultOptionValue">{{ defaultOptionLabel }}</option>
        <option v-for="option in options" :key="option.id" :value="option.id">
            {{ option.name }}
        </option>
    </select>
</template>
