<template>
  <div class="image">
    <img :src="caixaVaziaImage" alt="logo-serviceflow" />

    <p class="bold">Você não tem nenhum serviço!</p>
    <p class="message">Cadastre um seviços para poder faturar</p>

    <button type="button" @click="openCreateServiceModal">Novo Serviço</button>
  </div>
</template>

  <script>
import { mapMutations } from "vuex";
import { BACKEND_URL, SERVICE_URL } from "@/config/apiConfig";
import axios from "axios";
import caixaVaziaImage from '@/assets/caixa_vazia-PB.png';

export default {
  name: "NoServicesMessage",
  data() {
    return {
      caixaVaziaImage,
    };
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateServiceModal() {
      this.openModal({
        component: "ServiceCreateForm",
        listeners: {
          "new-service-event": this.addServiceCreated,
        },
      });
    },
    getServices() {
      axios
      .get(`${BACKEND_URL}${SERVICE_URL}`)
        .then((response) => {
          this.services = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    addServiceCreated(newService) {
      this.$emit("new-service-event", newService);
      console.log(
        "Novo service enviado pro componente pai:",
        newService.service
      );
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