<template>
    <div class="container mb-5">
        <OpportunitiesList template="index" />
    </div>
  </template>

<script>
// import { BACKEND_URL, TASK_URL_PARAMETER } from "@/config/apiConfig";
// import axios from "axios";
import OpportunitiesList from "@/components/lists/OpportunitiesList.vue";

export default {
  components: {
    OpportunitiesList,
    
  },
};

</script>
