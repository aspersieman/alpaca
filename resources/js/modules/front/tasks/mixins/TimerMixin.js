export default {
  data(){
    return {
    }
  },
  methods: {
    openTimerUpdateDialog(task, timer) {
      const self = this;
      self.$store.commit('setSelectedTask', task);
      self.$store.commit('setSelectedTimer', timer);
      self.$eventBus.$emit('timer-update-dialog', true)
    },
    openTimerAddDialog(task) {
      const self = this;
      self.$store.commit('setSelectedTask', task);
      self.$eventBus.$emit('timer-add-dialog', true)
    },
  }
}

