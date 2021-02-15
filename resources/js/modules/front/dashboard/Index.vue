<template>
  <div
    id="dasboard"
    class="page_wrap_vue pa-3"
  >
    <v-layout 
      row 
      class="mb-3" 
      wrap
    >
      <v-avatar>
        <v-icon>
          dashboard
        </v-icon>
      </v-avatar>
      <h1 class="subheading grey--text ml-4">
        Dashboard
      </h1>
    </v-layout>
    <v-container fluid>
      <v-row>
        <v-col align="center">
          <div
            class="date-picker-container"
          >
            <date-range-picker 
              v-model="dates"
            />
            <base-btn-primary
              @click="search()"
            >
              Go
            </base-btn-primary>
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <chart-timer-timeline
            :show-date-range="showDateRange"
          />
        </v-col>
        <v-col>
          <chart-bar-time-by-day
            :show-date-range="showDateRange"
          />
        </v-col>
        <v-col>
          <chart-pie-time-by-tag
            :show-date-range="showDateRange"
          />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>

import ChartBarTimeByDay from './components/ChartBarTimeByDay';
import ChartPieTimeByTag from './components/ChartPieTimeByTag';
import ChartTimerTimeline from './components/ChartTimerTimeline';
import DateRangePicker from '../../../components/core/DateRangePicker';
import EventNames from '../../../common/EventNames';

export default {
  name: 'Dashboard',
  components: {
    ChartBarTimeByDay,
    ChartPieTimeByTag,
    ChartTimerTimeline,
    DateRangePicker
  },
  data() {
    return {
      showDateRange: false,
      dates: [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
    }
  },
  mounted() { },
  methods: {
    search() {
      this.$eventBus.$emit(EventNames.CHART_PARAMETERS_CHANGED, { 'dates': this.dates });
    }
  }
}
</script>
<style scoped>
.date-picker-container {
  max-width: 215px;
}
</style>
