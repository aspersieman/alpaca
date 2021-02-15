<template>
  <div 
    v-if="getTimers(task, onlyActive).length > 0"
    class="timers"
  > 
    <timer 
      v-for="timer in getTimers(task, onlyActive)" 
      :key="timer.id"
      :timer="timer"
      :task="task"
    />
  </div>
</template>
<script>

import Timer from './Timer';
import TimerMixin from '../../../../mixins/TimerMixin';
import TaskMixin from '../../../../mixins/TaskMixin';
import EventNames from '../../../../common/EventNames';

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
    self.$eventBus.$on(EventNames.TIMER_START, (timer) => {
      self.startTimer(timer);
    });
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

.active-timer {
  padding-right: 6px;
  margin-right: -3px;
  padding-left: 2px;
  margin-left: -3px;
}
.timers {
  max-width: 500px;
}
.v-list-item {
  height: 20px;
}
.horizontal-center {
  margin: auto;
}

.fs-90 {
 font-size: 0.90rem;
}

.caption {
  white-space: nowrap;
}

.timer-stop {
  font-size: 32px !important;
}
</style>

