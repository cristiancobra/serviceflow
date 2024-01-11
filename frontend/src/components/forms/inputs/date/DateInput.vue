<template>
  <div class="mb-5">
    <VueDatePicker v-model="date" @input="updateModelValue" />
    <input
      class="form-control"
      type="datetime-local"
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
  import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  components: {
    VueDatePicker,
  },
  date() {
    return {
      date: new Date().now(),
    }
  },
  props: {
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
    // emitSave() {
    //   if (this.editedValue !== this.modelValue) {
    //     this.$emit("update-model-value", this.editedValue);
    //   }
    // },
    cancelEditing() {
      this.$emit("editing", false);
      // this.editing = false;
    },
    updateInput(event) {
      this.editedValue = event.target.value;
    },
  },
  mounted() {
    console.log(this.modelValue);
    const dataAtual = new Date();
    const timestampAtual = Date.now();
this.date = timestampAtual;
    console.log(dataAtual);
    console.log(timestampAtual);
    console.log(this.date);
  },
};
</script>
    
  <style scoped>
label {
  text-align: right;
}
</style>
  