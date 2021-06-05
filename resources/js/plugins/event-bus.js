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
