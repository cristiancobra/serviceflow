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
        <p v-else class="text-base-content/50">---</p>
      </div>
    </div>

    <!-- Linha 2: Linha horizontal com círculos -->
    <div class="timeline-line">
      <div
        class="timeline-circle"
        v-for="(date, index) in timelineEvents"
        :key="'circle-' + index"
        :class="[
          { 'active': date.value },
          date.circleClass
        ]"
      ></div>
    </div>

    <!-- Linha 3: Labels -->
    <div class="timeline-labels">
      <div
        class="timeline-label"
        v-for="(date, index) in timelineEvents"
        :key="'label-' + index"
      >
        <p :class="date.labelClass">{{ date.label }}</p>
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
        { 
          label: "Criada", 
          value: this.proposal.created_at,
          labelClass: "text-info font-semibold",
          circleClass: "circle-blue"
        },
        { 
          label: "Rascunhada", 
          value: this.proposal.draft_at,
          labelClass: "text-base-content/70 font-semibold",
          circleClass: "circle-gray"
        },
        { 
          label: "Enviada", 
          value: this.proposal.submitted_at,
          labelClass: "text-purple-700 font-semibold",
          circleClass: "circle-purple"
        },
        { 
          label: "Aceita", 
          value: this.proposal.accepted_at,
          labelClass: "text-success font-semibold",
          circleClass: "circle-green"
        },
        { 
          label: "Rejeitada", 
          value: this.proposal.rejected_at,
          labelClass: "text-error font-semibold",
          circleClass: "circle-red"
        },
        { 
          label: "Cancelada", 
          value: this.proposal.canceled_at,
          labelClass: "text-base-content/60 font-semibold",
          circleClass: "circle-gray"
        },
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
          { 
            label: "Criada", 
            value: newProposal?.created_at || "",
            labelClass: "text-info font-semibold",
            circleClass: "circle-blue"
          },
          { 
            label: "Rascunhada", 
            value: newProposal?.draft_at || "",
            labelClass: "text-base-content/70 font-semibold",
            circleClass: "circle-gray"
          },
          { 
            label: "Enviada", 
            value: newProposal?.submitted_at || "",
            labelClass: "text-purple-700 font-semibold",
            circleClass: "circle-purple"
          },
          { 
            label: "Aceita", 
            value: newProposal?.accepted_at || "",
            labelClass: "text-success font-semibold",
            circleClass: "circle-green"
          },
          { 
            label: "Rejeitada", 
            value: newProposal?.rejected_at || "",
            labelClass: "text-error font-semibold",
            circleClass: "circle-red"
          },
          { 
            label: "Cancelada", 
            value: newProposal?.canceled_at || "",
            labelClass: "text-base-content/60 font-semibold",
            circleClass: "circle-gray"
          },
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
  background: var(--color-base-300);
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
  background: var(--color-base-300);
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px var(--color-base-300);
  transition: all 0.3s ease;
}

/* Círculos coloridos quando ativos */
.timeline-circle.active.circle-blue::after {
  background: var(--color-info);
  box-shadow: 0 0 0 2px var(--color-info);
  width: 16px;
  height: 16px;
}

.timeline-circle.active.circle-gray::after {
  background: #6b7280;
  box-shadow: 0 0 0 2px color-mix(in oklab, var(--color-base-content) 60%, transparent);
  width: 16px;
  height: 16px;
}

.timeline-circle.active.circle-purple::after {
  background: #7c3aed;
  box-shadow: 0 0 0 2px #7c3aed;
  width: 16px;
  height: 16px;
}

.timeline-circle.active.circle-green::after {
  background: var(--color-success);
  box-shadow: 0 0 0 2px var(--color-success);
  width: 16px;
  height: 16px;
}

.timeline-circle.active.circle-red::after {
  background: var(--color-error);
  box-shadow: 0 0 0 2px var(--color-error);
  width: 16px;
  height: 16px;
}
</style>