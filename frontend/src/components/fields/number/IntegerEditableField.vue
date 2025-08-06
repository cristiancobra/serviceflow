<template>
  <div>
    <label v-if="label" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <p class="number-editable">
          <font-awesome-icon icon="edit" class="edit-icon" />
          {{ localValue }}
        </p>
    </div>
    <div v-else>
      <input type="number" v-model="localValue" :placeholder="placeholder" @keydown.esc="cancelEditing" @blur="emitSave" @keydown.enter.prevent="emitSave" />
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      localValue: this.modelValue ? parseInt(this.modelValue, 10) : '',
    };
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
    emitSave() {
      this.$emit("save", parseInt(this.localValue, 10));
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = this.modelValue ? parseInt(this.modelValue, 10) : '';
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue ? parseInt(newValue, 10) : '';
    },
  },
};
</script>

<style scoped>
.edit-icon {
  display: none;
  margin-left: 5px;
  color: var(--green);
}

.number-editable:hover .edit-icon {
  display: inline;
}

.number-editable {
  cursor: pointer;
  color: var(--primary);
}
</style>