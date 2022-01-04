import Vue from 'vue'

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
Vue.use(eventBus);
