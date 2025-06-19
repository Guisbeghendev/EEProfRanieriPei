// Importa a biblioteca Axios para fazer requisições HTTP
import axios from 'axios';

// Atribui o Axios ao objeto global window, tornando-o acessível em toda a aplicação
window.axios = axios;

// Define um cabeçalho padrão para todas as requisições Axios, indicando que são requisições AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Habilita o envio de credenciais (como cookies de sessão) em requisições cross-site.
// ISSO É CRÍTICO PARA O CSRF E SESSÕES NO LARAVEL!
window.axios.defaults.withCredentials = true;

// REMOVIDO: A lógica manual para ler a meta tag e configurar o X-CSRF-TOKEN.
// O Axios, por padrão, já lê o cookie 'XSRF-TOKEN' e o envia automaticamente
// como o cabeçalho 'X-XSRF-TOKEN'. Laravel é capaz de validar ambos 'X-CSRF-TOKEN'
// e 'X-XSRF-TOKEN'. A configuração manual de 'X-CSRF-TOKEN' estava causando
// a inconsistência e o erro 419. Ao remover esta seção, confiamos no
// mecanismo automático do Axios para enviar o token de forma consistente.
