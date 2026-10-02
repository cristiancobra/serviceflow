<template>
  <SectionCard title="Duração" icon="fas fa-hourglass-half">

    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-base-content/80 flex items-center">
          <font-awesome-icon icon="fas fa-file-contract" class="text-primary mr-2" />
          Horas Contratadas:
        </span>
        <span class="text-lg font-bold">
          {{ formatDuration(opportunity.contracted_hours || 0) }}
        </span>
      </div>

      <div>
        <progress
          class="progress progress-primary w-full"
          :value="barWidth(opportunity.contracted_hours || 0)"
          max="100"
        ></progress>
        <p class="text-xs text-right text-base-content/60">
          {{ hasContractedHours ? "100% (referência)" : "Nenhuma proposta aceita" }}
        </p>
      </div>

      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-base-content/80 flex items-center">
          <font-awesome-icon icon="fas fa-clock" class="text-primary mr-2" />
          Duração Total:
        </span>
        <span class="text-lg font-bold text-primary">
          {{ formatDuration(opportunity.duration_time) }}
        </span>
      </div>

      <div v-if="hasContractedHours">
        <progress
          class="progress w-full"
          :class="isOverContracted ? 'progress-error' : 'progress-primary'"
          :value="barWidth(opportunity.duration_time || 0)"
          max="100"
        ></progress>
        <p class="text-xs text-right" :class="isOverContracted ? 'text-error' : 'text-base-content/60'">
          {{ totalPercent }}% das horas contratadas
        </p>
      </div>
    </div>

    <ul v-if="durationByDepartment.length" class="mt-4 pt-4 border-t border-base-300 space-y-3">
      <li
        v-for="group in durationByDepartment"
        :key="group.department?.id ?? 'none'"
        class="text-sm"
      >
        <div class="flex items-center justify-between">
          <span class="flex items-center text-base-content/70">
            <font-awesome-icon
              :icon="`fa-solid ${group.department?.icon || 'fa-folder'}`"
              class="mr-2 w-4"
              :style="departmentColorStyle(group)"
            />
            {{ group.department?.name || "Sem departamento" }}
          </span>
          <span>
            <span class="text-xs text-base-content/60 mr-2">{{ percentOf(group.duration_time) }}%</span>
            <span class="font-semibold">{{ formatDuration(group.duration_time) }}</span>
          </span>
        </div>
        <progress
          class="progress w-full h-1.5"
          :style="departmentColorStyle(group)"
          :value="barWidth(group.duration_time)"
          max="100"
        ></progress>
      </li>
    </ul>
    <p v-if="durationByDepartment.length" class="text-xs text-base-content/50 mt-2">
      {{ hasContractedHours ? "Percentual sobre as horas contratadas." : "Percentual sobre a duração total (sem horas contratadas)." }}
    </p>
  </SectionCard>
</template>

<script>
import SectionCard from "@/components/common/SectionCard.vue";
import { formatDuration } from "@/utils/date/dateUtils";

export default {
  name: "OpportunityDurationSection",
  components: {
    SectionCard,
  },
  props: {
    opportunity: {
      type: Object,
      required: true,
    },
  },
  computed: {
    durationByDepartment() {
      return this.opportunity.duration_by_department || [];
    },
    hasContractedHours() {
      return (this.opportunity.contracted_hours || 0) > 0;
    },
    // Base dos percentuais: horas contratadas; sem elas, a própria duração total
    percentBase() {
      return this.hasContractedHours
        ? this.opportunity.contracted_hours
        : this.opportunity.duration_time || 0;
    },
    totalPercent() {
      return this.percentOf(this.opportunity.duration_time || 0);
    },
    isOverContracted() {
      return this.totalPercent > 100;
    },
  },
  methods: {
    formatDuration,
    // Todas as barras na mesma escala (o maior entre contratado e total = largura cheia),
    // para que estourar as horas contratadas apareça visualmente
    barWidth(seconds) {
      const scale = Math.max(this.opportunity.contracted_hours || 0, this.opportunity.duration_time || 0);
      if (!scale) return 0;
      return (seconds / scale) * 100;
    },
    percentOf(seconds) {
      if (!this.percentBase) return 0;
      return Math.round((seconds / this.percentBase) * 100);
    },
    // progress do DaisyUI pinta a barra com currentColor
    departmentColorStyle(group) {
      return group.department?.color ? { color: group.department.color } : {};
    },
  },
};
</script>
