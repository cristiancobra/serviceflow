import axios from 'axios';
import router from '@/router';
import store from '@/store';

// Configurar a URL base do backend
axios.defaults.baseURL = process.env.VUE_APP_BACKEND_URL;

// Permitir o envio de cookies com as requisições
axios.defaults.withCredentials = true;

// Adicionar um interceptor de requisição para enviar o cabeçalho X-XSRF-TOKEN
axios.interceptors.request.use((config) => {
  const csrfToken = document.cookie
    .split('; ')
    .find((row) => row.startsWith('XSRF-TOKEN='))
    ?.split('=')[1];

  if (csrfToken) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
  }

  return config;
});

// Adicionar um interceptor de resposta global
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      store.commit('setAuthenticated', false); 
      router.push({ name: 'login' });
    }
    return Promise.reject(error);
  }
);

export default axios;