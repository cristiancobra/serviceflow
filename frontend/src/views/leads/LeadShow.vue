<template>
  <div class="">
    <div class="container-card">
      <div class="card">
        <div class="title">
          {{ lead.name }}
        </div>
        <p>
          {{ lead.comments }}
        </p>
      </div>
    </div>

    <div class="container">
      <p>
        <font-awesome-icon icon="fas fa-envelope" />
        <span class="label"> Email: </span>
        {{ lead.email }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-mobile-alt" />
        <span class="label"> Celular: </span>
        {{ lead.cel_phone }}
      </p>
      <p>
        <font-awesome-icon icon="fab fa-linkedin" />
        <span class="label"> LinkedIn: </span>
        {{ lead.linkedin }}
      </p>
      <p>
        <font-awesome-icon icon="fab fa-facebook" />
        <span class="label"> Facebook: </span>
        {{ lead.facebook }}
      </p>
      <p>
        <font-awesome-icon icon="fab fa-instagram" />
        <span class="label"> Instagram: </span>
        {{ lead.instagram }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-external-link-alt" />
        <span class="label"> Outras redes sociais: </span>
        {{ lead.other_social_media }}
      </p>
    </div>

    <div class="container">
      <p>
        <font-awesome-icon icon="fas fa-calendar-alt" />
        <span class="label"> Data do primeiro contato: </span>
        {{ formatDateBr(lead.contact_date) }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-link" />
        <span class="label"> Origem: </span>
        {{ lead.source }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-comments" />
        <span class="label"> Canal do primeiro contato: </span>
        {{ lead.source_contact_channel }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Razão do primeiro contato: </span>
        {{ lead.reason_for_initial_contact }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Endereço: </span>
        {{ lead.address }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Complemento: </span>
        {{ lead.address_complement }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Bairro: </span>
        {{ lead.neighborhood }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Cidade: </span>
        {{ lead.city }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> Estado: </span>
        {{ lead.state }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> País: </span>
        {{ lead.country }}
      </p>
      <p>
        <font-awesome-icon icon="fas fa-info-circle" />
        <span class="label"> CEP: </span>
        {{ lead.zip_code }}
      </p>

    </div>
    <div class="row mt-5 mb-5">
      <div>
        <button class="offset-10 col-1 myButton delete" @click="deleteLead()">
          excluir
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, LEAD_URL } from "@/config/apiConfig";
import axios from "axios";
import { formatDateBr } from '@/utils/date/dateUtils'; 

export default {
  name: "LeadShow",
  data() {
    return {
      lead: [],
      leadId: "",
    };
  },

  methods: {
    formatDateBr,
    getLead() {
      axios
        .get(`${BACKEND_URL}${LEAD_URL}${this.leadId}`)
        .then((response) => {
          this.lead = response.data.data;
          console.log(this.lead);
        })
        .catch((error) => console.log(error));
    },
    setLeadId(leadId) {
      this.leadId = leadId;
    },
    async deleteLead() {
      axios
        .delete(`${BACKEND_URL}${LEAD_URL}${this.leadId}`)
        .then((response) => {
          this.data = response.data;
          // this.newLeadEvent(this.data);
          const successMessage = "Lead excluído com sucesso";
          this.$router.push({ name: "leadsIndex", query: { successMessage } });
        })
        .catch((error) => {
          console.error("Erro ao deletar lead:", error);
          // Lidar com o erro, se necessário
        });
    },
  },
  async mounted() {
    this.setLeadId(this.$route.params.id);
    this.getLead();
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
