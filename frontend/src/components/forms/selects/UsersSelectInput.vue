<template>
  <SelectInput :label="label" :name="name" v-model="localValue" :items="users" :fieldsToDisplay="fieldsToDisplay"
    fieldNull="Nenhum" :autoSelect=autoSelectUser @update:modelValue="updateInput" />
</template>

<script>
import { BACKEND_URL, USER_URL, USER_CURRENT_URL } from "@/config/apiConfig";
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
    fieldsToDisplay: [String, Array],
    // fieldNull: String,
    optionLabel: String,
    autoSelect: Boolean,
    type: String,
  },
  data() {
    return {
      users: [],
      localValue: this.modelValue,
      autoSelectUser: null,
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
    async getCurrentUser() {
      axios
        .get(`${BACKEND_URL}${USER_CURRENT_URL}`)
        .then((response) => {
          this.autoSelectUser = response.data.data;
          this.localValue = this.autoSelectUser;
          // this.$emit('update:modelValue', user);
        })
        .catch((error) => {
          console.error("Erro ao buscar usuário:", error);
        });
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
    autoSelectUser(newValue) {
      if (newValue !== null) {
        this.autoSelectUser = newValue;
      }
    }
  },
  mounted() {
    if(this.autoSelect) {
      this.getCurrentUser();
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