<template>
  <div class="mb-5 main-container">
    <font-awesome-icon icon="fas fa-calendar" />
    <label class="form-label  ms-2" :for="name">{{ label }}</label>

    <DateValue
      v-if="!editing"
      :id="name"
      :name="name"
      :model-value="modelValue"
      @click="startEditing"
    />

    <DateInput
      v-else
      @save="emitSave"
      @editing="cancelEditing"
      :model-value="modelValue"
      ref="editableInputRef"
    />
  </div>
</template>

<script>
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import DateValue from "@/components/forms/inputs/date/DateValue.vue";

export default {
  data() {
    return {
      editing: false,
      editedValue: null,
    };
  },
  components: {
    DateInput,
    DateValue,
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    startEditing() {
      this.editing = true;
    },
    emitSave(editedValue) {
      
        this.$emit("save", editedValue);
      
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
    },
    // updateInput(event) {
    //   this.editedValue = event.target.innerText;
    //   console.log(this.editedValue);
    // },
  },
};
</script>

<style scoped>
.form-label {
  font-weight: 900;
  padding-right: 1rem;
}
.main-container {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}
.show-label {
  text-align: left;
  font-weight: 800;
}
.editable-content {
  border: none;
  padding: 5px;
  cursor: pointer;
  background: transparent;
  text-align: left;
}
.editable-content[contenteditable="true"]:empty:before {
  content: attr(placeholder);
  color: #888;
}
</style>