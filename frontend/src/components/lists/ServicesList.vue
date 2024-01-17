<template>
  <div id="line-container" class="row">
    <div
      class="col m-3 card"
      v-for="service in services"
      v-bind:key="service.id"
    >
      <router-link
        :to="{
          name: 'serviceShow',
          params: { id: service.id },
        }"
      >
        <div id="name" class="row">
          <span class="icon big">
            <font-awesome-icon icon="fa-solid fa-tools" />
          </span>
        </div>

        <div class="row">
          <div class="col">
            <p class="name">
              {{ service.name }}
            </p>
          </div>
          <div class="col">
            <p class="price">R$ {{ service.price }}</p>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <p class="label">Tempo de trabalho</p>
          </div>
          <div class="col">
            <p class="money">
              {{ formatLaborHours(service.labor_hours) }}
            </p>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <p class="label">Custo da hora</p>
          </div>
          <div class="col">
            <p class="money">R$ {{ service.labor_hourly_rate }}</p>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <p class="label">Custo total de trabalho</p>
          </div>
          <div class="col">
            <p class="money">R$ {{ service.labor_hourly_rate_total }}</p>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <p class="label">Percentual de lucro</p>
          </div>
          <div class="col">
            <p class="money">{{ service.profit_percentage }} %</p>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <p class="label">Preço</p>
          </div>
          <div class="col">
            <p class="money">R$ {{ service.price }}</p>
          </div>
        </div>
      </router-link>

      <router-view />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, SERVICE_URL } from "@/config/apiConfig";
import axios from "axios";
// import CopyContentClipboard from "../CopyContentClipboard.vue";

export default {
  name: "ServicesList",
  components: {
    // CopyContentClipboard,
  },
  props: ["services"],
  data() {
    return {
      updatedservice: {
        id: null,
        name: null,
        labor_hours: null,
        labor_hourly_rate: null,
        labor_hourly_rate_total: null,
        profit_percentage: null,
        price: null,
      },
    };
  },
  methods: {
    formatLaborHours(seconds) {
      const hours = Math.floor(seconds / 3600);
      const minutes = Math.floor((seconds % 3600) / 60);
      return `${hours}:${String(minutes).padStart(2, "0")}`;
    },
    saveservice(service, field) {
      if (service.activeField === field) {
        service.editing = false;
        service.editingField = null;

        this.updatedservice.id = service.id;
        this.updatedservice.name = service.name;
        this.updatedservice.labor_hours = service.labor_hours;
        this.updatedservice.labor_hourly_rate = service.labor_hourly_rate;
        this.updatedservice.labor_hourly_rate_total =
          service.labor_hourly_rate_total;
        this.updatedservice.profit_percentage = service.profit_percentage;
        this.updatedservice.price = service.price;

        axios
          .put(
            `${BACKEND_URL}${SERVICE_URL}${service.id}`,
            this.updatedservice
          )
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
};
</script>

<style scoped>
.name {
  text-align: left;
  font-size: 20px;
  font-weight: 600;
}
.big {
  font-size: 44px;
  color: var(--green);
}
.card {
  border-style: solid;
  border-width: 2px;
  border-color: var(--green);
  border-radius: 6px;
  padding: 10px;
  background-color: var(--green-light);
  /* height: 15vh; */
}
.money {
  text-align: right;
  font-size: 16px;
  font-weight: 400;
}
.price {
  text-align: right;
  margin-top: 0px;
  margin-bottom: -0px;
  font-size: 1.2rem;
  font-weight: 800;
}
.icon {
  text-align: center;
  font-weight: 400;
}
.icon:hover {
}
.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center; /* Centraliza verticalmente */
  justify-content: center; /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 10px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center; /* Inicia a transformação a partir do centro */
}
.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}
a {
  color: rgb(61, 61, 61);
}
a:link {
  text-decoration: none;
}
a:visited {
  text-decoration: none;
}
a:hover {
  text-decoration: none;
}
a:active {
  text-decoration: none;
}
.label {
  text-align: left;
}
</style>
