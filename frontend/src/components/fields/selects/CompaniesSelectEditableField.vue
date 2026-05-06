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
  <CustomSelectInput v-else :label="label" :name="name" v-model="localValue" :items="companies"
    :fieldsToDisplay="fieldsToDisplay" :fieldNull="fieldNullValue" avatarType="company" @update:modelValue="updateInput" />
</template>

<script>
import { index, show } from "@/utils/requests/httpUtils";
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
      this.$emit('update:modelValue', newValue);
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