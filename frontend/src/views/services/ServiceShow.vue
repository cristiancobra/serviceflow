<template>
  <div class="page-container">
    <div class="page-header">
      <div class="title-container">
        <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
        <h1>SERVIÇO</h1>
      </div>
    </div>

    <div class="section-container">
      <div class="table-row">
        <div class="subtitle-container">
          <TextEditableField name="name" class="title" v-model="service.name"
            placeholder="descrição detalhada do serviço" @save="updateService('name', $event)" />
        </div>
      </div>
    </div>

    <div class="table-row">
      <div class="icon-column">
        <font-awesome-icon icon="fa fa-clock" />
      </div>
      <div class="title-column-3">
        Custo operacional
      </div>
      <div class="integer-column">
        <hours-decimal-editable-field name="labor_hours" v-model="service.labor_hours"
          placeholder="quantidade total de horas" @save="updateService('labor_hours', $event)" />
        h
      </div>
      <div class="price-column">
        <money-editable-field name="labor_hourly_rate" v-model="service.labor_hourly_rate"
          placeholder="valor da hora de trabalho" @save="updateService('labor_hourly_rate', $event)" />
      </div>
      <div class="total-price-column">
        <money-field name="labor_hourly_total" v-model="service.labor_hourly_total" />
      </div>
    </div>
    <div class="table-row">
      <div class="icon-column">
        <font-awesome-icon icon="fas fa-percent" />
      </div>
      <div class="title-column-2">
        Percentual de lucro:
      </div>
      <div class="price-column">
        <decimal-editable-field name="profit_percentage" v-model="service.profit_percentage"
          placeholder="percentual do lucro" @save="updateService('profit_percentage', $event)" />
        %
      </div>
      <div class="total-price-column">
        <money-editable-field name="profit" v-model="service.profit" @update="updateService('profit', $event)" />
      </div>
    </div>
    <div class="table-row">
      <div class="icon-column">
        <font-awesome-icon icon="fas fa-dollar-sign" />
      </div>
      <div class="title-column">
        Preço:
      </div>
      <div class="total-price-column">
        <money-field name="price" v-model="service.price" />
      </div>
    </div>
    <div class="table-row">
      <div class="icon-column">
        <font-awesome-icon icon="fa fa-calendar-alt" />
      </div>
      <div class="title-column">
        Data de criação:
      </div>
      <div class="price-column">
        {{ formatDateBr(service.created_at) }}
      </div>
    </div>

    <service-costs-section :service="service" @update-total-third-party-cost="updateTotalThirdPartyCost" />

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
import DecimalEditableField from "@/components/fields/number/DecimalEditableField";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField";
import TextEditableField from "@/components/fields/text/TextEditableField";
import MoneyField from '../../components/fields/number/MoneyField.vue';
import HoursDecimalEditableField from '../../components/fields/number/HoursDecimalEditableField.vue';
import ServiceCostsSection from '../../components/show/ServiceCostsSection.vue';

export default {
  data() {
    return {
      service: [],
      serviceId: "",
    };
  },
  components: {
    DecimalEditableField,
    HoursDecimalEditableField,
    TextEditableField,
    MoneyField,
    MoneyEditableField,
    ServiceCostsSection,
  },
  methods: {
    destroy,
    show,
    updateField,
    async deleteService() {
      this.response = await destroy('services', this.serviceId);
      this.$router.push({ name: "serviceIndex" });
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
    async getService() {
      this.service = await show('services', this.serviceId);
    },
    setServiceId(serviceId) {
      this.serviceId = serviceId;
    },
    async updateService(fieldName, editedValue) {
      this.service = await updateField("services", this.serviceId, fieldName, editedValue);
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
.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 1rem;
  flex-basis: 0%;
}

.integer-column {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-basis: 5%;
}

.price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  flex-basis: 10%;
}

.total-price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  flex-basis: 15%;
  font-weight: 800;
}

.title-column {
  display: flex;
  align-items: left;
  justify-content: left;
  flex-basis: 90%;
}

.title-column-2 {
  display: flex;
  align-items: left;
  justify-content: left;
  flex-basis: 80%;
}

.title-column-3 {
  display: flex;
  align-items: left;
  justify-content: left;
  flex-basis: 75%;
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
