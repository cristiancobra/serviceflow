<template>
  <div class="mb-1 main-container">
    <label class="form-label  ms-2" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <div class="container-date">
        <font-awesome-icon icon="fa-solid fa-calendar" class="icon" />
        {{ formatedDate }}
        <font-awesome-icon icon="fa-solid fa-clock" class="icon" />
        {{ formatedTime }}
      </div>
    </div>
    <VueDatePicker v-else class="form-control" :id="name" :name="name" :label="label" v-model="localValue"
      :placeholder="placeholder" @update:modelValue="emitSave" />
    <!-- <DateInput v-else @update:modelValue="emitSave" @editing="cancelEditing" v-model="localValue" ref="editableInputRef" /> -->
  </div>
</template>

<script>
import { convertDateTimeToLocal, convertDateTimeForServer, displayDate, displayTime } from "@/utils/date/dateUtils";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      // convertedDateTime: convertDateTimeToLocal(this.modelValue),
      editing: false,
      formatedDate: '',
      formatedTime: '',
      localValue: this.modelValue,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    convertDateTimeToLocal,
    convertDateTimeForServer,
    startEditing() {
      this.editing = true;
    },
    emitSave() {
      if (this.convertDateTimeToLocal(this.modelValue) !== this.localValue) {
        this.$emit("save", this.convertDateTimeForServer(this.localValue));
      }
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
    },
    displayLocal() {
      if (this.localValue != '1969-12-31 18:00:00' && this.localValue != '1969-12-31 21:00:00' && this.localValue != null) {
        const convertedDatetime = this.convertDateTimeToLocal(this.localValue)
        this.formatedDate = displayDate(convertedDatetime);
        this.formatedTime = displayTime(convertedDatetime);
      } else {
        this.formatedDate = '__/__/____';
        this.formatedTime = '__:__';
      }
    }
  },
  mounted() {
    this.displayLocal();
    document.addEventListener("keydown", this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.cancelEditing);
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
      this.displayLocal();
    },
  },
};
</script>

<style scoped>
.form-label {
  font-weight: 900;
  padding-right: 0rem;
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

.container-date {
  display: flex;
  align-items: left;
}

.icon {
  margin-right: 0.4rem;
  margin-left: 1rem;
  color: var(--primary);
}
</style>