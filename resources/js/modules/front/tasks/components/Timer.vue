<template>
  <div>
    <v-card 
      text 
      flat 
    >
      <div
        :class="showTimerForTask(task, timer) ? 'active-timer' : ''"
      >
        <v-layout
          row
          wrap
        >
          <v-flex 
            xs1
            sm1
            md1
            lg1
          >
            <v-icon class="ml-3">
              access_time
            </v-icon>
          </v-flex>
          <v-flex
            xs6
            sm3
            md3
            lg3
            class="ml-2"
          >
            <div class="fs-90 ellipsis">
              {{ timer.name }}
            </div>
            <div class="caption">
              {{ $appFormatters.timerFormat(timer.started_at, true) }}
            </div>
          </v-flex>
          <v-flex
            v-if="$vuetify.breakpoint.smAndUp"
            sm4
            md4
            lg4
          >
            <div 
              :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}"
            >
              <v-chip
                v-for="tag in timer.tagged"
                :key="tag.id"
                class="ma-2"
                x-small
              >
                {{ tag.tag_name }}
              </v-chip>
            </div>
          </v-flex>
          <v-spacer />
          <v-flex
            xs4
            sm3
            md3
            lg3 
            class="fs-90"
          >
            <v-layout 
              v-if="showTimerForTask(task, timer)"
              row
            >
              <v-flex
                class="horizontal-center fs-90"
              >
                {{ $store.state.activeTimerCounter.title }}
              </v-flex>
              <v-flex class="horizontal-center">
                <v-btn 
                  disabled
                  icon
                />
                <v-btn 
                  v-if="showTimerForTask(task, timer)" 
                  icon 
                  @click="stopTimer()"
                >
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon 
                        class="timer-stop"
                        v-on="on"
                      >
                        stop
                      </v-icon>
                    </template>
                    Stop timer
                  </v-tooltip>
                </v-btn>
              </v-flex>
            </v-layout>
            <v-layout 
              v-else 
              row
            >
              <v-flex
                v-if="$store.state.stoppedTimer && $store.state.stoppedTimer.id == timer.id"
                class="horizontal-center fs-90"
              >
                {{ $store.state.stoppedTimerValue }}
              </v-flex>
              <v-flex
                v-else
                class="horizontal-center fs-90"
              >
                {{ timerDuration(timer) }}
              </v-flex>
              <v-flex class="horizontal-center">
                <v-btn 
                  icon
                  @click="deleteTimer(task, timer)"
                >
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon v-on="on">
                        delete
                      </v-icon>
                    </template>
                    Delete timer
                  </v-tooltip>
                </v-btn>
                <v-btn
                  icon
                  @click="openTimerUpdateDialog(task, timer)"
                >
                  <v-tooltip bottom>
                    <template
                      #activator="{ on }"
                    >
                      <v-icon v-on="on">
                        edit
                      </v-icon>
                    </template>
                    Edit timer
                  </v-tooltip>
                </v-btn>
              </v-flex>
            </v-layout>
          </v-flex>
        </v-layout>
      </div>
    </v-card>
  </div>
</template>
<script>

import TimerMixin from '../../../../mixins/TimerMixin';
import EventNames from '../../../../common/EventNames';

export default {
  components: { },
  mixins: [ TimerMixin ],
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
    timer: {
      required: true,
      type: Object,
      default: function() {
        return {
          id: null,
          name: null,
          task_id: null, 
          user_id: null,
          started_at: null,
          stopped_at: null, 
          duration: 0,
          created_at: null,
          updated_at: null
        }
      }
    },
  },
  mounted() {
    const self = this;
    this.$eventBus.$on(EventNames.TIMER_START, (timer) => {
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
