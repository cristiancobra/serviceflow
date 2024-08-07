<template>
    <div class="list-container mb-5 mt-0">
        <div class="row">
            <div class="col d-flex justify-content-left">
                <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon pe-3 primary" />
                <h2 class="title">PROPOSTAS</h2>
            </div>
        </div>
        <div class="row mt-3 mb-4">
            <div class="col-10">
                <input type="text" class="form-control search-container" v-model="searchTerm"
                    placeholder="Digite para buscar" />
            </div>
            <div class="col-2 d-flex justify-content-end">
                <ProposalCreateForm @new-proposal-event="addProposalCreated" :opportunityId="opportunityId" />
            </div>
        </div>
        <div class="row" v-for="proposal in filteredProposals" v-bind:key="proposal.id">
            <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
                <font-awesome-icon icon="fa-solid fa-bullseye" class="primary big-icon" />
            </div>
            <div v-if="proposal.date_conclusion" class="col-1 status done">
                <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="done mb-3" />
            </div>
            <div v-else class="col-1 status canceled">
                <font-awesome-icon icon="fas fa-check-circle" style="font-size: 2rem;" class="canceled" />
            </div>
            <div class="col cards">
                <router-link :to="{ name: 'proposalShow', params: { id: proposal.id } }">
                    <div class="row title">
                        <div class="col">
                            <p class="name ps-2">
                                {{ proposal.name }}
                            </p>
                        </div>
                    </div>
                </router-link>
            </div>
            <div class="col-3 line-list d-flex align-items-center justify-content-center">
                <DateTimeValue v-if="proposal.date_conclusion" v-model="proposal.date_conclusion" classText="done"
                    classIcon='done' @save="updateProject('date_conclusion', $event, proposal.id)" />
                <DateTimeEditableInput v-else v-model="proposal.date_due"
                    :classText="getDeadlineClass(proposal.date_due)" :classIcon='getDeadlineClass(proposal.date_due)'
                    @save="updateProject('date_due', $event, proposal.id)" />
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, PROPOSALS_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import ProposalCreateForm from "../forms/ProposalCreateForm.vue";

export default {
    components: {
        ProposalCreateForm,
    },
    props: {
        opportunityId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            isActive: true,
            searchTerm: "",
            proposals: [],
            filteredProposals: [],
        };
    },
    methods: {
        getDeadlineClass,
        addProposalCreated(newProposal) {
            console.log("Nova proposta:", newProposal);
            this.filteredProposals.unshift(newProposal);
        },
        async getProposalsFromOpportunity(page = 1) {

            this.proposalsUrl = `${BACKEND_URL}${PROPOSALS_BY_OPPORTUNITY_URL}opportunity_id=${this.opportunityId}&per_page=10&page=${page}`;

            try {
                const response = await axios.get(this.proposalsUrl);

                this.proposals = response.data.data.map(proposal => {
                    return { ...proposal, editing: false }; // Adiciona a propriedade editing a cada proposal
                });

                this.paginationData = {
                    links: response.data.links,
                    meta: response.data.meta,
                };

            } catch (error) {
                console.error("Erro ao acessar propostas:", error);
            }
        },
        toggleForm() {
            this.isActive = !this.isActive;
        },
    },
    mounted() {
        this.getProposalsFromOpportunity();
    },
};
</script>