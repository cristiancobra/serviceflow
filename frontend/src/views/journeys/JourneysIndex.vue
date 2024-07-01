<template>
  <div class="container mb-5">
    <JourneysFilter
      @filter-canceled="getJourneysCanceled"
      @filter-doing="getJourneysDoing"
      @filter-done="getJourneysDone"
      @filter-late="getJourneysLate"
      @filter-to-do="getJourneysToDo"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <div class="row">
      <JourneysList @toggle="toggle" template="index" />
    </div>
  </div>
</template>

<script>
import JourneysList from "@/components/lists/JourneysList.vue";
import JourneysFilter from "@/components/filters/JourneysFilter.vue";
import SuccessMessage from '../../components/forms/messages/SuccessMessage.vue';

export default {
  name: "JourneysIndexView",
  components: {
    JourneysList,
    JourneysFilter,
    SuccessMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      journeys: [],
      newTask: null,
    };
  },
  methods: {
  },
};
</script>

<style scoped>
.headers-line {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 80px;
  margin-right: 80px;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  width: 60px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
  width: 60px;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
