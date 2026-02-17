<template>
  <SelectInput
    :label="label"
    :name="name"
    :modelValue="modelValue"
    :items="leads"
    :fieldsToDisplay="fieldsToDisplay"
    :fieldNull="fieldNull"
    @update:modelValue="updateInput"
  />
</template>
    
<script>
import { BACKEND_URL, LEAD_URL } from "@/config/apiConfig";
import axios from "axios";
import SelectInput from "./SelectInput.vue";

export default {
  components: {
    SelectInput,
  },
  props: {
    label: String,
    type: String,
    name: String,
    placeholder: String,
    fieldsToDisplay: [String, Array],
    fieldNull: String,
    optionLabel: String,
  },
  data() {
    return {
      leads: [],
      modelValue: null,
    };
  },
  methods: {
    updateInput(value) {
      this.modelValue = value;
    },
    async getLeads() {
      try {
        const response = await axios.get(`${BACKEND_URL}${LEAD_URL}`);
        this.leads = response.data.data;
      } catch (error) {
        console.log(error);
      }
    },
    // Método público para recarregar a lista
    async reload() {
      await this.getLeads();
    },
  },
  mounted() {
    this.getLeads();
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
