const eventBus = {
  install(Vue) {
    Vue.prototype.$eventBus = new Vue({
      methods: { 
        /* Auth */
        signUp() { 
          this.$emit('sign-up');
        },
        signIn() {
          this.$emit('sign-in');
        },
        userRegistered() {
          this.$emit('user-registered');
        },
        userLoggedIn() {
          this.$emit('user-logged-in');
        },
        userLoggedOut() {
          this.$emit('user-logged-out');
        },

        /* Dashboard/charts */
        dashboardParametersChanged(parameters) {
          this.$emit('dashboard-parameters-changed', parameters);
        },

        /* Tasks */
        tasksLoaded() {
          this.$emit('tasks-loaded');
        },
        taskAdded() {
          this.$emit('task-added');
        },
        taskUpdated() {
          this.$emit('task-updated');
        },
        taskDeleted() {
          this.$emit('task-deleted');
        },
        taskDialogAdd(show) {
          this.$emit('task-add-dialog', show);
        },

        /* Timers */
        timerAdded() {
          this.$emit('timer-added');
        },
        timerUpdated() {
          this.$emit('timer-updated');
        },
        timerDeleted() {
          this.$emit('timer-deleted');
        },

        /* Notes */
        noteAdded() {
          this.$emit('note-added');
        },
        noteUpdated() {
          this.$emit('note-updated');
        },
        noteDeleted() {
          this.$emit('note-deleted');
        },
      }
    })
  }
}

export default eventBus;
