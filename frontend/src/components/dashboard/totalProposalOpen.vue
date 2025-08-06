<template>
    <div>
        <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon"/>
        {{ totalProposals }}
    </div>
</template>

<script>
import axios from 'axios';
import { BACKEND_URL, PROPOSAL_COUNT } from "@/config/apiConfig";

export default {
    data() {
        return {
            totalProposals: 0,
        };
    },
    methods: {
        countProposals() {
            axios
                .get(`${BACKEND_URL}${PROPOSAL_COUNT}`)
                .then((response) => {
                    this.totalProposals = response.data.totalProposals;
                })
                .catch((error) => console.log(error));
        },
    },
    mounted() {
        this.countProposals();
    },
};
</script>