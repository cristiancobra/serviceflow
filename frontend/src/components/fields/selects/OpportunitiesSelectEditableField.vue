<template>
  <div v-if="!editing" @click="startEditing">
    <label class="form-label" :for="name">{{ label }}</label>
    <p v-if="selectedName">
      {{ selectedName }}
    </p>
    <p v-else>
      não possui
    </p>
  </div>
  <SelectInput v-else :label="label" :name="name" v-model="localValue" :items="opportunities"
    :fieldsToDisplay="fieldsToDisplay" :fieldNull="fieldNullValue" @update:modelValue="updateInput" />
</template>

<script>
import { index, show } from "@/utils/requests/httpUtils";
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
      selectedName: "",
      opportunities: [],
    };
  },
  methods: {
    async getProjects() {
      this.opportunities = await index("opportunities");
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
      const current = await show("opportunities", this.modelValue);
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