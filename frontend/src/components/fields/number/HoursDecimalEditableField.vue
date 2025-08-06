<template>
  <div class="main-container">
    <label class="form-label" :for="name">{{ label }}</label>
    <div v-if="!editing"  @click="startEditing">
      <div v-if="localValue">
        <p class="number-editable">
          <font-awesome-icon icon="edit" class="edit-icon" />
        {{ localValue }}
        </p>
      </div>
      <div v-else>
        não informado
      </div>
    </div>
    <div v-else>
      <div class="text-input-container">
        <input class="text-input text-end" type="number" step="0.1" :name="name" 
        v-model="localValue" :placeholder="placeholder" @keydown.esc="cancelEditing" @blur="emitSave" @keydown.enter.prevent="emitSave" />
      </div>
    </div>

  </div>
</template>

<script>
import { convertDecimalToSeconds, convertSecondsToDecimal } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      editing: false,
      localValue: this.convertSecondsToDecimal(this.modelValue),
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
    convertDecimalToSeconds,
    convertSecondsToDecimal,
    startEditing() {
      this.editing = true;
    },
    emitSave() {
      this.$emit("save", this.convertDecimalToSeconds(this.localValue));
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = this.modelValue
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.convertSecondsToDecimal(newValue);
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

label {
  font-size: 1rem;
}
.main-container {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}

.number-editable:hover .edit-icon {
  display: inline;
}

.number-editable {
  cursor: pointer;
  color: var(--primary);
}

.show-label {
  text-align: left;
  font-weight: 800;
}
</style>