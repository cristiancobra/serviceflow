import axios from 'axios';
import router from '@/router';
import store from '@/store';

// Adicione um interceptor de resposta global
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
