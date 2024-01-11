import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import CompaniesIndex from '../views/companies/CompaniesIndex.vue'
import CompanyShow from '../views/companies/CompanyShow.vue'
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
    path: '/companies',
    name: 'companiesIndex',
    component: CompaniesIndex
  },
  {
    path: '/companies/:id',
    name: 'companyShow',
    component: CompanyShow
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
    path: '/login',
    name: 'login',
    component: LoginView
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

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('access_token'); // Exemplo: verifique se há um token no armazenamento local
console.log(isAuthenticated);
  if (to.name !== 'login' && !isAuthenticated) {
    next({ name: 'login' }); // Redirecione o usuário para a página de login
  } else {
    next();
  }
})


export default router
