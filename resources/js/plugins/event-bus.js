const eventBus = {
  install(Vue) {
    Vue.prototype.$eventBus = new Vue({
      methods: { signUp() { this.$emit('sign-up')
        },
        signIn() {
          this.$emit('sign-in')
        },
        userRegistered() {
          this.$emit('user-registered')
        },
        userLoggedIn() {
          this.$emit('user-logged-in')
        },
        userLoggedOut() {
          this.$emit('user-logged-out')
        },
      }
    })
  }
}

export default eventBus;
