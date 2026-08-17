import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue')
  },
  {
    path: '/accounts/:id',
    name: 'accountShow',
    component: () => import('../views/accounts/AccountShow.vue')
  },
  {
    path: '/bank-accounts',
    name: 'bank-accounts',
    component: () => import('../views/bank-accounts/BankAccountsIndex.vue')
  },
  {
    path: '/bank-accounts/:id',
    name: 'bank-accounts-show',
    component: () => import('../views/bank-accounts/BankAccountShow.vue')
  },
  {
    path: '/companies',
    name: 'companiesIndex',
    component: () => import('../views/companies/CompaniesIndex.vue')
  },
  {
    path: '/companies/:id',
    name: 'companyShow',
    component: () => import('../views/companies/CompanyShow.vue')
  },
  {
    path: '/costs',
    name: 'costsIndex',
    component: () => import('../views/costs/CostsIndex.vue')
  },
  {
    path: '/costs/:id',
    name: 'costShow',
    component: () => import('../views/costs/CostShow.vue')
  },
  {
    path: '/financial',
    name: 'financial',
    component: () => import('../views/dashboards/FinancialView.vue')
  },
  {
    path: '/invoices/:id',
    name: 'invoiceShow',
    component: () => import('@/views/invoices/InvoiceShow.vue')
  },
  {
    path: '/invoices',
    name: 'invoicesIndex',
    component: () => import('@/views/invoices/InvoicesIndex.vue')
  },
  {
    path: '/contas-a-pagar',
    name: 'accountsPayable',
    component: () => import('@/views/invoices/AccountsPayableIndex.vue')
  },
  {
    path: '/leads',
    name: 'leadsIndex',
    component: () => import('../views/leads/LeadsIndex.vue')
  },
  {
    path: '/journeys',
    name: 'journeysIndex',
    component: () => import('../views/journeys/JourneysIndex.vue')
  },
  {
    path: '/leads/:id',
    name: 'leadShow',
    component: () => import('../views/leads/LeadShow.vue')
  },
  {
    path: '/links',
    name: 'linksIndex',
    component: () => import('../views/links/LinksIndex.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/services',
    name: 'serviceIndex',
    component: () => import('../views/services/ServicesIndex.vue')
  },
  {
    path: '/services/:id',
    name: 'serviceShow',
    component: () => import('../views/services/ServiceShow.vue')
  },
  {
    path: '/opportunities',
    name: 'opportunitiesIndex',
    component: () => import('../views/opportunities/OpportunitiesIndex.vue')
  },
  {
    path: '/opportunities/:id',
    name: 'opportunityShow',
    component: () => import('../views/opportunities/OpportunityShow.vue')
  },
  {
    path: '/projects',
    name: 'projectsIndex',
    component: () => import('../views/projects/ProjectsIndex.vue')
  },
  {
    path: '/projects/:id',
    name: 'projectShow',
    component: () => import('../views/projects/ProjectShow.vue')
  },
  {
    path: '/proposals',
    name: 'proposalsIndex',
    component: () => import('@/views/proposals/ProposalsIndex.vue')
  },
  {
    path: '/proposals/:id',
    name: 'proposalShow',
    component: () => import('@/views/proposals/ProposalShow.vue')
  },
  {
    path: '/tasks',
    name: 'tasksIndex',
    component: () => import('../views/tasks/TasksIndex.vue')
  },
  {
    path: '/transactions',
    name: 'transactionsIndex',
    component: () => import('../views/transactions/TransactionsIndex.vue')
  },
  {
    path: '/users/:id',
    name: 'userShow',
    component: () => import('@/views/users/UserShow.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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
