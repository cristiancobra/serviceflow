<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-link" class="icon" />
        <h2>LINKS</h2>
      </div>
      <div class="section-action">
        <button-new-form 
          target="link" 
          @open-modal="isCreateLinkModalVisible = true" 
        />
        <link-create-form
          v-model="isCreateLinkModalVisible"
          @new-link-event="addLinkCreated"
          :task-id="taskId"
          :opportunity-id="opportunityId"
        />
      </div>
    </div>

    <div class="table-row">
      <input
        type="text"
        class="form-control search-container"
        v-model="searchTerm"
        placeholder="Digite para buscar"
      />
    </div>

    <div
      v-for="link in localLinks"
      :key="link.id"
      class="table-row grid grid-cols-12 gap-4 items-center"
      :class="{ highlight: link.id === newLinkId }"
    >
      <div class="col-span-4">
        <font-awesome-icon icon="fa-solid fa-link" class="icon pe-3 primary" />
        <a class="link-name" :href="link.url" target="_blank">{{
          link.title
        }}</a>
      </div>
      <div class="col-span-4">
        <a class="link-url" :href="link.url" target="_blank">{{ link.url }}</a>
      </div>
      <div class="col-span-2">
        <span class="text-sm text-gray-600">{{ link.observations || '-' }}</span>
      </div>
      <div class="col-span-2 flex justify-center gap-2">
        <button
          class="button-circular delete"
          @click="confirmDeleteLink(link.id)"
        >
          <font-awesome-icon icon="fa-solid fa-trash" class="" />
        </button>
        <button class="button-circular ms-2" @click="copyLink(link.url)">
          <font-awesome-icon icon="fa-solid fa-copy" class="" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { destroy } from "@/utils/requests/httpUtils";
import LinkCreateForm from "@/components/forms/LinkCreateForm.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";

export default {
  name: "LinksList",
  components: {
    LinkCreateForm,
    ButtonNewForm,
  },
  data() {
    return {
      localLinks: this.links,
      searchTerm: "",
      newLinkId: null,
      isCreateLinkModalVisible: false,
    };
  },
  props: {
    links: {
      type: Array,
      required: true,
      default: () => [],
    },
    opportunityId: {
      type: Number,
      required: true,
    },
    taskId: {
      type: Number,
      required: true,
    },
  },
  watch: {
    links(newLinks) {
      this.localLinks = newLinks;
    },
  },
  methods: {
    addLinkCreated(newLink) {
      this.localLinks.unshift(newLink);
      this.newLinkId = newLink.id;
    },
    async deleteLink(linkId) {
      try {
        await destroy("links", linkId);
        this.localLinks = this.localLinks.filter((link) => link.id !== linkId);
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