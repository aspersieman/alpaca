require('@/common/bootstrap');
require('@/common/config');

import Vue from 'vue';
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/dist/vuetify.min.css';
import Notifications from 'vue-notification';

import router from '@/router/index';
import store from '@/store';
import eventBus from '@/plugins/event-bus';
import AxiosAjaxDetct from '@/common/AxiosAjaxDetect';
import Chartkick from 'vue-chartkick';
import Chart from 'chart.js';

import '@/plugins/base';
import analytics from '@/plugins/analytics';
import formatters from '@/plugins/formatters';
import debug from '@/plugins/debug';
import Authentication from '@/plugins/authentication';
import Authorization from '@/plugins/authorization';
import vuetify from '@/plugins/vuetify';
import VueProgressBar from 'vue-progressbar';
import progressBarOptions from '@/plugins/progressbar';
import shortkey from '@/plugins/shortkey';
import flash from '@/plugins/flash'

Vue.use(...analytics, router);
Vue.use(VueProgressBar, progressBarOptions);
Vue.use(Authentication);
Vue.use(Authorization);
Vue.use(debug);
Vue.use(formatters);
Vue.use(eventBus);
Vue.use(shortkey);
Vue.use(Chartkick.use(Chart));
Vue.use(Notifications);
Vue.use(flash);

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
