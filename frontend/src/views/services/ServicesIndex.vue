<template>
  <div>
    <div class="row justify-content-end pt-5 pe-5">
      <div class="slot openForm" @click="toggle()">+</div>
    </div>

    <div v-bind:class="{ hidden: isActive }">
      <ServiceCreateForm @new-service-event="addServiceCreated($event)" />
    </div>

    <template v-if="services.length > 0">
      <div class="row services-container">
        <ServicesList :services="services" />
      </div>
    </template>
    <template v-else>
      <NoServicesMessage @new-service-event="addServiceCreated($event)" />
    </template>
  </div>
</template>

<script>
import axios from "axios";
// import servicesFilter from "@/components/filters/servicesFilter.vue";
import ServicesList from "@/components/ServicesList.vue";
import NoServicesMessage from '@/components/messages/NoServicesMessage.vue';
import ServiceCreateForm from "@/components/forms/ServiceCreateForm.vue";

export default {
  name: "servicesIndex",
  components: {
    // servicesFilter,
    ServiceCreateForm,
    ServicesList,
    NoServicesMessage,
  },
  data() {
    return {
      isActive: true,
      hasError: false,
      data: null,
      services: [],
      newService: {},
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
      this.services.push(newService.service);
      console.log("Novo service adicionado:", newService.service);
      !this.toggle();
    },
  },
  mounted() {
    this.getServices();
  },
  computed: {
    servicesData() {
      return this.services || [];
    },
  },
};
</script>

<style scoped>
.filters-container {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 25%;
  margin-right: 25%;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.done {
  background-color: white;
  border-color: #2cb48d;
  color: #2cb48d;
}
.done:hover {
  background-color: #2cb48d;
  color: white;
}
.doing {
  background-color: white;
  border-color: #e78d1f;
  color: #e78d1f;
}
.doing:hover {
  background-color: #e78d1f;
  color: white;
}
.late {
  background-color: white;
  border-color: #b1388d;
  color: #b1388d;
}
.late:hover {
  background-color: #b1388d;
  color: white;
}
.openForm {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
}
.services-container {
  margin-left: 10%;
  margin-right: 10%;
  margin-bottom: 10%;
}
.hidden {
  display: none;
  transition: display 2s;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
