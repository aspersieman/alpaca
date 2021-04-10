export default {
  data() {
    return {
      show: false
    }
  },
  methods: {
    login(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('auth/login', provider)
          .then(() =>{
            this.$eventBus.userLoggedIn()
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    logout(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('auth/logout', provider)
          .then(() =>{
            this.$eventBus.userLoggedOut()
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    socialLogin(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('auth/socialLogin', provider)
          .then(() => {
            this.hideModal()
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    register(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('auth/register', payload)
          .then(() => {
            this.$eventBus.userRegistered()
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    hideModal() {
      this.show = false
    }
  }
}
