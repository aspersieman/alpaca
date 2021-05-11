export default {
  methods: {
    getTasks(payload) {
      return this.$store.dispatch('task/getTasks', payload)
        .then(() => this.$eventBus.tasksLoaded());
    },

    saveTask(payload) {
      return this.$store.dispatch('task/saveTask', payload)
        .then(({data}) =>{
          this.$eventBus.taskAdded(data.task);
          this.$flash.success({
            text: 'Task added successfully'
          });
        })
        .catch(error => {
          console.error(error);
          this.$flash.error({
            text: 'Error adding task'
          });
        });
    },

    updateTask(payload) {
      return this.$store.dispatch('task/updateTask', payload)
        .then(({data}) =>{
          this.$flash.success({
            text: 'Task updated successfully'
          });
          this.$eventBus.taskUpdated(data);
        })
        .catch(error => {
          console.error(error);
          this.$flash.error({
            text: 'Error updating task'
          });
        });
    },

    deleteTask(payload) {
      this.$store.dispatch('dialog/dialogShow',{
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this task?',
        okCb: () => {
          this.$store.dispatch('dialog/dialogHide');
          this.$store.dispatch('task/deleteTask', payload)
            .then(({data}) => {
              this.$flash.success({
                text: 'Task deleted successfully'
              });
              this.$eventBus.taskDeleted(data);
              this.$store.dispatch('dialog/dialogHide');
            }).catch((error) => {
              console.error(error)
              this.$flash.error({
                text: 'Error occurred deleting task'
              });
            })
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      });
    },

    getTaskTotalTime(task) {
      let totalDuration = 0;
      task?.timers?.forEach(timer => {
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

    getTaskTimers(task, onlyActive) {
      if (onlyActive) {
        let timers = task?.timers?.filter((element) => {
            if (this.$store.getters['timer/timerRunning']?.timer) {
              return element.id === this.$store.getters['timer/timerRunning']?.timer.id;
            }
          });
        return timers?.length > 0 ? [timers[0]] : [];
      }
      return task.timers;
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
  }
}
