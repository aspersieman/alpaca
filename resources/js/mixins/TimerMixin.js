export default {
  data(){
    return {
    }
  },
  methods: {
    saveTimer(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/saveTimer', provider)
          .then(() =>{
            this.$eventBus.timerAdded();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    updateTimer(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/updateTimer', provider)
          .then(() =>{
            this.$eventBus.timerUpdated();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    deleteTimer(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/deleteTimer', provider)
          .then(() =>{
            this.$eventBus.timerDeleted();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    /**
     * Calculate the amount of time spent on the task using the timer object.
     */
    timerDuration: function (timer) {
      if (timer.stopped_at != null) {
        const from = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const to = window.moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const seconds = parseInt(window.moment.duration(to.diff(from)).asSeconds())
        return this.$appFormatters.duration(seconds);
      }
      return '';
    },
  },
}
