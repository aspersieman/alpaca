export default {
  data(){
    return {
    }
  },
  methods: {
    /**
     * Conditionally pads a number with "0"
     */
    padNumber: number =>  (number > 9) ? number : "0" + number,

    /**
     * Splits seconds into hours, minutes, and seconds.
     */
    readableTimeFromSeconds: function(from, to) {
      const seconds = parseInt(window.moment.duration(to.diff(from)).asSeconds())
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this.padNumber(hours),
        seconds: this.padNumber(seconds % 60),
        minutes: this.padNumber(parseInt(seconds / 60, 10) % 60),
      }
    },

    /**
     * Calculate the amount of time spent on the task using the timer object.
     */
    calculateTimeSpent: function (timer) {
      if (timer.stopped_at != null) {
        const from = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const to = window.moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const time = this.readableTimeFromSeconds(from, to);
        return `${time.hours}:${time.minutes}:${time.seconds}`; // for syntax highlighting: `
      }
      return '';
    },

    /**
     * Format a datetime value for display in a timer
     */
    timerFormat(dateValue, time = false) {
      let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (time) {
        const date = window.moment(dateValue, 'YYYY-MM-DDTHH:mm:ss.SSS');
        return window.moment.tz(date, tz).format('YYYY-MM-DD @ HH:mm:SS');
      } else {
        return window.moment.tz(dateValue, tz).format('YYYY-MM-DD');
      }
    },
  },
}
