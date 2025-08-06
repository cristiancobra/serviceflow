<template>
  <div>
    <!-- <div class="home-date">
      <p>
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div> -->

    <!-- <div style="display: flex">
      <div class="small-container">
        <total-opportunities-open />
      </div>
      <div class="small-container">
        <total-proposal-open />
      </div>
    </div> -->
      <TasksCalendar />
  </div>
</template>

<script>
import "../assets/css/dashboard.css";
// import TotalOpportunitiesOpen from "../components/dashboard/totalOpportunitiesOpen.vue";
// import TotalProposalOpen from "../components/dashboard/totalProposalOpen.vue";
import TasksCalendar from "../components/lists/TasksCalendar.vue";

export default {
  data() {
    return {
      dateNow: "",
    };
  },
  components: {
    // TotalOpportunitiesOpen,
    // TotalProposalOpen,
    TasksCalendar,
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

<style scoped>
.home-date {
  display: flex;
  justify-content: center;
  margin-top: 10px;
}
</style>
