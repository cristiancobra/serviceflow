import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LeadsIndex from '../views/leads/LeadsIndex.vue'
import LeadShowComponent from '../components/LeadShow.vue'
import ProjectsIndexView from '../views/ProjectsIndexView.vue'
import ProjectView from '../views/ProjectView.vue'
import TasksIndexView from '../views/TasksIndexView.vue'
import TaskView from '../views/TaskView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/leads',
    name: 'leadsIndex',
    component: LeadsIndex
  },
  {
    path: '/leads/:id',
    name: 'leadShow',
    component: LeadShowComponent
  },
  {
    path: '/projects',
    name: 'projectsIndex',
    component: ProjectsIndexView
  },
  {
    path: '/project/:id',
    name: 'projectsShow',
    component: ProjectView
  },
  {
    path: '/tasks',
    name: 'tasksIndex',
    component: TasksIndexView
  },
  {
    path: '/task/:id',
    name: 'tasksShow',
    component: TaskView
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
