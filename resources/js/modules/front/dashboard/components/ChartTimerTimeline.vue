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
        v-if="chartByTimerTimers"
        align-top
        dense
      >
        <v-timeline-item
          v-for="(timer, index) in chartByTimerTimers"
          :key="index"
          :color="timelineItemColour(index)"
          small
        >
          <strong
            v-if="timer.dateStarted != null"
          >
            {{ timer.dateStarted }}
          </strong>
          <v-row
            v-if="$vuetify.breakpoint.mdAndUp"
            class="pt-1"
          >
            <v-col cols="4">
              <span class="caption">
                {{ $appFormatters.dateTimeFormat(timer.started_at, "HH:mm") }} - {{ $appFormatters.dateTimeFormat(timer.stopped_at, "HH:mm") }}
                ({{ calculateTimeSpent(timer) }})
              </span>
            </v-col>
            <v-col cols="6">
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
              ({{ calculateTimeSpent(timer) }})
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
        <v-flex
          class="time-footer-heading"
        >
          Total time:
        </v-flex>
        <v-flex
          class="time-footer-item"
        >
          {{ chartByTimerTotalTime[0] }}
        </v-flex>
        <v-flex
          class="time-footer-colon"
        >
          :
        </v-flex>
        <v-flex
          class="time-footer-item"
        >
          {{ chartByTimerTotalTime[1] }}
        </v-flex>
        <v-flex
          class="time-footer-colon"
        >
          :
        </v-flex>
        <v-flex
          class="time-footer-item"
        >
          {{ chartByTimerTotalTime[2] }}
        </v-flex>
      </v-layout>
      <v-layout row>
        <v-flex
          class="time-footer-heading"
        >
          &nbsp;
        </v-flex>
        <v-flex
          class="time-footer-item time-footer-value"
        >
          HH
        </v-flex>
        <v-flex
          class="time-footer-colon"
        >
          &nbsp;
        </v-flex>
        <v-flex
          class="time-footer-item time-footer-value"
        >
          MM
        </v-flex>
        <v-flex
          class="time-footer-colon"
        >
          &nbsp;
        </v-flex>
        <v-flex
          class="time-footer-item time-footer-value"
        >
          SS
        </v-flex>
      </v-layout>
    </v-card-subtitle>
  </v-card>
</template>
<script>

import { 
  mapActions, 
  mapGetters 
} from 'vuex'

import TimerMixin from '../../../../mixins/TimerMixin';
import EventNames from '../../../../common/EventNames';

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
    ...mapGetters('chart', ['chartByTimerLoading']),
    ...mapGetters('chart', ['chartByTimerData']),
    ...mapGetters('chart', ['chartByTimerTotalTime']),
    ...mapGetters('chart', ['chartByTimerTimers']),
  },
  created() {
    this.getData();
  },
  mounted() {
    this.$eventBus.$on([ EventNames.CHART_PARAMETERS_CHANGED, EventNames.CHART_TIMER_TIMELINE_PARAMETERS_CHANGED ], (values) => {
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
.time-footer-heading {
  width: 70px;
}
.time-footer-item {
  width: 17px;
  max-width: 17px;
}
.time-footer-colon {
  width: 4px;
  max-width: 4px;
}
.time-footer-value {
  padding-left: 2px;
  font-size: 8px;
}
</style>
