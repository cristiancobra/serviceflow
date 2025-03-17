<template>
  <div class="page-container">
    <div class="page-header">
      <div class="title-container">
        <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
        <h1 class="title">CUSTO DE PRODUÇÃO</h1>
      </div>
    </div>
    <div class="table-row">
      <div class="subtitle-container">
        <TextEditableField name="name" v-model="cost.name" placeholder="descrição detalhada do serviço"
          @save="updateService('name', $event)" class="title"/>
      </div>
    </div>
    <div class="table-row">
      <div class="column-80">
        <p>
          <font-awesome-icon icon="fa fa-clock" />
           Custo total
        </p>
      </div>
      <div class="column-20">
        <money-editable-field name="price" v-model="cost.price" @save="updateCost('price', $event)" />
      </div>
    </div>
 
    <div class="table-row pt-5 ">
      <div class="column-80">
        <p>
          <font-awesome-icon icon="fa fa-calendar-alt" />
          Data de criação:
        </p>
      </div>
      <div class="column-20">
        {{ formatDateBr(cost.created_at) }}
      </div>
    </div>
    <div class="table-row mt-5 mb-5">
      <div>
        <button class="offset-10 col-1 myButton delete" @click="deleteService()">
          excluir
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import TextEditableField from "@/components/fields/text/TextEditableField";
import MoneyEditableField from '../../components/fields/number/MoneyEditableField.vue';

export default {
  data() {
    return {
      cost: [],
      costId: "",
    };
  },
  components: {
    TextEditableField,
    MoneyEditableField,
  },
  methods: {
    destroy,
    show,
    updateField,
    async deleteService() {
      this.response = await destroy('costs', this.costId);
      this.$router.push({ name: "costIndex" });
    },
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
    async getCost() {
      this.cost = await show('costs', this.costId);
    },
    setCostId(costId) {
      this.costId = costId;
    },
    async updateCost(fieldName, editedValue) {
      console.log(fieldName, editedValue);
      this.cost = await updateField("costs", this.costId, fieldName, editedValue);
    },
  },
  async mounted() {
    this.setCostId(this.$route.params.id);
    this.getCost();
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
