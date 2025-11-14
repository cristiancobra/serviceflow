<template>
  <section class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-dollar" class="icon" />
      <h2>Operacional e margem de lucro</h2>
    </div>

    <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-1">
      <div class="flex items-center justify-center">
        <font-awesome-icon icon="fa fa-clock" class="list-icon text-black" />
      </div>
      <div class="flex items-center justify-start text-black">
        Custo operacional
      </div>
      <div class="flex items-center justify-center">
        <div class="flex items-center justify-center text-black">
          <hours-decimal-field
            name="total_hours"
            class="list-integer"
            v-model="localProposal.total_hours"
          />
          h
        </div>
      </div>
      <div class="flex items-center justify-end">
        <money-field
          name="total_operational_cost"
          v-model="localProposal.total_operational_cost"
        />
      </div>
    </div>

    <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-2" :class="{ highlight: isHighlighted }">
      <div class="flex items-center justify-center">
        <font-awesome-icon icon="fa fa-clock" class="text-black" />
      </div>
      <div class="flex items-center justify-start text-black">Custos de propdução</div>
      <div class="col-span-1"></div>
      <div class="flex items-center justify-end">
        <money-field
          name="total_third_party_cost"
          v-model="localProposal.total_third_party_cost"
        />
      </div>
    </div>

    <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-2">
      <div class="flex items-center justify-center">
        <font-awesome-icon icon="fas fa-percent" class="text-black" />
      </div>
      <div class="flex items-center justify-start text-black">Margem de lucro:</div>
      <div class="flex items-center justify-center">
        <div class="flex items-center justify-center text-black">
          {{ localProposal.total_profit_percentage }}
          %
        </div>
      </div>
      <div class="flex items-center justify-end">
        <money-field 
          name="total_profit" 
          v-model="localProposal.total_profit"
        />
      </div>
    </div>

    <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-2">
      <div class="flex items-center justify-center">
        <font-awesome-icon icon="fas fa-dollar-sign" class="text-black" />
      </div>
      <div class="flex items-center justify-start text-black">Preço final:</div>
      <div class="col-span-1"></div>
      <div class="flex items-center justify-end font-bold text-black">
        <money-field name="total_price" v-model="localProposal.total_price" />
      </div>
    </div>

    <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-2">
      <div class="flex items-center justify-center">
        <font-awesome-icon icon="fas fa-credit-card" class="text-black" />
      </div>
      <div class="flex items-center justify-start text-black">Parcelamento:</div>
      <div class="col-span-1"></div>
      <div class="flex items-center justify-end text-black">
        <integer-editable-field
          v-model="localProposal.installment_quantity"
          name="installment_quantity"
          @save="updateProposal('installment_quantity', $event)"
          placeholder="quantidade de parcelas"
        />
        x
      </div>
    </div>
  </section>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";
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