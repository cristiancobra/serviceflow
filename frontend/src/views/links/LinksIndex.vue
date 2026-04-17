<template>
  <div class="page-container">
      <div class="page-header">
          <div class="page-title">
              <font-awesome-icon icon="fa-solid fa-link" class="page-icon" />
              <h1>LINKS</h1>
          </div>
          <div class="page-action">
              <button-new-form 
                  target="link" 
                  @open-modal="isCreateLinkModalVisible = true" 
              />
              <link-create-form 
                  v-model="isCreateLinkModalVisible"
                  @new-link-event="addLinkCreated"
                  :task-id="0"
                  :opportunity-id="0"
              />
          </div>
      </div>

      <!-- LINKS SEM TAREFAS -->
      <section class="section-container">
          <div class="section-header">
              <div class="section-title">
                  <font-awesome-icon icon="fa-solid fa-link" class="icon" />
                  <h2>LINKS GERAIS</h2>
              </div>
          </div>
          
          <search-input v-model="searchTerm" placeholder="Digite para buscar links" />

          <div v-if="linksWithoutTask.length === 0" class="p-4 text-center">
              <p class="text-gray-500">Nenhum link geral</p>
          </div>

          <div v-else class="overflow-x-auto">
              <!-- Header da tabela -->
              <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-100 border-b border-gray-200 font-semibold text-sm text-gray-700">
                  <div class="col-span-3">Título</div>
                  <div class="col-span-3">URL</div>
                  <div class="col-span-4">Observações</div>
                  <div class="col-span-2 text-center">Ações</div>
              </div>
              
              <!-- Linhas da tabela -->
              <div 
                  v-for="link in linksWithoutTask" 
                  :key="link.id"
                  class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 hover:bg-gray-50 transition-colors items-center"
              >
                  <div class="col-span-3 flex items-center">
                      <font-awesome-icon icon="fa-solid fa-link" class="text-blue-500 mr-2" />
                      <a 
                          class="link-name hover:underline truncate" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.title"
                      >
                          {{ link.title }}
                      </a>
                  </div>
                  <div class="col-span-3">
                      <a 
                          class="link-url hover:underline text-gray-600 truncate block" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.url"
                      >
                          {{ link.url }}
                      </a>
                  </div>
                  <div class="col-span-4">
                      <span class="text-sm text-gray-600 truncate block" :title="link.observations">
                          {{ link.observations || '-' }}
                      </span>
                  </div>
                  <div class="col-span-2 flex justify-center gap-2">
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm"
                          @click="confirmDeleteLink(link.id)"
                          title="Excluir link"
                      >
                          <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
                      </button>
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors shadow-sm"
                          @click="copyLink(link.url)"
                          title="Copiar link"
                      >
                          <font-awesome-icon icon="fa-solid fa-copy" class="text-sm" />
                      </button>
                  </div>
              </div>
          </div>
      </section>

      <!-- LINKS DE TAREFAS -->
      <section class="section-container mt-6">
          <div class="section-header">
              <div class="section-title">
                  <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
                  <h2>LINKS DE TAREFAS</h2>
              </div>
          </div>

          <div v-if="linksWithTask.length === 0" class="p-4 text-center">
              <p class="text-gray-500">Nenhum link de tarefa</p>
          </div>

          <div v-else class="overflow-x-auto">
              <!-- Header da tabela -->
              <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-100 border-b border-gray-200 font-semibold text-sm text-gray-700">
                  <div class="col-span-3">Título</div>
                  <div class="col-span-2">URL</div>
                  <div class="col-span-3">Observações</div>
                  <div class="col-span-2">Tarefa</div>
                  <div class="col-span-2 text-center">Ações</div>
              </div>
              
              <!-- Linhas da tabela -->
              <div 
                  v-for="link in linksWithTask" 
                  :key="link.id"
                  class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 hover:bg-gray-50 transition-colors items-center"
              >
                  <div class="col-span-3 flex items-center">
                      <font-awesome-icon icon="fa-solid fa-link" class="text-blue-500 mr-2" />
                      <a 
                          class="link-name hover:underline truncate" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.title"
                      >
                          {{ link.title }}
                      </a>
                  </div>
                  <div class="col-span-2">
                      <a 
                          class="link-url hover:underline text-gray-600 truncate block" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.url"
                      >
                          {{ link.url }}
                      </a>
                  </div>
                  <div class="col-span-3">
                      <span class="text-sm text-gray-600 truncate block" :title="link.observations">
                          {{ link.observations || '-' }}
                      </span>
                  </div>
                  <div class="col-span-2">
                      <span v-if="link.task" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 truncate">
                          <font-awesome-icon icon="fa-solid fa-tasks" class="mr-1" />
                          {{ link.task.name }}
                      </span>
                  </div>
                  <div class="col-span-2 flex justify-center gap-2">
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm"
                          @click="confirmDeleteLink(link.id)"
                          title="Excluir link"
                      >
                          <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
                      </button>
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors shadow-sm"
                          @click="copyLink(link.url)"
                          title="Copiar link"
                      >
                          <font-awesome-icon icon="fa-solid fa-copy" class="text-sm" />
                      </button>
                  </div>
              </div>
          </div>
      </section>

      <!-- LINKS DE OPORTUNIDADES -->
      <section class="section-container mt-6">
          <div class="section-header">
              <div class="section-title">
                  <font-awesome-icon icon="fa-solid fa-bullseye" class="icon" />
                  <h2>LINKS DE OPORTUNIDADES</h2>
              </div>
          </div>

          <div v-if="linksWithOpportunity.length === 0" class="p-4 text-center">
              <p class="text-gray-500">Nenhum link de oportunidade</p>
          </div>

          <div v-else class="overflow-x-auto">
              <!-- Header da tabela -->
              <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-100 border-b border-gray-200 font-semibold text-sm text-gray-700">
                  <div class="col-span-3">Título</div>
                  <div class="col-span-2">URL</div>
                  <div class="col-span-3">Observações</div>
                  <div class="col-span-2">Oportunidade</div>
                  <div class="col-span-2 text-center">Ações</div>
              </div>
              
              <!-- Linhas da tabela -->
              <div 
                  v-for="link in linksWithOpportunity" 
                  :key="link.id"
                  class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 hover:bg-gray-50 transition-colors items-center"
              >
                  <div class="col-span-3 flex items-center">
                      <font-awesome-icon icon="fa-solid fa-link" class="text-blue-500 mr-2" />
                      <a 
                          class="link-name hover:underline truncate" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.title"
                      >
                          {{ link.title }}
                      </a>
                  </div>
                  <div class="col-span-2">
                      <a 
                          class="link-url hover:underline text-gray-600 truncate block" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.url"
                      >
                          {{ link.url }}
                      </a>
                  </div>
                  <div class="col-span-3">
                      <span class="text-sm text-gray-600 truncate block" :title="link.observations">
                          {{ link.observations || '-' }}
                      </span>
                  </div>
                  <div class="col-span-2">
                      <span v-if="link.opportunity" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 truncate">
                          <font-awesome-icon icon="fa-solid fa-bullseye" class="mr-1" />
                          {{ link.opportunity.name }}
                      </span>
                  </div>
                  <div class="col-span-2 flex justify-center gap-2">
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm"
                          @click="confirmDeleteLink(link.id)"
                          title="Excluir link"
                      >
                          <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
                      </button>
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors shadow-sm"
                          @click="copyLink(link.url)"
                          title="Copiar link"
                      >
                          <font-awesome-icon icon="fa-solid fa-copy" class="text-sm" />
                      </button>
                  </div>
              </div>
          </div>
      </section>

      <!-- LINKS DE PROJETOS -->
      <section class="section-container mt-6">
          <div class="section-header">
              <div class="section-title">
                  <font-awesome-icon icon="fa-solid fa-project-diagram" class="icon" />
                  <h2>LINKS DE PROJETOS</h2>
              </div>
          </div>

          <div v-if="linksWithProject.length === 0" class="p-4 text-center">
              <p class="text-gray-500">Nenhum link de projeto</p>
          </div>

          <div v-else class="overflow-x-auto">
              <!-- Header da tabela -->
              <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-100 border-b border-gray-200 font-semibold text-sm text-gray-700">
                  <div class="col-span-3">Título</div>
                  <div class="col-span-2">URL</div>
                  <div class="col-span-3">Observações</div>
                  <div class="col-span-2">Projeto</div>
                  <div class="col-span-2 text-center">Ações</div>
              </div>
              
              <!-- Linhas da tabela -->
              <div 
                  v-for="link in linksWithProject" 
                  :key="link.id"
                  class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 hover:bg-gray-50 transition-colors items-center"
              >
                  <div class="col-span-3 flex items-center">
                      <font-awesome-icon icon="fa-solid fa-link" class="text-blue-500 mr-2" />
                      <a 
                          class="link-name hover:underline truncate" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.title"
                      >
                          {{ link.title }}
                      </a>
                  </div>
                  <div class="col-span-2">
                      <a 
                          class="link-url hover:underline text-gray-600 truncate block" 
                          :href="link.url" 
                          target="_blank"
                          :title="link.url"
                      >
                          {{ link.url }}
                      </a>
                  </div>
                  <div class="col-span-3">
                      <span class="text-sm text-gray-600 truncate block" :title="link.observations">
                          {{ link.observations || '-' }}
                      </span>
                  </div>
                  <div class="col-span-2">
                      <span v-if="link.project" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 truncate">
                          <font-awesome-icon icon="fa-solid fa-project-diagram" class="mr-1" />
                          {{ link.project.name }}
                      </span>
                  </div>
                  <div class="col-span-2 flex justify-center gap-2">
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm"
                          @click="confirmDeleteLink(link.id)"
                          title="Excluir link"
                      >
                          <font-awesome-icon icon="fa-solid fa-trash-alt" class="text-sm" />
                      </button>
                      <button
                          class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 transition-colors shadow-sm"
                          @click="copyLink(link.url)"
                          title="Copiar link"
                      >
                          <font-awesome-icon icon="fa-solid fa-copy" class="text-sm" />
                      </button>
                  </div>
              </div>
          </div>
      </section>
  </div>
