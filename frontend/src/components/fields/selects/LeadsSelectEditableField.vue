<template>
  <div v-if="!editing" @click="startEditing" class="cursor-pointer hover:bg-gray-50 p-2 rounded">
    <label class="form-label" :for="name">{{ label }}</label>
    <p v-if="selectedName" class="text-gray-900 font-medium mt-1">
      {{ selectedName }}
    </p>
    <p v-else class="text-gray-400 italic mt-1">
      não possui
    </p>
  </div>
  <CustomSelectInput 
    v-else 
    :label="label" 
    :name="name" 
    v-model="localValue" 
    :items="leads"
    :fieldsToDisplay="fieldsToDisplay" 
    :fieldNull="fieldNullValue" 
    avatarType="lead" 
    :allow-create-new="true"
    @update:modelValue="updateInput" 
    @create-new="createNewLead"
  />
</template>

<script>
import { index, show, store } from "@/utils/requests/httpUtils";
import CustomSelectInput from "@/components/forms/selects/CustomSelectInput.vue";

export default {
  components: {
    CustomSelectInput,
  },
  props: {
    label: String,
    modelValue: null,
    name: String,
    placeholder: String,
    fieldNull: String,
    optionLabel: String,
    autoSelect: Boolean,
    type: String,
  },
  data() {
    return {
      editing: false,
      fieldNullValue: null,
      fieldsToDisplay: "name",
      localValue: this.modelValue,
      selectedName: "",
      leads: [],
      isCreating: false,
    };
  },
  methods: {
    async getCompanies() {
      this.leads = await index("leads");
    },
    async showName() {
      const current = await show("leads", this.modelValue);
      this.selectedName = current.name;
    },
    startEditing() {
      this.editing = true;
    },
    updateInput(newValue) {
      console.log("LeadsSelectEditableField updateInput:", newValue);
      this.$emit('update:modelValue', newValue);
      this.editing = false;
    },
    async createNewLead(leadName) {
      console.log("createNewLead:", leadName);
      if (!leadName.trim()) {
        return;
      }

      this.isCreating = true;
      try {
        const newLead = await store("leads", {
          name: leadName.trim(),
        });
        console.log("newLead created:", newLead);

        // Adiciona o novo lead à lista
        this.leads.push(newLead);

        // Seleciona o novo lead
        this.localValue = newLead.id;
        this.$emit('update:modelValue', newLead.id);

        // Atualiza o nome exibido
        this.selectedName = newLead.name;

        // Fecha a edição
        this.editing = false;
      } catch (error) {
        console.error("Erro ao criar novo lead:", error);
        alert("Erro ao criar novo cliente");
      } finally {
        this.isCreating = false;
      }
    },
  },
  watch: {
    modelValue(newValue) {
      if (newValue !== null) {
        this.localValue = newValue;
        this.showName();
      }
    },
  },
  mounted() {
    if (this.fieldNull) {
      this.fieldNullValue = this.fieldNull;
    }
    if (this.modelValue) {
      this.showName();
    }
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