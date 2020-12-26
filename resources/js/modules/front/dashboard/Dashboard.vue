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
          <v-spacer />
        </v-col>
        <v-col align="center">
          <v-container fluid>
            <v-row>
              <v-col>
                <date-range-picker />
              </v-col>
              <v-col>
                <base-btn-primary
                  @click="search()"
                >
                  Go
                </base-btn-primary>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
        <v-col align="center">
          <v-spacer />
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
import DateRangePicker from './components/DateRangePicker';

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
    }
  },
  mounted() { },
  methods: {
    search() {
      console.log("searching...");
    }
  }
}
</script>
