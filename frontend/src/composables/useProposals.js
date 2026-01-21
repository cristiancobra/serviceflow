import { ref } from "vue";
import axios from "axios";
import { BACKEND_URL, PROPOSALS_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { index, updateField } from "@/utils/requests/httpUtils";

export function useProposals(opportunityId) {
    const proposals = ref([]);
    const loading = ref(false);

    const fetchProposals = async (page = 1) => {
        loading.value = true;

        if (opportunityId) {
            const url =
                `${BACKEND_URL}${PROPOSALS_BY_OPPORTUNITY_URL}` +
                `opportunity_id=${opportunityId}&per_page=10&page=${page}`;

            const response = await axios.get(url);
            proposals.value = response.data.data;
        } else {
            proposals.value = await index("proposals");
        }

        loading.value = false;
    };

    const updateProposal = async (proposalId, fieldName, value) => {
        const updated = await updateField(
            "proposals",
            proposalId,
            fieldName,
            value
        );

        const indexFound = proposals.value.findIndex(
            (p) => p.id === proposalId
        );

        if (indexFound !== -1) {
            proposals.value[indexFound] = updated;
        }
    };

    const addProposal = (proposal) => {
        proposals.value.unshift(proposal);
    };

    return {
        proposals,
        loading,
        fetchProposals,
        updateProposal,
        addProposal,
    };
}
