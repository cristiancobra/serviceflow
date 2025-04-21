<template>
  <section class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-dollar" class="icon" />
      <h2>Custos e margem de lucro</h2>
    </div>
    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fa fa-clock" class="list-icon" />
      </div>
      <div class="column-65 column-name">
        Custo operacional
      </div>
      <div class="column-10 column-integer">
        <div style="display: flex; align-items: center; justify-content: center">
        <hours-decimal-field
          name="total_hours"
          class="list-integer"
          v-model="localProposal.total_hours"
          placeholder="quantidade total de horas"
          @save="updateProposal('total_hours', $event)"
        />
        h
        </div>
      </div>
      <div class="column-20 column-price">
        <money-field
          name="total_operational_cost"
          v-model="localProposal.total_operational_cost	"
        />
      </div>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-percent" />
      </div>
      <div class="column-55 column-name">Lucro:</div>
      <div class="column-20 column-price">
        <div style="display: flex; align-items: center; justify-content: center">
        <decimal-field
          name="total_profit_percentage"
          v-model="localProposal.total_profit_percentage"
          placeholder="percentual do lucro"
          @save="updateProposal('total_profit_percentage', $event)"
        />
        %
        </div>
      </div>
      <div class="column-20 column-price">
        <money-field name="total_profit" v-model="localProposal.total_profit" />
      </div>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-dollar-sign" />
      </div>
      <div class="column-75 column-name">Preço base:</div>
      <div class="column-20 column-price">
        <money-field name="price" v-model="localProposal.total_price" />
      </div>
    </div>


    <div class="table-row" :class="{ highlight: isHighlighted }">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fa fa-clock" />
      </div>
      <div class="column-75 column-name">Custos de propdução</div>
      <div class="column-20 column-price">
        <money-field
          name="total_third_party_cost"
          v-model="localProposal.total_third_party_cost"
        />
      </div>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-dollar-sign" />
      </div>
      <div class="column-75 column-name">Preço com custos:</div>
      <div class="column-20 column-total-price">
        <money-field name="total_price" v-model="localProposal.total_price" />
      </div>
    </div>

    <div class="table-row">
      <div class="column-5 column-icon">
        <font-awesome-icon icon="fas fa-credit-card" />
      </div>
      <div class="column-85 column-name">Parcelamento:</div>
      <div class="column-10 column-integer">
        <integer-editable-field
          v-model="localProposal.installment_quantity"
          name="installment_quantity"
          @save="updateProposal('installment_quantity', $event)"
          placeholder="quantidade de parcelas"
        />
      </div>
    </div>
  </section>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";
import DecimalField from "@/components/fields/number/DecimalField";
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import HoursDecimalField from "../fields/number/HoursDecimalField.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isHighlighted: false,
      localProposal: { ...this.proposal },
    };
  },
  components: {
    DecimalField,
    IntegerEditableField,
    HoursDecimalField,
    MoneyField,
  },
  methods: {
    formatDateBr,
    highlight() {
      this.isHighlighted = true;
      setTimeout(() => {
        this.isHighlighted = false;
      }, 2000);
    },
    updateProposal(field, value) {
      this.$emit("update-proposal", field, value);
    },
  },
  watch: {
    proposal: {
      handler(newProposal) {
        this.localProposal = { ...newProposal };
      },
      deep: true,
    },
    "localProposal.total_third_party_cost"(newVal) {
      this.localProposal.total_third_party_cost = newVal;
      this.highlight();
    },
  },
};
</script>