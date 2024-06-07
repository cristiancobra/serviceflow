<template>
  <SelectInput :label="label" :name="name" v-model="localValue" :items="projects" :fieldToDisplay="fieldToDisplay"
    fieldNull="Nenhum" :autoSelect=autoSelect @update:modelValue="updateInput" />
</template>

<script>
import { BACKEND_URL, PROJECT_URL } from "@/config/apiConfig";
import axios from "axios";
import SelectInput from "./SelectInput.vue";

export default {
  components: {
    SelectInput,
  },
  props: {
    label: String,
    modelValue: null,
    name: String,
    placeholder: String,
    fieldToDisplay: [String, Array],
    fieldNull: String,
    optionLabel: String,
    autoSelect: Boolean,
    type: String,
  },
  data() {
    return {
      projects: [],
      localValue: this.modelValue,
      authUser: null,
    };
  },
  methods: {
    async getProjects() {
      axios
        .get(`${BACKEND_URL}${PROJECT_URL}`)
        .then((response) => {
          this.projects = response.data.data;
          console.log("Projetos:", this.projects);
        })
        .catch((error) => console.log(error));
    },
    // async getCurrentUser() {
    //   axios
    //     .get(`${BACKEND_URL}${USER_CURRENT_URL}`)
    //     .then((response) => {
    //       const user = response.data.data;
    //       this.authUser = user.id;
    //     })
    //     .catch((error) => {
    //       console.error("Erro ao buscar usuário:", error);
    //     });
    // },
    updateInput(newValue) {
      console.log("Novo valor do modelo no pai:", newValue);
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
    // this.getCurrentUser();
    this.getProjects();
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