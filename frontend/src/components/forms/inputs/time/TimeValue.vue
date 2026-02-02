<template>
  <div v-if="convertedDateTime" class="m-0 p-0">
    <font-awesome-icon icon="fa-solid fa-clock" class="" />
    {{ formatedTime }}
  </div>
</template>


<script>
import { displayDate, displayTime } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      convertedDateTime: this.modelValue,
      formatedTime: '',
    };
  },
  props: {
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    // convertDateTimeToLocal,
    displayDate,
    displayTime,
    useDisplayTime(datetime) {
      if (datetime != '1969-12-31 18:00:00' && datetime != '1969-12-31 21:00:00' && datetime != null) {
      this.formatedTime = displayTime(this.convertedDateTime);
    } else {
      this.formatedTime = '--:--';
    }
  },
  },
  watch: {
    modelValue(newValue) {
      this.convertedDateTime = newValue;
      if (newValue != '1969-12-31 18:00:00' && newValue != '1969-12-31 21:00:00' && newValue != null) {
        this.formatedTime = displayTime(this.convertedDateTime);
      } else {
        this.formatedTime = '--:--';
      }  
    },
  },
  mounted() {
    this.useDisplayTime(this.modelValue);
  },
};
</script>