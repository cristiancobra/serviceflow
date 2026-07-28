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
    :items="companies"
    :fieldsToDisplay="fieldsToDisplay" 
    :fieldNull="fieldNullValue" 
    avatarType="company" 
    :allow-create-new="true"
    @update:modelValue="updateInput" 
    @create-new="createNewCompany"
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
      autoSelectUser: null,
      editing: false,
      fieldNullValue: null,
      fieldsToDisplay: "legal_name",
      localValue: this.modelValue,
      selectedName: "",
      companies: [],
      isCreating: false,
    };
  },
  methods: {
    async getCompanies() {
      this.companies = await index("companies");
    },
    // async getAuthenticatedUser() {
    //   axios
    //     .get(`${BACKEND_URL}${USER_CURRENT_URL}`)
    //     .then((response) => {
    //       this.autoSelectUser = response.data.data.id;
    //       this.localValue = this.autoSelectUser;
    //       this.$emit('update:modelValue', this.localValue);
    //     })
    //     .catch((error) => {
    //       console.error("Erro ao buscar usuário:", error);
    //     });
    // },
    async showName() {
      const current = await show("companies", this.modelValue);
      this.selectedName = current.business_name || current.legal_name;
    },
    startEditing() {
      this.editing = true;
    },
    updateInput(newValue) {
      console.log("CompaniesSelectEditableField updateInput:", newValue);
      this.$emit('update:modelValue', newValue);
      this.editing = false;
    },
    async createNewCompany(companyName) {
      console.log("createNewCompany:", companyName);
      if (!companyName.trim()) {
        return;
      }

      this.isCreating = true;
      try {
        const newCompany = await store("companies", {
          legal_name: companyName.trim(),
          business_name: companyName.trim(),
        });
        console.log("newCompany created:", newCompany);

        // Adiciona a nova empresa à lista
        this.companies.push(newCompany);

        // Seleciona a nova empresa
        this.localValue = newCompany.id;
        this.$emit('update:modelValue', newCompany.id);

        // Atualiza o nome exibido
        this.selectedName = newCompany.business_name || newCompany.legal_name;

        // Fecha a edição
        this.editing = false;
      } catch (error) {
        console.error("Erro ao criar nova empresa:", error);
        alert("Erro ao criar nova empresa");
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
    // autoSelectUser(newValue) {
    //   if (newValue !== null) {
    //     this.autoSelectUser = newValue;
    //   }
    // }
  },
  mounted() {
    if (this.fieldNull) {
      this.fieldNullValue = this.fieldNull;
    }
    if (this.autoSelect) {
      this.getAuthenticatedUser();
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