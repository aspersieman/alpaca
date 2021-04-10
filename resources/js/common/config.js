import Vue from 'vue';
Vue.config.productionTip = false;
Vue.config.devtools = process.env.NODE_ENV === 'development';
if (process.env.NODE_ENV === 'development' && window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__.Vue = app.constructor;
}
