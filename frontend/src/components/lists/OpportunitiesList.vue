<template>
    <div class="list-container mb-5 mt-0">
        <div class="row align-items-start">
            <div class="col-1">
                <font-awesome-icon icon="fa-solid fa-tasks" class="icon" />
            </div>
            <div class="col-8">
                <h2 class="title">OPORTUNIDADES</h2>
            </div>
            <div class="col-3 d-flex justify-content-end">
                <OpportunityCreateForm @new-opportunity-event="addOpportunityCreated" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3 mt-3">
                <input type="text" class="form-control search-container" v-model="searchTerm"
                    placeholder="Digite para buscar" />
            </div>
        </div>
        <div class="row" v-for="opportunity in filteredOpportunities" v-bind:key="opportunity.id">
            <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="primary big-icon" />
            </div>
            <div v-if="opportunity.date_conclusion" class="col-1 status done">
                <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done mb-3" />
            </div>
            <div v-else class="col-1 status canceled">
                <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
            </div>
            <div class="col cards">
                <router-link :to="{ name: 'opportunityShow', params: { id: opportunity.id } }">
                    <div class="row title">
                        <div class="col">
                            <p class="cards-title">
                                {{ opportunity.name }}
                            </p>
                        </div>
                    </div>
                </router-link>
            </div>
            <div class="col-3 line-list d-flex align-items-center justify-content-center">
                <DateTimeValue v-if="opportunity.date_conclusion" v-model="opportunity.date_conclusion" classText="done"
                    classIcon='done' @save="updateProject('date_conclusion', $event, opportunity.id)" />
                <DateTimeEditableInput v-else v-model="opportunity.date_due" :classText="getDeadlineClass(opportunity.date_due)"
                    :classIcon='getDeadlineClass(opportunity.date_due)'
                    @save="updateProject('date_due', $event, opportunity.id)" />
            </div>
        </div>
    </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import OpportunityCreateForm from "../forms/OpportunityCreateForm.vue";

export default {
    components: {
        OpportunityCreateForm,
    },
    data() {
        return {
            isActive: true,
            searchTerm: "",
            opportunities: [],
            filteredOpportunities: [],
        };
    },
    methods: {
        getDeadlineClass,
        addOpportunityCreated(newOpportunity) {
            console.log("Nova oportunidade:", newOpportunity);
            this.filteredOpportunities.unshift(newOpportunity);
        },
        async getOpportunities() {
            try {
                this.filteredOpportunities = await index(`opportunities`);
                console.log("Oportunidades:", this.opportunities);
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