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