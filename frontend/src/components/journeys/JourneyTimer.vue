<template>
  <span class="journey-timer">
    <font-awesome-icon icon="fa-solid fa-clock" class="journey-timer-icon" />
    {{ formatted }}
  </span>
</template>

<script>
import { mapGetters } from "vuex";
import { formatElapsedTime } from "@/utils/date/dateUtils";

export default {
  name: "JourneyTimer",
  props: {
    // Segundos já acumulados de jornadas fechadas da mesma tarefa, somados ao tempo ao vivo da jornada em execução
    baseSeconds: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    ...mapGetters(["openJourneyElapsedSeconds"]),
    formatted() {
      return formatElapsedTime(this.baseSeconds + this.openJourneyElapsedSeconds);
    },
  },
};
</script>

<style scoped>
.journey-timer {
  font-variant-numeric: tabular-nums;
  display: inline-flex;
  align-items: center;
  gap: 0.35em;
}

.journey-timer-icon {
  font-size: 1em;
  color: #b1b7c2;
  margin-top: -3px;
}
</style>
