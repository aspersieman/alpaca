const eventBus = {
  install(Vue) {
    Vue.prototype.$eventBus = new Vue({
      emits: [
        'sign-up',
        'sign-in',
        'user-registered',
        'user-logged-in',
        'user-logged-out',
        'dashboard-parameters-changed',
        'tasks-loaded',
        'task-loaded',
        'task-added',
        'task-updated',
        'task-deleted',
        'task-dialog-add',
        'task-dialog-update',
        'timer-added',
        'timer-updated',
        'timer-deleted',
        'timer-stopped',
        'timer-dialog-add',
        'timer-dialog-update',
        'notes-loaded',
        'note-added',
        'note-updated',
        'note-deleted',
        'note-drawer-toggle',
        'note-dialog-add',
        'note-dialog-update',
      ],
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
        taskLoaded(task) {
          this.$emit('task-loaded', task);
        },
        taskAdded(task) {
          this.$emit('task-added', task);
        },
        taskUpdated(task) {
          this.$emit('task-updated', task);
        },
        taskDeleted(task) {
          this.$emit('task-deleted', task);
        },
        taskDialogAdd(show) {
          this.$emit('task-dialog-add', show);
        },
        taskDialogUpdate(show) {
          this.$emit('task-dialog-update', show);
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
        timerStopped() {
          this.$emit('timer-stopped');
        },
        timerDialogAdd(show) {
          this.$emit('timer-dialog-add', show);
        },
        timerDialogUpdate(show) {
          this.$emit('timer-dialog-update', show);
        },

        /* Notes */
        notesLoaded() {
          this.$emit('notes-loaded');
        },
        noteAdded() {
          this.$emit('note-added');
        },
        noteUpdated() {
          this.$emit('note-updated');
        },
        noteDeleted() {
          this.$emit('note-deleted');
        },
        noteDrawerToggle(state) {
          this.$emit('note-drawer-toggle', state);
        },
        noteDialogAdd(show) {
          this.$emit('note-dialog-add', show);
        },
        noteDialogUpdate(show) {
          this.$emit('note-dialog-update', show);
        },
      }
    })
  }
}

export default eventBus;
