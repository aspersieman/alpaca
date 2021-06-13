<template>
  <v-card
    class="mx-auto"
  >
    <v-card
      light
      flat
      :loading="chartByTimerLoading"
    >
      <v-card-title class="pa-2 grey lighten-3">
        <v-avatar>
          <v-icon>
            timeline
          </v-icon>
        </v-avatar>
        <h3 class="title">
          Timer timeline
        </h3>
      </v-card-title>
      <v-card-subtitle
        class="pa-2 grey lighten-3"
      >
        Timeline of time logged by date range
      </v-card-subtitle>
      <v-container
        v-if="showDateRange"
      >
        <v-row>
          <v-col align="center">
            <v-menu
              v-model="dateRangeMenu"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template #activator="{ on, attrs }">
                <v-text-field
                  v-model="dateRange"
                  class="date-range-input"
                  label="Date range"
                  prepend-icon="mdi-calendar"
                  readonly
                  dense
                  v-bind="attrs"
                  v-on="on"
                />
              </template>
              <v-date-picker
                value="dates"
                light
                range
              />
            </v-menu>
            <base-btn-primary
              @click="getData()"
            >
              Go
            </base-btn-primary>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
    <v-card-text class="py-0">
      <v-timeline
        v-if="chartByTimerTimers.length > 0"
        align-top
        dense
      >
        <span
          v-for="(timer, index) in chartByTimerTimers"
          :key="index"
        >
          <v-divider
            v-if="timer.dateStarted != null && index != 0"
            class="timer-day-divider"
          />
          <strong
            v-if="timer.dateStarted != null"
          >
            {{ timer.dateStarted }}
          </strong>
          <v-timeline-item
            :color="timelineItemColour(index)"
            small
          >
            <v-row
              v-if="$vuetify.breakpoint.mdAndUp"
              class="pt-1"
            >
              <v-col cols="3">
                <span class="caption">
                  {{ $appFormatters.dateTimeFormat(timer.started_at, "HH:mm") }} - {{ $appFormatters.dateTimeFormat(timer.stopped_at, "HH:mm") }}
                  ({{ timerDuration(timer) }})
                </span>
              </v-col>
              <v-col>
                <v-icon>
                  access_time
                </v-icon>
                <strong>
                  {{ timer.timer_name }}
                </strong>
                <div class="caption ellipsis">
                  <v-icon
                    small
                  >
                    task_alt
                  </v-icon>
                  {{ timer.task_name }}
                </div>
              </v-col>
            </v-row>
            <div
              v-if="$vuetify.breakpoint.smAndDown"
            >
              <div class="caption fullwidth">
                {{ $appFormatters.dateTimeFormat(timer.started_at, "HH:mm") }} - {{ $appFormatters.dateTimeFormat(timer.stopped_at, "HH:mm") }}
                ({{ timerDuration(timer) }})
              </div>
              <div class="fullwidth ellipsis">
                <v-icon>
                  access_time
                </v-icon>
                <strong
                  class="ellipsis"
                >
                  {{ timer.timer_name }}
                </strong>
              </div>
              <div class="caption ellipsis fullwidth">
                <v-icon
                  small
                >
                  task_alt
                </v-icon>
                {{ timer.task_name }}
              </div>
            </div>
          </v-timeline-item>
        </span>
      </v-timeline>
      <div
        v-else
      >
        No timers found
      </div>
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      <v-layout row>
        Total time:
        <v-spacer />
        {{ chartByTimerTotalTime }}
      </v-layout>
    </v-card-subtitle>
  </v-card>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import TimerMixin from '@/mixins/TimerMixin';

export default {
  components: { },
  mixins: [
    TimerMixin
  ],
  props: {
    showDateRange: {
      required: false,
      type: Boolean,
      default: function() {
        return true
      }
    },
    dates: {
      required: false,
      type: Array,
      default: function() {
        return [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ]
      }
    }
  },
  data() {
    return {
      dateRangeMenu: false,
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
    ...mapGetters('chart', ['chartByTimerLoading', 'chartByTimerData', 'chartByTimerTotalTime', 'chartByTimerTimers']),
  },
  created() {
    this.getData();
  },
  mounted() {
    this.$eventBus.$on([ 'dashboard-parameters-changed', 'dashboard-chart-timer-timeline-parameters-changed' ], (values) => {
      this.getData(values);
    });
  },
  methods: {
    ...mapActions({
        getChartByTimerData: 'chart/getChartByTimerData',
    }),
    getData(parameters) {
      const self = this;
      let dateStart = self.dates[0];
      let dateEnd = self.dates[1];
      if (
        typeof parameters !== 'undefined'
        && typeof parameters.dates !== 'undefined'
      ) {
        dateStart = parameters.dates[0];
        dateEnd = parameters.dates[1];
      }
      const params = {
        start: dateStart,
        end: dateEnd,
        userIds: [ this.$auth.user?.id ]
      };
      this.getChartByTimerData(params);
    },
    timelineItemColour(index) {
      const mod = index % 2;
      return mod === 1 ? 'teal lighten-3' : 'pink';
    },
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

.fullwidth {
  width: 100%;
}
.theme--light.v-timeline:before {
  background: rgba(0, 0, 0, 0);
}
.timer-day-divider {
  padding-bottom: 10px;
}
</style>
<style>
.v-timeline-item__divider {
  justify-content: start !important;
  width: 30px !important;
  min-width: 30px !important;
}
.v-timeline-item {
  justify-content: flex-end;
}
.v-timeline--dense .v-timeline-item__body {
  max-width: 100% !important;
}
</style>
