<template>
  <div>
    <label v-if="label" class="" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <div v-if="modelValue" class="">
        <p class="text-base-content">
        {{ modelValue }}
        </p>
      </div>
      <div v-else class="text-base-content/50">{{ emptyText }}</div>
    </div>
    <div v-else-if="editing">
      <input
        class="bg-base-100 text-base-content w-100 border border-primary-500 rounded-md px-3 py-2 focus:border-primary-600 focus:ring-2 focus:ring-primary-200 focus:outline-none"
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
    emptyText: {
      type: String,
      default: "não informado",
    },
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