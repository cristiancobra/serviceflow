<template>
  <div class="">
    <label v-if="label" :for="label">{{ label }}</label>
    <VueDatePicker :name="name" :label="label" v-model="localValue" format="dd/MM/yyyy HH:mm" locale="pt-BR" select-text="Selecionar" cancel-text="Cancelar"
      :placeholder="placeholder" teleport teleport-center @update:modelValue="emitSave" />
  </div>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      localValue: this.modelValue,
    };
  },
  props: {
    name: String,
    label: String,
    placeholder: String,
    modelValue: [String, Number],
    autoFillNow: Boolean, 
  },
  methods: {
    emitSave() {
      if (this.modelValue !== this.localValue) {
        this.$emit("update:modelValue", this.localValue);

        // this.$emit("update:modelValue", this.localValue);
      }
    },
  },
  mounted() {
    if (this.autoFillNow) {
      this.localValue = new Date(); // String no formato ISO 8601
      this.emitSave();
    }
    if (!this.localValue) {
      this.localValue = "não informado";
    }   

    document.addEventListener('keydown', this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.cancelEditing);
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
};
</script>

<style scoped>
label {
  text-align: right;
}
</style>