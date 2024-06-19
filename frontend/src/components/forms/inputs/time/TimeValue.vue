<template>
  <div v-if="convertedDateTime" class="container-date">
    <font-awesome-icon icon="fa-solid fa-clock" class="icon" />
    {{ formatedTime }}
  </div>
</template>


<script>
import { convertDateTimeToLocal, displayDate, displayTime } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      convertedDateTime: convertDateTimeToLocal(this.modelValue),
      formatedTime: '',
    };
  },
  props: {
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    convertDateTimeToLocal,
    displayDate,
    displayTime,
  },
  mounted() {
    if (this.modelValue != '1969-12-31 18:00:00' && this.modelValue != '1969-12-31 21:00:00' && this.modelValue != null) {
      this.formatedTime = displayTime(this.convertedDateTime);
    } else {
      this.formatedTime = '--:--';
    }
  },
};
</script>


<style scoped>
.container-date {
  display: flex;
  align-items: left;
}

.icon {
  margin-right: 0.4rem;
  color: var(--primary);
}

label {
  text-align: right;
}
</style>