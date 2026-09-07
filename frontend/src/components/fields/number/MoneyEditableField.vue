<template>
  <div>
    <label v-if="label" class="form-label" :for="name">{{ label }}</label>
    <div v-if="!editing"  @click="startEditing">
      <div v-if="localValue !== null && localValue !== undefined && localValue !== ''">
        <p class="price-editable text-right">
          <font-awesome-icon icon="edit" class="edit-icon" />
          {{ formatCurrencySymbol(localValue) }}
        </p>
      </div>
      <div v-else>
        não informado
      </div>
    </div>
    <money-input
      v-else
      :name="name"
      :placeholder="placeholder"
      v-model="editingValue"
      @keydown.esc="cancelEditing"
      @blur="emitSave"
      @keydown.enter.prevent="emitSave"
    />

  </div>
</template>

<script>
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import MoneyInput from "@/components/forms/inputs/money/MoneyInput.vue";

export default {
  components: {
    MoneyInput,
  },
  data() {
    return {
      editing: false,
      localValue: this.modelValue,
      editingValue: null,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
    status: String,
  },
  emits: ['update:modelValue', 'save'],
  methods: {
    formatCurrencySymbol,
    startEditing() {
      this.editingValue = this.localValue;
      this.editing = true;
    },
    emitSave() {
      this.localValue = this.editingValue;
      this.$emit("update:modelValue", this.editingValue);
      this.$emit("save", this.editingValue);
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = this.modelValue;
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
};
</script>

<style scoped>
.edit-icon {
  display: none;
  margin-left: 5px;
  color: var(--green);
}

.price-editable:hover .edit-icon {
  display: inline;
}

.main-container {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}

.price-editable {
  cursor: pointer;
  color: var(--primary);
}

.show-label {
  text-align: left;
  font-weight: 800;
}
</style>