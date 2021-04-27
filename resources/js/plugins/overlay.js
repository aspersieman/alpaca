const flash = {
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

export default flash
