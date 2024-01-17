<template>
  <div>
    <div class="container-card">
      <div class="card">
        <div class="title">
          {{ service.name }}
        </div>
      </div>
    </div>
    <div class="container">
      <p>
        <font-awesome-icon icon="fa fa-clock" />
        <span class="label"> Horas de trabalho: </span>
        {{ service.labor_hours }}
      </p>
      <p>
        <font-awesome-icon icon="fa fa-money-bill-alt" />
        <span class="label"> Custo da hora de trabalho: </span>
        {{ service.labor_hourly_rate }}
      </p>
      <p>
        <font-awesome-icon icon="fa fa-calendar-check" />
        <span class="label"> Total de horas de trabalho: </span>
        {{ service.labor_hourly_rate_total }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-percent" />
        <span class="label"> Percentual de lucro: </span>
        {{ service.profit_percentage }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-money-bill" />
        <span class="label"> Lucro: </span>
        {{ service.profit }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-dollar-sign" />
        <span class="label"> Preço: </span>
        {{ service.price }}
      </p>
    </div>

    <div class="container">
      <p>
        <font-awesome-icon icon="fa fa-calendar-alt" />
        <span class="label"> Data de criação: </span>
        {{ formatDateBr(service.created_at) }}
      </p>

    </div>
    <div class="row mt-5 mb-5">
      <div>
        <button class="offset-10 col-1 myButton delete" @click="deleteService()">
          excluir
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { BACKEND_URL, SERVICE_URL } from "@/config/apiConfig";
import axios from "axios";

export default {
  name: "ServiceShow",
  data() {
    return {
      service: [],
      serviceId: "",
    };
  },

  methods: {

    formatDateBr(date) {
      // Verifica se a data é válida
      if (!date) return "";

      const dateObj = new Date(date);
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
      const year = dateObj.getFullYear();

      // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
      const dateBr = `${day}/${month}/${year}`;

      return dateBr;
    },

    getService() {
      axios
        .get(`${BACKEND_URL}${SERVICE_URL}${this.serviceId}`)
        .then((response) => {
          this.service = response.data.data;
          console.log(this.service);
        })
        .catch((error) => console.log(error));
    },

    setServiceId(serviceId) {
      this.serviceId = serviceId;
    },
    
    async deleteService() {
      axios
        .delete(`${BACKEND_URL}${SERVICE_URL}${this.serviceId}`)
        .then((response) => {
          this.data = response.data.data;
          // this.newLeadEvent(this.data);
          const successMessage = "Serviço excluído com sucesso";
          this.$router.push({ name: "servicesIndex", query: { successMessage } });
        })
        .catch((error) => {
          console.error("Erro ao deletar serviço:", error);
          // Lidar com o erro, se necessário
        });
    },
  },
  async mounted() {
    this.setServiceId(this.$route.params.id);
    this.getService();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-align: left;
  font-weight: 400;
}
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
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
  font-weight: 800;
}
.card {
  text-align: left;
  background-color: white;
  border-style: solid;
  border-width: 3px;
  border-color: #48d1cc;
  border-radius: 20px;
  padding-top: 10px;
  padding-left: 20px;
  padding-bottom: 10px;
}
.title {
  font-size: 32px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
}
.container-card {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
}

.container {
  margin-left: 200px;
  margin-right: 180px;
  margin-bottom: 60px;
}

.myButton {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  /* margin: 0 4px 0 4px; */
  color: white;
  font-weight: 800;
  /* width: 120px; */
}
.delete {
  background-color: #ffa1a1;
  border-color: #c82333;
  color: #c82333;
}
.delete:hover {
  background-color: #c82333;
  border-color: #c82333;
  color: white;
}
</style>
