import EventNames from '../common/EventNames';

export default {
  data(){
    return {
    }
  },
  methods: {
    openTaskDialog: function(task) {
      const self = this;
      if (task) {
        self.$store.commit('setSelectedTask', task);
        self.$eventBus.$emit(EventNames.TASK_UPDATE_DIALOG, true)
      } else {
        self.$store.commit('setSelectedTask', null);
        self.$eventBus.$emit(EventNames.TASK_ADD_DIALOG, true)
      }
    },
    loadTasks() {
      const self = this;
      window.axios.get('/api/tasks/current').then(response => {
        self.tasks = response.data.data;
        self.$store.commit('setTasks', response.data.data);
        self.tasksCopy = response.data.data;
      });
    },
    saveOrder() {
      var tasks = [];
      for (let index = 0; index < this.tasks.length; index++) {
        const currentTask = this.tasks[index];
        tasks.push({ "id": currentTask.id, "order": index });
      }
      if (tasks.length > 1) {
        window.axios.post(`/api/tasks/order`, { "tasks": tasks })
          .then()
          .catch(error => {
          console.log(error);
          this.$store.commit('showSnackbar', {
            message: error.response.data.data.message,
            color: 'error',
            duration: 3000
          });
        });
      }
    },

    deleteTask() {
      const self = this;

      self.$store.commit('showDialog',{
        type: "confirm",
        icon: 'warning',
        title: "Confirm deletion",
        message: "Are you sure you want to delete this task?",
        okCb: ()=>{
          const currentTask = self.task;
          window.axios.delete('/api/tasks/'+parseInt(currentTask.id))
            .then(function(response) {
              self.$store.commit('setSelectedTask', currentTask);
              self.$store.commit('showSnackbar',{
                message: response.data.message,
                color: 'success',
                duration: 3000
              });
              self.$eventBus.$emit(EventNames.TASK_DELETED);
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
        window.axios.post(`/api/tasks/${currentTask.id}/status`, { status: newStatus })
            .then()
            .catch(error => {
            console.log(error);
            self.$store.commit('showSnackbar', {
              message: error.response.data.data.message,
              color: 'error',
              duration: 3000
            });
        });
        currentTask.status = newStatus
        self.btnColor = btnColor
      }
    },
    updateName() {
      const currentTask = this.task;

      if (currentTask.name != this.newName) {
        window.axios.post(`/api/tasks/${currentTask.id}/name`, { name: this.newName })
            .then(response => {
            currentTask.name = this.newName;
            return response;
          })
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.data.message,
              color: 'error',
              duration: 3000
          });
        });
      }

      this.editMode = false;
    },
    getActiveTask() {
      const task_id = this.$store.state.task.id == 0 || typeof this.$store.state.task.id == 'undefined' ? null : this.$store.state.task.id;
      let task = this.$store.state.tasks
        .filter(function(element) {
          return element.id === task_id;
        });
      task = task.length > 0 ? task[0] : null;
      return task;
    },
    getTimers(task, onlyActive) {
      const self = this;
      if (onlyActive) {
        let timer = task.timers
          .filter(function(element) {
            if (self.$store.state.activeTimerCounter.timer) {
              return element.id === self.$store.state.activeTimerCounter.timer.id;
            }
          });
        return timer.length > 0 ? [timer[0]] : [];
      }
      return task.timers;
    },
    getTotalTime(task) {
      let totalDuration = 0;
      task.timers.forEach(timer => {
        totalDuration += timer.duration || 0;
      });
      return this.$appFormatters.duration(totalDuration, 'seconds');
    }
  }
}
