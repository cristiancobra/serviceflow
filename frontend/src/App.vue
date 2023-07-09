<template>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img :src="require('@/assets/logo-serviceflow-ROXO.png')" class="logo" alt="logo-serviceflow">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <router-link to="/"> Home --</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/leads"> Contatos --</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/projects"> Projetos --</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/tasks"> Tarefas </router-link>
        </li>
        
      </ul>
    </div>
  </div>
</nav>
<router-view />

</template>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
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
  color: #42b983;
}

.logo {
  width: 170px;
  height: 40px;
}
</style>
