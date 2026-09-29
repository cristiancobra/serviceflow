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
          <p class="text-base-content/50">{{ emptyMessage }}</p>
      </div>

      <div v-else class="overflow-x-auto">
          <!-- Header da tabela -->
          <div class="grid gap-4 px-4 py-2 bg-base-200 border-b border-base-300 font-semibold text-sm text-base-content/80" :class="gridClass">
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
              class="grid gap-4 px-4 py-0.5 border-b border-base-300 hover:bg-base-200 transition-colors items-center"
              :class="gridClass"
          >
              <div :class="titleColClass" class="flex items-center">
                  <a 
                      class="text-sm text-info font-semibold hover:underline truncate" 
                      :href="link.url" 
                      target="_blank"
                      :title="link.title"
                  >
                      {{ link.title }}
                  </a>
              </div>
              <div :class="urlColClass">
                  <p
                      class="text-sm text-base-content/70 truncate block"
                      :href="link.url" 
                      target="_blank"
                      :title="link.url"
                  >
                      {{ link.url }}
              </p>
              </div>
              <div :class="observationsColClass">
                  <span class="text-sm text-base-content/70 truncate block" :title="link.observations">
                      {{ link.observations || '-' }}
                  </span>
              </div>
              <div v-if="showTaskColumn" :class="taskColClass">
                  <span v-if="link.task" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-base-200 text-base-content truncate">
                      <font-awesome-icon icon="fa-solid fa-tasks" class="mr-1" />
                      {{ link.task.name }}
                  </span>
              </div>
              <div :class="actionsColClass" class="flex justify-center gap-2">
                  <delete-icon-button
                      size="w-5 h-5"
                      icon-size="text-[10px]"
                      title="Excluir link"
                      confirm-message="Tem certeza que deseja excluir este link?"
                      @confirm="$emit('delete-link', link.id)"
                  />
                  <button
                      class="w-5 h-5 flex items-center justify-center rounded-full bg-info text-white hover:bg-info transition-colors shadow-sm"
                      @click="$emit('copy-link', link.url)"
                      title="Copiar link"
                  >
                      <font-awesome-icon icon="fa-solid fa-copy" class="text-[10px]" />
                  </button>
              </div>
          </div>
      </div>
  </section>
</template>

<script>
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";

export default {
  name: 'TaskLinksList',
  components: {
      DeleteIconButton,
  },
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
      },
      emptyMessage: {
          type: String,
          default: 'Nenhum link de tarefa'
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
