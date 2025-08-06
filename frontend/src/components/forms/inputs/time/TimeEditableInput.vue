<template>
  <div>
    <label v-if="label" class="ms-2" :for="name">{{ label }}</label>
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
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
      console.log('timeeditabela', newValue);
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