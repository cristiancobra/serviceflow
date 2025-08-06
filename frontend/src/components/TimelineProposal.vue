<template>
  <div class="">
    <!-- Linha 1: Datas -->
    <div class="timeline-dates">
      <div
        class="timeline-date"
        v-for="(date, index) in timelineEvents"
        :key="index"
      >
        <p v-if="date.value">{{ formatDateBr(date.value) }}</p>
        <p v-else>---</p>
      </div>
    </div>

    <!-- Linha 2: Linha horizontal com círculos -->
    <div class="timeline-line">
      <div
        class="timeline-circle"
        v-for="(date, index) in timelineEvents"
        :key="index"
      ></div>
    </div>

    <!-- Linha 3: Labels -->
    <div class="timeline-labels">
      <div
        class="timeline-label"
        v-for="(date, index) in timelineEvents"
        :key="index"
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

<style>
.timeline {
  display: flex;
  flex-direction: column; /* Organiza as linhas verticalmente */
  align-items: center; /* Centraliza os itens horizontalmente */
  width: 100%; /* Garante que a timeline ocupe toda a largura */
  margin: 20px 0;
}

.timeline-dates,
.timeline-line,
.timeline-labels {
  display: flex;
  justify-content: space-between; /* Distribui os itens uniformemente */
  align-items: center; /* Centraliza os itens verticalmente */
  width: 100%; /* Cada linha ocupa toda a largura */
  margin-bottom: 20px; /* Espaçamento entre as linhas */
}

.timeline-date,
.timeline-label {
  text-align: center;
  flex: 1; /* Faz com que os itens tenham o mesmo tamanho */
}

.timeline-line {
  position: relative;
  display: flex;
  justify-content: space-between; /* Distribui os círculos uniformemente */
  align-items: center;
  width: 100%;
  margin-bottom: 20px;
  text-align: center;
}

.timeline-line::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background: #ccc; /* Linha horizontal */
  z-index: -1; /* Coloca a linha atrás dos círculos */
}

.timeline-circle {
  width: 12px;
  height: 12px;
  background: #007bff;
  border-radius: 50%; /* Faz o círculo */
  border: 2px solid #fff;
  box-shadow: 0 0 0 2px #ccc;
  z-index: 1; /* Garante que os círculos fiquem acima da linha */
}
</style>