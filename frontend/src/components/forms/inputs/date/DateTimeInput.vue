<template>
  <div class="">
    <label v-if="label" :for="label" class="form-label">{{ label }}</label>
    <VueDatePicker :name="name" :label="label" v-model="localValue" :placeholder="placeholder"
      @update:modelValue="emitDateChange" />
  </div>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { formatDateTimeToLocal } from "@/utils/date/dateUtils";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      localValue: this.formatDateTimeToLocal(this.modelValue),
    };
  },
  props: {
    autoFillNow: {
      type: Boolean,
      default: false, // Define como false por padrão
    },
    modelValue: {
      type: String,
      default: null,
    },
    name: String,
    label: String,
    placeholder: String,
  },
  methods: {
    formatDateTimeToLocal,
    emitDateChange() {
      console.log("emitDateChange", this.localValue)
      if (this.autoFillNow && this.modelValue === null) {
        this.localValue = new Date();
      }
      this.$emit("update:modelValue", this.localValue);
    },
  },
};
</script>

<style scoped>
label {
  text-align: right;
}
</style>