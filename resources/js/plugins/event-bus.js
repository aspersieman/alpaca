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
