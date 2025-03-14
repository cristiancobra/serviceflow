<template>
    <div class="page-container">
        <div class="row">
            <div class="col d-flex justify-content-left">
                <font-awesome-icon icon="fa-solid fa-tasks" class="icon pe-3 primary" />
                <h2 class="title">LINKS</h2>
            </div>
        </div>
        <div class="row mt-3 mb-4">
            <div class="col-9">
                <input type="text" class="form-control search-container" v-model="searchTerm"
                    placeholder="Digite para buscar" />
            </div>
            <div class="col-2 d-flex justify-content-end">
                <link-create-form @new-link-event="addLinkCreated" :task-id="taskId" :opportunity-id="opportunityId" />
            </div>
        </div>
        <div v-for="link in localLinks" :key="link.id" class="row mt-3 mb-4"
            :class="{ 'highlight': link.id === newLinkId }">
            <div class="col-6">
                <font-awesome-icon icon="fa-solid fa-link" class="icon pe-3 primary" />
                <a class="link-name" :href="link.url" target="_blank">{{ link.title }}</a>
            </div>
            <div class="col-5">
                <a class="link-url" :href="link.url" target="_blank">{{ link.url }}</a>
            </div>
            <div class="col-1 d-flex justify-content-center">
                <button class="button-circular delete" @click="confirmDeleteLink(link.id)">
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
import { destroy } from '@/utils/requests/httpUtils';
import LinkCreateForm from '@/components/forms/LinkCreateForm.vue';

export default {
    name: 'LinksList',
    components: {
        LinkCreateForm,
    },
    data() {
        return {
            localLinks: this.links,
            searchTerm: '',
            newLinkId: null,
        };
    },
    props: {
        links: {
            type: Array,
            required: true,
            default: () => []
        },
        opportunityId: {
            type: Number,
            required: true
        },
        taskId: {
            type: Number,
            required: true
        }
    },
    watch: {
        links(newLinks) {
            this.localLinks = newLinks;
        }
    },
    methods: {
        addLinkCreated(newLink) {
            this.localLinks.unshift(newLink);
            this.newLinkId = newLink.id;
        },
        async deleteLink(linkId) {
            try {
                await destroy('links', linkId);
                this.localLinks = this.localLinks.filter(link => link.id !== linkId);
            } catch (error) {
                console.error("Erro ao deletar o link:", error);
            }
        },
        confirmDeleteLink(linkId) {
            if (window.confirm("Tem certeza que deseja excluir este link?")) {
                this.deleteLink(linkId);
            }
        }
    }
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