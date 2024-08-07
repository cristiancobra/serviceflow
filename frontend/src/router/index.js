import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import CompaniesIndex from '../views/companies/CompaniesIndex.vue'
import CompanyShow from '../views/companies/CompanyShow.vue'
import JourneysIndex from '../views/journeys/JourneysIndex.vue'
import LeadsIndex from '../views/leads/LeadsIndex.vue'
import LeadShow from '../views/leads/LeadShow.vue'
import OpportunitiesIndex from '../views/opportunities/OpportunitiesIndex.vue'
import OpportunityShow from '../views/opportunities/OpportunityShow.vue'
import ServicesIndex from '../views/services/ServicesIndex.vue'
import ServiceShow from '../views/services/ServiceShow.vue'
import store from '@/store'
import ProjectsIndex from '../views/projects/ProjectsIndex.vue'
import ProjectShow from '../views/projects/ProjectShow.vue'
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
    path: '/journeys',
    name: 'journeysIndex',
    component: JourneysIndex
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
    path: '/opportunities',
    name: 'opportunitiesIndex',
    component: OpportunitiesIndex
  },
  {
    path: '/opportunities/:id',
    name: 'opportunityShow',
    component: OpportunityShow
  },
  {
    path: '/projects',
    name: 'projectsIndex',
    component: ProjectsIndex
  },
  {
    path: '/projects/:id',
    name: 'projectShow',
    component: ProjectShow
  },
  {
    path: '/tasks',
    name: 'tasksIndex',
    component: TasksIndex
  },
  {
    path: '/tasks/:id',
    name: 'taskShow',
    component: TaskShow
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.state.isAuthenticated;
  
  if (to.name !== 'login' && !isAuthenticated) {
    next({ name: 'login' });
  } else {
    next();
  }
});


export default router
