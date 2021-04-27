<template>
  <v-card
    class="mx-auto timeline-card"
  >
    <v-card
      light
      flat
      :loading="chartByDayLoading"
    >
      <v-card-title class="pa-2 grey lighten-3">
        <v-avatar>
          <v-icon>
            bar_chart
          </v-icon>
        </v-avatar>
        <h3 class="title">
          Time by day
        </h3>
      </v-card-title>
      <v-card-subtitle
        class="pa-2 grey lighten-3"
      >
        Total time logged by day
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
                v-model="dates"
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
      <column-chart
        xtitle="Date"
        ytitle="Time logged"
        :download="true"
        :data="chartByDayData"
      />
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      <v-layout row>
        Total time:
        <v-spacer />
        {{ chartByDayTotalTime }}
      </v-layout>
    </v-card-subtitle>
  </v-card>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'ChartBarWeeklyTime',
  props: {
    showDateRange: {
      required: false,
      type: Boolean,
      default: function() {
        return true
      }
    }
  },
  data() {
    return {
      dateRangeMenu: false,
      dates: [ new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
    ...mapGetters('chart', ['chartByDayLoading', 'chartByDayData', 'chartByDayTotalTime']),
  },
  created() {
    this.getData();
  },
  mounted() {
    this.$eventBus.$on([ 'dashboard-parameters-changed', 'dashboard-chart-bar-time-by-day-parameters-changed' ], (values) => {
      this.getData(values);
    });
  },
  methods: {
    ...mapActions({
        getChartByDayData: 'chart/getChartByDayData',
    }),
    getData(parameters) {
      let dateStart = this.dates[0];
      let dateEnd = this.dates[1];
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
      this.getChartByDayData(params);
    },
  }
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";
</style>
