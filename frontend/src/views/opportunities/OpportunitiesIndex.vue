<template>
    <div class="page-container">
        <PageHeader title="OPORTUNIDADES" icon="fa-solid fa-bullseye">
          <template #actions>
            <button type="button" class="btn bg-base-100 text-primary border-0 hover:bg-base-200" @click="openCreateOpportunityModal">
              <font-awesome-icon icon="fa-solid fa-plus" />
              Nova Oportunidade
            </button>
          </template>
        </PageHeader>

        <section class="mt-8 mb-20 px-8">

            <SearchInput v-model="searchTerm" placeholder="Digite para buscar oportunidades" />

            <div class="list-line" v-for="opportunity in filteredOpportunities" v-bind:key="opportunity.id">
                <div class="flex items-center justify-center w-16 shrink-0">
                    <font-awesome-icon v-if="opportunity.date_conclusion" icon="fas fa-check-circle"
                        style="font-size: 2rem;" class="done" />
                    <font-awesome-icon v-else-if="opportunity.date_canceled" icon="fas fa-x" style="font-size: 2rem;"
                        class="" />
                    <font-awesome-icon v-else icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
                </div>

                <!-- Coluna de Avatares -->
                <div class="flex items-center gap-2 min-w-[120px]">
                    <user-avatar :photo="opportunity.user?.photo" :name="opportunity.user?.name"
                        :user-id="opportunity.user?.id" :overlap="true" />
                    <company-avatar :photo="opportunity.company?.photo"
                        :business-name="opportunity.company?.business_name"
                        :legal-name="opportunity.company?.legal_name" :company-id="opportunity.company?.id" />
                    <lead-avatar :photo="opportunity.lead?.photo" :name="opportunity.lead?.name"
                        :lead-id="opportunity.lead?.id" :overlap="true" />
                </div>

                <div class="flex-1 min-w-0">
                    <router-link :to="{ name: 'opportunityShow', params: { id: opportunity.id } }">
                        <div class="title">
                            <p class="text-base-content ps-2">
                                {{ opportunity.name }}
                            </p>
                        </div>
                    </router-link>
                </div>
                <div class="flex items-center justify-end w-48 shrink-0">
                    <span v-if="opportunity.date_conclusion" class="text-sm text-success" title="Concluída em">
                        <font-awesome-icon icon="fa-solid fa-check" class="me-1" />
                        {{ displayDate(opportunity.date_conclusion) }}
                    </span>
                    <span v-else-if="opportunity.date_due" class="text-sm" :class="getDeadlineClass(opportunity.date_due)"
                        title="Prazo">
                        <font-awesome-icon icon="fa-solid fa-calendar" class="me-1" />
                        {{ displayDate(opportunity.date_due) }}
                    </span>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index } from "@/utils/requests/httpUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import UserAvatar from "@/components/common/UserAvatar.vue";
import { displayDate } from "@/utils/date/dateUtils";
import SearchInput from "@/components/filters/SearchInput.vue";
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
    components: {
    PageHeader,
        CompanyAvatar,
        LeadAvatar,
        UserAvatar,
        SearchInput,
    },
    data() {
        return {
            isActive: true,
            searchTerm: "",
            opportunities: [],
        };
    },
    computed: {
        filteredOpportunities() {
            if (!this.searchTerm) {
                return this.opportunities;
            }
            return this.opportunities.filter(opportunity =>
                opportunity.name.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        }
    },
    methods: {
        ...mapMutations(["openModal"]),
        getDeadlineClass,
        displayDate,
        openCreateOpportunityModal() {
            this.openModal({
                component: "OpportunityCreateForm",
                listeners: {
                    "new-opportunity-event": this.addOpportunityCreated,
                },
            });
        },
        addOpportunityCreated(newOpportunity) {
            this.opportunities.unshift(newOpportunity);
        },
        async getOpportunities() {
            try {
                this.opportunities = await index(`opportunities`);
            } catch (error) {
                console.error("Erro ao acessar oportunidades:", error);
            }
        },
        toggleForm() {
            this.isActive = !this.isActive;
        },
    },
    mounted() {
        this.getOpportunities();
    },
};
</script>