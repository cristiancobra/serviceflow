<template>
  <div class="container mb-5">
    <card-header
      :name="company.business_name"
      :status="company.status"
      :secondLine="company.legal_name"
      fa-icon="fa-solid fa-briefcase"
      @save="updateCompany('business_name', $event)"
    />

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
import { BACKEND_URL, COMPANY_URL_PARAMENTER } from "@/config/apiConfig";
import axios from "axios";
import { formatDateBr } from "@/utils/date/dateUtils";
import { formatDuration } from "@/utils/date/dateUtils";
import CardHeader from "../../components/layout/CardHeader.vue";

export default {
  components: { CardHeader },
  name: "CompanyShow",
  data() {
    return {
      companies: [],
      company: {},
      companyId: "",
    };
  },
  methods: {
    formatDateBr,
    formatDuration,
    getCompany() {
      axios
        .get(`${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`)
        .then((response) => {
          this.company = response.data.data;
          this.companyLoaded = true; // Marque a tarefa como carregada
        })
        .catch((error) => console.log(error));
    },
    setcompanyId(companyId) {
      this.companyId = companyId;
    },
    async deleteCompany() {
      axios
        .delete(`${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`)
        .then((response) => {
          this.data = response.data;
          // this.newcompanyEvent(this.data);
          const successMessage = "company excluído com sucesso";
          this.$router.push({
            name: "companiesIndex",
            query: { successMessage },
          });
        })
        .catch((error) => {
          console.error("Erro ao deletar company:", error);
          // Lidar com o erro, se necessário
        });
    },
    async updateCompany(fieldName, editedValue) {
      try {
console.log("editedValue", editedValue);
        const updatedField = { [fieldName]: editedValue };

        const response = await axios.put(
          `${BACKEND_URL}${COMPANY_URL_PARAMENTER}${this.companyId}`,
          updatedField
        );

        this.company = response.data.data;
        console.log("Company", this.company);
        console.log("response", response);
      } catch (error) {
        console.error("Erro ao atualizar a tarefa:", error);
      }
    },
  },

  mounted() {
    this.setcompanyId(this.$route.params.id);
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
