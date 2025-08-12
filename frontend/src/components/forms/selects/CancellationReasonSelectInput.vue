<template>
  <SelectInput
    :label="label"
    :name="name"
    v-model="localValue"
    :items="cancellationReasons"
    :fieldsToDisplay="fieldsToDisplay"
    :placeholder="placeholder"
    @update:modelValue="updateInput"
  />
</template>

<script>
import { BACKEND_URL } from "@/config/apiConfig";
import axios from "axios";
import SelectInput from "./SelectInput.vue";

export default {
  components: {
    SelectInput,
  },
  props: {
    modelValue: null,
    name: String,
    autoSelect: Boolean,
    type: String,
  },
  data() {
    return {
      cancellationReasons: [],
      label: "Motivo do cancelamento:",
      localValue: this.modelValue,
      fieldsToDisplay: ['reason'],
      placeholder: "Selecione...",
    };
  },
  methods: {
    async getCancellationReasons() {
      try {
        const response = await axios.get(`${BACKEND_URL}tasks/cancellation-reasons`);
        this.cancellationReasons = response.data.data;
      } catch (error) {
        console.error("Erro ao buscar motivos de cancelamento:", error);
      }
    },
    updateInput(newValue) {
      this.$emit("update:modelValue", newValue);
    },
  },
  watch: {
    modelValue(newValue) {
      if (newValue !== null) {
        this.localValue = newValue;
      }
    },
  },
  mounted() {
    if (!this.modelValue) {
      this.localValue = ""; // Garante que o placeholder seja exibido por padrão
    }
    this.getCancellationReasons();
  },
};
</script>

<style scoped>
label {
  text-align: right;
}

.input-field {
  margin-bottom: 1rem;
}
</style>