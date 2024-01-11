<template>
  <div class="mb-5 main-container">
    <label class="form-label" :for="name">{{ label }}</label>

    <TextValue
      v-if="!editing"
      :id="name"
      :name="name"
      :model-value="modelValue"
      @click="startEditing"
    />

    <TextInput
      v-else
      @save="emitSave"
      @editing="cancelEditing"
      :model-value="modelValue"
      ref="editableInputRef"
    />
  </div>
</template>

<script>
import TextInput from "@/components/forms/inputs/text/TextInput.vue";
import TextValue from "@/components/forms/inputs/text/TextValue.vue";

export default {
  data() {
    return {
      editing: false,
      editedValue: null,
    };
  },
  components: {
    TextInput,
    TextValue,
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
      // this.editedValue = this.modelValue;
      // this.$nextTick(() => {
      //   this.$refs.editableInputRef.focus();
      // });
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