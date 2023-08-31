<template>
  <div class="image">
    <img :src="require('@/assets/caixa_vazia-PB.png')" alt="logo-serviceflow" />

    <p class="bold">Você não tem nenhum serviço!</p>
    <p class="message">Cadastre um seviços para poder faturar</p>

    <div v-bind:class="{ hidden: isActive }">
      <ServiceCreateForm @new-service-event="addServiceCreated($event)" />
    </div>
  </div>
</template>
  
  <script>
import axios from "axios";
import ServiceCreateForm from "@/components/forms/ServiceCreateForm.vue";

export default {
  name: "NoServicesMessage",
  components: {
    ServiceCreateForm,
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
    getServices() {
      axios
        .get("http://localhost:8191/api/services")
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