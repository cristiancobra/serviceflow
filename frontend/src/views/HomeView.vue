<template>
  <div class="container">
    <div class="row mt-4">
      <p class="text-center">
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>
    <div class="row mt-3 mb-4 me-5 ms-3">
      <div class="col-2">
        <div class="small-container">
          <total-opportunities-open />
        </div>
      </div>
      <div class="col-2">
        <div class="small-container">
          <total-proposal-open />
        </div>
      </div>
    </div>
    <TasksHomeList />
  </div>
</template>

<script>
import "../assets/css/dashboard.css";
import TotalOpportunitiesOpen from "../components/dashboard/totalOpportunitiesOpen.vue";
import TotalProposalOpen from "../components/dashboard/totalProposalOpen.vue";
import TasksHomeList from "../components/lists/TasksHomeList.vue";

export default {
  data() {
    return {
      dateNow: "",
    };
  },
  components: {
    TotalOpportunitiesOpen,
    TotalProposalOpen,
    TasksHomeList,
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
