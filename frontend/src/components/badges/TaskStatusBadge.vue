<template>
  <div class="flex items-center gap-1" :title="statusTitle">
    <font-awesome-icon 
      :icon="statusIcon" 
      class="text-2xl" 
      :class="statusClass" 
    />
  </div>
</template>

<script>
export default {
  name: "TaskStatusBadge",
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  computed: {
    statusIcon() {
      // Prioridade: Cancelada > Concluída > Fazendo (com jornadas) > Status atual
      if (this.task.date_canceled) {
        return "fas fa-times-circle";
      }
      
      if (this.isValidDate(this.task.date_conclusion)) {
        return "fas fa-check-circle";
      }
      
      // Fazendo: tarefa sem conclusão, sem cancelamento, mas COM jornadas
      if (this.hasActiveJourneys) {
        return "fas fa-play-circle";
      }
      
      // Baseado no status da tarefa
      const statusIcons = {
        'wait': 'fas fa-pause-circle',
        'to-do': 'fas fa-circle-exclamation',
      };
      
      return statusIcons[this.task.status] || "fas fa-circle";
    },
    statusClass() {
      // Prioridade: Cancelada > Concluída > Fazendo (com jornadas) > Status atual
      if (this.task.date_canceled) {
        return "text-red-500";
      }
      
      if (this.isValidDate(this.task.date_conclusion)) {
        return "text-success";
      }
      
      // Fazendo: tarefa sem conclusão, sem cancelamento, mas COM jornadas
      if (this.hasActiveJourneys) {
        return "text-blue-500";
      }
      
      // Baseado no status da tarefa
      const statusClasses = {
        'wait': 'text-yellow-500',
        'to-do': 'text-gray-400',
      };
      
      return statusClasses[this.task.status] || "text-gray-400";
    },
    statusTitle() {
      // Prioridade: Cancelada > Concluída > Fazendo (com jornadas) > Status atual
      if (this.task.date_canceled) {
        return "Tarefa cancelada";
      }
      
      if (this.isValidDate(this.task.date_conclusion)) {
        return "Tarefa concluída";
      }
      
      // Fazendo: tarefa sem conclusão, sem cancelamento, mas COM jornadas
      if (this.hasActiveJourneys) {
        return "Tarefa em andamento (com tempo registrado)";
      }
      
      // Baseado no status da tarefa
      const statusTitles = {
        'wait': 'Tarefa aguardando',
        'to-do': 'Tarefa a fazer',
      };
      
      return statusTitles[this.task.status] || "Status indefinido";
    },
    hasActiveJourneys() {
      // Verifica se a tarefa tem jornadas relacionadas e não foi concluída/cancelada
      return (
        !this.task.date_canceled &&
        !this.isValidDate(this.task.date_conclusion) &&
        this.task.journeys &&
        Array.isArray(this.task.journeys) &&
        this.task.journeys.length > 0
      );
    },
  },
  methods: {
    isValidDate(date) {
      if (
        date != "1969-12-31 18:00:00" &&
        date != "1969-12-31 21:00:00" &&
        date != "1970-01-01 00:00:00" &&
        date != null
      ) {
        return true;
      }
      return false;
    },
  },
};
</script>