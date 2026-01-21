<template>
  <div class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
      <h2>Serviços</h2>
    </div>
    
    <!-- Cabeçalho das colunas -->
    <div class="flex w-full text-xs text-gray-600 font-semibold pb-2 pt-2 border-b border-gray-300 bg-gray-100">
      <div class="w-[24%] ps-2 text-center">Serviço</div>
      <div class="w-[7%] text-center">Qtd</div>
      <div class="w-[9%] text-center">Horas</div>
      <div class="w-[11%] text-center">Taxa/h</div>
      <div class="w-[11%] text-center">Custo Op.</div>
      <div class="w-[9%] text-center">Margem %</div>
      <div class="w-[11%] text-center">Lucro unitário</div>
      <div class="w-[11%] text-center">Lucro total</div>
      <div class="w-[11%] text-center">Preço unitário</div>
      <div class="w-[13%] text-center">Preço final</div>
    </div>

    <div class="flex w-full py-2 border-b border-gray-100 hover:bg-gray-50 text-sm" v-for="service in proposalServices" v-bind:key="service.id">
      <router-link
        class="no-link w-[24%] flex items-center ps-2 gap-2"
        :to="{ name: 'serviceShow', params: { id: service.service_id } }"
        :key="service.service_id"
      >
        <font-awesome-icon icon="fa-solid fa-coins" class="primary text-black text-xs flex-shrink-0" />
        <div class="text-black truncate text-sm">
            {{ service.name }}
        </div>
      </router-link>
      <div class="w-[7%] flex items-center justify-center text-black text-sm">
        <integer-editable-field
          v-model="service.quantity"
          @save="emitUpdateProposal('quantity', service.service_id, $event)"
        />
      </div>
      <div class="w-[9%] flex items-center justify-center text-black text-sm">
        <hours-decimal-editable-field
          v-model="service.labor_hours"
          @save="emitUpdateProposal('labor_hours', service.service_id, $event)"
        />
      </div>
      <div class="w-[11%] flex items-center justify-end text-black pr-2 text-sm">
        <money-editable-field
          v-model="service.labor_hourly_rate"
          @save="emitUpdateProposal('labor_hourly_rate', service.service_id, $event)"
        />
      </div>
      <div class="w-[11%] flex items-center justify-end text-black pr-2 text-sm">
        <money-field name="labor_hourly_rate_total" v-model="service.labor_hourly_rate_total" />
      </div>
      <div class="w-[9%] flex items-center justify-center text-black text-sm">
        <decimal-editable-field
          v-model="service.profit_percentage"
          @save="emitUpdateProposal('profit_percentage', service.service_id, $event)"
        />
        <span class="ps-1 text-[10px]">%</span>
      </div>
      <div class="w-[11%] flex items-center justify-end text-black pr-2 text-sm">
        <money-editable-field
          v-model="service.profit"
          @save="emitUpdateProposal('profit', service.service_id, $event)"
        />
      </div>
      <div class="w-[11%] flex items-center justify-end text-black pr-2 text-sm">
        <money-editable-field
          v-model="service.total_profit"
          @save="emitUpdateProposal('total_profit', service.service_id, $event)"
        />
      </div>
      <div class="w-[11%] flex items-center justify-end text-black pr-2 text-sm">
        <money-field name="price" v-model="service.price" />
      </div>
      <div class="w-[13%] flex items-center color-primary-500 justify-end font-bold pr-2 text-sm">
        <money-field name="total_price" v-model="service.total_price" />
      </div>
    </div>
  </div>
</template>

<script>
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import IntegerEditableField from "../fields/number/IntegerEditableField.vue";
import DecimalEditableField from "../fields/number/DecimalEditableField.vue";
import HoursDecimalEditableField from "../fields/number/HoursDecimalEditableField.vue";

export default {
  props: {
    proposalServices: {
      type: Array,
      required: false,
    },
  },
  components: {
    MoneyField,
    MoneyEditableField,
    IntegerEditableField,
    DecimalEditableField,
    HoursDecimalEditableField,
  },
  data() {
    return {
      editingProfitPercentage: {},
      editingProfitPercentageValue: {},
    };
  },
  methods: {
    async emitUpdateProposal(fieldName, serviceId, editedValue) {
      this.$emit("update-proposal", fieldName, serviceId, editedValue);
    },
    startEditingProfitPercentage(serviceId) {
      this.$set(this.editingProfitPercentage, serviceId, true);
      this.$set(this.editingProfitPercentageValue, serviceId, this.proposalServices.find(service => service.service_id === serviceId).profit_percentage);
      this.$nextTick(() => {
        this.$refs.profitPercentageInput.focus();
      });
    },
    cancelEditingProfitPercentage(serviceId) {
      this.$set(this.editingProfitPercentage, serviceId, false);
    },
    saveEditingProfitPercentage(serviceId) {
      const editedValue = this.editingProfitPercentageValue[serviceId];
      this.emitUpdateProposal('profit_percentage', serviceId, editedValue);
      this.$set(this.editingProfitPercentage, serviceId, false);
    },
  },
};
</script>

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
  /* margin: 1rem; */
  flex-basis: 10%;
}

.price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  /* margin: 1rem; */
  flex-basis: 10%;
}

.total-price-column {
  display: flex;
  align-items: center;
  justify-content: right;
  /* margin: 1rem; */
  flex-basis: 15%;
  font-weight: 800;
}

.title-column {
  display: flex;
  align-items: left;
  justify-content: left;
  /* margin: 1rem; */
  flex-basis: 70%;
}
</style>