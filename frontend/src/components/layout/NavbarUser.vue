<template>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
      <img :src="require('@/assets/logo-serviceflow-ROXO.png')" class="logo" alt="logo-serviceflow">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item primary-color">
          <router-link to="/">Home</router-link>
        </li>
        <li class="nav-item red">
          <router-link to="/leads">Contatos</router-link>
        </li>
        <li class="nav-item blue">
          <router-link to="/companies">Empresas</router-link>
        </li>
        <li class="nav-item green">
          <router-link to="/services">Serviços</router-link>
        </li>
        <li class="nav-item orange">
          <router-link to="/projects">Projetos</router-link>
        </li>
        <li class="nav-item purple">
          <router-link to="/tasks">Tarefas</router-link>
        </li>
        
      </ul>
    </div>
    <LogoutButton />
   
</nav>


</template>


<script>
import LogoutButton from "@/components/buttons/LogoutButton.vue"

export default {
  components: {
    LogoutButton
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
}

nav {
  padding: 30px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  border-width: 2px;
  border-style: solid;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  font-weight: 800;
  width: 120px;
}

.nav-item {
  padding-right: 12px;
  padding-top: 1.2vh;
}

.nav-item a {
  text-decoration: none;
}

.logo {
  width: 170px;
  height: 40px;
}


.blue a {
  color: var(--blue);
}

.orange a {
  color: var(--orange);
}

.green a {
  color: var(--green);
}

.purple a {
  color: var(--purple);
}

.red a {
  color: var(--red);
}
</style>