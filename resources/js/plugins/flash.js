import Vue from 'vue'
import store from '@/store';

const flash = {
  install(Vue, { store }) {
    Vue.prototype.$flash = new Vue({
      methods: {
        success({text}) {
          this.notify({text, color: 'green'})
        },
        error({text}) {
          this.notify({text, color: 'red'})
        },
        info({text}) {
          this.notify({text, color: 'grey'})
        },
        warn({text}) {
          this.notify({text, color: 'orange'})
        },
        notify({text, color}) {
          store.commit(
            'flash/showMessage',
            { text, color },
            { root: true }
          );
        }
      }
    })
  }
}

Vue.use(flash, { store });
