<template>
  <v-card
    class="mx-auto timeline-card"
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
          Timeline
        </h3>
      </v-card-title>
      <v-card-subtitle
        class="pa-2 grey lighten-3"
      >
        Timeline of recent timers
      </v-card-subtitle>
      <v-container>
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
                v-model="dates"
                light
                range
              />
            </v-menu>
            <base-btn-primary
              @click="getTimelineData()"
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
          <strong> {{ dateTimeFormat(timer.started_at, "ddd, D MMM") }} </strong>
          <v-row class="pt-1">
            <v-col cols="4">
              <span class="caption">
                {{ dateTimeFormat(timer.started_at, "HH:mm") }} - {{ dateTimeFormat(timer.stopped_at, "HH:mm") }}
                ({{ calculateTimeSpent(timer) }})
              </span>
            </v-col>
            <v-col cols="6">
              <v-icon>
                access_time
              </v-icon>
              <strong
                class="ellipsis"
              >
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
        </v-timeline-item>
      </v-timeline>
    </v-card-text>
    <v-card-subtitle
      class="timeline-footer"
    >
      Total time: 12:34:56
    </v-card-subtitle>
  </v-card>
</template>
<script>

import TimerMixin from '../../../../mixins/TimerMixin';
import methods from '../../../../common/Methods';

export default {
  components: { },
  mixins: [
    TimerMixin
  ],
  props: { },
  data() {
    return {
      dateRangeMenu: false,
      dates: [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
      loading: false,
      timers: []
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
  },
  created() {
    this.getTimelineData();
  },
  mounted() { },
  methods: {
    getTimelineData() {
      const self = this;
      self.loading = true;
      const params = {
        start: self.dates[0],
        end: self.dates[1],
        userIds: self.userIds
      };
      window.axios.post('/api/chart/timersbydate', params).then(response => {
        self.timers = response.data;
        self.loading = false;
        methods.debug(self.timers);
      });
    },
    dateTimeFormat(dateTime, format) {
      return methods.dateTimeFormat(dateTime, format)
    },
    timelineItemColour(index) {
      const mod = index % 2;
      return mod === 1 ? 'teal lighten-3' : 'pink';
    },
    duration(seconds) {
      return window.moment().startOf('day').seconds(seconds).format('HH:mm:ss');
    }
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

.timeline-date-select {
  padding-left: 3px;
}

.timeline-card {
  max-width: 400px;
  min-width: 340px;
}

.date-range-input {
  max-width: 215px;
}

.timeline-footer {
  background-color: $selected-light-grey;
}
</style>
