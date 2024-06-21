<template>
  <div class="text-input-container">
    <label v-if="label" class="form-label" :for="name">{{ label }}</label>
    <input class="text-input" :type="type" :id="name" :name="name" v-model="localValue" :placeholder="placeholder"
      @keydown.esc="cancelEditing" @blur="emitSave" @keydown.enter.prevent="emitSave" />
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  data() {
    return {
      localValue: this.modelValue,
    };
  },
  methods: {
    emitSave() {
      console.log("Emitsave");
      if (this.localValue !== this.modelValue) {
        this.$emit("save", this.localValue);
      }
    },
    cancelEditing() {
      this.$emit("editing", false);
      this.localValue = this.modelValue
    },
  },
};
</script>

<style scoped>
.text-input {
  width: 100%;
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid var(--gray);
  border-radius: 4px;
  margin-top: 0.5rem;
}

.text-input-container {
  margin-bottom: 1rem;
  width: 100%;
}

label {
  text-align: right;
}
</style>