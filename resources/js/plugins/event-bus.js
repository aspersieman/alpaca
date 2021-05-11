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
        taskDialogUpdate(show, task) {
          this.$emit('task-dialog-update', show, task);
        },

        /* Timers */
        timerAdded(timer) {
          this.$emit('timer-added', timer);
        },
        timerUpdated(timer) {
          this.$emit('timer-updated', timer);
        },
        timerDeleted(timer) {
          this.$emit('timer-deleted', timer);
        },
        timerStopped(timer) {
          this.$emit('timer-stopped', timer);
        },
        timerDialogAdd(show, task) {
          this.$emit('timer-dialog-add', show, task);
        },
        timerDialogUpdate(show, timer) {
          this.$emit('timer-dialog-update', show, timer);
        },

        /* Notes */
        notesLoaded() {
          this.$emit('notes-loaded');
        },
        noteAdded(note) {
          this.$emit('note-added', note);
        },
        noteUpdated(note) {
          this.$emit('note-updated', note);
        },
        noteDeleted(note) {
          this.$emit('note-deleted', note);
        },
        noteDrawerToggle(state, notes = null, task = null) {
          this.$emit('note-drawer-toggle', state, notes, task);
        },
        noteDialogAdd(show, task = null) {
          this.$emit('note-dialog-add', show, task);
        },
        noteDialogUpdate(show, note) {
          this.$emit('note-dialog-update', show, note);
        },
      }
    })
  }
}

export default eventBus;
