<template>
  <div class="page-container">
    <div class="row mt-3 mb-4">
      <div class="col-2">
          Propostas abertas:
      </div>
      <div class="col-1 d-flex justify-content-end">
        {{ reports.acceptedProposalsCount }}
      </div>
    </div>
    <div class="row mt-3 mb-4">
      <div class="col-2">
          Total em propostas:
      </div>
      <div class="col-1">
        <money-field name="total_price" v-model="reports.total" />
      </div>
    </div>
  </div>
</template>

<script>
import { getTotalProposals } from '../../utils/requests/httpUtils';
import MoneyField from '../../components/fields/number/MoneyField.vue';

export default {
  data() {
    return {
      reports: "",
    };
  },
  components: {
    MoneyField,
  },
  methods: {
    async fetchTotalProposals() {
      try {
        this.reports = await getTotalProposals();
      } catch (error) {
        console.error('Erro ao buscar o total de propostas:', error);
      }
    },
  },
  created() {
    this.fetchTotalProposals();
  },
};
</script>

<style scoped>

</style>