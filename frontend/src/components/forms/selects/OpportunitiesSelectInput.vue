<template>
  <SelectInput :label="label" :name="name" v-model="localValue" :items="opportunities" :fieldToDisplay="fieldToDisplay"
    :fieldNull="fieldNullValue" :autoSelect=autoSelect @update:modelValue="updateInput" />
</template>

<script>
import { fetchIndexData } from "@/utils/requests/httpUtils";
import SelectInput from "./SelectInput.vue";

export default {
  components: {
    SelectInput,
  },
  props: {
    autoSelect: Boolean,
    fieldNull: String,
    fieldToDisplay: [String, Array],
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
      authUser: null,
      fieldNullValue: null,
    };
  },
  methods: {
    async getOpportunities() {
      try {
        this.opportunities = await fetchIndexData(`opportunities`);
        console.log("Oportunidades:", this.opportunities);
      } catch (error) {
        console.error("Erro ao acessar oportunidades:", error);
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
    this.getOpportunities();
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