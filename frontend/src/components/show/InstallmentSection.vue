<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fas fa-dollar" class="icon" />
        <h2>
          Faturamento
        </h2>
      </div>
      <div class="action-container">
        <invoice-create-form @new-invoice-event="addInvoiceCreated" :proposal="proposal" />
      </div>
    </div>

    <div class="table-row" v-for="invoice in proposal.invoices" :key="invoice.id">
      <router-link :to="{ name: 'invoiceShow', params: { id: invoice.id } }" class="router-row">
        <div class="icon-column">
          <font-awesome-icon icon="fa fa-calendar" />
        </div>
        <div class="date-column">
          {{ invoice.date_due }}
        </div>
        <div class="price-column">
          <money-field name="price" v-model="invoice.price" />
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import InvoiceCreateForm from "@/components/forms/InvoiceCreateForm.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      localInvoices: this.proposal.invoices,
    };
  },
  components: {
    InvoiceCreateForm,
    MoneyField,
  },
};
</script>

<style scoped>
.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  margin: 1rem;
  flex-basis: 0%;
}

.date-column {
    display: flex;
    align-items: center;
    justify-content: left;
    flex-basis: 80%;
}

.price-column {
    display: flex;
    align-items: center;
    justify-content: right;
    flex-basis: 20%;
}

.router-row {
  display: flex;
  align-items: left;
  justify-content: left;
  width: 100%;
  text-decoration: none;
  color: black;
}
</style>