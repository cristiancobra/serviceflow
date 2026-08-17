import { createApp } from 'vue'
import App from './App.vue'
import '@/config/axiosConfig'; 
import router from './router'

// assets
import "./assets/css/main.css";
// import "./assets/css/style.css";
// import './assets/css/cards.css';
// import './assets/css/forms.css';
// import './assets/css/lists.css';
// import './assets/css/login.css';
// import './assets/css/modal.css';
// import './assets/css/projects.css';
// import './assets/css/proposals.css';
// import './assets/css/show.css';
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
import {
  faArrowDown, faArrowLeft, faArrowUp,
  faBalanceScale, faBolt, faBriefcase, faBuilding, faBuildingColumns, faBullseye,
  faCalendar, faCalendarAlt, faCalendarCheck, faCalendarPlus,
  faCamera, faChartLine, faCheck, faCheckCircle, faCheckSquare,
  faChevronDown, faChevronRight, faChevronUp,
  faCircle, faCircleCheck, faCircleDot, faCircleExclamation,
  faCity, faClipboard, faClock, faCogs, faCoins, faComments, faCopy, faCreditCard,
  faDollar, faDollarSign, faEdit, faEnvelope,
  faExchangeAlt, faExclamationCircle, faExternalLinkAlt,
  faEye, faEyeSlash,
  faFileContract, faFileInvoice, faFileInvoiceDollar,
  faFlag, faFlask, faFolder, faFolderOpen,
  faGlobe, faHand, faHashtag, faHeart, faHistory, faHome, faHourglassHalf,
  faIdCard, faInbox, faInfo, faInfoCircle,
  faLandmark, faLink, faList, faLock,
  faMagnifyingGlass, faMailBulk, faMapMarkerAlt, faMinus, faMobileAlt,
  faMoneyBill, faMoneyBillWave,
  faPauseCircle, faPen, faPercent, faPhone, faPlay, faPlayCircle,
  faPlus, faPlusCircle, faProjectDiagram,
  faReceipt, faRotate, faSearch, faServer, faShield, faShoppingCart,
  faSignOut, faSpinner, faStop, faSync,
  faTag, faTags, faTasks, faTimes, faTimesCircle, faTools, faTrash, faTrashAlt,
  faTriangleExclamation, faTruck,
  faUniversity, faUser, faUserCircle, faUserPlus, faUserTie, faUsers,
  faWallet, faX, faXmark
} from '@fortawesome/free-solid-svg-icons'
import { faFacebook, faInstagram, faLinkedin } from '@fortawesome/free-brands-svg-icons'

/* add icons to the library */
library.add(
  faArrowDown, faArrowLeft, faArrowUp,
  faBalanceScale, faBolt, faBriefcase, faBuilding, faBuildingColumns, faBullseye,
  faCalendar, faCalendarAlt, faCalendarCheck, faCalendarPlus,
  faCamera, faChartLine, faCheck, faCheckCircle, faCheckSquare,
  faChevronDown, faChevronRight, faChevronUp,
  faCircle, faCircleCheck, faCircleDot, faCircleExclamation,
  faCity, faClipboard, faClock, faCogs, faCoins, faComments, faCopy, faCreditCard,
  faDollar, faDollarSign, faEdit, faEnvelope,
  faExchangeAlt, faExclamationCircle, faExternalLinkAlt,
  faEye, faEyeSlash,
  faFileContract, faFileInvoice, faFileInvoiceDollar,
  faFlag, faFlask, faFolder, faFolderOpen,
  faGlobe, faHand, faHashtag, faHeart, faHistory, faHome, faHourglassHalf,
  faIdCard, faInbox, faInfo, faInfoCircle,
  faLandmark, faLink, faList, faLock,
  faMagnifyingGlass, faMailBulk, faMapMarkerAlt, faMinus, faMobileAlt,
  faMoneyBill, faMoneyBillWave,
  faPauseCircle, faPen, faPercent, faPhone, faPlay, faPlayCircle,
  faPlus, faPlusCircle, faProjectDiagram,
  faReceipt, faRotate, faSearch, faServer, faShield, faShoppingCart,
  faSignOut, faSpinner, faStop, faSync,
  faTag, faTags, faTasks, faTimes, faTimesCircle, faTools, faTrash, faTrashAlt,
  faTriangleExclamation, faTruck,
  faUniversity, faUser, faUserCircle, faUserPlus, faUserTie, faUsers,
  faWallet, faX, faXmark,
  faFacebook, faInstagram, faLinkedin
)

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