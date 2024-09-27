<template>
  <div class="container">
    <div class="list-container mt-5">
      <div class="row align-items-start pb-5">
        <div class="col-1">
          <font-awesome-icon icon="fa-solid fa-tools" class="primary big-icon" />
        </div>
        <div class="col">
          <h2 class="title">PROPOSTA</h2>
        </div>
      </div>
      <div class="row pb-5" v-if="proposal.opportunity">
        {{ proposal.opportunity.name }}
      </div>
      <div class="row">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fa fa-clock" />
            <span class="label"> Horas de trabalho: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <hours-decimal-editable-field name="total_hours" v-model="proposal.total_hours"
            placeholder="descrição detalhada do serviço" @save="updateService('total_hours', $event)" />
        </div>
      </div>
      <div class="row">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fa fa-calendar-check" />
            <span class="label"> Total de horas de trabalho: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <money-field name="total_operational_cost" v-model="proposal.total_operational_cost" />
        </div>
      </div>
      <div class="row">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fas fa-percent" />
            <span class="label"> Percentual de lucro: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <decimal-editable-field name="total_profit" v-model="proposal.total_profit"
            placeholder="percentual do lucro" @save="updateService('total_profit', $event)" />
        </div>
      </div>
      <div class="row">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fas fa-money-bill" />
            <span class="label"> Lucro: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <money-field name="total_profit" v-model="proposal.total_profit" />
        </div>
      </div>
      <div class="row">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fas fa-dollar-sign" />
            <span class="label"> Preço: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <money-field name="price" v-model="proposal.total_price" />
        </div>
      </div>
      <div class="row pt-5 ">
        <div class="col-5">
          <p>
            <font-awesome-icon icon="fa fa-calendar-alt" />
            <span class="label"> Data de criação: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          {{ formatDateBr(proposal.created_at) }}
        </div>
      </div>
      <div class="row pt-5">
        <p class="title">
          Itens da proposta
        </p>
      </div>
      <div class="row service-item pt-1 pb-1" v-for="proposalItem in proposal.proposalItems"
        v-bind:key="proposalItem.id">
        <div class="col-1 offset-2 d-flex align-items-center justify-content-center">
          <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
        </div>
        <div class="col-7">
          <p class="name ps-2">
            {{ proposalItem.name }}
          </p>
        </div>
        <div class="col">
          {{ proposalItem.quantity }}
        </div>
        <div class="col text-end">
          {{ proposalItem.total_price }}
        </div>
      </div>


      <div class="row mt-5 mb-5">
        <div>
          <button class="offset-10 col-1 myButton delete" @click="deleteProposal()">
            excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import DecimalEditableField from "@/components/fields/number/DecimalEditableField";
import MoneyField from '../../components/fields/number/MoneyField.vue';
import HoursDecimalEditableField from '../../components/fields/number/HoursDecimalEditableField.vue';

export default {
  name: "ServiceShow",
  data() {
    return {
      proposal: [],
      proposalId: "",
    };
  },
  components: {
    DecimalEditableField,
    HoursDecimalEditableField,
    MoneyField,
  },
  methods: {
    destroy,
    show,
    updateField,
    async deleteProposal() {
      this.response = await destroy('proposals', this.proposalId);
      // const successMessage = "Serviço excluído com sucesso";
      // this.$router.push({ name: "proposalsIndex", query: { successMessage } });
    },
    formatDateBr(date) {
      if (!date) return "";

      const dateObj = new Date(date);
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
      const year = dateObj.getFullYear();

      // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
      const dateBr = `${day}/${month}/${year}`;

      return dateBr;
    },
    async getProposal() {
      this.proposal = await show('proposals', this.proposalId);
    },
    setProposalId(proposalId) {
      this.proposalId = proposalId;
    },
    async updateService(fieldName, editedValue) {
      this.proposal = await updateField("proposals", this.proposalId, fieldName, editedValue);
    },
  },
  async mounted() {
    this.setProposalId(this.$route.params.id);
    this.getProposal();
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
