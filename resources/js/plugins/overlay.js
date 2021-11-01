import Vue from 'vue'
import store from '@/store';

const overlay = {
  install(Vue, { store }) {
    Vue.prototype.$overlay = new Vue({
      methods: {
        show(payload) {
          store.commit(
            'overlay/showOverlay',
            payload,
            { root: true }
          );
        }
      }
    })
  }
}

Vue.use(overlay, { store });
