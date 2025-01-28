<template>
    <div class="list-container">
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
                <link-create-form @new-link-event="addLinkCreated" :task-id="taskId" />
            </div>
        </div>
        <div v-for="link in localLinks" :key="link.id" class="row mt-3 mb-4"
            :class="{ 'highlight': link.id === newLinkId }">
            <div class="col-5">
                <a :href="link.url" target="_blank">{{ link.title }}</a>
            </div>
            <div class="col-6">
                <a :href="link.url" target="_blank">{{ link.url }}</a>
            </div>
            <div class="col-1 d-flex justify-content-center">
                <button class="button delete" @click="confirmDeleteLink(link.id)">
                    <font-awesome-icon icon="fa-solid fa-trash" class="" />
                    excluir
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
.highlight {
    animation: highlightAnimation 4s ease-out;
}

@keyframes highlightAnimation {
    0% {
        background-color: var(--done-color);
        border-color: var(--done-color);
        border-width: 5px;
    }

    100% {
        background-color: transparent;
    }
}
</style>