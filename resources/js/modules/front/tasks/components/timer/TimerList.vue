<template>
  <div
    v-if="getTaskTimers(task, onlyActive).length > 0"
  >
    <timer
      v-for="timer in getTaskTimers(task, onlyActive)"
      :key="timer.id"
      :timer="timer"
      :task="task"
    />
  </div>
</template>
<script>

import Timer from './Timer';
import TimerMixin from '@/mixins/TimerMixin';
import TaskMixin from '@/mixins/TaskMixin';

export default {
  components: {
    Timer
  },
  mixins: [
    TimerMixin,
    TaskMixin
  ],
  props: {
    task: {
      required: true,
      type: Object,
      default: function() {
        return {
          name: null,
          due: null,
          status: null,
          order: null,
          timers: []
        }
      }
    },
    onlyActive: {
      required: false,
      type: Boolean,
      default: false
    }
  },
  created() {
    const self = this;
    self.$eventBus.$on('timer-start', (timer) => {
      self.startTimer(timer);
    });
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../../sass/index";
</style>
