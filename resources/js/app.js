// Importa o CSS da aplicação
import '../css/app.css';

// Importa o arquivo de bootstrap que configura o Axios e o token CSRF
import './bootstrap';

// Importa a biblioteca Flowbite para componentes UI
// O Flowbite geralmente é inicializado via data-attributes ou JS.
// A importação 'flowbite' aqui pode ser o caminho para o arquivo JS principal do Flowbite.
// Certifique-se de que o caminho 'flowbite' esteja correto de acordo com sua instalação via npm.
// Se você instalou o pacote 'flowbite' e está usando a versão modular, esta linha está ok.
// Caso contrário, pode precisar de um caminho mais específico, tipo 'flowbite/dist/flowbite.js'.
import 'flowbite';

// Importa as funções necessárias do Inertia.js para criar a aplicação Vue
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';

// Define o nome da aplicação, usando a variável de ambiente ou um padrão
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Cria a aplicação Inertia.js
createInertiaApp({
    // Define o título da página baseado no nome da aplicação
    title: (title) => `${title} - ${appName}`,

    // Resolve os componentes de página Vue dinamicamente
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),

    // Configura e monta a aplicação Vue
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            // REMOVIDO: .use(ZiggyVue, Ziggy) - O Ziggy não será usado.
            .mount(el);
    },

    // Configura a barra de progresso do Inertia
    progress: {
        color: '#4B5563',
    },

    // Tratamento de erros globais para requisições Inertia.js
    onError: (error) => {
        // Se o erro for 419 (Page Expired) ou 401 (Unauthorized),
        // recarrega a página atual para obter um novo token CSRF e recomeçar a sessão.
        if (error.response && (error.response.status === 419 || error.response.status === 401)) {
            console.error('Erro de sessão/CSRF (419/401 detectado). Recarregando a página para renovar a sessão...');
            window.location.reload(); // Força o recarregamento da página
            return false; // Previne que o Inertia continue processando este erro
        }
        // Para outros erros não relacionados a sessão/CSRF, apenas loga.
        console.error('Erro Inertia inesperado:', error);
    },
});
