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
        <div v-for="proposal in proposals" v-bind:key="proposal.id">
            <router-link :to="{ name: 'proposalShow', params: { id: proposal.id } }">
                <div class="row proposal-item pt-1 pb-1">
                    <div class="col-1 d-flex align-items-center justify-content-center" id="col-user">
                        <font-awesome-icon icon="fa-solid fa-file-invoice" class="primary big-icon" />
                    </div>
                    <div class="col">
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
                    </div>
                </div>
                <div class="row service-item pt-1 pb-1" v-for="proposalService in proposal.proposalServices" v-bind:key="proposalService.id">
                    <div class="col-1 offset-2 d-flex align-items-center justify-content-center">
                        <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
                    </div>
                    <div class="col-7">
                        <p class="name ps-2">
                            {{ proposalService.name }}
                        </p>
                    </div>
                    <div class="col">
                        {{ proposalService.quantity }}
                    </div>
                    <div class="col text-end">
                        {{ proposalService.total_price }}
                    </div>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, PROPOSALS_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { formatDateBr } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import ProposalCreateForm from "../forms/ProposalCreateForm.vue";
import MoneyField from '../fields/number/MoneyField.vue';

export default {
    components: {
        ProposalCreateForm,
        MoneyField,
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
        addProposalCreated(newProposal) {
            this.proposals.unshift(newProposal);
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