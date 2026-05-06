<template>
    <div class="page-container">
        <div class="page-header">
            <div class="page-title">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="page-icon" />
                <h1>OPORTUNIDADES</h1>
            </div>
            <div class="page-action">
                <button-new-form target="opportunity" @open-modal="isCreateOpportunityModalVisible = true" />
                <opportunity-create-form v-model="isCreateOpportunityModalVisible"
                    @new-opportunity-event="addOpportunityCreated" />
            </div>
        </div>

        <section class="section-container">

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
                            <p class="text-black ps-2">
                                {{ opportunity.name }}
                            </p>
                        </div>
                    </router-link>
                </div>
                <div class="flex items-center justify-end w-48 shrink-0">
                    <DateTimeValue v-if="opportunity.date_conclusion" v-model="opportunity.date_conclusion"
                        classText="done" classIcon='done'
                        @save="updateProject('date_conclusion', $event, opportunity.id)" />
                    <DateTimeEditableInput v-else v-model="opportunity.date_due"
                        :classText="getDeadlineClass(opportunity.date_due)"
                        :classIcon='getDeadlineClass(opportunity.date_due)'
                        @save="updateProject('date_due', $event, opportunity.id)" />
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import UserAvatar from "@/components/common/UserAvatar.vue";
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "@/components/fields/datetime/DateTimeValue.vue";
import OpportunityCreateForm from "@/components/forms/OpportunityCreateForm.vue";
import SearchInput from "@/components/filters/SearchInput.vue";
import ButtonNewForm from "@/components/buttons/ButtonNewForm.vue";

export default {
    components: {
        CompanyAvatar,
        LeadAvatar,
        UserAvatar,
        DateTimeEditableInput,
        DateTimeValue,
        OpportunityCreateForm,
        SearchInput,
        ButtonNewForm,
    },
    data() {
        return {
            isActive: true,
            searchTerm: "",
            opportunities: [],
            isCreateOpportunityModalVisible: false,
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
        getDeadlineClass,
        addOpportunityCreated(newOpportunity) {
            this.isCreateOpportunityModalVisible = false;
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