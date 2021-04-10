// vendor
require('@/common/bootstrap');
require('@/common/config');

import Vue from 'vue';
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css';

import router from '@/router/index';
import store from '@/store';
import eventBus from '@/plugins/event-bus';
import AxiosAjaxDetct from '@/common/AxiosAjaxDetect';

import analytics from '@/plugins/analytics';
import formatters from '@/plugins/formatters';
import Authentication from '@/plugins/authentication'
import Authorization from '@/plugins/authorization'
import '@/plugins/base';
import vuetify from '@/plugins/vuetify'
import progressbar from '@/plugins/progressbar'
import shortkey from '@/plugins/shortkey'
import Chartkick from 'vue-chartkick';
import Chart from 'chart.js';

Vue.use(...analytics, router);
Vue.use(...progressbar);
Vue.use(Authentication);
Vue.use(Authorization);
Vue.use(formatters);
Vue.use(eventBus);
Vue.use(shortkey)
Vue.use(Chartkick.use(Chart))

new Vue({
  vuetify,
  el: '#app',
  router,
  store,
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
