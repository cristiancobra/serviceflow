<template>
    <div class="page-container">
        <div class="page-header">
            <div class="page-title">
                <font-awesome-icon icon="fa-solid fa-user" class="page-icon" />
                <div>
                    <h1>CONTATOS</h1>
                    <p class="subtitle">Gerenciador de leads e contatos comerciais</p>
                </div>
            </div>
            <div class="page-action">
                <button type="button" class="btn-create" @click="isCreateLeadModalVisible = true">
                    <font-awesome-icon icon="fa-solid fa-plus" class="me-2" />
                    Novo Contato
                </button>
                <lead-create-form 
                    v-model="isCreateLeadModalVisible"
                    @new-lead-event="addLeadCreated" 
                />
            </div>
        </div>

        <section class="section-container">
            <div class="search-container">
                <SearchInput v-model="searchTerm" placeholder="🔍 Buscar por nome, email ou telefone..." />
            </div>

            <div v-if="filteredLeads.length === 0" class="empty-state">
                <font-awesome-icon icon="fa-solid fa-inbox" class="empty-icon" />
                <h3>Nenhum contato encontrado</h3>
                <p>{{ searchTerm ? 'Tente ajustar sua busca' : 'Comece criando seu primeiro contato' }}</p>
            </div>

            <div v-else class="leads-grid">
                <div class="lead-card" v-for="lead in filteredLeads" v-bind:key="lead.id">
                    <router-link :to="{ name: 'leadShow', params: { id: lead.id } }" class="card-link">
                        <div class="card-header">
                            <div class="avatar">
                                <font-awesome-icon icon="fa-solid fa-user-circle" class="avatar-icon" />
                            </div>
                            <div class="card-title">
                                <h3>{{ lead.name }}</h3>
                            </div>
                        </div>
                        
                        <div class="card-body">
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
                    </router-link>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import LeadCreateForm from "@/components/forms/LeadCreateForm.vue";
import SearchInput from "@/components/filters/SearchInput.vue";

export default {
    name: "LeadsList",
    components: {
        LeadCreateForm,
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
        }
    },
    methods: {
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
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    gap: 20px;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 1;
}

.page-icon {
    font-size: 48px;
    color: var(--primary, #007bff);
    background: rgba(0, 123, 255, 0.1);
    padding: 15px;
    border-radius: 12px;
    width: 78px;
    height: 78px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-title h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    letter-spacing: -0.5px;
}

.subtitle {
    margin: 5px 0 0 0;
    color: #718096;
    font-size: 14px;
    font-weight: 500;
}

.page-action {
    display: flex;
    gap: 10px;
}

.btn-create {
    background: linear-gradient(135deg, var(--primary, #007bff) 0%, #0056b3 100%);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

.btn-create:active {
    transform: translateY(0);
}

.section-container {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.search-container {
    margin-bottom: 30px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-icon {
    font-size: 64px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin: 20px 0 10px;
    color: #2d3748;
    font-size: 20px;
}

.empty-state p {
    margin: 0;
    color: #a0aec0;
    font-size: 14px;
}

.leads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.lead-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.lead-card:hover {
    border-color: var(--primary, #007bff);
    box-shadow: 0 12px 24px rgba(0, 123, 255, 0.15);
    transform: translateY(-4px);
}

.card-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.card-header {
    padding: 20px;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.05) 0%, rgba(0, 123, 255, 0.02) 100%);
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.avatar {
    flex-shrink: 0;
}

.avatar-icon {
    font-size: 40px;
    color: var(--primary, #007bff);
}

.card-title h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1a202c;
    word-break: break-word;
}

.card-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 8px;
    border-radius: 6px;
    background: #f7fafc;
    transition: background 0.2s ease;
}

.lead-card:hover .info-item {
    background: #edf2f7;
}

.info-icon {
    font-size: 14px;
    color: var(--primary, #007bff);
    margin-top: 2px;
    flex-shrink: 0;
}

.info-text {
    font-size: 13px;
    color: #4a5568;
    word-break: break-all;
    line-height: 1.4;
}

.no-info {
    padding: 8px;
    color: #a0aec0;
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

    .page-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .page-title {
        width: 100%;
    }

    .page-icon {
        width: 60px;
        height: 60px;
        font-size: 36px;
    }

    .page-title h1 {
        font-size: 24px;
    }

    .page-action {
        width: 100%;
    }

    .btn-create {
        flex: 1;
        justify-content: center;
    }

    .section-container {
        padding: 20px;
    }

    .leads-grid {
        grid-template-columns: 1fr;
    }

    .card-header {
        padding: 16px;
    }

    .card-body {
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .page-container {
        padding: 15px;
    }

    .page-title h1 {
        font-size: 20px;
    }

    .subtitle {
        font-size: 12px;
    }

    .btn-create {
        padding: 10px 20px;
        font-size: 14px;
    }

    .section-container {
        padding: 15px;
        border-radius: 12px;
    }

    .card-title h3 {
        font-size: 16px;
    }

    .info-text {
        font-size: 12px;
    }
}
</style>
