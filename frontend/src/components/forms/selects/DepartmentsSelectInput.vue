<template>
  <SelectInput
    :label="label"
    :name="name"
    :modelValue="modelValue"
    :items="departments"
    :fieldsToDisplay="fieldsToDisplay"
    :fieldNull="fieldNull"
    @update:modelValue="updateInput"
  />
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
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
    fieldsToDisplay: {
      type: [String, Array],
      default: 'name'
    },
    fieldNull: {
      type: String,
      default: 'Sem departamento'
    },
    modelValue: null,
  },
  data() {
    return {
      departments: [],
    };
  },
  methods: {
    updateInput(value) {
      this.$emit('update:modelValue', value);
    },
    async getDepartments() {
      try {
        const allDepartments = await index("departments");
        // Filtra apenas departamentos ativos para seleção
        this.departments = (allDepartments || []).filter(dept => dept.active);
      } catch (error) {
        console.error("Erro ao carregar os departamentos:", error);
        this.departments = [];
      }
    },
    // Método público para recarregar a lista
    async reload() {
      await this.getDepartments();
    },
  },
  mounted() {
    this.getDepartments();
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
