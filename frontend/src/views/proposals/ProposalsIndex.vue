<template>
  <div>
    <TasksFilter
      @filter-canceled="getTasksCanceled"
      @filter-doing="getTasksDoing"
      @filter-done="getTasksDone"
      @filter-late="getTasksLate"
      @filter-to-do="getTasksToDo"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <ProposalsList template="index" />
  </div>
</template>

<script>
import ProposalsList from "@/components/lists/ProposalsList.vue";
import TasksFilter from "@/components/filters/TasksFilter.vue";
import SuccessMessage from "../../components/forms/messages/SuccessMessage.vue";

export default {
  name: "TasksIndexView",
  components: {
    ProposalsList,
    TasksFilter,
    SuccessMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      tasks: [],
      filteredTasks: [],
      newTask: null,
    };
  },
};
</script>