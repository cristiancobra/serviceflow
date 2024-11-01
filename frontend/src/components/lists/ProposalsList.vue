<template>
    <div class="list-container">
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
        <div v-for="proposal in proposals" v-bind:key="proposal.id">
            <div class="row proposal-item pt-1 pb-1">
                <div class="col-2 d-flex align-items-center justify-content-center" id="col-user">
                    <select-status-button :status="proposal.status"
                        @update:modelValue="updateProposal('status', proposal.id, $event)" />
                </div>
                <div class="col-10">
                    <router-link :to="{ name: 'proposalShow', params: { id: proposal.id } }">
                        <div class="row title">
                            <div class="col">
                                {{ formatDateBr(proposal.date) }}
                            </div>
                            <div class="col-5 ">
                                <p v-if="proposal.description" class="name ps-2">
                                    {{ proposal.description }}
                                </p>
                                <p v-else class="name ps-2">
                                    ---
                                </p>
                            </div>
                            <div class="col text-end">
                                <money-field name="total_price" v-model="proposal.total_price" />
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, PROPOSALS_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { formatDateBr } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import { index, updateField } from "@/utils/requests/httpUtils";
import ProposalCreateForm from "../forms/ProposalCreateForm.vue";
import SelectStatusButton from "../buttons/SelectStatusButton.vue";
import MoneyField from '../fields/number/MoneyField.vue';

export default {
    components: {
        ProposalCreateForm,
        MoneyField,
        SelectStatusButton,
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
        formatDateBr,
        getDeadlineClass,
        updateField,
        addProposalCreated(newProposal) {
            this.proposals.unshift(newProposal);
        },

        async getProposalsFromOpportunity(page = 1) {

            this.proposalsUrl = `${BACKEND_URL}${PROPOSALS_BY_OPPORTUNITY_URL}opportunity_id=${this.opportunityId}&per_page=10&page=${page}`;

            try {
                const response = await axios.get(this.proposalsUrl);

                this.proposals = response.data.data;

                this.paginationData = {
                    links: response.data.links,
                    meta: response.data.meta,
                };

            } catch (error) {
                console.error("Erro ao acessar propostas:", error);
            }
        },
        async getProposals() {
            this.proposals = await index("proposals");
        },
        async updateProposal(fieldName, proposalId, editedValue) {
            const updatedProposal = await updateField("proposals", proposalId, fieldName, editedValue);
            const proposalIndex = this.proposals.findIndex(proposal => proposal.id === proposalId);
            if (proposalIndex !== -1) {
                this.proposals[proposalIndex] = updatedProposal;
            }
        },
        toggleForm() {
            this.isActive = !this.isActive;
        },
    },
    mounted() {
        if(this.opportunityId) {
            this.getProposalsFromOpportunity();
        } else {
            this.getProposals();
        }
    },
};
</script>