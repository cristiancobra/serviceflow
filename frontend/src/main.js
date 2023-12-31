import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import "./assets/css/style.css"
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
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

createApp(App)
.use(router)
.use(VueMask)
.use(VueDatePicker)
.component('font-awesome-icon', FontAwesomeIcon)
.mount('#app')