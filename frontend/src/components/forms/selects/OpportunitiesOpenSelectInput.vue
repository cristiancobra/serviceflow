<template>
  <SelectInput 
    :label="label" 
    :name="name" 
    v-model="localValue" 
    :items="opportunities" 
    :fieldsToDisplay="fieldsToDisplay"
    :fieldNull="fieldNullValue" 
    :autoSelect="autoSelect" 
    @update:modelValue="updateInput" 
  />
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";
import SelectInput from "./SelectInput.vue";

export default {
  components: {
    SelectInput,
  },
  props: {
    autoSelect: Boolean,
    fieldNull: String,
    fieldsToDisplay: [String, Array],
    label: String,
    modelValue: null,
    name: String,
    optionLabel: String,
    placeholder: String,
    type: String,
  },
  data() {
    return {
      opportunities: [],
      localValue: this.modelValue,
      fieldNullValue: null,
    };
  },
  methods: {
    async getOpenOpportunities() {
      // Busca apenas oportunidades abertas via API
      try {
        console.log('Buscando oportunidades abertas...');
        const response = await axios.get(`${BACKEND_URL}opportunities/open`);
        this.opportunities = response.data.data;
        console.log('Oportunidades carregadas:', this.opportunities);
        console.log('Total de oportunidades:', this.opportunities?.length || 0);
      } catch (error) {
        console.error("Erro ao acessar oportunidades abertas:", error);
        console.error("Detalhes do erro:", error.response?.data);
      }
    },
    updateInput(newValue) {
      this.$emit('update:modelValue', newValue);
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
    if (this.fieldNull) {
      this.fieldNullValue = this.fieldNull;
    }
    this.getOpenOpportunities();
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
