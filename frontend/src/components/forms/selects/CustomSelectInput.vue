<template>
  <div class="mb-4 relative" v-click-outside="closeDropdown">
    <label class="block text-sm font-semibold text-base-content mb-2" :for="name">{{ label }}</label>
    
    <!-- Selected value display -->
    <div
      @click="toggleDropdown"
      :class="[
        'w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm cursor-pointer transition-all duration-200 ease-in-out hover:border-base-content/20',
        isOpen ? 'ring-2 ring-primary border-info' : '',
        disabled ? 'bg-base-200 text-base-content/60 cursor-not-allowed' : ''
      ]"
    >
      <div v-if="selectedItem" class="flex items-center gap-2">
        <!-- Avatar -->
        <component
          v-if="avatarType"
          :is="avatarComponent"
          v-bind="getAvatarProps(selectedItem)"
          size="sm"
        />
        <span>{{ displayItemText(selectedItem) }}</span>
      </div>
      <span v-else-if="fieldNull" class="text-base-content/70">{{ fieldNull }}</span>
      <span v-else class="text-base-content/50">{{ placeholder || 'Selecione...' }}</span>
      
      <!-- Arrow icon -->
      <font-awesome-icon
        :icon="isOpen ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-base-content/50 text-sm pointer-events-none"
        style="margin-top: 12px;"
      />
    </div>

    <!-- Dropdown options -->
    <transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
      >
        <!-- Search input -->
        <div class="sticky top-0 bg-base-100 p-2 border-b border-base-300">
          <input
            :value="searchQuery || ''"
            @input="searchQuery = $event.target.value"
            type="text"
            placeholder="Buscar..."
            class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            @click.stop
          />
        </div>

        <!-- Null option -->
        <div
          v-if="fieldNull"
          @click="selectItem(null)"
          class="px-3 py-2 hover:bg-base-200 cursor-pointer transition-colors text-base-content/70"
        >
          {{ fieldNull }}
        </div>
        
        <!-- Filtered Items -->
        <div
          v-for="item in filteredItems"
          :key="item.id"
          @click="selectItem(item)"
          :class="[
            'px-3 py-2 hover:bg-info/10 cursor-pointer transition-colors flex items-center gap-2',
            localValue === item.id ? 'bg-info/10' : ''
          ]"
        >
          <!-- Avatar -->
          <component
            v-if="avatarType"
            :is="avatarComponent"
            v-bind="getAvatarProps(item)"
            size="sm"
          />
          <span class="text-base-content">{{ displayItemText(item) }}</span>
        </div>

        <!-- Create new option -->
        <div
          v-if="searchQuery && searchQuery.trim() && filteredItems.length === 0 && allowCreateNew"
          @click="$emit('create-new', searchQuery.trim())"
          class="px-3 py-2 hover:bg-info/10 cursor-pointer transition-colors flex items-center gap-2 text-info font-medium border-t border-base-300"
        >
          <font-awesome-icon icon="fa-solid fa-plus" class="text-sm" />
          <span>Criar: {{ searchQuery.trim() }}</span>
        </div>

        <!-- No results message -->
        <div
          v-if="searchQuery && searchQuery.trim() && filteredItems.length === 0 && !allowCreateNew"
          class="px-3 py-4 text-center text-base-content/60"
        >
          Nenhum resultado encontrado
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import UserAvatar from "@/components/common/UserAvatar.vue";

export default {
  name: "CustomSelectInput",
  components: {
    CompanyAvatar,
    LeadAvatar,
    UserAvatar,
  },
  props: {
    label: String,
    name: String,
    items: Array,
    fieldsToDisplay: [String, Array],
    fieldNull: String,
    modelValue: [String, Number],
    placeholder: String,
    disabled: {
      type: Boolean,
      default: false,
    },
    avatarType: {
      type: String,
      validator: (value) => ['company', 'lead', 'user', null].includes(value),
      default: null
    },
    allowCreateNew: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      localValue: this.modelValue,
      isOpen: false,
      searchQuery: "",
    };
  },
  computed: {
    selectedItem() {
      if (!this.localValue) return null;
      return this.items.find(item => item.id === this.localValue);
    },
    avatarComponent() {
      const components = {
        company: 'CompanyAvatar',
        lead: 'LeadAvatar',
        user: 'UserAvatar'
      };
      return components[this.avatarType];
    },
    filteredItems() {
      if (!this.searchQuery || !this.searchQuery.trim()) {
        return this.items;
      }

      const query = this.searchQuery.toLowerCase();
      return this.items.filter(item => {
        const displayText = this.displayItemText(item).toLowerCase();
        return displayText.includes(query);
      });
    }
  },
  methods: {
    toggleDropdown() {
      if (!this.disabled) {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
          this.$nextTick(() => {
            const input = this.$el.querySelector('input[placeholder="Buscar..."]');
            if (input) input.focus();
          });
        } else {
          this.searchQuery = "";
        }
      }
    },
    closeDropdown() {
      this.isOpen = false;
      this.searchQuery = "";
    },
    selectItem(item) {
      console.log("CustomSelectInput selectItem:", item);
      if (item === null) {
        this.localValue = null;
      } else {
        this.localValue = item.id;
      }
      console.log("emitting update:modelValue with:", this.localValue);
      this.$emit("update:modelValue", this.localValue);
      this.closeDropdown();
    },
    displayItemText(item) {
      if (!item) return '';
      
      if (Array.isArray(this.fieldsToDisplay)) {
        const displayedValues = this.fieldsToDisplay.map(
          (field) => item[field]
        );
        const nonNullValues = displayedValues.filter(
          (value) => value !== null && value !== undefined
        );
        return nonNullValues.join(" - ");
      } else {
        return item[this.fieldsToDisplay];
      }
    },
    getAvatarProps(item) {
      if (!item) return {};
      
      const props = {
        companyId: null,
        leadId: null,
        userId: null,
      };

      switch (this.avatarType) {
        case 'company':
          return {
            ...props,
            photo: item.photo || null,
            businessName: item.photo ? item.business_name : null,
            legalName: item.photo ? item.legal_name : null,
          };
        case 'lead':
          return {
            ...props,
            photo: item.photo || null,
            name: item.photo ? item.name : null,
          };
        case 'user':
          return {
            ...props,
            photo: item.photo || null,
            name: item.photo ? item.name : null,
          };
        default:
          return props;
      }
    }
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
  mounted() {
    if (this.modelValue !== undefined) {
      this.localValue = this.modelValue;
    } else if (this.fieldNull) {
      this.localValue = null;
    }
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value();
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  }
};
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
