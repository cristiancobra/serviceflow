<template>
  <div class="main-container">
    <label class="form-label" :for="name">{{ label }}</label>

    <TextValue
      v-if="!editing"
      :id="name"
      :name="name"
      v-model="localValue"
      @click="startEditing"
    />

    <TextInput
      v-else
      @save="emitSave"
      @editing="cancelEditing"
      v-model="localValue"
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
      localValue: this.modelValue,
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
    status: String,
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
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
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

</style>