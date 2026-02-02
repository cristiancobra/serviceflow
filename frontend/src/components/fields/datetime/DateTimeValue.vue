<template>
  <div v-if="convertedDateTime" class="container-date">
    <span class="default-text ps-1 pe-2"  :class="classText">
      {{ formatedDate }}
    </span>
    <font-awesome-icon icon="fa-solid fa-clock"  :class="localClassIcon"/>
    <span class="default-text ps-1" :class="classText">
      {{ formatedTime }}
    </span>
  </div>
</template>


<script>
import { displayDate, displayTime } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      convertedDateTime: this.modelValue,
      formatedDate: '',
      formatedTime: '',
      localClassIcon: '',
    };
  },
  props: {
    name: String,
    classText: String,
    classIcon: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    // convertDateTimeToLocal,
  },
  mounted() {
    if (this.modelValue != '1969-12-31 18:00:00'
        && this.modelValue != '1969-12-31 21:00:00'
        && this.modelValue != '1970-01-01 00:00:00'
        && this.modelValue != null
      ) {
      this.formatedDate = displayDate(this.convertedDateTime);
      this.formatedTime = displayTime(this.convertedDateTime);
    } else {
      this.formatedDate = '__/__/____';
      this.formatedTime = '__:__';
    }

    if(!this.classIcon)  {
      this.localClassIcon = 'icon';
    } else {
      this.localClassIcon = this.classIcon;
    }
  },
};
</script>



<style scoped>
.container-date {
  display: flex;
  justify-content: end;
}

.icon {
  margin-right: 0.4rem;
  margin-left: 1rem;
  color: var(--primary);
}
</style>