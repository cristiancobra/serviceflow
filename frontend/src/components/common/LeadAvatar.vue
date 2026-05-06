<template>
  <component
    :is="leadId ? 'router-link' : 'div'"
    :to="leadId ? { name: 'leadShow', params: { id: leadId } } : null"
    :class="[
      'flex items-center justify-center rounded-full border-2',
      sizeClasses,
      overlap ? 'ml-[-10px]' : '',
      hasLeadData ? 'border-white' : 'border-gray-300 bg-gray-200',
      leadId ? 'cursor-pointer hover:opacity-80 transition-opacity' : '',
      customClass
    ]"
    :title="displayTitle"
  >
    <!-- Foto -->
    <img
      v-if="photo"
      :src="`${imagesPath}${photo}`"
      :alt="name"
      class="w-full h-full rounded-full object-cover"
    />
    
    <!-- Iniciais -->
    <div
      v-else-if="name"
      class="w-full h-full flex items-center justify-center text-white text-xs font-bold rounded-full"
      :style="{ backgroundColor: getInitialsColor(name) }"
    >
      {{ getInitials(name) }}
    </div>
    
    <!-- Ícone fallback -->
    <font-awesome-icon
      v-else
      icon="fa-solid fa-user"
      class="text-sm text-gray-500"
    />
  </component>
</template>

<script>
import { IMAGES_PATH } from "@/config/apiConfig";

export default {
  name: "LeadAvatar",
  props: {
    photo: {
      type: String,
      default: null
    },
    name: {
      type: String,
      default: null
    },
    leadId: {
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
    hasLeadData() {
      return this.photo || this.name;
    },
    displayTitle() {
      return this.name || "Lead sem nome";
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
