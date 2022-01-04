require('@/common/bootstrap');
require('@/common/config');

import Vue from 'vue';

import AxiosAjaxDetct from '@/common/AxiosAjaxDetect';
import intercept from '@/helpers/interceptors';

import router from '@/router/index';
import store from '@/store';
import vuetify from './plugins/vuetify'

import '@/plugins';

import '@/formatters';

new Vue({
  el: '#app',
  router,
  vuetify,
  store,
  created() {
    intercept(this);
  },
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
