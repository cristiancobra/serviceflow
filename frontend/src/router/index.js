import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LeadsIndex from '../views/leads/LeadsIndex.vue'
import LeadShow from '../views/leads/LeadShow.vue'
import ServicesIndex from '../views/services/ServicesIndex.vue'
import ServiceShow from '../views/services/ServiceShow.vue'
import ProjectsIndexView from '../views/ProjectsIndexView.vue'
import ProjectView from '../views/ProjectView.vue'
import TasksIndex from '../views/tasks/TasksIndex.vue'
import TaskShow from '../views/tasks/TaskShow.vue'

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
    component: LeadShow
  },
  {
    path: '/services',
    name: 'serviceIndex',
    component: ServicesIndex
  },
  {
    path: '/services/:id',
    name: 'serviceShow',
    component: ServiceShow
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
    component: TasksIndex
  },
  {
    path: '/task/:id',
    name: 'tasksShow',
    component: TaskShow
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
