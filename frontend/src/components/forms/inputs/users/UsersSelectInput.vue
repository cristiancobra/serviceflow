<template>
  <SelectInput
    :label="label"
    :name="name"
    :modelValue="modelValue"
    :items="users"
    fieldToDisplay="name"
    fieldNull="Nenhum"
    @update:modelValue="updateInput"
  />
</template>
    
  <script>
import axios from "axios";
import SelectInput from '../SelectInput.vue';

export default {
  components: {
    SelectInput,
  },
  props: {
    label: String,
    type: String,
    name: String,
    placeholder: String,
    // fieldToDisplay: [String, Array],
    // fieldNull: String,
    optionLabel: String,
  },
  data() {
    return {
      users: [],
      modelValue: null,
    };
  },
  methods: {
    updateInput(value) {
      this.modelValue = value;
    },
    displayItemText(item) {
      if (Array.isArray(this.fieldToDisplay)) {
        return this.fieldToDisplay.map((field) => item[field]).join(" - ");
      } else {
        return item[this.fieldToDisplay];
      }
    },
    getUsers() {
      axios
        .get("http://localhost:8191/api/users")
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
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
  