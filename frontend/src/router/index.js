import { createRouter, createWebHistory } from 'vue-router'
import AccountShow from '../views/accounts/AccountShow.vue'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import CompaniesIndex from '../views/companies/CompaniesIndex.vue'
import CompanyShow from '../views/companies/CompanyShow.vue'
import CostsIndex from '../views/costs/CostsIndex.vue'
import CostShow from '../views/costs/CostShow.vue'
import Financial from '../views/dashboards/FinancialView.vue'
import JourneysIndex from '../views/journeys/JourneysIndex.vue'
import LeadsIndex from '../views/leads/LeadsIndex.vue'
import LeadShow from '../views/leads/LeadShow.vue'
import InvoiceShow from '@/views/invoices/InvoiceShow.vue'
import OpportunitiesIndex from '../views/opportunities/OpportunitiesIndex.vue'
import OpportunityShow from '../views/opportunities/OpportunityShow.vue'
import ServicesIndex from '../views/services/ServicesIndex.vue'
import ServiceShow from '../views/services/ServiceShow.vue'
import store from '@/store'
import ProjectsIndex from '../views/projects/ProjectsIndex.vue'
import ProjectShow from '../views/projects/ProjectShow.vue'
import ProposalsIndex from '@/views/proposals/ProposalsIndex.vue'
import ProposalShow from '@/views/proposals/ProposalShow.vue'
import TasksIndex from '../views/tasks/TasksIndex.vue'
import TaskShow from '../views/tasks/TaskShow.vue'
import UserShow from '@/views/users/UserShow.vue'


const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/accounts/:id',
    name: 'accountShow',
    component: AccountShow
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
    path: '/costs',
    name: 'costsIndex',
    component: CostsIndex
  },
  {
    path: '/costs/:id',
    name: 'costShow',
    component: CostShow
  },
  {
    path: '/financial',
    name: 'financial',
    component: Financial
  },
  {
    path: '/invoices/:id',
    name: 'invoiceShow',
    component: InvoiceShow
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
    path: '/proposals',
    name: 'proposalsIndex',
    component: ProposalsIndex
  },
  {
    path: '/proposals/:id',
    name: 'proposalShow',
    component: ProposalShow
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
  {
    path: '/users/:id',
    name: 'userShow',
    component: UserShow
  }
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
