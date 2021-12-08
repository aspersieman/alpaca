import Vue from 'vue'
import { VuePlausible } from 'vue-plausible'

if (process.env.NODE_ENV === 'production') {
  Vue.use(VuePlausible, {
    domain: "lamoid.com",
    enableAutoPageviews: true,
    apiHost: "https://plausible.nicolaas.me"
  })
}
