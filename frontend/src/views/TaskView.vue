<template>
  <div class="about">
    <h1>
      DETALHES DA TAREFA
    </h1>
  </div>  
  <task-show />
</template>

<script>
// @ is an alias to /src
import TaskShow from '@/components/TaskShow.vue'

export default {
  name: 'TaskView',
  components: {
    TaskShow
  }
}
</script>
