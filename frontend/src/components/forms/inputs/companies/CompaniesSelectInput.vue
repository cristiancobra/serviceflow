<template>
  <div class="mb-5">
    <label class="form-label" :for="name">{{ label }}</label>
    <select class="form-select" :id="name" :name="name" @input="updateInput">
      <option v-if="fieldNull" selected value="null">{{ fieldNull }}</option>
      <option v-if="optionLabel" disabled selected value="">
        {{ optionLabel }}
      </option>
      <option v-for="item in items" :key="item.id" :value="item.id">
        {{ displayItemText(item) }}
      </option>
    </select>
  </div>
</template>
    
  <script>
import axios from "axios";

export default {
  props: {
    label: String,
    type: String,
    name: String,
    placeholder: String,
    fieldToDisplay: [String, Array],
    fieldNull: String,
    optionLabel: String,
  },
  data() {
    return {
      users: [],
    };
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
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
  