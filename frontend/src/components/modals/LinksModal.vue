<template>
  <div
    class="modal-panel bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto"
    :class="compact ? 'max-w-2xl' : 'max-w-6xl'"
  >
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-50 to-blue-25 border-b border-gray-200 px-8 py-6 z-10">
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
              <font-awesome-icon icon="fa-solid fa-link" class="text-3xl text-primary" />
              <h1 class="text-2xl font-bold text-gray-800">LINKS</h1>
            </div>
            <div class="flex items-center gap-2">
              <button-new-form
                  target="link"
                  @open-modal="openCreateLinkModal"
              />
              <close-button @click="closeModal" />
            </div>
          </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-6">
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
                              class="text-blue-600 font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-3">
                          <a
                              class="text-sm text-gray-600 hover:underline truncate block"
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
                          <delete-icon-button
                              size="w-8 h-8"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
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
          <task-links-list
              :links="linksWithTask"
              container-class="mt-6"
              @delete-link="deleteLink"
              @copy-link="copyLink"
          />

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
                              class="text-blue-600 font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-2">
                          <a
                              class="text-sm text-gray-600 hover:underline truncate block"
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
                          <delete-icon-button
                              size="w-8 h-8"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
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
                              class="text-blue-600 font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-2">
                          <a
                              class="text-sm text-gray-600 hover:underline truncate block"
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
                          <delete-icon-button
                              size="w-8 h-8"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
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
      </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index, destroy } from "@/utils/requests/httpUtils";
import SearchInput from "@/components/filters/SearchInput.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";
import TaskLinksList from "@/components/lists/TaskLinksList.vue";
import CloseButton from "@/components/buttons/CloseButton.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";

export default {
  name: "LinksModal",
  components: {
      SearchInput,
      ButtonNewForm,
      TaskLinksList,
      CloseButton,
      DeleteIconButton,
  },
  props: {
      compact: {
          type: Boolean,
          default: false,
      },
  },
  emits: ["close"],
  data() {
      return {
          searchTerm: "",
          links: [],
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
      ...mapMutations(["openModal"]),
      openCreateLinkModal() {
          this.openModal({
              component: "LinkCreateForm",
              props: { taskId: 0, opportunityId: 0 },
              listeners: {
                  "new-link-event": this.addLinkCreated,
              },
          });
      },
      addLinkCreated(newLink) {
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
      copyLink(url) {
          navigator.clipboard.writeText(url).then(() => {
              alert("Link copiado para a área de transferência!");
          }).catch(err => {
              console.error("Erro ao copiar link:", err);
          });
      },
      closeModal() {
          this.$emit("close");
      },
  },
  mounted() {
      this.getLinks();
  },
};
</script>

<style scoped>
.modal-panel {
  animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
