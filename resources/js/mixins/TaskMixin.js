export default {
  methods: {
    getTaskTotalTime(task) {
      let totalDuration = 0;
      task?.timers?.forEach(timer => {
        totalDuration += timer.duration || 0;
      });
      return this.$appFormatters.duration(totalDuration);
    },

    getTaskPriorityDescription(priority) {
      let description = '';
      switch (priority) {
        case 1:
          description = 'High';
          break;
        case 2:
          description = 'Medium';
          break;
        case 3:
          description = 'Low';
          break;
      }
      return description;
    },

    getTaskTimers(task, onlyActive) {
      if (onlyActive) {
        let timers = task?.timers?.filter((element) => {
            if (this.$store.getters['timer/timerRunning']?.timer) {
              return element.id === this.$store.getters['timer/timerRunning']?.timer.id;
            }
          });
        return timers?.length > 0 ? [timers[0]] : [];
      }
      return task.timers;
    },
  }
}
