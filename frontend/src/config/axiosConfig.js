import axios from 'axios';
import router from '@/router'; // Certifique-se de importar seu objeto de roteamento

// Adicione um interceptor de resposta global
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('access_token');
      router.push({ name: 'login' });
    }
    return Promise.reject(error);
  }
);

export default axios;
