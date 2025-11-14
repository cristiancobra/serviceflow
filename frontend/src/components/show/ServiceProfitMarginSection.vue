<template>
  <section class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-dollar" class="icon" />
      <h2>Custos e margem de lucro</h2>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fa fa-clock" />
      </div>
      <div class="column-65 column-name">Custo operacional</div>
      <div class="column-10 column-integer">
        <div
          style="display: flex; align-items: center; justify-content: center"
        >
          <hours-decimal-editable-field
            name="labor_hours"
            v-model="localService.labor_hours"
            placeholder="quantidade total de horas"
            @save="updateService('labor_hours', $event)"
          />
          h
        </div>
      </div>
      <div class="column-20 column-price">
        <money-editable-field
          name="labor_hourly_rate"
          v-model="localService.labor_hourly_rate"
          placeholder="valor da hora de trabalho"
          @save="updateService('labor_hourly_rate', $event)"
        />
      </div>
      <div class="column-20 column-total-price">
        <money-field
          name="labor_hourly_total"
          v-model="localService.labor_hourly_total"
        />
      </div>
    </div>


    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fa fa-coins" />
      </div>
      <div class="column-75 column-name">Custos de produção</div>
      <div class="column-20 column-total-price">
        <money-field
          name="production_costs"
          v-model="localService.production_costs"
        />
      </div>
    </div>


    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-percent" />
      </div>
      <div class="column-75 column-name">Margem de lucro:</div>
      <div class="column-20 column-price">
        <div
          style="display: flex; align-items: center; justify-content: center"
        >
          <decimal-editable-field
            name="profit_percentage"
            v-model="localService.profit_percentage"
            placeholder="percentual do lucro"
            @save="updateService('profit_percentage', $event)"
          />
          %
        </div>
      </div>
      <div class="column-20 column-total-price">
        <money-editable-field
          name="profit"
          v-model="localService.profit"
          @save="updateService('profit', $event)"
        />
      </div>
    </div>
    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-dollar-sign" />
      </div>
      <div class="column-75 column-name">Preço base:</div>
      <div class="column-20 column-total-price">
        <money-field name="price" v-model="localService.price" />
      </div>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-dollar-sign" />
      </div>
      <div class="column-75 column-name">Preço com custos:</div>
      <div class="column-20 column-total-price">
        <money-field name="final_price" v-model="localService.final_price" />
      </div>
    </div>
    
  </section>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";
import DecimalEditableField from "@/components/fields/number/DecimalEditableField";
// import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import HoursDecimalEditableField from "../../components/fields/number/HoursDecimalEditableField.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField";

export default {
  props: {
    service: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isHighlighted: false,
      localService: { ...this.service },
    };
  },
  components: {
    DecimalEditableField,
    // IntegerEditableField,
    HoursDecimalEditableField,
    MoneyField,
    MoneyEditableField,
  },
  methods: {
    formatDateBr,
    highlight() {
      this.isHighlighted = true;
      setTimeout(() => {
        this.isHighlighted = false;
      }, 2000);
    },
    updateService(field, value) {
      this.$emit("update-service", field, value);
    },
  },
  watch: {
    service: {
      handler(newService) {
        this.localService = { ...newService };
      },
      deep: true,
    },
    "localService.total_third_party_cost"(newVal) {
      this.localTotalThirdPartyCost = newVal;
      this.highlight();
    },
  },
};
</script>