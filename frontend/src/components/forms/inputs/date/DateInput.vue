<template>
  <div class="">
    <label v-if="label" :for="label">{{ label }}</label>
    <DatePicker :name="name" :label="label" v-model="localValue" format="dd/MM/yyyy HH:mm"
      :placeholder="placeholder" @update:modelValue="emitSave" />
  </div>
</template>

<script>
import DatePicker from "@/components/forms/inputs/date/DatePicker.vue";

export default {
  components: {
    DatePicker,
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