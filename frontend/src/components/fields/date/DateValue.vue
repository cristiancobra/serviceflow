<template>
  <div v-if="convertedDateTime" class="container-date">
    <font-awesome-icon icon="fa-solid fa-calendar" class="icon" />
    <span class="default-text">
      {{ formatedDate }}
    </span>
    <font-awesome-icon icon="fa-solid fa-clock" class="icon" />
    <span class="default-text">
      {{ formatedTime }}
    </span>
  </div>
</template>


<script>
import { convertDateTimeToLocal, displayDate, displayTime } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      convertedDateTime: convertDateTimeToLocal(this.modelValue),
      formatedDate: '',
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
  },
  mounted() {
    if (this.modelValue != '1969-12-31 18:00:00' && this.modelValue != '1969-12-31 21:00:00' && this.modelValue != null) {
      this.formatedDate = displayDate(this.convertedDateTime);
      this.formatedTime = displayTime(this.convertedDateTime);
    } else {
      this.formatedDate = '__/__/____';
      this.formatedTime = '__:__';
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
  margin-left: 1rem;
}
</style>