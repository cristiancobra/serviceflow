<template>
  <div class="main-container">
    <label class="form-label" :for="name">{{ label }}</label>
    <div v-if="!editing"  @click="startEditing">
      <div v-if="localValue !== null && localValue !== undefined && localValue !== ''">
        <p class="number-editable">
          <font-awesome-icon icon="edit" class="edit-icon" />
        {{ localValue }}
        </p>
      </div>
      <div v-else>
        <p class="text-center">
        não informado
        </p>
      </div>
    </div>
    <div v-else>
      <div class="">
        <input class="input-number" type="number" step="0.1" :name="name" 
        v-model="localValue" :placeholder="placeholder" @keydown.esc="cancelEditing" @blur="emitSave" @keydown.enter.prevent="emitSave" />
      </div>
    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      localValue: (this.modelValue !== null && this.modelValue !== undefined) ? parseFloat(this.modelValue).toFixed(2) : '',
    };
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
    },
    emitSave() {
      this.$emit("save", parseFloat(this.localValue).toFixed(2));
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.localValue = (this.modelValue !== null && this.modelValue !== undefined) ? parseFloat(this.modelValue).toFixed(2) : '';
    },
      
  },
  watch: {
    modelValue(newValue) {
      this.localValue = (newValue !== null && newValue !== undefined) ? parseFloat(newValue).toFixed(2) : '';
    },
  },
  mounted() {
    console.log("localValue", this.localValue);
  }
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