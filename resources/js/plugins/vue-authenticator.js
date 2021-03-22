import VueAuthenticate from "vue-authenticate";
import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios';
import VueAxios from 'vue-axios'

Vue.use(Vuex)
Vue.use(VueAxios, axios)

export const getVueAuthenticate = () => {
  const baseUrl = process.env.NODE_ENV === 'development'
    ? 'https://alpaca.vagrant'
    : 'https://alpaca.nicolaas.me';
  return new VueAuthenticate(Vue.prototype.$http, {
    providers: {
      facebook: {
        clientId: '425948981955698',
        redirectUri: baseUrl + '/auth/facebook/callback' // Your client app URL
      },
      google: {
        clientId: '340639301402-0u9qst24dn4va778opjhq3o3hcpv6ona.apps.googleusercontent.com',
        redirectUri: baseUrl + '/auth/google/callback' // Your client app URL
      }
    }
  })
}

