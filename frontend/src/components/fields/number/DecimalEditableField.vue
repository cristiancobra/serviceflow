<template>
  <div class="main-container">
    <label class="form-label" :for="name">{{ label }}</label>
    <div v-if="!editing"  @click="startEditing">
      <div v-if="localValue">
        <p class="text-end">
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
export default {
  data() {
    return {
      editing: false,
      localValue: this.modelValue ? parseFloat(this.modelValue).toFixed(2) : '',
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
    startEditing() {
      this.editing = true;
    },
    emitSave() {
      this.$emit("save", parseFloat(this.localValue).toFixed(2));
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = this.modelValue ? parseFloat(this.modelValue).toFixed(2) : '';
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue ? parseFloat(newValue).toFixed(2) : '';
    },
  },
};
</script>

<style scoped>
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