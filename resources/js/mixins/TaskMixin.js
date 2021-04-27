export default {
  methods: {
    getTasks(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/getTasks', payload)
          .then(() =>{
            this.$eventBus.tasksLoaded();
            resolve()
          })
          .catch(error => reject(error))
      })
    },

    saveTask(task) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/saveTask', task)
          .then(() =>{
            this.$eventBus.taskAdded();
            this.$flash.success({
              text: 'Task added successfully'
            });
            this.getTasks();
            resolve()
          })
          .catch(error => {
            const message = error?.response?.data?.message || 'ERROR';
            console.error(message);
            this.$flash.error({
              text: 'Error adding task'
            });
            reject(error);
          });
      });
    },

    updateTask(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/updateTask', payload)
          .then(() =>{
            this.text.success({
              content: 'Task updated successfully'
            });
            this.getTasks();
            this.$eventBus.taskUpdated();
            resolve()
          })
          .catch(error => {
            const message = error.response.data.message || 'ERROR';
            console.error(message);
            this.$flash.error({
              text: 'Error updating task'
            });
            reject(error);
          });
      });
    },

    deleteTask(taskId) {
      this.$store.dispatch('dialog/dialogShow',{
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this task?',
        okCb: ()=>{
          new Promise((resolve, reject) => {
            this.$store.dispatch('task/deleteTask', taskId)
              .then(({data}) => {
                this.getTasks();
                this.$flash.success({
                  text: 'Task deleted successfully'
                });
                this.$eventBus.taskDeleted();
                this.$store.dispatch('dialog/dialogHide');
                resolve(data)
              }).catch((error) => {
                console.error(error)
                this.$flash.error({
                  text: 'Error occurred deleting task'
                });
                this.$store.dispatch('dialog/dialogHide');
                reject(error);
              })
          });
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      });
    },

    getTaskActive() {
      const task_id = this.$store.getters['task/task'].id == 0
        || typeof this.$store.getters['task/task'].id == 'undefined'
        ? null : this.$store.getters['task/task'].id;
      let task = this.$store.getters['task/tasks']
        .filter(function(element) {
          return element.id === task_id;
        });
      return task.length > 0 ? task[0] : null;
    },

    getTaskTotalTime(task) {
      let totalDuration = 0;
      task.timers.forEach(timer => {
        totalDuration += timer.duration || 0;
      });
      return this.$appFormatters.duration(totalDuration);
    },

    getTaskPriorityDescription(priority) {
      let description = '';
      switch (priority) {
        case 1:
          description = 'High';
          break;
        case 2:
          description = 'Medium';
          break;
        case 3:
          description = 'Low';
          break;
      }
      return description;
    },

    changeTaskStatus(task) {
      var newStatus = null;
      switch (task.status) {
        case 'ongoing':
          newStatus = 'complete';
          break;
        case 'complete':
          newStatus = 'overdue';
          break;
        case 'overdue':
          newStatus = 'ongoing';
          break;
        default:
          break;
      }
      if (newStatus) {
        task.status = newStatus
        this.updateTask(task);
      }
    },

    getTaskPriorityById(priorityId) {
      let priority = this.$store.getters['task/priorities']
        .filter(function(element) {
          return element.id == priorityId;
        })[0];
      priority = priority || { id: 0, name: '' };
      return priority;
    },

    getTaskTimers(task, onlyActive) {
      const self = this;
      if (onlyActive) {
        let timers = task.timers
          .filter(function(element) {
            if (self.$store.getters['timer/timerRunning'].timer) {
              return element.id === self.$store.getters['timer/timerRunning'].timer.id;
            }
          });
        return timers.length > 0 ? [timers[0]] : [];
      }
      return task.timers;
    },
  }
}
