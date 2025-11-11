<template>
  <button 
    type="button"
    :class="buttonClasses"
    @click="$emit('click')"
    :disabled="disabled"
  >
    <font-awesome-icon 
      v-if="icon" 
      :icon="icon" 
      :class="iconClasses"
    />
    <slot></slot>
  </button>
</template>

<script>
export default {
  name: 'ButtonNew',
  props: {
    icon: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    size: {
      type: String,
      default: 'default',
      validator: (value) => ['small', 'default', 'large'].includes(value)
    }
  },
  emits: ['click'],
  computed: {
    buttonClasses() {
      const baseClasses = [
        'inline-flex',
        'items-center',
        'justify-center',
        'text-center',
        'font-black',
        'text-white',
        'bg-primary-500',
        'hover:bg-primary-200',
        'hover:text-primary-500',
        'transition-all',
        'duration-200',
        'border-0',
        'rounded-full',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-primary-500',
        'focus:ring-opacity-50',
        'shadow-lg',
        'hover:shadow-xl'
      ]

      // Adicionar classes de tamanho
      if (this.size === 'small') {
        baseClasses.push('px-2', 'py-2', 'text-sm')
      } else if (this.size === 'large') {
        baseClasses.push('px-6', 'py-4', 'text-lg')
      } else {
        baseClasses.push('px-2.5', 'py-2.5', 'text-base')
      }

      // Estado desabilitado
      if (this.disabled) {
        baseClasses.push('opacity-50', 'cursor-not-allowed')
      } else {
        baseClasses.push('cursor-pointer')
      }

      return baseClasses
    },
    iconClasses() {
      return this.$slots.default ? 'mr-2' : ''
    }
  }
}
</script>