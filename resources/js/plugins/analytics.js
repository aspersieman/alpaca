import Vue from 'vue'
import router from '@/router/index';
import VueGtag from "vue-gtag";

const analytics = [ VueGtag, {
  config: { id: "G-634ZXVJ4GH" },
  enabled: process.env.NODE_ENV === 'production',
  disableScriptLoad: process.env.NODE_ENV !== 'production',
} ];

Vue.use(...analytics, router);
