<template>
  <component
    :is="companyId ? 'router-link' : 'div'"
    :to="companyId ? { name: 'companyShow', params: { id: companyId } } : null"
    :class="[
      'flex items-center justify-center rounded-full border-2',
      sizeClasses,
      overlap ? 'ml-[-10px]' : '',
      hasCompanyData ? 'border-white' : 'border-gray-300 bg-gray-200',
      companyId ? 'cursor-pointer hover:opacity-80 transition-opacity' : '',
      customClass
    ]"
    :title="displayTitle"
  >
    <!-- Foto -->
    <img
      v-if="photo"
      :src="`${imagesPath}${photo}`"
      :alt="companyName"
      class="w-full h-full rounded-full object-cover"
    />
    
    <!-- Iniciais -->
    <div
      v-else-if="companyName"
      class="w-full h-full flex items-center justify-center text-white text-xs font-bold rounded-full"
      :style="{ backgroundColor: getInitialsColor(companyName) }"
    >
      {{ getInitials(companyName) }}
    </div>
    
    <!-- Ícone fallback -->
    <font-awesome-icon
      v-else
      icon="fa-solid fa-briefcase"
      class="text-sm text-gray-500"
    />
  </component>
</template>

<script>
import { IMAGES_PATH } from "@/config/apiConfig";

export default {
  name: "CompanyAvatar",
  props: {
    photo: {
      type: String,
      default: null
    },
    businessName: {
      type: String,
      default: null
    },
    legalName: {
      type: String,
      default: null
    },
    companyId: {
      type: Number,
      default: null
    },
    size: {
      type: String,
      default: "md",
      validator: (value) => ["sm", "md", "lg"].includes(value)
    },
    overlap: {
      type: Boolean,
      default: false
    },
    customClass: {
      type: String,
      default: ""
    }
  },
  computed: {
    imagesPath() {
      return IMAGES_PATH;
    },
    companyName() {
      return this.businessName || this.legalName;
    },
    hasCompanyData() {
      return this.photo || this.companyName;
    },
    displayTitle() {
      return this.companyName || "Empresa sem nome";
    },
    sizeClasses() {
      const sizes = {
        sm: "w-6 h-6",
        md: "w-8 h-8",
        lg: "w-10 h-10"
      };
      return sizes[this.size];
    }
  },
  methods: {
    getInitials(name) {
      if (!name) return "??";
      const words = name.trim().split(" ").filter((word) => word.length > 0);
      if (words.length === 0) return "??";
      if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
      return (words[0][0] + words[words.length - 1][0]).toUpperCase();
    },
    getInitialsColor(name) {
      if (!name) return "#94a3b8";
      const colors = [
        "#ef4444", "#f97316", "#f59e0b", "#84cc16",
        "#10b981", "#14b8a6", "#06b6d4", "#3b82f6",
        "#6366f1", "#8b5cf6", "#a855f7", "#ec4899"
      ];
      let hash = 0;
      for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
      }
      return colors[Math.abs(hash) % colors.length];
    }
  }
};
</script>
