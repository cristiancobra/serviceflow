<template>
  <SelectInput
    :label="label"
    :name="name"
    :value="modelValue"
    :items="users"
    :fieldToDisplay="fieldToDisplay"
    fieldNull="Nenhum"
    :autoSelect=autoSelect
    @update:modelValue="updateInput"
  />
</template>
    
  <script>
import { BACKEND_URL, USER_URL, USER_CURRENT_URL } from "@/config/apiConfig";
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
    fieldToDisplay: [String, Array],
    // fieldNull: String,
    optionLabel: String,
    autoSelect: Boolean,
  },
  data() {
    return {
      users: [],
      modelValue: null,
    };
  },
  methods: {
    updateInput(value) {
      this.modelValue = value;
    },
    async getUsers() {
      axios
        .get(`${BACKEND_URL}${USER_URL}`)
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    async getCurrentUser() {
      axios
        .get(`${BACKEND_URL}${USER_CURRENT_URL}`)
        .then((response) => {
          const user = response.data.data;
          this.modelValue = user.id;
        })
        .catch((error) => {
          console.error("Erro ao buscar usuário:", error);
        });
    },
  },
  watch: {
    modelValue(newValue) {
      // Verifica se o novo valor de modelValue não é nulo
      // e executa alguma ação, se necessário
      if (newValue !== null) {
        this.modelValue = newValue;
      }
    },
  },
  mounted() {
    this.getCurrentUser();
    this.getUsers();
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
  