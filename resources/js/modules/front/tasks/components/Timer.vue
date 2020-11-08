<template>
  <div 
    v-if="task.timers.length > 0"
    class="timers"
  >
    <v-card 
      v-for="timer in task.timers" 
      :key="timer.id"
      text 
      flat 
    >
      <div :class="showTimerForTask(task, timer) ? 'active-timer' : ''">
        <v-layout
          row
          wrap
          class="timer"
        >
          <v-flex 
            xs1
            sm1
            md1
            lg1
          >
            <v-icon class="ml-3">
              access_time
            </v-icon>
          </v-flex>
          <v-flex
            xs6
            sm3
            md3
            lg3
            class="ml-2"
          >
            <div class="fs-90 ellipsis">
              {{ timer.name }}
            </div>
            <div class="caption">
              {{ dateFormat(timer.started_at, true) }}
            </div>
          </v-flex>
          <v-flex
            v-if="$vuetify.breakpoint.smAndUp"
            sm4
            md4
            lg4
          >
            <div 
              :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}"
            >
              <v-chip
                v-for="tag in timer.tagged"
                :key="tag.id"
                class="ma-2"
                x-small
              >
                {{ tag.tag_name }}
              </v-chip>
            </div>
          </v-flex>
          <v-spacer />
          <v-flex
            xs4
            sm3
            md3
            lg3 
            class="fs-90"
          >
            <div v-if="showTimerForTask(task, timer)">
              {{ $store.state.activeTimerCounter.title }}
              <v-btn 
                v-if="showTimerForTask(task, timer)" 
                icon 
                @click="stopTimer()"
              >
                <v-tooltip bottom>
                  <template #activator="{ on }">
                    <v-icon v-on="on">
                      stop_circle
                    </v-icon>
                  </template>
                  Stop timer
                </v-tooltip>
              </v-btn>
            </div>
            <v-layout 
              v-else 
              row
            >
              <v-flex
                v-if="$store.state.stoppedTimer && $store.state.stoppedTimer.id == timer.id"
                class="horizontal-center fs-90"
              >
                {{ $store.state.stoppedTimerValue }}
              </v-flex>
              <v-flex
                v-else
                class="horizontal-center fs-90"
              >
                {{ calculateTimeSpent(timer) }}
              </v-flex>
              <v-flex class="horizontal-center">
                <v-btn 
                  icon
                  @click="deleteTimer(task, timer)"
                >
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon v-on="on">
                        delete
                      </v-icon>
                    </template>
                    Delete timer
                  </v-tooltip>
                </v-btn>
                <v-btn
                  icon
                  @click="openTimerUpdateDialog(task, timer)"
                >
                  <v-tooltip bottom>
                    <template
                      #activator="{ on }"
                    >
                      <v-icon v-on="on">
                        edit
                      </v-icon>
                    </template>
                    Edit timer
                  </v-tooltip>
                </v-btn>
              </v-flex>
            </v-layout>
          </v-flex>
        </v-layout>
      </div>
    </v-card>
  </div>
</template>
<script>

import moment from 'moment';
import TimerMixin from '../mixins/TimerMixin';

export default {
  components: { },
  mixins: [ TimerMixin ],
  props: {
    task: {
      required: true,
      type: Object,
      default: function() {
        return {
          name: null,
          due: null,
          status: null,
          order: null,
          timers: []
        }
      }
    },
  },
  data() {
    return {
    }
  },
  mounted() {
    const self = this;
    this.$eventBus.$on('timer-start', (value) => {
      self.startTimer(value);
    });
  },
  methods: {
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
      if (this.$store.state.activeTimerCounter.timer === null || this.$store.state.activeTimerCounter.timer.id != timer.id) {
        // remove 'z' from timezone, moment doesn't like it
        // when doing maths
        const started = moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const now = moment();

        this.$store.state.activeTimerCounter.timer = timer
        this.$store.state.activeTimerCounter.seconds = parseInt(moment.duration(now.diff(started)).asSeconds())
        this.$store.state.activeTimerCounter.ticker = setInterval(() => {
          const time = this._readableTimeFromSeconds(++this.$store.state.activeTimerCounter.seconds)
          this.$store.state.activeTimerCounter.title = `${time.hours}:${time.minutes}:${time.seconds}`
        }, 1000);
      }
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

          this.$store.commit('setStoppedTimer', response.data.data);
          this.$store.commit('setStoppedTimerValue', this.calculateTimeSpent(response.data.data));
          // Reset the activeTimerCounter and timer string
          this.$store.commit('setActiveTimerCounter', { seconds: 0, timer: null });
          this.$store.state.activeTimerCounter.title = 'Timing...';
        })
    },

    /**
     * Delete a timer.
     */
    deleteTimer: function (task, timer) {
      const self = this;
      self.$store.commit('showDialog',{
        type: "confirm",
        icon: 'warning',
        title: "Confirm deletion",
        message: "Are you sure you want to delete this timer?",
        okCb: ()=>{
          window.axios.delete(`/api/tasks/${task.id}/timer/${timer.id}`)
            .then(function(response) {
              task.timers = task.timers.filter(function(element) {
                return element.id !== timer.id;
              });
              self.$store.commit('showSnackbar',{
                message: response.data.message,
                color: 'success',
                duration: 3000
              });
              self.$eventBus.$emit('timer-deleted');
            })
            .catch(error => {
              self.$store.commit('hideLoader');
              if (error.response) {
                self.$store.commit('showSnackbar',{
                  message: error.response.data.data.message,
                  color: 'error',
                  duration: 10000
                });
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log('Error', error.message);
              }
            });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },

    dateFormat(dateValue, time = false) {
      if (time) {
        return moment(dateValue).format('YYYY-MM-DD @ HH:mm:SS');
      } else {
        return moment(dateValue).format('YYYY-MM-DD');
      }
    },
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

.active-timer {
  background-color: $selected-light-grey;
}
.timers {
  max-width: 500px;
}
.v-list-item {
  height: 20px;
}
.horizontal-center {
  margin: auto;
}

.fs-90 {
 font-size: 0.90rem;
}

.caption {
  white-space: nowrap;
}
</style>
