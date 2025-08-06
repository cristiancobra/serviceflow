<template>
  <div v-if="convertedDate" class="container-date">
    <span class="default-text">
      {{ formatedDate }}
    </span>
  </div>
</template>


<script>
import { convertDateTimeToLocal, displayDate } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      convertedDate: convertDateTimeToLocal(this.modelValue),
      formatedDate: '',
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
      this.formatedDate = displayDate(this.convertedDate);
    } else {
      this.formatedDate = '';
    }
  },
};
</script>



<style scoped>
.container-date {
  display: flex;
  justify-content: end;
  padding-top: 0.4rem;
}

.icon {
  margin-right: 0.4rem;
  margin-left: 1rem;
  color: var(--primary);
}
</style>