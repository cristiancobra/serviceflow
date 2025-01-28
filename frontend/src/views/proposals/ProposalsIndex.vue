<template>
  <div>
    <TasksFilter @filter-canceled="getTasksCanceled" @filter-doing="getTasksDoing" @filter-done="getTasksDone"
      @filter-late="getTasksLate" @filter-to-do="getTasksToDo" />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <ProposalsList template="index" />

  </div>
</template>

<script>
import ProposalsList from "@/components/lists/ProposalsList.vue";
import TasksFilter from "@/components/filters/TasksFilter.vue";
import SuccessMessage from '../../components/forms/messages/SuccessMessage.vue';

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
