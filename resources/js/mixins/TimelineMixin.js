export default {
  data(){
    return {
    }
  },
  methods: {
    loadTimeline() {
      const self = this;
      window.axios.get('/api/tasks/current').then(response => {
        self.tasks = response.data.data;
        self.$store.commit('setTasks', response.data.data);
        self.tasksCopy = response.data.data;
        self.searchByStatus();
      });
    },
  }
}

