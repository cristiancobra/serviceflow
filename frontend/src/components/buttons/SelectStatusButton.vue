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
        class="absolute z-10 mt-2 w-full min-w-[160px] bg-white rounded-lg shadow-lg border border-gray-200 py-2"
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
        'draft': 'bg-gray-100 text-gray-800 border-gray-400 hover:bg-gray-200 focus:ring-gray-400',
        'submitted': 'bg-purple-100 text-purple-800 border-purple-400 hover:bg-purple-200 focus:ring-purple-400',
        'accepted': 'bg-emerald-100 text-emerald-800 border-emerald-500 hover:bg-emerald-200 focus:ring-emerald-500',
        'rejected': 'bg-red-100 text-red-800 border-red-400 hover:bg-red-200 focus:ring-red-400',
        'canceled': 'bg-orange-100 text-orange-800 border-orange-400 hover:bg-orange-200 focus:ring-orange-400',
        'paid': 'bg-blue-100 text-blue-800 border-blue-500 hover:bg-blue-200 focus:ring-blue-500',
      };
      return classes[this.modelValue?.value] || 'bg-gray-100 text-gray-600 border-gray-300';
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
        'draft': 'bg-gray-100 text-gray-800 border-gray-400 hover:bg-gray-200',
        'submitted': 'bg-purple-100 text-purple-800 border-purple-400 hover:bg-purple-200',
        'accepted': 'bg-emerald-100 text-emerald-800 border-emerald-500 hover:bg-emerald-200',
        'rejected': 'bg-red-100 text-red-800 border-red-400 hover:bg-red-200',
        'canceled': 'bg-orange-100 text-orange-800 border-orange-400 hover:bg-orange-200',
        'paid': 'bg-blue-100 text-blue-800 border-blue-500 hover:bg-blue-200',
      };
      return classes[value] || 'bg-gray-100 text-gray-800 border-gray-400';
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