<template>
  <div>
    <div class='headers-line'>
        <div class="col-1 slot done">
          concluidas
        </div>
        <div class="col-1 slot doing">
          andamento
        </div>
        <div class="col-1 slot late">
          atrasadas
        </div>
        <div class="col-1 slot new" @click='toggle()'>
          +
        </div>
    </div>

    <div v-bind:class='{ hidden: isActive }' >
      <TaskCreateForm @new-task-event='addTaskCreated($event)' />
    </div>

    <div class="row tasks-container">
      <TasksList :tasks='tasks' />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import TasksList from '@/components/TasksList.vue'
import TaskCreateForm from '@/components/forms/TaskCreateForm.vue'

export default {
  name: 'TasksIndexView',
  components: {
    TaskCreateForm,
    TasksList,
  },
  data() {
    return {
      isActive: true,
      hasError: false,
      data: null,
      tasks: [],
      newTask: {
        id: null,
        name: null,
        description: null,
        company_id: null,
        contact_id: null,
        user_id: null,
        date_start: null,
        date_due: null,
      }
  }
},
  methods: {
    toggle() {
        this.isActive = !this.isActive;
      },
      getTasks() {
        axios
          .get("http://localhost:8191/api/tasks")
          .then((response) => {
            this.tasks = response.data.data;
          })
          .catch((error) => console.log(error));
      },
      addTaskCreated($event) {
        // console.log($event);
        // this.newTask = $event;
        this.data = $event;
        console.log(this.data.id);
            this.newTask.id = this.data.id;
            this.newTask.name = this.data.name;
            this.newTask.description = this.data.description;
            this.newTask.company_id = '1';
            this.newTask.contact_id = '2';
            this.newTask.user_id = '3';
            this.newTask.date_start = this.data.date_start;
            this.newTask.date_due = this.data.date_due;

          this.tasks.push( this.newTask );
          console.log('add TAREFFA adicionado');
          console.log(this.tasks);
      }
    },
    mounted() {
      this.getTasks();
    }
}
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
.slot{
  border-width: 2px;
  border-style: solid;
  border-color:white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.done {
  background-color: white;
  border-color: #2CB48D;
  color: #2CB48D;
}
.done:hover {
  background-color: #2CB48D;
  color: white;
}
.doing {
  background-color: white;  
  border-color: #E78D1F;
  color: #E78D1F;
}
.doing:hover {
  background-color: #E78D1F;
  color: white;
}
.late {
  background-color: white;  
  border-color: #B1388D;
  color: #B1388D;
}
.late:hover {
  background-color: #B1388D;
  color: white;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #FF3EB5;
  color: #FF3EB5;
  margin-left: 50px;
  width: 60px;
  font-size: 16px;
}
.new:hover {
  background-color: #FF3EB5;
  color: white;
  margin-left: 50px;
  width: 60px;
}
.tasks-container {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
}
.hidden {
    display: none;
    transition: display 2s;
}
.show {
    display: block;
    transition: display 2s;
}
</style>
