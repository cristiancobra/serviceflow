<template>
  <div class="flex items-center whitespace-nowrap">
    <label v-if="label" class="form-label me-3" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <span class="default-text" :class="classText">
        {{ formatedDate }}
      </span>
      <font-awesome-icon icon="fa-solid fa-clock" class="ms-2 me-1 text-gray-400" />
      <span class="default-text" :class="classText">
        {{ formatedTime }}
      </span>
    </div>
    <VueDatePicker v-else class="form-control" :id="name" :name="name" :label="label" v-model="localValue"
      :placeholder="placeholder" @update:modelValue="emitSave" />
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
      localValue: this.convertDateTimeToLocal(this.modelValue),
    };
  },
  props: {
    classText: String,
    classIcon: String,
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
      if (!this.localValue) {
        // Se a data local for vazia, emita um valor null ou uma string vazia
        this.$emit("save", null);
        console.log('Emitindo save com valor nulo');
      } else if (this.convertDateTimeToLocal(this.modelValue) !== this.localValue) {
        this.$emit("save", this.convertDateTimeForServer(this.localValue));
        console.log('Emitindo save:', this.convertDateTimeForServer(this.localValue));
        console.log('LocalValue:', this.localValue);
      }
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
    },
    displayLocal() {
      if (this.localValue != '1969-12-31 18:00:00'
        && this.localValue != '1969-12-31 21:00:00'
        && this.localValue != '1970-01-01 00:00:00'
        && this.localValue != null
      ) {
        this.formatedDate = displayDate(this.localValue);
        this.formatedTime = displayTime(this.localValue);
      } else {
        this.localValue = null;
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
      this.localValue = this.convertDateTimeToLocal(newValue);
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

.show-label {
  text-align: left;
  font-weight: 800;
}
</style>