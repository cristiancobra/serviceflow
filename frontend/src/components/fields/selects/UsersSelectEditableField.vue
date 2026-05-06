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
  <CustomSelectInput v-else :label="label" :name="name" v-model="localValue" :items="users" :fieldsToDisplay="fieldsToDisplay"
    :fieldNull="fieldNullValue" avatarType="user" @update:modelValue="updateInput" />
</template>

<script>
import { BACKEND_URL, USER_CURRENT_URL } from "@/config/apiConfig";
import { index, show } from "@/utils/requests/httpUtils";
import axios from "axios";
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
      fieldsToDisplay: "name",
      localValue: this.modelValue,
      selectedName: "",
      users: [],
    };
  },
  methods: {
    async getUsers() {
      this.users = await index("users");
    },
    async getAuthenticatedUser() {
      axios
        .get(`${BACKEND_URL}${USER_CURRENT_URL}`)
        .then((response) => {
          this.autoSelectUser = response.data.data.id;
          this.localValue = this.autoSelectUser;
          this.$emit('update:modelValue', this.localValue);
        })
        .catch((error) => {
          console.error("Erro ao buscar usuário:", error);
        });
    },
    async showName() {
      const current = await show("users", this.modelValue);
      this.selectedName = current.name;
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
    autoSelectUser(newValue) {
      if (newValue !== null) {
        this.autoSelectUser = newValue;
      }
    }
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