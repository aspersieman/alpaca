<template>
  <v-card
    class="mx-auto"
  >
    <v-card
      light
      flat
      :loading="loading"
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
        align-top
        dense
      >
        <v-timeline-item
          v-for="(timer, index) in timers"
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
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      Total time: {{ totalTime }}
    </v-card-subtitle>
  </v-card>
</template>
<script>

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
      loading: false,
      timers: [],
      days: [],
      totalDuration: 0,
      totalTime: "00:00:00",
      userIds: [ window.APP_GLOBALS.AUTH_USER ],
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
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
      self.loading = true;
      const params = {
        start: dateStart,
        end: dateEnd,
        userIds: self.userIds
      };
      window.axios.post('/api/chart/bytimer', params).then(response => {
        self.totalDuration = 0;
        self.totalTime = "00:00:00";
        let timers = response.data;
        self.timers = [];
        self.days = [];
        timers.forEach(timer => {
          let dateStarted = this.$appFormatters.dateTimeFormat(timer.started_at, "ddd, D MMM")
          timer.dateStarted = null;
          if (!self.days.includes(dateStarted)) {
            self.days.push(dateStarted);
            timer.dateStarted = dateStarted;
          }
          self.totalDuration += timer.duration || 0;
          self.timers.push(timer);
        });
        self.totalTime = self.$appFormatters.duration(self.totalDuration, 'seconds');
        self.loading = false;
      });
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
</style>
