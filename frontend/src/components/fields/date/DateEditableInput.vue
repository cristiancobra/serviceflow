<template>
  <div class="flex items-center whitespace-nowrap">
    <label v-if="label" class="form-label me-3" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing" class="cursor-pointer">
      <span class="default-text" :class="classText">
        {{ formatedDate }}
      </span>
      <font-awesome-icon icon="fa-solid fa-calendar" class="ms-2 me-1 text-gray-400" />
    </div>
    <VueDatePicker 
      v-else 
      class="form-control" 
      :id="name" 
      :name="name" 
      :label="label" 
      v-model="localValue"
      :placeholder="placeholder" 
      format="dd/MM/yyyy" 
      :enable-time-picker="false"
      @update:modelValue="handleDateChange"
    />
  </div>
</template>

<script>
import { displayDate } from "@/utils/date/dateUtils";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      editing: false,
      formatedDate: '',
      localValue: this.parseToDateObject(this.modelValue),
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
    // Converte string YYYY-MM-DD para objeto Date local (sem timezone)
    parseToDateObject(dateString) {
      if (!dateString) return null;
      
      const parts = dateString.toString().split('-');
      if (parts.length === 3) {
        // Criar um Date no meio do dia para evitar problemas de timezone
        return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
      }
      
      return null;
    },
    
    // Converte objeto Date para string YYYY-MM-DD (apenas data, sem timezone)
    dateObjectToString(dateObj) {
      if (!dateObj || !(dateObj instanceof Date)) return null;
      
      const padZero = (num) => num.toString().padStart(2, '0');
      const year = dateObj.getFullYear();
      const month = padZero(dateObj.getMonth() + 1);
      const day = padZero(dateObj.getDate());
      
      return `${year}-${month}-${day}`;
    },
    
    startEditing() {
      this.editing = true;
    },
    
    handleDateChange(newValue) {
      // newValue é um objeto Date retornado pelo VueDatePicker
      const dateString = this.dateObjectToString(newValue);
      this.localValue = newValue; // Manter o objeto Date no v-model
      this.emitSave(dateString);
    },
    
    emitSave(dateString = null) {
      const valueToSend = dateString || this.dateObjectToString(this.localValue);
      
      if (!valueToSend || !this.localValue) {
        this.$emit("save", null);
        console.log('Emitindo save com valor nulo');
      } else if (this.dateObjectToString(this.parseToDateObject(this.modelValue)) !== valueToSend) {
        this.$emit("save", valueToSend);
        console.log('Emitindo save:', valueToSend);
      }
      this.editing = false;
    },
    
    displayLocal() {
      if (this.localValue && this.localValue instanceof Date) {
        const dateString = this.dateObjectToString(this.localValue);
        
        if (dateString != '1969-12-31' && dateString != '1970-01-01') {
          this.formatedDate = displayDate(dateString);
        } else {
          this.formatedDate = '__/__/____';
        }
      } else {
        this.formatedDate = '__/__/____';
      }
    }
  },
  mounted() {
    this.displayLocal();
  },
  beforeUnmount() {
    // cleanup if needed
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.parseToDateObject(newValue);
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