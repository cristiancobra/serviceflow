<template>
  <div class="main-container">
    <label v-if="label" class="" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <div v-if="modelValue" class="">
        {{ modelValue }}
      </div>
      <div v-else>não informado</div>
    </div>
    <div v-else>
      <input
        class="text-input"
        type="text"
        :name="name"
        v-model="localValue"
        :placeholder="placeholder"
        @keydown.esc="cancelEditing"
        @blur="emitSave"
        @keydown.enter.prevent="emitSave"
      />
    </div>
  </div>
</template>

<script>
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
    startEditing() {
      this.editing = true;
    },
    emitSave() {
      this.$emit("save", this.localValue);
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