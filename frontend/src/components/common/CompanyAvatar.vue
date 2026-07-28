<template>
  <div class="relative inline-block group">
    <component
      :is="companyIdData && !editable ? 'router-link' : 'div'"
      :to="companyIdData && !editable ? { name: 'companyShow', params: { id: companyIdData } } : null"
      :class="[
        'flex items-center justify-center rounded-full border-2',
        sizeClasses,
        overlap ? 'ml-[-10px]' : '',
        hasCompanyData ? 'border-white' : 'border-gray-300 bg-gray-200',
        companyIdData && !editable ? 'cursor-pointer hover:opacity-80 transition-opacity' : '',
        customClass
      ]"
      :title="displayTitle"
    >
      <!-- Foto -->
      <img
        v-if="photoData"
        :src="`${imagesPath}${photoData}`"
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

    <!-- Ícone de edição (câmera) -->
    <div
      v-if="editable"
      class="absolute bottom-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity"
      @click.stop="triggerFileInput"
      :title="'Alterar foto'"
    >
      <div
        class="flex items-center justify-center rounded-full bg-blue-500 text-white cursor-pointer shadow-md"
        :class="editIconSizeClasses"
      >
        <font-awesome-icon icon="fa-solid fa-camera" class="text-xs" />
      </div>
    </div>

    <!-- Input de arquivo oculto -->
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      class="hidden"
      @change="handleFileChange"
    />
  </div>
</template>

<script>
import { IMAGES_PATH } from "@/config/apiConfig";

export default {
  name: "CompanyAvatar",
  props: {
    // Aceita um objeto Company completo (para convenience)
    company: {
      type: Object,
      default: null
    },
    // Ou dados individuais (para flexibility)
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
    },
    // Novo prop para permitir edição
    editable: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      // Estado para modo edição não é necessário - apenas usamos o prop
    };
  },
  computed: {
    imagesPath() {
      return IMAGES_PATH;
    },
    // Extrai dados do objeto company se fornecido, caso contrário usa as props individuais
    photoData() {
      return this.company?.photo || this.photo;
    },
    businessNameData() {
      return this.company?.business_name || this.businessName;
    },
    legalNameData() {
      return this.company?.legal_name || this.legalName;
    },
    companyIdData() {
      return this.company?.id || this.companyId;
    },
    companyName() {
      return this.businessNameData || this.legalNameData;
    },
    hasCompanyData() {
      return this.photoData || this.companyName;
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
    },
    editIconSizeClasses() {
      const sizes = {
        sm: "w-4 h-4",
        md: "w-5 h-5",
        lg: "w-6 h-6"
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
    },
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileChange(event) {
      const file = event.target.files?.[0];
      if (file) {
        // Emitir evento com o arquivo selecionado
        this.$emit("photo-change", file);
        
        // Limpar o input para permitir seleção do mesmo arquivo novamente
        event.target.value = "";
      }
    }
  }
};
</script>
