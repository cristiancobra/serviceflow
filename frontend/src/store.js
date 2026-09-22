// store.js
import { createStore } from 'vuex';
import axios from 'axios';
import { API_SANCTUM_URL, LOGIN_URL, LOGOUT_URL, BACKEND_URL, CHECK_TOKEN_URL, JOURNEY_CHECK_OPEN } from "@/config/apiConfig";
import router from '@/router';

// Único setInterval, ligado/desligado conforme openJourney muda (ver plugin journeyTickerPlugin).
let journeyTickInterval = null;

function journeyTickerPlugin(store) {
  store.watch(
    (state) => !!state.openJourney,
    (hasOpenJourney) => {
      if (hasOpenJourney && !journeyTickInterval) {
        store.commit('setNow', Date.now());
        journeyTickInterval = setInterval(() => {
          store.commit('setNow', Date.now());
        }, 1000);
      } else if (!hasOpenJourney && journeyTickInterval) {
        clearInterval(journeyTickInterval);
        journeyTickInterval = null;
      }
    },
    { immediate: true }
  );
}

export default createStore({
  plugins: [journeyTickerPlugin],
  state: {
    openJourney: false,
    now: Date.now(),
    isAuthenticated: false,
    userData: null,
    themePreference: 'auto',
    photo: null,
    messageStatus: null,
    messageText: null,
    updatedTask: null,
    openModals: [],
  },
  mutations: {
    clearMessage(state) {
      state.messageStatus = null;
      state.messageText = null;
    },
    setAccountId(state, accountId) {
      state.accountId = accountId;
    },
    setAuthenticated(state, isAuthenticated) {
      state.isAuthenticated = isAuthenticated;
    },
    setMessage(state, { status, text }) {
      state.messageStatus = status;
      state.messageText = text;
    },
    setOpenJourney(state, openJourney) {
      state.openJourney = openJourney;
    },
    setNow(state, now) {
      state.now = now;
    },
    setUserData(state, userData) {
      state.userData = userData;
    },
    setThemePreference(state, themePreference) {
      state.themePreference = themePreference;
    },
    setUpdatedTask(state, task) {
      state.updatedTask = task;
    },
    openModal(state, { component, props = {}, listeners = {}, id } = {}) {
      const modalId = id || component;
      state.openModals = state.openModals.filter(modal => modal.id !== modalId);
      state.openModals.push({ id: modalId, component, props, listeners });
    },
    closeModal(state, id) {
      state.openModals = state.openModals.filter(modal => modal.id !== id);
    },
  },
  actions: {
    async checkAuthentication({ commit }) {
      try {
        const response = await axios.get(`${BACKEND_URL}${CHECK_TOKEN_URL}`);
        commit('setAuthenticated', response.status === 200);
        if (response.data?.theme_preference) {
          commit('setThemePreference', response.data.theme_preference);
        }
      } catch (error) {
        commit('setAuthenticated', false);
        console.error('Erro ao verificar autenticação:', error);
        router.push({ name: 'login' });
      }
    },
    async checkOpenJourneys({ commit }) {
      try {
        const response = await axios.get(`${BACKEND_URL}${JOURNEY_CHECK_OPEN}`);
        commit('setOpenJourney', response.data.openJourney);
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
        console.error('Erro ao verificar validade do token:', error);
      }
    },
    async login({ commit }, credentials) {
      try {
        // axios.defaults.withCredentials = true;
        await axios.get(API_SANCTUM_URL);
        const response = await axios.post(`${BACKEND_URL}${LOGIN_URL}`,credentials);
        commit('setAuthenticated', true);
        commit('setUserData', response.data.user);
        commit('setAccountId', response.data.user.account_id); 
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
      commit('setUserData', null); 
      router.push({ name: 'login' });
    } catch (error) {
      console.error("Erro de logout:", error);
    }
    },
    setMessage({ commit }, message) {
      commit('setMessage', message);
    },
    clearMessage({ commit }) {
      commit('clearMessage');
    },
  },
  getters: {
    messageStatus: state => state.messageStatus,
    messageText: state => state.messageText,
    openJourneyElapsedSeconds: (state) => {
      if (!state.openJourney || !state.openJourney.start) return 0;
      const startMs = new Date(state.openJourney.start).getTime();
      if (Number.isNaN(startMs)) return 0;
      return Math.max(0, Math.floor((state.now - startMs) / 1000));
    },
  },
});