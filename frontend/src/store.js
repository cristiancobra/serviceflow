// store.js
import { createStore } from 'vuex';
import axios from 'axios';
import { API_SANCTUM_URL, LOGIN_URL, LOGOUT_URL, BACKEND_URL, CHECK_TOKEN_URL, JOURNEY_CHECK_OPEN } from "@/config/apiConfig";
import router from '@/router';

export default createStore({
  state: {
    openJourney: false,
    isAuthenticated: false,
  },
  mutations: {
    setOpenJourney(state, openJourney) {
      state.openJourney = openJourney;
    },
    setAuthenticated(state, isAuthenticated) {
      state.isAuthenticated = isAuthenticated;
    },
  },
  actions: {
    async checkOpenJourneys({ commit }) {
      try {
        const response = await axios.get(`${BACKEND_URL}${JOURNEY_CHECK_OPEN}`);
        commit('setOpenJourney', response.data.openJourney);
        console.log('Jornadas abertas:', response.data.openJourney);
      } catch (error) {
        console.error('Erro ao verificar jornadas abertas:', error);
      }
    },
    async checkTokenValidity({ commit }) {
      try {
        const response = await axios.get(`${BACKEND_URL}${CHECK_TOKEN_URL}`);
        commit('setAuthenticated', response.status === 200);
      } catch (error) {
        commit('setAuthenticated', false);
        localStorage.removeItem('access_token');
        console.error('Erro ao verificar validade do token:', error);
      }
    },
    async login({ commit }, credentials) {
      try {
        axios.defaults.withCredentials = true;
        await axios.get(API_SANCTUM_URL);
        await axios.post(`${BACKEND_URL}${LOGIN_URL}`,credentials);
        commit('setAuthenticated', true);
        await this.dispatch('checkOpenJourneys');
        setTimeout(() => {
          router.push({ name: 'home' });
        }, 600);
      } catch (error) {
        console.error('Erro ao realizar login:', error);
        commit('setAuthenticated', false);
        router.push({ name: 'login' });
      }
    },
    async logout({ commit }) {
      try {
      axios.defaults.withCredentials = true;
      await axios.post(`${BACKEND_URL}${LOGOUT_URL}`);
      commit('setAuthenticated', false);
      router.push({ name: 'login' });
    } catch (error) {
      console.error("Erro de logout:", error);
    }
    },
  },
});