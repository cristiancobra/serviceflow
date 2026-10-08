<template>
  <ModalCard
    title="Jornadas"
    :subtitle="taskName"
    icon="fa-solid fa-clock"
    size="lg"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <div v-if="isLoading" class="p-5 text-center text-base-content/60">
      Carregando jornadas...
    </div>
    <journeys-list
      v-else
      :journeys="journeys"
      :paginationData="paginationData"
      :task-id="taskId"
      @journey-updated="emitTaskUpdated"
      @journey-deleted="emitTaskUpdated"
    />
  </ModalCard>
</template>

<script>
import axios from "axios";
import { mapState } from "vuex";
import { BACKEND_URL, JOURNEY_BY_TASK_URL_QUERY, TASK_URL_PARAMETER } from "@/config/apiConfig";
import ModalCard from "@/components/modals/ModalCard.vue";
import JourneysList from "@/components/lists/JourneysList.vue";

export default {
  name: "TaskJourneysModal",
  components: {
    ModalCard,
    JourneysList,
  },
  props: {
    taskId: {
      type: [Number, String],
      required: true,
    },
    taskName: {
      type: String,
      default: "",
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  // task-updated: duração da tarefa mudou (payload: tarefa recarregada); o App.vue repassa ao store
  emits: ["close", "task-updated"],
  data() {
    return {
      journeys: [],
      paginationData: null,
      isLoading: true,
    };
  },
  computed: {
    ...mapState(["openJourney"]),
  },
  methods: {
    async getJourneys() {
      try {
        const response = await axios.get(
          `${BACKEND_URL}${JOURNEY_BY_TASK_URL_QUERY}task_id=${this.taskId}`
        );
        this.journeys = response.data.data;
        this.paginationData = response.data;
      } catch (error) {
        console.error("Erro ao buscar jornadas da tarefa:", error);
      } finally {
        this.isLoading = false;
      }
    },
    async emitTaskUpdated() {
      try {
        const response = await axios.get(`${BACKEND_URL}${TASK_URL_PARAMETER}${this.taskId}`);
        this.$emit("task-updated", response.data.data);
      } catch (error) {
        console.error("Erro ao recarregar a tarefa:", error);
      }
    },
  },
  watch: {
    // Recarrega quando uma jornada é iniciada/parada fora deste modal (ex: botão do TaskDetailModal)
    openJourney() {
      this.getJourneys();
    },
  },
  mounted() {
    this.getJourneys();
  },
};
</script>