</template>

<script>
import { index, destroy } from "@/utils/requests/httpUtils";
import LinkCreateForm from "@/components/forms/LinkCreateForm.vue";
import SearchInput from "@/components/filters/SearchInput.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";

export default {
  components: {
      LinkCreateForm,
      SearchInput,
      ButtonNewForm,
  },
  data() {
      return {
          searchTerm: "",
          links: [],
          isCreateLinkModalVisible: false,
      };
  },
  computed: {
      filteredLinks() {
          if (!this.searchTerm) {
              return this.links;
          }
          return this.links.filter(link => 
              link.title.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
              link.url.toLowerCase().includes(this.searchTerm.toLowerCase())
          );
      },
      linksWithoutTask() {
          return this.filteredLinks.filter(link => !link.task_id && !link.project_id && !link.opportunity_id);
      },
      linksWithTask() {
          return this.filteredLinks.filter(link => link.task_id);
      },
      linksWithOpportunity() {
          return this.filteredLinks.filter(link => link.opportunity_id && !link.task_id && !link.project_id);
      },
      linksWithProject() {
          return this.filteredLinks.filter(link => link.project_id && !link.task_id && !link.opportunity_id);
      }
  },
  methods: {
      addLinkCreated(newLink) {
          this.isCreateLinkModalVisible = false;
          this.links.unshift(newLink);
      },
      async getLinks() {
          try {
              this.links = await index(`links`);
          } catch (error) {
              console.error("Erro ao acessar links:", error);
          }
      },
      async deleteLink(linkId) {
          try {
              await destroy("links", linkId);
              this.links = this.links.filter((link) => link.id !== linkId);
          } catch (error) {
              console.error("Erro ao deletar o link:", error);
          }
      },
      confirmDeleteLink(linkId) {
          if (window.confirm("Tem certeza que deseja excluir este link?")) {
              this.deleteLink(linkId);
          }
      },
      copyLink(url) {
          navigator.clipboard.writeText(url).then(() => {
              alert("Link copiado para a área de transferência!");
          }).catch(err => {
              console.error("Erro ao copiar link:", err);
          });
      },
  },
  mounted() {
      this.getLinks();
  },
};
</script>

<style scoped>
.link-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-color);
}

.link-url {
  font-size: 0.9rem;
  font-weight: 400;
  color: var(--secondary-color);
}
</style>
