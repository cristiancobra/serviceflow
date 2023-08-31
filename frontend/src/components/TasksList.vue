<template>
  <div>
    <div class="row">
      <div class="col-12 mb-3">
        <input
          type="text"
          class="form-control search-container"
          v-model="searchTerm"
          placeholder="Digite para buscar"
        />
      </div>
    </div>
    <div class="row">
      <div class="col-6 g-5" v-for="task in filteredTasks" v-bind:key="task.id">
        <div class="card" v-bind:class="getStatusClass(task.status)">
          <router-link :to="{ name: 'tasksShow', params: { id: task.id } }">
            <div class="row ms-1">
              <div
                class="col-2 status"
                v-bind:class="getStatusClass(task.status)"
              >
                <font-awesome-icon :icon="getStatusIcon(task.status)" />
                <p class="label" v-if="task.duration_time">
                  {{ formatDuration(task.duration_time) }}
                </p>
                <p class="label" v-if="task.duration_days">
                  <font-awesome-icon
                    icon="fa-solid fa-calendar-alt"
                    style="color: rgb(48, 48, 48)"
                  />
                  {{ task.duration_days }}
                </p>
              </div>
              <div class="col-10 ps-3">
                <p class="title">
                  {{ task.name }}
                </p>
                <p class="description">
                  {{ trimDescription(task.description) }}
                </p>
              </div>
            </div>
          </router-link>
        </div>

        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
// import axios from "axios";
import { formatDuration } from "@/utils/date/dateUtils";
import { getStatusClass } from "@/utils/card/cardUtils";
import { getStatusIcon } from "@/utils/card/cardUtils";

export default {
  name: "TasksList",
  props: ["tasks"],
  data() {
    return {
      searchTerm: "",
    };
  },
  methods: {
    formatDuration,
    getStatusClass,
    getStatusIcon,
    trimDescription(description) {
      if (description) {
        return description.substring(0, 110);
      }
    },
  },
  computed: {
    filteredTasks() {
      if (this.searchTerm === "") {
        return this.tasks;
      } else {
        const lowerSearchTerm = this.searchTerm.toLowerCase();
        return this.tasks.filter((task) => {
          return (
            task.name.toLowerCase().includes(lowerSearchTerm) ||
            (task.description && task.description.toLowerCase().includes(lowerSearchTerm))
          );
        });
      }
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.title {
  text-align: left;
  font-size: 20px;
  font-weight: 800;
  margin: 5px;
  margin-top: 10px;
  color: black;
}
.description {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: rgb(61, 61, 61);
}
a:link {
  text-decoration: none;
}
a:visited {
  text-decoration: none;
}
a:hover {
  text-decoration: none;
}
a:active {
  text-decoration: none;
}
.card {
  border-style: solid;
  border-width: 2px;
  border-color: gray;
  border-radius: 6px;
  padding: 10px;
  padding-right: 0px;
  height: 15vh;
}
.card:hover {
  border-width: 5px;
  border-radius: 14px;
}
.done {
  background-color: var(--verde-claro);
  border-color: var(--verde);
  color: var(--verde);
}
.doing {
  background-color: var(--azul-claro);
  border-color: var(--azul);
  color: var(--azul);
}
.to-do {
  background-color: var(--laranja-claro);
  border-color: var(--laranja);
  color: var(--laranja);
}
.wait {
  background-color: var(--cinza-claro);
  border-color: var(--cinza);
  color: var(--cinza);
}
.status {
  text-align: center;
  font-size: 3rem;
}

.label {
  margin-top: 0px;
  margin-bottom: -0px;
  font-size: 1.2rem;
  font-weight: 800;
}

.search-container {
  border-width: 2px;
  border-style: solid;
  border-color: lightgray;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: black;
}
</style>
