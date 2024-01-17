<template>
  <div class="container mb-5">
    <div class="card">
        
          <div class="row ms-1">
            <div class="col-1 status">
            <span class="icon big">
            <font-awesome-icon icon="fa-solid fa-briefcase" />
          </span>
              
            </div>
            <div v-if="company.business_name" class="col-11 ps-3">
              <p class="title">
                {{ company.business_name }}
              </p>
              <p class="description">
                {{ company.legal_name }}
              </p>
            </div>
            <div v-else class="col-11 ps-3">
              <p class="title">
                {{ company.legal_name }}
              </p>
            </div>
          </div>
        
      </div>

    <div class="row">
      <p class="mb-5">
        {{ company.description }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> CNPJ: </span>
        {{ company.cnpj }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> Email: </span>
        {{ company.email }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-calendar" />
        <span class="label"> Telefone celular: </span>
        {{ company.cel_phone }}
      </p>
    </div>

    <div class="row mt-5 mb-5">
      <button class="offset-10 col-1 myButton delete" @click="deleteCompany()">
        excluir
      </button>
    </div>

  </div>
</template>

<script>
import { BACKEND_URL, COMPANY_URL } from "@/config/apiConfig";
import axios from "axios";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";

export default {
  name: "CompanyShow",
  data() {
    return {
      companies: [],
      company: [],
      companyId: "",
    };
  },
  methods: {
    formatDateBr,
    formatDuration,
    getCompany() {
      axios
        .get(`${BACKEND_URL}${COMPANY_URL}${this.companyId}`)
        .then((response) => {
          this.company = response.data.data;
          this.companyLoaded = true; // Marque a tarefa como carregada
        })
        .catch((error) => console.log(error));
    },
    setTaskId(companyId) {
      this.companyId = companyId;
    },
    async deleteCompany() {
      axios
        .delete(`${BACKEND_URL}${COMPANY_URL}${this.companyId}`)
        .then((response) => {
          this.data = response.data;
          // this.newTaskEvent(this.data);
          const successMessage = "Task excluído com sucesso";
          this.$router.push({ name: "companiesIndex", query: { successMessage } });
        })
        .catch((error) => {
          console.error("Erro ao deletar company:", error);
          // Lidar com o erro, se necessário
        });
    },
  },
  async mounted() {
    this.setTaskId(this.$route.params.id);
    this.getCompany();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-align: left;
  font-size: 1.2rem;
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
  font-size: 1.2rem;
  text-align: center;
}
.card {
  margin-bottom: 60px;
  margin-top: 60px;
  border-style: solid;
  border-width: 2px;
  border-color: var(--blue);
  border-radius: 6px;
  padding: 10px;
  padding-right: 0px;
  height: 15vh;
  background-color: var(--blue-light);
}
.description {
  margin-top: -2%;
}
.title {
  font-size: 26px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
  color: black;
}

.container {
  margin-left: 10vw;
  margin-right: 10vw;
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
.icon {
  text-align: center;
  font-weight: 400;
}
.big {
  font-size: 56px;
  color: var(--blue);
}
</style>
