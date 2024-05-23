<template>
  <div class="">
    <label v-if="label" :for="label" class="form-label">{{ label }}</label>
    <VueDatePicker
      :name="name"
      :label="label"
      v-model="modelValue"
      :placeholder="placeholder"
      @update:modelValue="emitDateChange"
    />
  </div>
</template>
    
  <script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { formatDateTimeForServer } from "@/utils/date/dateUtils";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      modelValue: null,
    };
  },
  props: {
    name: String,
    label: String,
    placeholder: String,
    autoFillNow: {
      type: Boolean,
      default: false, // Define como false por padrão
    },
  },
  methods: {
    formatDateTimeForServer,
    emitDateChange() {
      if (this.autoFillNow && this.modelValue === null) {
        this.modelValue = new Date();
      }
      this.$emit("update:modelValue", formatDateTimeForServer(this.modelValue));
    },
  },
  mounted() {
    this.emitDateChange();
  },
};
</script>
    
  <style scoped>
label {
  text-align: right;
}
</style>
  