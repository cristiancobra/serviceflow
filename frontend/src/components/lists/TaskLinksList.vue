<template>
  <section class="" :class="containerClass">
      <div v-if="showHeader" class="section-header">
          <div class="section-title">
              <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
              <h2>LINKS DE TAREFAS</h2>
          </div>
          <div class="section-action">
              <slot name="action"></slot>
          </div>
      </div>

      <div v-if="links.length === 0" class="p-4 text-center">
          <p class="text-gray-500">Nenhum link de tarefa</p>
      </div>

      <div v-else class="overflow-x-auto">
          <!-- Header da tabela -->
          <div class="grid gap-4 px-4 py-3 bg-gray-100 border-b border-gray-200 font-semibold text-sm text-gray-700" :class="gridClass">
              <div :class="titleColClass">Título</div>
              <div :class="urlColClass">URL</div>
              <div :class="observationsColClass">Observações</div>
              <div v-if="showTaskColumn" :class="taskColClass">Tarefa</div>
              <div :class="actionsColClass">Ações</div>
          </div>
          
          <!-- Linhas da tabela -->
          <div 
              v-for="link in links" 
              :key="link.id"
              class="grid gap-4 px-4 py-1 border-b border-gray-200 hover:bg-gray-50 transition-colors items-center"
              :class="gridClass"
          >
              <div :class="titleColClass" class="flex items-center">
                  <font-awesome-icon icon="fa-solid fa-link" class="text-blue-500 mr-2" />
                  <a 
                      class="text-blue-600 font-semibold hover:underline truncate" 
                      :href="link.url" 
                      target="_blank"
                      :title="link.title"
                  >
                      {{ link.title }}
                  </a>
              </div>
              <div :class="urlColClass">
                  <p 
                      class="text-sm text-gray-600 truncate block" 
                      :href="link.url" 
                      target="_blank"
                      :title="link.url"
                  >
                      {{ link.url }}
              </p>
              </div>
              <div :class="observationsColClass">
                  <span class="text-sm text-gray-600 truncate block" :title="link.observations">
                      {{ link.observations || '-' }}
                  </span>
              </div>
              <div v-if="showTaskColumn" :class="taskColClass">
                  <span v-if="link.task" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 truncate">
                      <font-awesome-icon icon="fa-solid fa-tasks" class="mr-1" />
                      {{ link.task.name }}
                  </span>
              </div>
              <div :class="actionsColClass" class="flex justify-center gap-2">
                  <button
                      class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm"
                      @click="$emit('delete-link', link.id)"
                      title="Excluir link"
                  >
                      <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
                  </button>
                  <button
                      class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors shadow-sm"
                      @click="$emit('copy-link', link.url)"
                      title="Copiar link"
                  >
                      <font-awesome-icon icon="fa-solid fa-copy" class="text-sm" />
                  </button>
              </div>
          </div>
      </div>
  </section>
</template>

<script>
export default {
  name: 'TaskLinksList',
  props: {
      links: {
          type: Array,
          required: true,
          default: () => []
      },
      showHeader: {
          type: Boolean,
          default: true
      },
      showTaskColumn: {
          type: Boolean,
          default: true
      },
      containerClass: {
          type: String,
          default: ''
      }
  },
  computed: {
      gridClass() {
          return this.showTaskColumn ? 'grid-cols-12' : 'grid-cols-10';
      },
      titleColClass() {
          return 'col-span-3';
      },
      urlColClass() {
          return 'col-span-2';
      },
      observationsColClass() {
          return 'col-span-3';
      },
      taskColClass() {
          return 'col-span-2';
      },
      actionsColClass() {
          return 'col-span-2 text-center';
      }
  },
  emits: ['delete-link', 'copy-link']
};
</script>

<style scoped>
/* Estilos removidos - usando Tailwind classes inline */
</style>
