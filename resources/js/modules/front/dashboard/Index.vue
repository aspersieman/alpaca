<template>
  <div
    id="dasboard"
    class="page_wrap_vue pa-3"
  >
    <v-container class="my-2">
      <v-row>
        <v-col
          class="mb-3" 
        >
          <v-layout 
            row 
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
          <v-layout 
            v-if="$vuetify.breakpoint.mdAndUp"
            row 
            wrap
          >
            <h4 class="subheading grey--text ml-4">
              Get an overeview of your productivity
            </h4>
          </v-layout>
        </v-col>
      </v-row>
      <v-layout 
        row 
        class="mb-1 mt-1 " 
        wrap
        transition="scroll-y-transition"
      >
        <span
          class="date-picker-container"
        >
          <date-range-picker 
            v-model="dates"
          />
        </span>
        <base-btn-primary
          class="action-button"
          @click="search()"
        >
          Go
        </base-btn-primary>
      </v-layout>
    </v-container>
    <v-container 
      fluid
      class="charts-container"
    >
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
import DateRangePicker from '@/components/core/DateRangePicker';

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
  methods: {
    search() {
      this.$eventBus.dashboardParametersChanged({ 'dates': this.dates });
    }
  }
}
</script>
<style scoped>
.row {
  margin-left: unset;
  border-radius: unset !important;
}
.date-picker-container {
  max-width: 215px;
}
.action-button {
  margin-left: 10px;
}
.charts-container {
  margin-top: -50px;
}
</style>
