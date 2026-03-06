<template>
  <div class="text-black text-right">
    <div class="price">
      <p>
        {{ formatCurrencySymbol(localValue || 0) }}
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