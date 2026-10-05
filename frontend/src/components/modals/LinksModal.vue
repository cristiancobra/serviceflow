<template>
  <ModalCard
    title="LINKS"
    :subtitle="taskName"
    icon="fa-solid fa-link"
    size="xl"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="closeModal"
  >
          <div class="flex justify-end mb-4">
              <button-new-form
                  target="link"
                  @open-modal="openCreateLinkModal"
              />
          </div>

          <!-- LINKS DA TAREFA (quando aberto a partir de uma tarefa) -->
          <section v-if="taskId" class="mb-6">
              <section-header title="LINKS DESTA TAREFA" icon="fa-solid fa-check-circle" />
              <task-links-list
                  :links="linksOfCurrentTask"
                  :show-header="false"
                  :show-task-column="false"
                  @delete-link="deleteLink"
                  @copy-link="copyLink"
              />
          </section>

          <!-- LINKS DA OPORTUNIDADE DA TAREFA -->
          <section v-if="opportunityId" class="mb-6">
              <section-header title="LINKS DESTA OPORTUNIDADE" icon="fa-solid fa-bullseye" />
              <task-links-list
                  :links="linksOfCurrentOpportunity"
                  empty-message="Nenhum link desta oportunidade"
                  :show-header="false"
                  :show-task-column="false"
                  @delete-link="deleteLink"
                  @copy-link="copyLink"
              />
          </section>

          <!-- LINKS SEM TAREFAS -->
          <section class="mb-6">
              <section-header title="LINKS GERAIS" icon="fa-solid fa-link" />

              <search-input v-model="searchTerm" placeholder="Digite para buscar links" />

              <div v-if="linksWithoutTask.length === 0" class="p-4 text-center">
                  <p class="text-base-content/60">Nenhum link geral</p>
              </div>

              <div v-else class="overflow-x-auto">
                  <!-- Header da tabela -->
                  <div class="grid grid-cols-12 gap-4 px-4 py-2 bg-base-200 border-b border-base-300 font-semibold text-sm text-base-content/80">
                      <div class="col-span-3">Título</div>
                      <div class="col-span-3">URL</div>
                      <div class="col-span-4">Observações</div>
                      <div class="col-span-2 text-center">Ações</div>
                  </div>

                  <!-- Linhas da tabela -->
                  <div
                      v-for="link in linksWithoutTask"
                      :key="link.id"
                      class="grid grid-cols-12 gap-4 px-4 py-0.5 border-b border-base-300 hover:bg-base-200 transition-colors items-center"
                  >
                      <div class="col-span-3 flex items-center">
                          <a
                              class="text-sm text-info font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-3">
                          <a
                              class="text-sm text-base-content/70 hover:underline truncate block"
                              :href="link.url"
                              target="_blank"
                              :title="link.url"
                          >
                              {{ link.url }}
                          </a>
                      </div>
                      <div class="col-span-4">
                          <span class="text-sm text-base-content/70 truncate block" :title="link.observations">
                              {{ link.observations || '-' }}
                          </span>
                      </div>
                      <div class="col-span-2 flex justify-center gap-2">
                          <delete-icon-button
                              size="w-5 h-5"
                              icon-size="text-[10px]"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
                          <button
                              class="w-5 h-5 flex items-center justify-center rounded-full bg-info text-white hover:bg-info transition-colors shadow-sm"
                              @click="copyLink(link.url)"
                              title="Copiar link"
                          >
                              <font-awesome-icon icon="fa-solid fa-copy" class="text-[10px]" />
                          </button>
                      </div>
                  </div>
              </div>
          </section>

          <!-- LINKS DE TAREFAS -->
          <task-links-list
              :links="linksOfOtherTasks"
              container-class="mb-6"
              @delete-link="deleteLink"
              @copy-link="copyLink"
          />

          <!-- LINKS DE OPORTUNIDADES -->
          <section class="mb-6">
              <section-header title="LINKS DE OPORTUNIDADES" icon="fa-solid fa-bullseye" />

              <div v-if="linksOfOtherOpportunities.length === 0" class="p-4 text-center">
                  <p class="text-base-content/60">Nenhum link de oportunidade</p>
              </div>

              <div v-else class="overflow-x-auto">
                  <!-- Header da tabela -->
                  <div class="grid grid-cols-12 gap-4 px-4 py-2 bg-base-200 border-b border-base-300 font-semibold text-sm text-base-content/80">
                      <div class="col-span-3">Título</div>
                      <div class="col-span-2">URL</div>
                      <div class="col-span-3">Observações</div>
                      <div class="col-span-2">Oportunidade</div>
                      <div class="col-span-2 text-center">Ações</div>
                  </div>

                  <!-- Linhas da tabela -->
                  <div
                      v-for="link in linksOfOtherOpportunities"
                      :key="link.id"
                      class="grid grid-cols-12 gap-4 px-4 py-0.5 border-b border-base-300 hover:bg-base-200 transition-colors items-center"
                  >
                      <div class="col-span-3 flex items-center">
                          <a
                              class="text-sm text-info font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-2">
                          <a
                              class="text-sm text-base-content/70 hover:underline truncate block"
                              :href="link.url"
                              target="_blank"
                              :title="link.url"
                          >
                              {{ link.url }}
                          </a>
                      </div>
                      <div class="col-span-3">
                          <span class="text-sm text-base-content/70 truncate block" :title="link.observations">
                              {{ link.observations || '-' }}
                          </span>
                      </div>
                      <div class="col-span-2">
                          <span v-if="link.opportunity" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success truncate">
                              <font-awesome-icon icon="fa-solid fa-bullseye" class="mr-1" />
                              {{ link.opportunity.name }}
                          </span>
                      </div>
                      <div class="col-span-2 flex justify-center gap-2">
                          <delete-icon-button
                              size="w-5 h-5"
                              icon-size="text-[10px]"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
                          <button
                              class="w-5 h-5 flex items-center justify-center rounded-full bg-info text-white hover:bg-info transition-colors shadow-sm"
                              @click="copyLink(link.url)"
                              title="Copiar link"
                          >
                              <font-awesome-icon icon="fa-solid fa-copy" class="text-[10px]" />
                          </button>
                      </div>
                  </div>
              </div>
          </section>

          <!-- LINKS DE PROJETOS -->
          <section class="mb-6">
              <section-header title="LINKS DE PROJETOS" icon="fa-solid fa-project-diagram" />

              <div v-if="linksWithProject.length === 0" class="p-4 text-center">
                  <p class="text-base-content/60">Nenhum link de projeto</p>
              </div>

              <div v-else class="overflow-x-auto">
                  <!-- Header da tabela -->
                  <div class="grid grid-cols-12 gap-4 px-4 py-2 bg-base-200 border-b border-base-300 font-semibold text-sm text-base-content/80">
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
                      class="grid grid-cols-12 gap-4 px-4 py-0.5 border-b border-base-300 hover:bg-base-200 transition-colors items-center"
                  >
                      <div class="col-span-3 flex items-center">
                          <a
                              class="text-sm text-info font-semibold hover:underline truncate"
                              :href="link.url"
                              target="_blank"
                              :title="link.title"
                          >
                              {{ link.title }}
                          </a>
                      </div>
                      <div class="col-span-2">
                          <a
                              class="text-sm text-base-content/70 hover:underline truncate block"
                              :href="link.url"
                              target="_blank"
                              :title="link.url"
                          >
                              {{ link.url }}
                          </a>
                      </div>
                      <div class="col-span-3">
                          <span class="text-sm text-base-content/70 truncate block" :title="link.observations">
                              {{ link.observations || '-' }}
                          </span>
                      </div>
                      <div class="col-span-2">
                          <span v-if="link.project" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-info/10 text-info truncate">
                              <font-awesome-icon icon="fa-solid fa-project-diagram" class="mr-1" />
                              {{ link.project.name }}
                          </span>
                      </div>
                      <div class="col-span-2 flex justify-center gap-2">
                          <delete-icon-button
                              size="w-5 h-5"
                              icon-size="text-[10px]"
                              title="Excluir link"
                              confirm-message="Tem certeza que deseja excluir este link?"
                              @confirm="deleteLink(link.id)"
                          />
                          <button
                              class="w-5 h-5 flex items-center justify-center rounded-full bg-info text-white hover:bg-info transition-colors shadow-sm"
                              @click="copyLink(link.url)"
                              title="Copiar link"
                          >
                              <font-awesome-icon icon="fa-solid fa-copy" class="text-[10px]" />
                          </button>
                      </div>
                  </div>
              </div>
          </section>
  </ModalCard>
