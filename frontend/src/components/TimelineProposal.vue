<template>
  <div class="timeline">
    <!-- Linha 1: Datas -->
    <div class="timeline-dates">
      <div
        class="timeline-date"
        v-for="(date, index) in timelineEvents"
        :key="'date-' + index"
      >
        <p v-if="date.value">{{ formatDateBr(date.value) }}</p>
        <p v-else class="text-gray-400">---</p>
      </div>
    </div>

    <!-- Linha 2: Linha horizontal com círculos -->
    <div class="timeline-line">
      <div
        class="timeline-circle"
        v-for="(date, index) in timelineEvents"
        :key="'circle-' + index"
        :class="{ 'active': date.value }"
      ></div>
    </div>

    <!-- Linha 3: Labels -->
    <div class="timeline-labels">
      <div
        class="timeline-label"
        v-for="(date, index) in timelineEvents"
        :key="'label-' + index"
      >
        <p>{{ date.label }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDateBr } from "@/utils/date/dateUtils";

export default {
  name: "TimelineProposal",
  props: {
    proposal: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      timelineEvents: [
        { label: "Criada", value: this.proposal.created_at },
        { label: "Rascunhada", value: this.proposal.draft_at },
        { label: "Enviada", value: this.proposal.submitted_at },
        { label: "Aceita", value: this.proposal.accepted_at },
        { label: "Rejeitada", value: this.proposal.rejected_at },
        { label: "Cancelada", value: this.proposal.canceled_at },
      ],
    };
  },
  methods: {
    formatDateBr,
  },
  watch: {
    proposal: {
      immediate: true,
      handler(newProposal) {
        this.timelineEvents = [
          { label: "Criada", value: newProposal?.created_at || "" },
          { label: "Rascunhada", value: newProposal?.draft_at || "" },
          { label: "Enviada", value: newProposal?.submitted_at || "" },
          { label: "Aceita", value: newProposal?.accepted_at || "" },
          { label: "Rejeitada", value: newProposal?.rejected_at || "" },
          { label: "Cancelada", value: newProposal?.canceled_at || "" },
        ];
      },
    },
  },
};
</script>

<style scoped>
.timeline {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  width: 100%;
  padding: 20px 0;
}

.timeline-dates,
.timeline-line,
.timeline-labels {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.timeline-dates {
  margin-bottom: 10px;
}

.timeline-labels {
  margin-top: 10px;
}

.timeline-date,
.timeline-label {
  flex: 1;
  text-align: center;
  font-size: 0.875rem;
}

.timeline-date p,
.timeline-label p {
  margin: 0;
  padding: 0 5px;
}

.timeline-line {
  position: relative;
  height: 20px;
  margin: 10px 0;
}

.timeline-line::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 5%;
  right: 5%;
  height: 2px;
  background: #e5e7eb;
  transform: translateY(-50%);
}

.timeline-circle {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

.timeline-circle::after {
  content: "";
  width: 14px;
  height: 14px;
  background: #e5e7eb;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px #d1d5db;
  transition: all 0.3s ease;
}

.timeline-circle.active::after {
  background: #3b82f6;
  box-shadow: 0 0 0 2px #3b82f6;
  width: 16px;
  height: 16px;
}
</style>