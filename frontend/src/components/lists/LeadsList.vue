<template>
    <div class="">

        <section class="mt-8 mb-20 rounded-xl sm:rounded-2xl bg-base-100 p-4 sm:p-5 md:p-8 shadow-xl">
            <div class="search-container">
                <SearchInput v-model="searchTerm" placeholder="🔍 Buscar por nome, email ou telefone..." />
            </div>

            <EmptyState
                v-if="filteredLeads.length === 0"
                text="Nenhum contato encontrado"
                :description="searchTerm ? 'Tente ajustar sua busca' : 'Comece criando seu primeiro contato'"
                icon="fa-solid fa-inbox"
            />

            <div v-else class="leads-grid">
                <div class="lead-card" v-for="lead in filteredLeads" v-bind:key="lead.id">
                    <div role="button" class="card-link" @click="openLeadModal(lead)">
                        <div class="flex items-center gap-4 p-5 max-md:p-4 border-b border-base-300 bg-gradient-to-br from-primary/5 to-primary/[0.02]">
                            <div class="avatar">
                                <img 
                                    v-if="lead.photo" 
                                    :src="`${imagesPath}${lead.photo}`" 
                                    :alt="lead.name"
                                    class="avatar-image"
                                />
                                <font-awesome-icon v-else icon="fa-solid fa-user-circle" class="avatar-icon" />
                            </div>
                            <div class="card-title">
                                <h3>{{ lead.name }}</h3>
                            </div>
                        </div>
                        
                        <div class="card-body gap-3 p-5 max-md:p-4">
                            <div v-if="lead.email" class="info-item">
                                <font-awesome-icon icon="fa-solid fa-envelope" class="info-icon" />
                                <span class="info-text">{{ lead.email }}</span>
                            </div>
                            <div v-if="lead.cel_phone" class="info-item">
                                <font-awesome-icon icon="fa-solid fa-phone" class="info-icon" />
                                <span class="info-text">{{ lead.cel_phone }}</span>
                            </div>
                            <div v-if="!lead.email && !lead.cel_phone" class="no-info">
                                <small>Sem informações de contato</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index } from "@/utils/requests/httpUtils";
import { IMAGES_PATH } from "@/config/apiConfig";
import SearchInput from "@/components/filters/SearchInput.vue";
import EmptyState from "@/components/layout/EmptyState.vue";

export default {
    name: "LeadsList",
    components: {
    EmptyState,
        SearchInput,
    },
    data() {
        return {
            isActive: true,
            searchTerm: "",
            leads: [],
            isCreateLeadModalVisible: false,
        };
    },
    computed: {
        filteredLeads() {
            if (!this.searchTerm) {
                return this.leads;
            }
            return this.leads.filter(lead => 
                lead.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                (lead.email && lead.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                (lead.cel_phone && lead.cel_phone.toLowerCase().includes(this.searchTerm.toLowerCase()))
            );
        },
        imagesPath() {
            return IMAGES_PATH;
        }
    },
    methods: {
        ...mapMutations(["openModal"]),
        openLeadModal(lead) {
            this.openModal({
                component: "LeadDetailModal",
                props: { leadId: lead.id },
                listeners: {
                    "lead-updated": this.replaceLead,
                    "lead-deleted": this.removeLead,
                },
                id: `lead-${lead.id}`,
            });
        },
        replaceLead(updated) {
            const index = this.leads.findIndex((lead) => lead.id === updated.id);
            if (index !== -1) this.leads.splice(index, 1, { ...this.leads[index], ...updated });
        },
        removeLead(id) {
            this.leads = this.leads.filter((lead) => lead.id !== id);
        },
        addLeadCreated(newLead) {
            this.isCreateLeadModalVisible = false;
            this.leads.unshift(newLead);
        },
        async getLeads() {
            try {
                this.leads = await index(`leads`);
            } catch (error) {
                console.error("Erro ao acessar contatos:", error);
            }
        },
    },
    mounted() {
        this.getLeads();
    },
};
</script>

<style scoped>
* {
    box-sizing: border-box;
}

.page-container {
    padding: 30px;
    background: linear-gradient(135deg, var(--color-base-200) 0%, var(--color-base-300) 100%);
    min-height: 100vh;
}


.search-container {
    margin-bottom: 30px;
}

.leads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.lead-card {
    background: var(--color-base-100);
    border: 1px solid var(--color-base-300);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.lead-card:hover {
    border-color: var(--color-primary);
    box-shadow: 0 12px 24px color-mix(in oklab, var(--color-primary) 15%, transparent);
    transform: translateY(-4px);
}

.card-link {
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.avatar {
    flex-shrink: 0;
}

.avatar-icon {
    font-size: 40px;
    color: var(--color-primary);
}

.avatar-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--color-primary);
}

.card-title h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--color-base-content);
    word-break: break-word;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 8px;
    border-radius: 6px;
    background: var(--color-base-200);
    transition: background 0.2s ease;
}

.lead-card:hover .info-item {
    background: var(--color-base-200);
}

.info-icon {
    font-size: 14px;
    color: var(--color-primary);
    margin-top: 2px;
    flex-shrink: 0;
}

.info-text {
    font-size: 13px;
    color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
    word-break: break-all;
    line-height: 1.4;
}

.no-info {
    padding: 8px;
    color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
    font-size: 13px;
    font-style: italic;
}

/* Responsivo */
@media (max-width: 1024px) {
    .leads-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .page-container {
        padding: 20px;
    }


    .leads-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-container {
        padding: 15px;
    }


    .card-title h3 {
        font-size: 16px;
    }

    .info-text {
        font-size: 12px;
    }
}
</style>
