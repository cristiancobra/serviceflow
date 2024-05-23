<template>
  <div class="">
    <input
      class="form-control"
      :type="type"
      :id="name"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      @input="updateInput"
      @keydown.esc="cancelEditing"
      @blur="emitSave"
      @keydown.enter.prevent="emitSave"
    />
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
  methods: {
    // updateInput(event) {
    //   this.$emit("update:modelValue", event.target.value);
    // },
    startEditing() {
      this.editing = true;
      this.editedValue = this.modelValue;
      this.$nextTick(() => {
        this.$refs.editableInputRef.focus();
      });
    },
    emitSave() {
      if (this.editedValue !== this.modelValue) {
        this.$emit("save", this.editedValue);
      }
      // this.editing = false;
    },
    cancelEditing() {
      this.$emit("editing", false);
      // this.editing = false;
    },
    updateInput(event) {
      this.editedValue = event.target.value;
    },
  },
  mounted() {
    console.log("ta no input");
  },
};
</script>
    
  <style scoped>
label {
  text-align: right;
}
</style>
  