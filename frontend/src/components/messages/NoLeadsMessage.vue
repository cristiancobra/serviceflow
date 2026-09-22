<template>
  <div class="image">
    <img :src="caixaVaziaImage" alt="logo-serviceflow" />

    <p class="bold">Você não tem nenhum contato!</p>
    <p class="message">Cadastre seu primeiro</p>

    <button type="button" @click="openCreateLeadModal">Novo Contato</button>
  </div>
</template>

  <script>
  import { BACKEND_URL, LEAD_URL } from "@/config/apiConfig";
import { mapMutations } from "vuex";
import axios from "axios";
import caixaVaziaImage from '@/assets/caixa_vazia-PB.png';

export default {
  name: "NoLeadsMessage",
  data() {
    return {
      caixaVaziaImage,
    };
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateLeadModal() {
      this.openModal({
        component: "LeadCreateForm",
        listeners: {
          "new-lead-event": this.addLeadCreated,
        },
      });
    },
    getLeads() {
      axios
      .get(`${BACKEND_URL}${LEAD_URL}`)
        .then((response) => {
          this.leads = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    addLeadCreated(newLead) {
        this.$emit("new-lead-event", newLead);
        console.log("Novo lead enviado pro componente pai:", newLead.lead);
    },
  },
};
</script>
  
  <style>
.image {
  margin-top: 110px;
}
.bold {
  font-size: 26px;
  font-weight: 800;
  margin-top: 40px;
}
.message {
  font-size: 22px;
  font-weight: 400;
  margin-top: 20px;
}
</style>