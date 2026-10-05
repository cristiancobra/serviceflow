<template>
    <div class="">
        <section class="section-container">
            <div class="search-container">
                <SearchInput v-model="searchTerm" placeholder="🔍 Buscar por nome, CNPJ, email ou telefone..." />
            </div>

            <EmptyState
                v-if="filteredCompanies.length === 0"
                text="Nenhuma empresa encontrada"
                :description="searchTerm ? 'Tente ajustar sua busca' : 'Comece criando sua primeira empresa'"
                icon="fa-solid fa-inbox"
            />

            <div v-else class="companies-grid">
                <div class="company-card" v-for="company in filteredCompanies" v-bind:key="company.id">
                    <div role="button" class="card-link" @click="openCompanyModal(company)">
                        <div class="flex items-center gap-4 p-5 max-md:p-4 border-b border-base-300 bg-gradient-to-br from-primary/5 to-primary/[0.02]">
                            <div class="avatar">
                                <font-awesome-icon icon="fa-solid fa-briefcase" class="avatar-icon" />
                            </div>
                            <div class="card-title">
                                <h3>{{ company.business_name || company.legal_name }}</h3>
                            </div>
                        </div>
                        
                        <div class="card-body gap-3 p-5 max-md:p-4">
                            <div v-if="company.cnpj" class="info-item">
                                <font-awesome-icon icon="fa-solid fa-id-card" class="info-icon" />
                                <span class="info-text">{{ company.cnpj }}</span>
                            </div>
                            <div v-if="company.email" class="info-item">
                                <font-awesome-icon icon="fa-solid fa-envelope" class="info-icon" />
                                <span class="info-text">{{ company.email }}</span>
                            </div>
                            <div v-if="company.cel_phone" class="info-item">
                                <font-awesome-icon icon="fa-solid fa-phone" class="info-icon" />
                                <span class="info-text">{{ company.cel_phone }}</span>
                            </div>
                            <div v-if="!company.email && !company.cel_phone && !company.cnpj" class="no-info">
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
import SearchInput from "@/components/filters/SearchInput.vue";
import EmptyState from "@/components/layout/EmptyState.vue";

export default {
  name: "CompaniesList",
  components: {
    EmptyState,
    SearchInput,
  },
  data() {
    return {
      isActive: true,
      searchTerm: "",
      companies: [],
      isCreateCompanyModalVisible: false,
    };
  },
  computed: {
    filteredCompanies() {
      if (!this.searchTerm) {
        return this.companies;
      }
      return this.companies.filter(company => {
        const name = company.business_name || company.legal_name || '';
        return name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
          (company.cnpj && company.cnpj.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
          (company.email && company.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
          (company.cel_phone && company.cel_phone.toLowerCase().includes(this.searchTerm.toLowerCase()));
      });
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCompanyModal(company) {
      this.openModal({
        component: "CompanyDetailModal",
        props: { companyId: company.id },
        listeners: {
          "company-updated": this.replaceCompany,
          "company-deleted": this.removeCompany,
        },
        id: `company-${company.id}`,
      });
    },
    replaceCompany(updated) {
      const index = this.companies.findIndex((company) => company.id === updated.id);
      if (index !== -1) this.companies.splice(index, 1, { ...this.companies[index], ...updated });
    },
    removeCompany(id) {
      this.companies = this.companies.filter((company) => company.id !== id);
    },
    addCompanyCreated(newCompany) {
      this.isCreateCompanyModalVisible = false;
      this.companies.unshift(newCompany);
    },
    async getCompanies() {
      try {
        this.companies = await index(`companies`);
      } catch (error) {
        console.error("Erro ao acessar empresas:", error);
      }
    },
  },
  mounted() {
    this.getCompanies();
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

.section-container {
    background: var(--color-base-100);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.search-container {
    margin-bottom: 30px;
}

.companies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.company-card {
    background: var(--color-base-100);
    border: 1px solid var(--color-base-300);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.company-card:hover {
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

.company-card:hover .info-item {
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
    .companies-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .page-container {
        padding: 20px;
    }

    .section-container {
        padding: 20px;
    }

    .companies-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-container {
        padding: 15px;
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
