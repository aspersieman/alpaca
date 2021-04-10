export default {
  data(){
    return {
    }
  },
  methods: {
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
