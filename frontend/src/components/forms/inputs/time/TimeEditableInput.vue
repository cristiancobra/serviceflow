<template>
  <div class="mb-1 main-container">
    <label class="form-label  ms-2" :for="name">{{ label }}</label>
      <TimeValue v-if="!editing" :id="name" :name="name" v-model="localValue" @click="startEditing" />
      <TimeInput v-else @update:modelValue="emitSave" @editing="cancelEditing" v-model="localValue" ref="editableInputRef" />
  </div>
</template>

<script>
import TimeInput from "@/components/forms/inputs/time/TimeInput.vue";
import TimeValue from "@/components/forms/inputs/time/TimeValue.vue";

export default {
  data() {
    return {
      editing: false,
      editedValue: null,
      localValue: this.modelValue,
    };
  },
  components: {
    TimeInput,
    TimeValue,
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
  },
  mounted() {
    document.addEventListener("keydown", this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.cancelEditing);
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