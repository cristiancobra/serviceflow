<template>

  <div>

    <div class="container-card">
          <div class="card">
            <div class="title">
              {{ project.name }}
            </div>
            <p>
              {{ project.description }}
            </p>
          </div>
    </div>
    <div class="container">
      <p>
          Responsável: {{ project.user_id }}
      </p>
      <p>
          Cliente: {{ project.contact_id }}
      </p>
      <p>
          Empresa: {{ project.company_id }}
      </p>
    </div>

  </div>

</template>

<script>
import axios from "axios";

export default {
  name: "ProjectShow",
  data() {
    return {
      project: [],
      projectId: '',
    };
  },
  
methods: {
    getProject() {
      axios
        .get(`http://localhost:8180/api/projects/${this.projectId}`)
        .then((response) => {
          this.project = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    setProjectId (projectId) {
       this.projectId = projectId;
   },
},
async mounted() {
    this.setProjectId(this.$route.params.id);
    this.getProject();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
    text-align: left;
    font-weight: 400;
}
h3 {
  margin: 40px 0 0;
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
  background-color: white;
  border-style: solid;
  border-width: 3px;
  border-color: #48d1cc;
  border-radius: 20px;
  padding-top: 10px;
  padding-left: 20px;
  padding-bottom: 10px;
}
.title {
  font-size: 32px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
}
.container-card {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
}

.container {
  margin-left: 200px;
  margin-right: 180px;
  margin-bottom: 60px;
}
</style>
