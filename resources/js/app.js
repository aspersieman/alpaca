// vendor
require('./common/bootstrap');

import Vue from 'vue';
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css';

import VueAnalytics from 'vue-analytics';
import Chartkick from 'vue-chartkick';
import Chart from 'chart.js';

// app
import router from '@/router/index';
import formatters from '@/common/formatters';
import store from '@/store';
import eventBus from '@/common/Event';
import AxiosAjaxDetct from '@/common/AxiosAjaxDetect';

import Authentication from '@/plugins/authentication'
import Authorization from '@/plugins/authorization'
import '@/plugins/base';
import vuetify from '@/plugins/vuetify'
// TODO import and use like other plugins
import viewprogressbar from '@/plugins/vueprogressbar'

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
Vue.use(Authentication)
Vue.use(Authorization)
Vue.use(formatters);
Vue.use(eventBus);
const VueShortkey = require('vue-shortkey');
Vue.use(VueShortkey)
Vue.use(Chartkick.use(Chart))

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
});

if (process.env.NODE_ENV === 'development' && window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__.Vue = app.constructor;
}
