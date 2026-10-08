<template>
  <div>
    <DateTimeEditableInput :key="inputKey" :name="name" :label="label" :modelValue="modelValue"
      :classText="classText" :date-only="dateOnly" @save="onSave" />

    <!-- Adiamento: pede o motivo antes de salvar -->
    <div v-if="pendingDate" class="mt-3 rounded-lg border border-warning/40 bg-warning/10 p-3">
      <p class="text-sm font-semibold mb-2">
        Adiar de {{ formatDate(modelValue) }} para {{ formatDate(pendingDate) }}. Por quê?
      </p>
      <div class="flex flex-wrap gap-2 mb-2">
        <button v-for="item in reasons" :key="item.value" type="button" class="btn btn-sm"
          :class="reason === item.value ? 'btn-primary' : 'btn-ghost border-base-300'" @click="reason = item.value">
          {{ item.label }}
        </button>
      </div>
      <textarea v-model="note" class="textarea textarea-sm w-full mb-2" rows="2" maxlength="500"
        :placeholder="reason === 'other' ? 'Descreva o motivo (obrigatório)' : 'Observação (opcional)'" />
      <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost btn-sm" @click="cancel">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" :disabled="!canConfirm" @click="confirm">
          Adiar prazo
        </button>
      </div>
    </div>

    <!-- Histórico do prazo -->
    <div v-if="changes.length" class="mt-2 text-xs text-base-content/60">
      <button type="button" class="hover:text-primary" @click="showHistory = !showHistory">
        Prazo original: {{ formatDate(changes[0].previous_date) }}
        <template v-if="postponedCount"> · adiado {{ postponedCount }}x</template>
        <font-awesome-icon :icon="showHistory ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="ms-1" />
      </button>
      <ul v-if="showHistory" class="mt-2 flex flex-col gap-1">
        <li v-for="change in changes" :key="change.id">
          <span class="font-semibold text-base-content/80">
            {{ formatDate(change.previous_date) }} → {{ change.new_date ? formatDate(change.new_date) : 'sem prazo' }}
          </span>
          <span v-if="change.reason_label"> · {{ change.reason_label }}</span>
          <span v-if="change.note"> · "{{ change.note }}"</span>
          <span class="block">
            {{ change.user?.name || '—' }} em {{ formatDate(change.created_at, true) }}
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";
import { displayDate, displayTime } from "@/utils/date/dateUtils";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";

// Motivos vêm do backend uma vez por sessão
let reasonsCache = null;

/**
 * Campo de prazo (date_due) de tarefa/oportunidade. Ao adiar um prazo já
 * definido pede o motivo antes de salvar e mostra o histórico de alterações.
 * Emite save com o payload pronto para o PUT:
 * { date_due, date_due_change_reason?, date_due_change_note? }
 */
export default {
  name: "DueDateEditableInput",
  components: {
    DateTimeEditableInput,
  },
  props: {
    modelValue: [String, Number],
    name: { type: String, default: "date_due" },
    label: String,
    classText: String,
    // Histórico vindo do resource (due_date_changes)
    changes: { type: Array, default: () => [] },
    // Coluna só de data (oportunidade): valores "YYYY-MM-DD", sem hora
    dateOnly: { type: Boolean, default: false },
  },
  emits: ["save"],
  data() {
    return {
      reasons: reasonsCache || [],
      pendingDate: null,
      reason: null,
      note: "",
      inputKey: 0,
      showHistory: false,
    };
  },
  computed: {
    canConfirm() {
      return this.reason && (this.reason !== "other" || this.note.trim());
    },
    postponedCount() {
      return this.changes.filter(c => c.new_date && this.compare(c.new_date, c.previous_date) > 0).length;
    },
  },
  methods: {
    formatDate(value, withTime = !this.dateOnly) {
      if (!value) return "sem prazo";
      return withTime ? `${displayDate(value)} ${displayTime(value)}` : displayDate(value);
    },
    // > 0 quando a for depois de b
    compare(a, b) {
      // Só data: "YYYY-MM-DD" compara como texto, sem passar por Date (que leria como UTC)
      if (this.dateOnly) {
        return String(a).slice(0, 10).localeCompare(String(b).slice(0, 10));
      }
      return new Date(a) - new Date(b);
    },
    onSave(value) {
      if (this.modelValue && value && this.compare(value, this.modelValue) > 0) {
        this.pendingDate = value;
        this.reason = null;
        this.note = "";
        this.loadReasons();
        return;
      }
      this.$emit("save", { [this.name]: value });
    },
    confirm() {
      this.$emit("save", {
        [this.name]: this.pendingDate,
        date_due_change_reason: this.reason,
        date_due_change_note: this.note.trim() || null,
      });
      this.pendingDate = null;
    },
    cancel() {
      this.pendingDate = null;
      // Recria o input para voltar a exibir o prazo salvo
      this.inputKey++;
    },
    async loadReasons() {
      if (this.reasons.length) return;
      try {
        const response = await axios.get(`${BACKEND_URL}due-date-change-reasons`);
        reasonsCache = response.data.data;
        this.reasons = reasonsCache;
      } catch (error) {
        console.error("Erro ao buscar motivos de adiamento:", error);
      }
    },
  },
};
</script>
