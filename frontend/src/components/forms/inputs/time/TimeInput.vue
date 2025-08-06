<template>
  <div>
    <label v-if="label" :for="label">{{ label }}</label>
    <VueDatePicker :name="name" :label="label" v-model="localValue" :placeholder="placeholder"
      @update:modelValue="emitSave" />
  </div>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { convertDateTimeToLocal, convertDateTimeForServer } from "@/utils/date/dateUtils";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      localValue: this.convertDateTimeToLocal(this.modelValue),
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
    convertDateTimeToLocal,
    convertDateTimeForServer,
    emitSave() {
      if (this.modelValue !== this.localValue) {
        this.$emit("update:modelValue", this.convertDateTimeForServer(this.localValue));
      }
    },
  },
  mounted() {
    if (this.autoFillNow && this.modelValue === null) {
      this.localValue = new Date();
    }
    if (this.modelValue === '1969-12-31 18:00:00' && this.modelValue === '1969-12-31 21:00:00' || this.modelValue === null) {
      this.localValue = "";
    }
  }
};
</script>