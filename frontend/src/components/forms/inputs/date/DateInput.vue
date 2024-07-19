<template>
  <div class="">
    <label v-if="label" :for="label" class="form-label">{{ label }}</label>
    <VueDatePicker class="form-control" :name="name" :label="label" v-model="localValue"
      :placeholder="placeholder" @update:modelValue="emitSave" />
  </div>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { convertDateTimeForServer } from "@/utils/date/dateUtils";
import { convertDateTimeToLocal } from "@/utils/date/dateUtils";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      date: new Date(),
      localValue: this.convertDateTimeToLocal(this.modelValue),
    };
  },
  props: {
    name: String,
    label: String,
    placeholder: String,
    modelValue: [String, Number],
  },
  methods: {
    convertDateTimeToLocal,
    convertDateTimeForServer,
    emitSave() {
      if (this.modelValue !== this.localValue) {
        this.$emit("update:modelValue", this.convertDateTimeForServer(this.localValue));
      }
    },
  },
  mounted() {
    const timestampAtual = Date.now();
    this.date = timestampAtual;

    if (!this.modelValue) {
      this.localValue = "não informado";
    }

    document.addEventListener('keydown', this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.cancelEditing);
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.convertDateTimeToLocal(newValue);
    },
  },
};
</script>

<style scoped>
label {
  text-align: right;
}
</style>