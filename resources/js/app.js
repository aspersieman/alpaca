// vendor
require('./common/Bootstrap');
window.Vue = require('vue');

// 3rd party
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css';
import Vue from 'vue';
import vuetify from './plugins/vuetify'
import viewprogressbar from './plugins/vueprogressbar'

// app
import router from './router/Router';
import store from './common/Store';
import eventBus from './common/Event';
import formatters from './common/Formatters';
import AxiosAjaxDetct from './common/AxiosAjaxDetect';
import VueAnalytics from 'vue-analytics';
import './plugins/base'

Vue.config.productionTip = false;

if (process.env.NODE_ENV === 'production') {
  Vue.use(VueAnalytics, {
    id: 'TODO INSERT ID HERE',
    router,
    debug: {
      sendHitTask: process.env.NODE_ENV === 'production'
    }
  });
}
Vue.use(formatters);
Vue.use(eventBus);

Vue.config.devtools = process.env.NODE_ENV === 'development';

const app = new Vue({
  vuetify,
  el: '#app',
  eventBus,
  router,
  store,
  data: () => ({
    drawer: false,
  }),
  mounted() {
    const self = this;
    // progress bar top
    AxiosAjaxDetct.init(()=>{
      self.$Progress.start();
    },()=>{
      self.$Progress.finish();
    });
  },
  computed: { },
  methods: { }
});

if (process.env.NODE_ENV === 'development' && window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__.Vue = app.constructor;
}
