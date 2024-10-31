<template>
  <div>
    <label v-if="label" class="form-label" :for="name">{{ label }}</label>
    <div v-if="!editing"  @click="startEditing">
      <div v-if="localValue">
        <p class="text-end">
        {{ formatCurrencySymbol(localValue) }}
        </p>
      </div>
      <div v-else>
        não informado
      </div>
    </div>
    <div v-else class="">
        <input class="input-money" type="text" :name="name" 
        v-model="localValue" :placeholder="placeholder" @keydown.esc="cancelEditing" @blur="emitSave" @keydown.enter.prevent="emitSave" />
    </div>

  </div>
</template>

<script>
import { convertBrToCurrency, convertCurrencyToBr, formatCurrencySymbol } from "@/utils/number/moneyUtils";

export default {
  data() {
    return {
      editing: false,
      localValue: this.modelValue,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
    status: String,
  },
  methods: {
    convertBrToCurrency,
    convertCurrencyToBr,
    formatCurrencySymbol,
    startEditing() {
      this.editing = true;
    },
    emitSave() {
      this.$emit("update", this.convertBrToCurrency(this.localValue));
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = this.modelValue
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.convertCurrencyToBr(newValue);
    },
  },
};
</script>

<style scoped>
.input-money {
  text-align: right;
  width: 100%;
}

.main-container {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}

.show-label {
  text-align: left;
  font-weight: 800;
}
</style>