export default {
  methods: {
    saveTimer(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/saveTimer', payload)
          .then(({data}) =>{
            this.$store.dispatch('timer/startTimer', data.data)
              .then(() => {
                this.$store.dispatch('task/getTasks');
              });
            this.$flash.success({
              text: 'Timer added successfully'
            });
            this.$eventBus.timerAdded();
            resolve()
          }).catch(error => {
            console.error(error);
            this.$flash.error({
              text: 'Error adding timer'
            });
            reject(error);
          });
      });
    },

    updateTimer(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/updateTimer', payload)
          .then(() =>{
            this.$flash.success({
              text: 'Timer updated successfully'
            });
            this.$store.dispatch('task/getTasks');
            this.$eventBus.timerUpdated();
            resolve()
          })
          .catch((error) => {
            console.error(message);
            this.$flash.error({
              text: 'Error updating timer'
            });
            reject(error);
          });
      })
    },

    deleteTimer(payload) {
      const settings = {
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this timer?',
        okCb: ()=>{
          new Promise((resolve, reject) => {
            this.$store.dispatch('timer/deleteTimer', payload)
              .then(({data}) => {
                task.timers = task.timers.filter(function(element) {
                  return element.id !== timer.id;
                });
                this.$flash.success({
                  text: data.data.message,
                });
                this.$store.dispatch('task/getTasks');
                this.$eventBus.timerDeleted();
                this.$store.dispatch('dialog/dialogHide');
                resolve(data);
              }).catch((error) => {
                console.error(error)
                this.$flash.error({
                  text: 'Error occurred deleting timer'
                });
                this.$store.dispatch('dialog/dialogHide');
                reject(error);
              })
          });
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      };
      this.$store.dispatch('dialog/dialogShow', settings);
    },

    stopTimer(payload) {
      console.log("TimerMixin: stopTimer");
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/stopTimer', payload)
          .then(() =>{
            this.$store.dispatch('timer/resetTimerRunning');
            this.$store.dispatch('task/getTasks');
            resolve()
          }).catch(error => {
            console.error(error);
            this.$flash.error({
              text: 'Error stopping timer'
            });
            reject(error);
          });
      });
    },

    /**
     * Calculate the amount of time spent on the task using the timer object.
     */
    timerDuration(timer) {
      if (timer.stopped_at != null) {
        const from = window.moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const to = window.moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
        const seconds = parseInt(window.moment.duration(to.diff(from)).asSeconds())
        return this.$appFormatters.duration(seconds);
      }
      return '';
    },

    /**
     * Determines if there is a running timer and whether it belongs to the task
     * passed into the function.
     */
    showTimerForTask(task, timer) {
      return this.$store.getters['timer/timerRunning'].timer &&
        this.$store.getters['timer/timerRunning'].timer.id === timer.id &&
        this.$store.getters['timer/timerRunning'].timer.task_id === task.id
    },
  },
}
