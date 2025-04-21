<template>
  <div class="timeline">
    <div
      class="timeline-item"
      v-for="(date, index) in timelineEvents"
      :key="index"
    >
      <div class="timeline-date">
        <p>{{ formatDateBr(date.value) }}</p>
      </div>
      <div class="timeline-content">
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
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      timelineEvents: [
        { label: "Criada", value: this.task.created_at },
        { label: "Rascunhada", value: this.task.draft_at },
        { label: "Enviada", value: this.task.submitted_at },
        { label: "Aceita", value: this.task.accepted_at },
        { label: "Rejeitada", value: this.task.rejected_at },
        { label: "Cancelada", value: this.task.canceled_at },
      ],
    };
  },
  methods: {
    formatDateBr,
  },
};
</script>

<style>
.timeline {
  display: flex;
  flex-direction: row; /* Alinha os itens horizontalmente */
  position: relative;
  margin: 20px 0;
  padding-top: 20px;
}

.timeline-item {
  display: flex;
  flex-direction: column; /* Alinha a data e o conteúdo verticalmente */
  align-items: center;
  margin-right: 40px; /* Espaçamento entre os itens */
  position: relative;
}

.timeline-item:last-child {
  margin-right: 0; /* Remove o espaçamento do último item */
}

.timeline-date {
  font-size: 14px;
  color: #666;
  margin-bottom: 10px; /* Espaçamento entre a data e o conteúdo */
}

.timeline-content {
  background: #f9f9f9;
  padding: 10px 15px;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.timeline-item::before {
  content: "";
  position: absolute;
  top: 50%; /* Centraliza verticalmente */
  left: -20px; /* Ajusta a posição horizontal */
  width: 12px;
  height: 12px;
  background: #007bff;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 2px #ccc;
}

.timeline-item:first-child::before {
  content: ""; /* Remove o ponto para o primeiro item */
}

.timeline::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background: #ccc;
  z-index: -1; /* Coloca a linha atrás dos itens */
}
</style>