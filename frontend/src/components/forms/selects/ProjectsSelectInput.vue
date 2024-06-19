<template>
  <SelectInput :label="label" :name="name" v-model="localValue" :items="projects" :fieldToDisplay="fieldToDisplay"
  :fieldNull="fieldNullValue" :autoSelect=autoSelect @update:modelValue="updateInput" />
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
    fieldNull: String,
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
      fieldNullValue: null,
    };
  },
  methods: {
    async getProjects() {
      axios
        .get(`${BACKEND_URL}${PROJECT_URL}`)
        .then((response) => {
          this.projects = response.data.data;
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
    if(this.fieldNull) {
      this.fieldNullValue = this.fieldNull;
    }
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