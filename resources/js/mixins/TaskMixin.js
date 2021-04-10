export default {
  methods: {
    getTasks(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/getTasks', provider)
          .then(() =>{
            this.$eventBus.tasksLoaded();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    saveTask(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/saveTask', provider)
          .then(() =>{
            this.$eventBus.taskAdded();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    updateTask(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/updateTask', provider)
          .then(() =>{
            this.$eventBus.taskUpdated();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    deleteTask(provider) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('task/deleteTask', provider)
          .then(() =>{
            this.$eventBus.taskDeleted();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    getTotalTime(task) {
      let totalDuration = 0;
      task.timers.forEach(timer => {
        totalDuration += timer.duration || 0;
      });
      return this.$appFormatters.duration(totalDuration);
    },
    getPriorityDescription(priority) {
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
    changeStatus() {
      const self = this;
      const currentTask = self.task;

      var newStatus = null;
      var btnColor = null;
      switch (currentTask.status) {
        case 'ongoing':
          newStatus = 'complete';
          btnColor = 'success';
          break;
        case 'complete':
          newStatus = 'overdue';
          btnColor = 'error';
          break;
        case 'overdue':
          newStatus = 'ongoing';
          btnColor = 'info';
          break;
        default:
          break;
      }
      if (newStatus) {
        currentTask.status = newStatus
        this.$store.dispatch('task/updateTask', task);
        self.btnColor = btnColor
      }
    },
    getPriorityById(priorityId) {
      let priority = this.$store.getters['task/priorities']
        .filter(function(element) {
          return element.id == priorityId;
        })[0];
      priority = priority || { id: 0, name: '' };
      return priority;
    }
  }
}
