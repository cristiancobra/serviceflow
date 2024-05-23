<template>
  <div class="about">
    <h1>DETALHES DO PROJETO</h1>
  </div>
  <project-show />
</template>

<script>
// @ is an alias to /src
import ProjectShow from '@/components/ProjectShow.vue'

export default {
  name: 'ProjectView',
  components: {
    ProjectShow
  }
}
</script>
