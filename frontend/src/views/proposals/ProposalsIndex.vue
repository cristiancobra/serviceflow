<template>
  <div>
    <ProposalsFilter @filter-change="handleFilterChange" />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <ProposalsList template="index" :filterStatus="selectedStatus" />
  </div>
</template>

<script>
import ProposalsList from "@/components/lists/ProposalsList.vue";
import ProposalsFilter from "@/components/filters/ProposalsFilter.vue";
import SuccessMessage from "../../components/forms/messages/SuccessMessage.vue";

export default {
  name: "ProposalsIndexView",
  components: {
    ProposalsList,
    ProposalsFilter,
    SuccessMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      selectedStatus: null,
    };
  },
  methods: {
    handleFilterChange(status) {
      this.selectedStatus = status;
    },
  },
};
</script>