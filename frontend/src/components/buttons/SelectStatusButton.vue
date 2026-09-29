<template>
  <div class="relative inline-block">
    <!-- Button -->
    <button
      @click="isOpen = !isOpen"
      :class="buttonClasses"
      class="inline-flex items-center justify-center px-4 py-2 rounded-full text-xs font-bold border-2 transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 min-w-[120px]"
    >
      {{ modelValue?.label || "definir situação" }}
      <svg 
        class="ml-2 h-4 w-4 transition-transform duration-200" 
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <ul 
        v-if="isOpen" 
        class="absolute z-10 mt-2 w-full min-w-[160px] bg-base-100 rounded-lg shadow-lg border border-base-300 py-2"
      >
        <li 
          v-for="item in items" 
          :key="item.value" 
          @click="selectStatus(item)"
          class="cursor-pointer"
        >
          <div 
            :class="getItemClasses(item.value)"
            class="mx-2 my-1 px-3 py-2 rounded-full text-xs font-bold border-2 text-center transition-all duration-150 hover:scale-105"
          >
            {{ item.label }}
          </div>
        </li>
      </ul>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isOpen: false,
      modelValue: null,
      items: [
        { value: "draft", label: "rascunho" },
        { value: "submitted", label: "enviada" },
        { value: "accepted", label: "aceita" },
        { value: "rejected", label: "rejeitada" },
        { value: "canceled", label: "cancelada" },
        { value: "paid", label: "paga" },
      ],
    };
  },
  props: {
    status: {
      type: String,
      required: true,
    },
  },
  computed: {
    buttonClasses() {
      const classes = {
        'draft': 'bg-base-200 text-base-content border-base-content/20 hover:bg-base-300 focus:ring-base-content/20',
        'submitted': 'bg-purple-100 text-purple-800 border-purple-400 hover:bg-purple-200 focus:ring-purple-400',
        'accepted': 'bg-success/10 text-success border-success hover:bg-success/10 focus:ring-success',
        'rejected': 'bg-error/10 text-error border-error hover:bg-error/10 focus:ring-error',
        'canceled': 'bg-warning/10 text-warning border-warning hover:bg-warning/10 focus:ring-warning',
        'paid': 'bg-info/10 text-info border-info hover:bg-info/10 focus:ring-primary',
      };
      return classes[this.modelValue?.value] || 'bg-base-200 text-base-content/70 border-base-300';
    },
  },
  methods: {
    selectStatus(status) {
      this.modelValue = status;
      this.isOpen = false;
      this.$emit("update:modelValue", status.value);
    },
    getItemClasses(value) {
      const classes = {
        'draft': 'bg-base-200 text-base-content border-base-content/20 hover:bg-base-300',
        'submitted': 'bg-purple-100 text-purple-800 border-purple-400 hover:bg-purple-200',
        'accepted': 'bg-success/10 text-success border-success hover:bg-success/10',
        'rejected': 'bg-error/10 text-error border-error hover:bg-error/10',
        'canceled': 'bg-warning/10 text-warning border-warning hover:bg-warning/10',
        'paid': 'bg-info/10 text-info border-info hover:bg-info/10',
      };
      return classes[value] || 'bg-base-200 text-base-content border-base-content/20';
    },
  },
  watch: {
    status(newStatus) {
      this.modelValue = this.items.find((item) => item.value === newStatus);
    },
  },
  mounted() {
    this.modelValue = this.items.find((item) => item.value === this.status);
  },
};
</script>

<style scoped>
/* Sem estilos personalizados - tudo em Tailwind! */
</style>