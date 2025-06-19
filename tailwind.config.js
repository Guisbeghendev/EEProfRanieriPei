import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Importe o plugin @tailwindcss/typography
import typography from '@tailwindcss/typography';

// Importe o plugin do Flowbite.
// Note que Flowbite é um plugin de tema e também precisa ser carregado como um plugin JS.
import flowbite from 'flowbite/plugin';


/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/flowbite/**/*.js', // Essencial para o Flowbite ser varrido pelo Tailwind
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: { // Suas cores personalizadas. Adicionei variações de hover e focus para os botões.
                // Cores Primárias da sua paleta
                roxo1: '#61152d',
                roxo2: '#681630',
                laranja1: '#c89c20',
                laranja2: '#cd862a',
                laranja3: '#c9583e',
                preto1: '#0f0e0f', // Mantido para elementos de texto ou fundos escuros
                prata1: '#d1d1d1', // Mantido para texto ou elementos claros
                branco1: '#f8f8f8', // Mantido para fundos ou texto muito claro

                // Cores Secundárias (se aplicáveis ou para acentos específicos)
                verde1: '#2e5935',
                azul1: '#1b365d',
                amarelo1: '#c5a100',
                vermelho1: '#9e1b1b',
                rosa1: '#a54161',
                dourado1: '#bfa14f',
                cinza1: '#8a8a8a',

                // Variações de Hover e Focus para as cores principais (adicionadas com base no uso anterior)
                'roxo1-hover': '#7a1939', // Um pouco mais claro/saturado que roxo1/roxo2 para hover
                'roxo2-hover': '#7a1939', // Pode ser a mesma cor de hover para ambos, ou ligeiramente diferente
                'roxo2-focus': '#9c2049', // Cor para o anel de foco (ainda mais claro)
                'laranja1-hover': '#e0b223', // Um pouco mais claro/saturado que laranja1
                'laranja2-hover': '#e6a032', // Um pouco mais claro/saturado que laranja2
                'laranja1-focus': '#f3c734', // Cor para o anel de foco (ainda mais claro)
            },
        },
    },

    plugins: [
        forms,
        typography, // Importado como 'typography' e usado aqui.
        flowbite // Flowbite é um plugin de tema, então sua importação está correta aqui.
    ],
};
