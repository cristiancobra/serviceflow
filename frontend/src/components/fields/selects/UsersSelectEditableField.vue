<template>
  <div v-if="!editing" @click="startEditing">
    <label class="form-label" :for="name">{{ label }}</label>
    <p>
      {{ username }}
    </p>
  </div>
  <SelectInput v-else :label="label" :name="name" v-model="localValue" :items="users" :fieldsToDisplay="fieldsToDisplay"
    :fieldNull="fieldNullValue" @update:modelValue="updateInput" />
</template>

<script>
import { BACKEND_URL, USER_URL, USER_CURRENT_URL } from "@/config/apiConfig";
import { show } from "@/utils/requests/httpUtils";
import axios from "axios";
import SelectInput from "@/components/forms/selects/SelectInput.vue";

export default {
  components: {
    SelectInput,
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
      username: "",
      users: [],
    };
  },
  methods: {
    async getUsers() {
      axios
        .get(`${BACKEND_URL}${USER_URL}`)
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((error) => console.log(error));
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
    async showUsername() {
      const currentUser = await show("users", this.modelValue);
      this.username = currentUser.name;
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
        this.showUsername();
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
      this.showUsername();
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