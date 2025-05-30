import { createApp } from 'vue'
import App from './App.vue'
import '@/config/axiosConfig'; 
import router from './router'

// assets
import './assets/css/cards.css';
import './assets/css/forms.css';
import './assets/css/lists.css';
import './assets/css/login.css';
import './assets/css/modal.css';
import './assets/css/projects.css';
import './assets/css/proposals.css';
import './assets/css/show.css';
import "./assets/css/style.css"
// import "bootstrap/dist/css/bootstrap.min.css"
// import "bootstrap"
import store from './store';
import VueMask from '@devindex/vue-mask'; // vue mask
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'

/* add icons to the library */
library.add(fas, fab)

// paginate
import Paginate from "vuejs-paginate-next";

createApp(App)
.use(router)
.use(store)
.use(VueMask)
.use(VueDatePicker)
.use(Paginate)
.component('font-awesome-icon', FontAwesomeIcon)
.mount('#app')