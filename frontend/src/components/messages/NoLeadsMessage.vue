<template>
  <div class="image">
    <img :src="require('@/assets/caixa_vazia-PB.png')" alt="logo-serviceflow" />

    <p class="bold">Você não tem nenhum contato!</p>
    <p class="message">Cadastre seu primeiro</p>

    <div v-bind:class="{ hidden: isActive }">
      <LeadCreateForm @new-lead-event="addLeadCreated($event)" />
    </div>
  </div>
</template>
  
  <script>
  import { BACKEND_URL, LEAD_URL } from "@/config/apiConfig";
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import axios from "axios";

export default {
  name: "NoLeadsMessage",
  components: {
    LeadCreateForm,
  },
  data() {
    return {
      isActive: true,
    };
  },
  methods: {
    toggle() {
      this.isActive = !this.isActive;
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