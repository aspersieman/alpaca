<template>
  <div
    v-if="isTimerRunning === true"
  >
    <v-bottom-sheet inset>
      <template #activator="{ on, attrs }">
        <v-speed-dial
          v-if="$auth.check && !small"
          :class="{'timer-button-singleton': $vuetify.breakpoint.mdAndUp}"
          absolute
          fixed
          top
          right
        >
          <template #activator>
            <v-btn
              fab
              v-bind="attrs"
              v-on="on"
            >
              <core-clock
                size="medium"
              />
            </v-btn>
          </template>
        </v-speed-dial>
        <span
          v-if="$auth.check && !small"
          :class="{ 'timer-time singleton md' : $vuetify.breakpoint.mdAndUp, 'timer-time singleton sm' : $vuetify.breakpoint.smAndDown }"
        >
          {{ timerTime }}
        </span>
        <span
          v-if="$auth.check && small"
        >
          <span
            class="timer-time-multiple"
          >
            {{ timerTime }}
          </span>
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
          >
            <core-clock
              size="small"
            />
          </v-btn>
        </span>
      </template>
      <v-card tile>
        <v-list>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title
                class="ellipsis"
              >
                <v-icon>
                  access_time
                </v-icon>
                {{ timerName }}
              </v-list-item-title>
              <v-list-item-subtitle
                class="timer-started"
              >
                {{ timerStarted }}
              </v-list-item-subtitle>
              <v-list-item-subtitle
                class="ellipsis caption"
              >
                <v-icon
                  small
                >
                  task_alt
                </v-icon>
                {{ taskName }}
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-content
              v-if="$vuetify.breakpoint.mdAndUp && timerTags && timerTags.length > 0"
            >
              <div
                :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}"
              >
                <v-chip
                  v-for="tag in timerTags"
                  :key="tag.id"
                  class="ma-2"
                  x-small
                >
                  {{ tag.tag_name }}
                </v-chip>
              </div>
            </v-list-item-content>
            <div
              v-else
            >
              <v-spacer
                v-if="$vuetify.breakpoint.mdAndUp"
              />
            </div>

            <v-list-item-icon>
              <v-container>
                <v-row>
                  <v-btn
                    class="success"
                    @click="$store.dispatch('timer/stopTimer', timer)"
                  >
                    Stop
                  </v-btn>
                </v-row>
                <v-row
                  justify="center"
                >
                  {{ timerTime }}
                </v-row>
              </v-container>
            </v-list-item-icon>
          </v-list-item>
        </v-list>
      </v-card>
    </v-bottom-sheet>
  </div>
</template>
<script>

import { mapGetters } from 'vuex';
import TimerMixin from '@/mixins/TimerMixin';
import CoreClock from '@/components/core/Clock';

export default {
  components: {
    CoreClock
  },
  mixins: [ TimerMixin ],
  props: {
    small: {
      required: false,
      type: Boolean,
      default: false
    }
  },
  computed: {
    taskName: function() {
      return this.timerRunning?.timer?.task?.name;
    },
    timerName: function() {
      return this.timerRunning?.timer?.name;
    },
    timerTime: function() {
      return this.timerRunning?.title;
    },
    timerTags: function() {
      return this.timerRunning?.timer?.tagged;
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
@import "../../../../../../sass/index";
.timer-button-singleton {
  margin-top: 64px;
  margin-right: 60px;
}
.timer-started {
  margin-left: 27px;
}
.timer-time {
  background-color: $active-green;
  border-radius: 10px 0px 0px 10px;
  padding: 5px;
}
.timer-time.singleton {
  position: fixed;
  top: 90px;
  margin: 2px;
  z-index: 3;
}
.timer-time.singleton.sm {
  right: 67px;
}
.timer-time.singleton.md {
  right: 126px;
}
.timer-time.multiple {
  margin-right: -7px;
}

</style>
