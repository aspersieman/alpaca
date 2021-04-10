export default {
  data(){
    return {
    }
  },
  methods: {
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
