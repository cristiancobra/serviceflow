<template>
  <div class="money-container">
    <div v-if="localValue" class="disabled">
      <p class="text-end">
        {{ formatCurrencySymbol(localValue) }}
      </p>
    </div>
  </div>
</template>

<script>
import { convertBrToCurrency, convertCurrencyToBr, formatCurrencySymbol } from "@/utils/number/moneyUtils";

export default {
  data() {
    return {
      localValue: this.modelValue,
    };
  },
  props: {
    name: String,
    modelValue: [String, Number],
  },
  methods: {
    convertBrToCurrency,
    convertCurrencyToBr,
    formatCurrencySymbol,
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.convertCurrencyToBr(newValue);
    },
  },
};
</script>

<style scoped>
.money-container {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
}

.money-container .disabled {
background-color: lightgrey;
}
</style>