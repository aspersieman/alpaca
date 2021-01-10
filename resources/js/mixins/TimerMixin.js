import methods from '../common/Methods';

export default {
  data(){
    return {
    }
  },
  methods: {
    openTimerUpdateDialog(task, timer) {
      const self = this;
      self.$store.commit('setSelectedTask', task);
      self.$store.commit('setSelectedTimer', timer);
      self.$eventBus.$emit('timer-update-dialog', true)
    },

    openTimerAddDialog(task) {
      const self = this;
      self.$store.commit('setSelectedTask', task);
      self.$eventBus.$emit('timer-add-dialog', true)
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
     * Calculate the amount of time spent on the task using the timer object.
     */
    calculateTimeSpent: function (timer) {
      if (timer.stopped_at != null) {
        const started = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const stopped = window.moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const seconds = parseInt(window.moment.duration(stopped.diff(started)).asSeconds())
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

    isActiveTimer: function () {
      return this.$store.state.activeTimerCounter.timer &&
        this.$store.state.activeTimerCounter.timer.id != null;
    },

    /**
     * Start counting the timer. Tick tock.
     */
    startTimer: function (timer) {
      if (this.$store.state.activeTimerCounter.timer === null || this.$store.state.activeTimerCounter.timer.id != timer.id) {
        // remove 'z' from timezone, moment doesn't like it
        // when doing maths
        const started = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const now = window.moment();
        this.$store.commit('setActiveTimerCounter', {
          seconds: parseInt(window.moment.duration(now.diff(started)).asSeconds()), 
          timer: timer,
          title: 'Timing...',
          task: timer.task
        });

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
          this.$eventBus.$emit('timer-updated');
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

    timerFormat(dateValue, time = false) {
      let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (time) {
        const date = window.moment(dateValue, 'YYYY-MM-DDTHH:mm:ss.SSS');
        return window.moment.tz(date, tz).format('YYYY-MM-DD @ HH:mm:SS');
      } else {
        return window.moment.tz(dateValue, tz).format('YYYY-MM-DD');
      }
    },

    setActiveTimer(timer = null) {
      const self = this;
      if (timer) {
        self.$store.commit('setActiveTimer', timer);
        self.$eventBus.$emit('timer-start', timer)
      } else {
        window.axios.get('/api/tasks/timers/active').then(response => {
          if (response.data.data.id !== undefined) {
            self.$store.commit('setActiveTimer', response.data.data);
            self.$eventBus.$emit('timer-start', response.data.data)
          }
        });
      }
    },

    addTimer() {
      const task = this.$store.state.task;
      const self = this;
      if(this.name.length > 0) {
        const timer = {
          name: this.name,
          task_id: this.task_id,
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
          tags: this.tags
        };
        window.axios.post(`/api/tasks/${task.id}/timers`, timer)
          .then(response => {
            self.setActiveTimer(response.data.data);
            this.$eventBus.$emit('timer-added');
            this.$eventBus.$emit('timer-start', response.data.data);
            this.loading = false;
            this.dialog = false;
            this.formReset();
            this.$store.commit('showSnackbar',{
              message: "Timer added",
              color: 'success',
              duration: 1000
            });
          })
          .catch(error => {
          console.log(error);
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
          });
        this.loading = true;
      }
    },
    updateTimer() {
      if(this.name.length > 0 && this.startDate.length >= 4) {
        this.loading = true;
        
        const started_at = window.moment(this.startDate + " " + this.startTime, "YYYY-MM-DD HH:mm:ss");
        const timer = {
          name: this.name,
          started_at: started_at.format("YYYY-MM-DD HH:mm:ss"),
          stopped_at: started_at.add(this.duration, 'minutes').format("YYYY-MM-DD HH:mm:ss"),
          tags: this.tags
        };

        window.axios.put(`/api/tasks/${this.$store.state.task.id}/timer/${this.$store.state.timer.id}`, timer)
          .then(response => {
            this.loading = false;
            this.dialog = false;
            this.formReset();
            this.$store.commit('showSnackbar',{
              message: "Timer updated",
              color: 'success',
              duration: 1000
            });
            this.$eventBus.$emit('timer-updated');
            return response;
        })
          .catch(error => {
          console.log(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });
      }
    },
  },

  getTimersByDates(fromDate, toDate) {
    methods.debug(fromDate);
    methods.debug(toDate);

    const started_at = window.moment(this.startDate + " " + this.startTime, "YYYY-MM-DD HH:mm:ss");
    const timer = {
      name: this.name,
      started_at: started_at.format("YYYY-MM-DD HH:mm:ss"),
      stopped_at: started_at.add(this.duration, 'minutes').format("YYYY-MM-DD HH:mm:ss"),
      tags: this.tags
    };

    window.axios.put(`/api/timers/${this.$store.state.task.id}/timer/${this.$store.state.timer.id}`, timer)
      .then(response => {
        this.loading = false;
        this.dialog = false;
        this.formReset();
        this.$store.commit('showSnackbar',{
          message: "Timer updated",
          color: 'success',
          duration: 1000
        });
        this.$eventBus.$emit('timer-updated');
        return response;
      })
      .catch(error => {
        console.log(error);
        this.$store.commit('showSnackbar',{
          message: error.response.data.message,
          color: 'error',
          duration: 3000
        });
        this.loading = false;
      });
  }
}

