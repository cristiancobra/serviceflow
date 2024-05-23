<template>
  <SelectInput
    :label="label"
    :name="name"
    :modelValue="modelValue"
    :items="companies"
    :fieldsToDisplay="fieldsToDisplay"
    fieldNull="Nenhum"
    @update:modelValue="updateInput"
  />
</template>
    
  <script>
import { BACKEND_URL, COMPANY_URL } from "@/config/apiConfig";
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
      companies: [],
      modelValue: null,
    };
  },
  methods: {
    updateInput(value) {
      this.modelValue = value;
    },
    getCompanies() {
      axios
        .get(`${BACKEND_URL}${COMPANY_URL}`)
        .then((response) => {
          this.companies = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getCompanies();
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
  