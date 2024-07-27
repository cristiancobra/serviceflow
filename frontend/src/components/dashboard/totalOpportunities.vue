<template>
    <div>
        <font-awesome-icon icon="fa-solid fa-bullseye" class="icon"/>
        {{ totalOpportunities }}
    </div>
</template>

<script>
import axios from 'axios';
import { BACKEND_URL, OPPORTUNITY_COUNT } from "@/config/apiConfig";

export default {
    name: "SmallContainer",
    data() {
        return {
            totalOpportunities: 0,
        };
    },
    methods: {
        countOpportunities() {
            axios
                .get(`${BACKEND_URL}${OPPORTUNITY_COUNT}`)
                .then((response) => {
                    this.totalOpportunities = response.data.totalOpportunities;
                })
                .catch((error) => console.log(error));
        },
    },
    mounted() {
        this.countOpportunities();
    },
};
</script>