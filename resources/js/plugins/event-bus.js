const eventBus = {
  install(Vue) {
    Vue.prototype.$eventBus = new Vue({
      emits: [
        'dashboard-parameters-changed',
        'note-drawer-toggle',
        'note-dialog-add',
        'note-dialog-update',
      ],
      methods: {
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