</template>

<script>
import { mapMutations } from "vuex";
import { index, destroy } from "@/utils/requests/httpUtils";
import SearchInput from "@/components/filters/SearchInput.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";
import TaskLinksList from "@/components/lists/TaskLinksList.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";
import SectionHeader from "@/components/layout/SectionHeader.vue";

export default {
  name: "LinksModal",
  components: {
    SectionHeader,
      SearchInput,
      ButtonNewForm,
      TaskLinksList,
      ModalCard,
      DeleteIconButton,
  },
  props: {
      // Opcional: quando informado, os links desta tarefa aparecem primeiro e novos links já saem vinculados a ela
      taskId: {
          type: [Number, String],
          default: null,
      },
      taskName: {
          type: String,
          default: "",
      },
      // Opcional: oportunidade da tarefa, cujos links aparecem logo abaixo dos links da tarefa
      opportunityId: {
          type: [Number, String],
          default: null,
      },
      compact: {
          type: Boolean,
          default: false,
      },
  },
  emits: ["close", "links-changed"],
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
      linksOfCurrentTask() {
          return this.linksWithTask.filter(link => this.isCurrentTask(link));
      },
      linksOfOtherTasks() {
          return this.linksWithTask.filter(link => !this.isCurrentTask(link));
      },
      linksWithOpportunity() {
          return this.filteredLinks.filter(link => link.opportunity_id && !link.task_id && !link.project_id);
      },
      linksOfCurrentOpportunity() {
          return this.linksWithOpportunity.filter(link => this.isCurrentOpportunity(link));
      },
      linksOfOtherOpportunities() {
          return this.linksWithOpportunity.filter(link => !this.isCurrentOpportunity(link));
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
              props: { taskId: this.taskId || 0, opportunityId: 0 },
              listeners: {
                  "new-link-event": this.addLinkCreated,
              },
          });
      },
      isCurrentTask(link) {
          return !!this.taskId && Number(link.task_id) === Number(this.taskId);
      },
      isCurrentOpportunity(link) {
          return !!this.opportunityId && Number(link.opportunity_id) === Number(this.opportunityId);
      },
      emitLinksChanged() {
          if (this.taskId) {
              this.$emit("links-changed", this.links.filter(link => this.isCurrentTask(link)));
          }
      },
      addLinkCreated(newLink) {
          this.links.unshift(newLink);
          this.emitLinksChanged();
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
              this.emitLinksChanged();
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
