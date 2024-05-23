<template>
  <div class="mb-1 main-container">
    <font-awesome-icon icon="fas fa-calendar" />
    <label class="form-label  ms-2" :for="name">{{ label }}</label>

    <template v-if="!modelValue && !editing">
      <p @click="startEditing" class="not-informed">Não informado</p>
    </template>
    <template v-else>
      <DateValue v-if="!editing" :id="name" :name="name" :model-value="formatedDate" @click="startEditing" />

      <DateInput v-else @save="emitSave" @editing="cancelEditing" :value="modelValue" ref="editableInputRef" />
    </template>
  </div>
</template>

<script>
import DateInput from "@/components/forms/inputs/date/DateInput.vue";
import DateValue from "@/components/forms/inputs/date/DateValue.vue";
import { convertUtcToLocal } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      editing: false,
      editedValue: null,
      formatedDate: null,
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
    convertUtcToLocal,
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
  },
  mounted() {
    document.addEventListener("keydown", this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.cancelEditing);
  },
  watch: {
    modelValue(newValue) {
      if (newValue != '1969-12-31 18:00:00' && newValue != '1969-12-31 21:00:00') {
      this.formatedDate = convertUtcToLocal(newValue);
      }
    },
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