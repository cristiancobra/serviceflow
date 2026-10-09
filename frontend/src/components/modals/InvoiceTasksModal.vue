<template>
  <ModalCard
    title="Tarefas"
    :subtitle="invoiceName"
    icon="fa-solid fa-tasks"
    size="xl"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <div v-if="!invoice" class="p-5 text-center text-base-content/60">
      Carregando tarefas...
    </div>
    <tasks-list-section
      v-else
      :show-title="false"
      :tasks="invoice.tasks || []"
      :invoice="invoice"
      sortOrder="asc"
      @task-created="$emit('tasks-changed')"
    />
  </ModalCard>
</template>

<script>
import { mapState } from "vuex";
import { show } from "@/utils/requests/httpUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import TasksListSection from "@/components/lists/TasksListSection.vue";

export default {
  name: "InvoiceTasksModal",
  components: {
    ModalCard,
    TasksListSection,
  },
  props: {
    invoiceId: {
      type: [Number, String],
      required: true,
    },
    invoiceName: {
      type: String,
      default: "",
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // tasks-changed: tarefa criada ou alterada (quem abriu o modal recarrega a fatura)
  emits: ["close", "tasks-changed"],
  data() {
    return {
      invoice: null,
    };
  },
  computed: {
    // Alterações feitas no TaskDetailModal chegam pelo store (o App.vue repassa task-updated)
    ...mapState(["updatedTask"]),
  },
  watch: {
    updatedTask(task) {
      if (task && this.invoice?.tasks?.some((t) => t.id === task.id)) {
        this.$emit("tasks-changed");
      }
    },
  },
  async mounted() {
    this.invoice = await show("invoices", this.invoiceId);
  },
};
</script>
