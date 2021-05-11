export default {
  methods: {
    saveTimer(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('timer/saveTimer', payload)
          .then((data) =>{
            this.$store.dispatch('task/getTask', data.task_id)
              .then(() => {
                this.$store.dispatch('timer/startTimer', data)
                  .then(() => {
                    this.$flash.success({
                      text: 'Timer added successfully'
                    });
                    this.$eventBus.timerAdded(data);
                  });
              });
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
      const started_at = window.moment(payload.startDate + ' ' + payload.startTime, 'YYYY-MM-DD HH:mm:ss');
      const timer = {
        name: payload.name,
        started_at: started_at.format('YYYY-MM-DD HH:mm:ss'),
        stopped_at: started_at.add(payload.duration, 'minutes').format('YYYY-MM-DD HH:mm:ss'),
        tags: payload.tags,
        task_id: payload.task_id
      };
      this.$store.dispatch('timer/updateTimer', timer)
        .then((data) =>{
          this.$flash.success({
            text: 'Timer updated successfully'
          });
          this.$store.dispatch('task/getTask', payload.task_id);
          this.$eventBus.timerUpdated(data);
        })
        .catch((error) => {
          console.error(error);
          this.$flash.error({
            text: 'Error updating timer'
          });
        });
    },

    deleteTimer(payload) {
      const settings = {
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this timer?',
        okCb: ()=>{
          this.$store.dispatch('timer/deleteTimer', payload)
            .then(({data}) => {
              task.timers = task.timers.filter(function(element) {
                return element.id !== timer.id;
              });
              this.$flash.success({
                text: data.data.message,
              });
              this.$store.dispatch('task/getTask', data.task_id);
              this.$eventBus.timerDeleted(payload);
              this.$store.dispatch('dialog/dialogHide');
            }).catch((error) => {
              console.error(error)
              this.$flash.error({
                text: 'Error occurred deleting timer'
              });
              this.$store.dispatch('dialog/dialogHide');
            })
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      };
      this.$store.dispatch('dialog/dialogShow', settings);
    },

    stopTimer(payload) {
      return this.$store.dispatch('timer/stopTimer', payload)
        .then((data) =>{
          this.$store.dispatch('timer/resetTimerRunning');
          this.$store.dispatch('task/getTask', data.task_id)
            .then(() => this.$eventBus.timerStopped(data));
        }).catch(error => {
          console.error(error);
          this.$flash.error({
            text: 'Error stopping timer'
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
