<template>

  <div class="row">
    <div class="col mb-5" v-for="task in tasks" v-bind:key="task.id">
        <div class="row">


          <div id='card-column' class="col-9 card">
            <router-link :to="{name:'tasksShow',params:{id:task.id}}">
              <p class='title'>
                {{ task.name }}
              </p>
              <p class='description'>
                {{ trimDescription( task.description ) }}
              </p>
            </router-link>
          </div>
          <div class='col-2 slots-column'>
            <div class="slot active">
              <router-link :to="{name:'tasksShow',params:{id:task.id}}">
                <font-awesome-icon icon="fa-solid fa-eye" />
              </router-link>
            </div>
            <div class="slot inactive">
              <font-awesome-icon icon="fa-solid fa-bullseye" />
            </div>
            <div class="slot inactive">
              <font-awesome-icon icon="fa-solid fa-coins" />
            </div>
            <div class="slot inactive">
              <font-awesome-icon icon="fa-solid fa-list-check" />
            </div>
          </div>


        </div>
        <router-view/>
    </div>
  </div>
</template>

<script>
// import axios from "axios";

export default {
  name: "TasksList",
  props: [
    'tasks',
  ],
  methods: {
    trimDescription(description) {
      if( description ) {
        return description.substring(0, 130);
      }
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.title {
  text-align: left;
  font-size: 18px;
  font-weight: 800;
  margin: 5px;
  margin-top: 10px;
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
  text-align: left;
  margin-right: 0;
  border-style: solid;
  border-right-style: none;
  border-width: 2px;
  border-color: #007E8B;
  border-radius: 6px 0 0 6px;
  padding: 10px;
  width: 800px;
  height: 160px;
  background-color:white;
}
.slot {
  display:block;
  text-align: center;
  border-style: solid;
  border-width: 2px;
  border-color: #007E8B;
  border-radius: 0 6px 6px 0;
  padding-top: 6px;
  padding-bottom: 6px;
  background-color:pink;
}
.slot.active {
  border-left-style: none;
  background-color:white;
}
.slot.inactive {
  background-color:lightgray;
}
.slots-column {
    margin-left: -12px;
}
</style>
