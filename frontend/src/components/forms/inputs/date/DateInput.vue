<template>
  <div class="">
    <label v-if="label" :for="label" class="form-label">{{ label }}</label>
    <VueDatePicker class="form-control" :id="name" :name="name" :label="label" v-model="modelValue" :placeholder="placeholder"
      @update:modelValue="emitSave" />
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
      date: new Date(),
      modelValue: null,
    };
  },
  props: {
    name: String,
    placeholder: String,
    value: [String, Number],
  },
  methods: {
    formatDateTimeForServer,
    startEditing() {
      this.editing = true;
      this.$nextTick(() => {
        this.$refs.editableInputRef.focus();
      });
    },
    emitSave() {
      if (this.modelValue !== this.value) {
        this.$emit("save", this.formatDateTimeForServer(this.modelValue));
        console.log("emitSave", this.formatDateTimeForServer(this.modelValue));
      }
    },
    cancelEditing() {
      this.$emit("editing", false);
    },
  },
  mounted() {
    console.log("value", this.value);
    console.log("modelValue", this.modelValue);
    const timestampAtual = Date.now();
    this.date = timestampAtual;

    if (this.value) {
      this.modelValue = this.value;
    } else {
      this.modelValue = "não informado";
    }

  },
  watch: {
    value(newValue) {
      console.log("watch value", newValue);
      this.modelValue = newValue;
    },
  },
};
</script>

<style scoped>
label {
  text-align: right;
}
</style>