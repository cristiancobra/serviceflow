<template>
  <div class="mb-4 relative" v-click-outside="closeDropdown">
    <label class="block text-sm font-semibold text-gray-900 mb-2" :for="name">{{ label }}</label>
    
    <!-- Selected value display -->
    <div
      @click="toggleDropdown"
      :class="[
        'w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer transition-all duration-200 ease-in-out hover:border-gray-400',
        isOpen ? 'ring-2 ring-blue-500 border-blue-500' : '',
        disabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : ''
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
      <span v-else-if="fieldNull" class="text-gray-600">{{ fieldNull }}</span>
      <span v-else class="text-gray-400">{{ placeholder || 'Selecione...' }}</span>
      
      <!-- Arrow icon -->
      <font-awesome-icon
        :icon="isOpen ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm pointer-events-none"
        style="margin-top: 12px;"
      />
    </div>

    <!-- Dropdown options -->
    <transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
      >
        <!-- Null option -->
        <div
          v-if="fieldNull"
          @click="selectItem(null)"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer transition-colors text-gray-600"
        >
          {{ fieldNull }}
        </div>
        
        <!-- Items -->
        <div
          v-for="item in items"
          :key="item.id"
          @click="selectItem(item)"
          :class="[
            'px-3 py-2 hover:bg-blue-50 cursor-pointer transition-colors flex items-center gap-2',
            localValue === item.id ? 'bg-blue-100' : ''
          ]"
        >
          <!-- Avatar -->
          <component
            v-if="avatarType"
            :is="avatarComponent"
            v-bind="getAvatarProps(item)"
            size="sm"
          />
          <span class="text-gray-900">{{ displayItemText(item) }}</span>
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
    }
  },
  data() {
    return {
      localValue: this.modelValue,
      isOpen: false,
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
    }
  },
  methods: {
    toggleDropdown() {
      if (!this.disabled) {
        this.isOpen = !this.isOpen;
      }
    },
    closeDropdown() {
      this.isOpen = false;
    },
    selectItem(item) {
      if (item === null) {
        this.localValue = null;
      } else {
        this.localValue = item.id;
      }
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
    } else {
      this.localValue = '';
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
