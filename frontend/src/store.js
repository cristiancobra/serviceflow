// store.js
import { createStore } from 'vuex';
import axios from 'axios';
import { BACKEND_URL, JOURNEY_CHECK_OPEN } from "@/config/apiConfig";

export default createStore({
  state: {
    openJourney: false,
  },
  mutations: {
    setOpenJourney(state, openJourney) {
      state.openJourney = openJourney;
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
  },
});
