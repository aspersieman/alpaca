<template>
  <div>
    <v-list :dense="dense" :two-line="twoLine" subheader v-if="task.timers.length > 0">
      <template v-for="timer in task.timers">
        <v-list-item
          :key="timer.id" 
          :class="showTimerForTask(task, timer) ? 'active-timer' : ''">
          <v-list-item-icon>
            <v-icon>access_time</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="timer.name">{{timer.name}}</v-list-item-title>
            <v-list-item-subtitle>
              {{ dateFormat(timer.created_at) }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-icon>
            <div v-if="showTimerForTask(task, timer)">
              {{ $store.state.activeTimerCounter.title }}
              <v-btn icon v-if="showTimerForTask(task, timer)" class="btn btn-sm btn-secondary" @click="stopTimer()">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">stop</v-icon>
                  </template>
                  Stop timer
                </v-tooltip>
              </v-btn>
            </div>
            <div v-else>
              <div v-if="$store.state.stoppedTimer && $store.state.stoppedTimer.id == timer.id">
                {{ $store.state.stoppedTimerValue }}
              </div>
              <div v-else>
                {{ calculateTimeSpent(timer) }}
              </div>
              <v-btn icon @click="deleteTimer(task, timer)">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">delete</v-icon>
                  </template>
                  Delete timer
                </v-tooltip>
              </v-btn>
              <v-btn icon @click="openTimerDialog(task, timer)">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">edit</v-icon>
                  </template>
                  Edit timer
                </v-tooltip>
              </v-btn>
            </div>
          </v-list-item-icon>
        </v-list-item>
      </template>
    </v-list>
    <p v-else>No work logged yet.</p>
  </div>
</template>
<script>
import TimerDialog from './TimerDialog';

export default {
  components: {
    TimerDialog
  },
  data() {
    return {
      newValues: [],
      timers: [],
    }
  },
  props: {
    task: {
      name: null,
      due: null,
      status: null,
      order: null,
      tags: [],
      timers: []
    },
    dense: {
      type: Boolean
    },
    twoLine: {
      type: Boolean
    }
  },
  created() { },
  mounted() {
    const self = this;
    this.$eventBus.$on('TIMER_START', (value) => {
      self.startTimer(value);
    });
  },
  methods: {
    openTimerDialog(task, timer) {
      const self = this;
      self.$store.commit('setActiveTask', task);
      self.$store.commit('setActiveTimer', timer);
      self.$eventBus.$emit('TIMER_UPDATE_DIALOG', true)
    },

    /**
     * Conditionally pads a number with "0"
     */
    _padNumber: number =>  (number > 9) ? number : "0" + number,

    /**
     * Splits seconds into hours, minutes, and seconds.
     */
    _readableTimeFromSeconds: function(seconds) {
      seconds = parseInt(seconds);
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60),
      }
    },

    /**
     * Redirects user back to login page if logged out
     */
    authenticationCheck: function(statusCode) {
      if (parseInt(statusCode) == 419) {
        // Logged out
        this.$router.go("/");
      }
    },

    /**
     * Calculate the amount of time spent on the task using the timer object.
     */
    calculateTimeSpent: function (timer) {
      if (timer.stopped_at) {
        const started = moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const stopped = moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const seconds = parseInt(moment.duration(stopped.diff(started)).asSeconds())
        const time = this._readableTimeFromSeconds(seconds);
        return `${time.hours}:${time.minutes}:${time.seconds}`; // for syntax highlighting: `
      }
      return '';
    },

    /**
     * Determines if there is an active timer and whether it belongs to the task
     * passed into the function.
     */
    showTimerForTask: function (task, timer) {
      return this.$store.state.activeTimerCounter.timer &&
        this.$store.state.activeTimerCounter.timer.id === timer.id &&
        this.$store.state.activeTimerCounter.timer.task.id === task.id
    },

    /**
     * Start counting the timer. Tick tock.
     */
    startTimer: function (timer) {
      // remove 'z' from timezone, moment doesn't like it
      // when doing maths
      const started = moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
      const now = moment();

      this.$store.state.activeTimerCounter.timer = timer
      this.$store.state.activeTimerCounter.seconds = parseInt(moment.duration(now.diff(started)).asSeconds())
      this.$store.state.activeTimerCounter.ticker = setInterval(() => {
        const time = this._readableTimeFromSeconds(++this.$store.state.activeTimerCounter.seconds)
        this.$store.state.activeTimerCounter.title = `${time.hours}:${time.minutes}:${time.seconds}`
      }, 1000)
    },

    /**
     * Stop the timer from the API and then from the local activeTimerCounter.
     */
    stopTimer: function () {
      const timer = {
        tz: Intl.DateTimeFormat().resolvedOptions().timeZone
      };
      window.axios.post(`/api/tasks/${this.$store.state.activeTimerCounter.timer.id}/timers/stop`, timer)
        .then(response => {
          // Stop the ticker
          clearInterval(this.$store.state.activeTimerCounter.ticker)

          this.$store.commit('setStoppedTimer', response.data);
          this.$store.commit('setStoppedTimerValue', this.calculateTimeSpent(response.data));
          // Reset the activeTimerCounter and timer string
          this.$store.commit('setActiveTimerCounter', { seconds: 0, timer: null });
          this.$store.state.activeTimerCounter.title = 'Timing...';
        })
    },

    /**
     * Delete a timer.
     */
    deleteTimer: function (task, timer) {
      window.axios.delete(`/api/tasks/${task.id}/timer/${timer.id}`)
        .then(
          response => {
            task.timers = task.timers
              .filter(function(element) {
                return element.id !== timer.id;
              });
          })
        .catch(error => {
          this.authenticationCheck(error.response.status);
        });
    },

    dateFormat(dateValue) {
      return moment(dateValue).format('YYYY-MM-DD')
    },
  },
  computed: {
    getActiveTimerString() {
      return ;
    }
  }
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/_variables";
.active-timer {
  background-color: $selected-light-grey;
  border-left: solid 4px $selected-dark-grey;
  border-top-right-radius: 30px;
  border-bottom-right-radius: 30px;
}
</style>
