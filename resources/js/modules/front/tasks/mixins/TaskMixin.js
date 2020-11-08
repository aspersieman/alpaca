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
        self.$eventBus.$emit('task-update-dialog', true)
      } else {
        self.$store.commit('setSelectedTask', null);
        self.$eventBus.$emit('task-add-dialog', true)
      }
    },
  }
}
