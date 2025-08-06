<template>
    <div class="page-container">
        <div class="page-header">
            <div class="page-title">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="page-icon" />
                <h1>OPORTUNIDADES</h1>
            </div>
            <div class="page-action">
                <OpportunityCreateForm @new-opportunity-event="addOpportunityCreated" />
            </div>
        </div>

        <section class="section-container">
        <div class="search-container">
            <input type="text" class="search-input" v-model="searchTerm" placeholder="Digite para buscar" />
        </div>

        <div class="list-line" v-for="opportunity in filteredOpportunities" v-bind:key="opportunity.id">
            <div class="icons-column">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="primary big-icon" />
                <font-awesome-icon v-if="opportunity.date_conclusion" icon="fas fa-check-circle"
                    style="font-size: 2rem;" class="done" />
                <font-awesome-icon v-else-if="opportunity.date_canceled" icon="fas fa-x" style="font-size: 2rem;"
                    class="" />
                <font-awesome-icon v-else icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
            </div>
            <div class="task-column">
                <router-link :to="{ name: 'opportunityShow', params: { id: opportunity.id } }">
                    <div class="title">
                        <p class="name ps-2">
                            {{ opportunity.name }}
                        </p>
                    </div>
                </router-link>
            </div>
            <div class="date-column">
                <DateTimeValue v-if="opportunity.date_conclusion" v-model="opportunity.date_conclusion" classText="done"
                    classIcon='done' @save="updateProject('date_conclusion', $event, opportunity.id)" />
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
import DateTimeEditableInput from "@/components/fields/datetime/DateTimeEditableInput.vue";
import DateTimeValue from "@/components/fields/datetime/DateTimeValue.vue";
import OpportunityCreateForm from "../forms/OpportunityCreateForm.vue";

export default {
    components: {
        DateTimeEditableInput,
        DateTimeValue,
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
            this.filteredOpportunities.unshift(newOpportunity);
        },
        async getOpportunities() {
            try {
                this.filteredOpportunities = await index(`opportunities`);
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