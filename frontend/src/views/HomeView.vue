<template>
  <div>
    <div style="display: flex;">
      <p class="text-end">
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>

    <div style="display: flex;">
      <div class="small-container">
        <total-opportunities-open />
      </div>
      <div class="small-container">
        <total-proposal-open />
      </div>
    </div>

    <TasksList template="home" />

  </div>
</template>

<script>
import "../assets/css/dashboard.css";
import TotalOpportunitiesOpen from "../components/dashboard/totalOpportunitiesOpen.vue";
import TotalProposalOpen from "../components/dashboard/totalProposalOpen.vue";
import TasksList from "../components/lists/TasksList.vue";

export default {
  data() {
    return {
      dateNow: "",
    };
  },
  components: {
    TotalOpportunitiesOpen,
    TotalProposalOpen,
    TasksList,
  },
  methods: {
    getDateNow() {
      const data = new Date();
      const options = { year: "numeric", month: "long", day: "numeric" };
      this.dateNow = data.toLocaleDateString("pt-BR", options);
    },
  },
  mounted() {
    this.getDateNow();
  },
};
</script>
