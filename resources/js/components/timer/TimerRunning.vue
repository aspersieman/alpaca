<template>
  <div
    v-if="isTimerRunning === true"
  >
    <v-bottom-sheet inset>
      <template #activator="{ on, attrs }">
        <div
          :class="{'timer-button-block': $vuetify.breakpoint.mdAndUp}"
        >
          <v-btn
            class="timer-button"
            v-bind="attrs"
            v-on="on"
          >
            Running Timer
            <v-icon>
              access_time
            </v-icon>
          </v-btn>
          <span
            class="hidden-md-and-down"
          >
            {{ timerTime }}
          </span>
        </div>
      </template>
      <v-card tile>
        <v-list>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title
                class="ellipsis"
              >
                Task: {{ taskName }}
              </v-list-item-title>
              <v-list-item-subtitle
                class="ellipsis caption"
              >
                Timer: {{ timerName }}
              </v-list-item-subtitle>
              <v-list-item-subtitle
                class="caption"
              >
                {{ timerStarted }}
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-spacer />

            {{ timerTime }}
            <v-list-item-icon>
              <v-btn
                class="success"
                @click="stopTimer()"
              >
                Stop
              </v-btn>
            </v-list-item-icon>
          </v-list-item>
        </v-list>
      </v-card>
    </v-bottom-sheet>
  </div>
</template>
<script>

import { mapGetters } from 'vuex';
import TimerMixin from '../../mixins/TimerMixin';

export default {
  mixins: [ TimerMixin ],
  computed: {
    taskName: function() {
      return this.timerRunning?.task?.name;
    },
    timerName: function() {
      return this.timerRunning?.timer?.name;
    },
    timerTime: function() {
      return this.timerRunning?.title;
    },
    timerStarted: function() {
      return this.$appFormatters.timerFormat(this.timerRunning.timer.started_at, true);
    },
    showTimer: function() {
      return this.timerRunning != null;
    },
    ...mapGetters('timer', ['timer', 'timerRunning', 'isTimerRunning'])
  },
}
</script>

<style scoped lang="scss">
@import "../../../sass/index";

.timer-button {
  background-color: $active-green !important;
}

.timer-button-block {
  border: solid 1px $active-green;
  margin: 2px;
  padding: 5px;
  border-radius: 4px;
}
</style>

