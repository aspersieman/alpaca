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
        :data="chartData"
      />
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      Total time: {{ totalTime }}
    </v-card-subtitle>
  </v-card>
</template>
<script>
import EventNames from '../../../../common/EventNames';

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
      userIds: [ window.APP_GLOBALS.AUTH_USER ],
      loading: false,
      chartData: [],
      totalDuration: 0,
      totalTime: "00:00:00"
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
  },
  created() {
    const self = this;
    self.getData();
  },
  mounted() {
    this.$eventBus.$on([ EventNames.CHART_PARAMETERS_CHANGED, EventNames.CHART_BAR_TIME_BY_DAY_PARAMETERS_CHANGED ], (values) => {
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
      window.axios.post('/api/chart/byday', params).then(response => {
        self.chartData = [];
        let days = response.data;
        days.forEach(day => {
          let formattedDay = this.$appFormatters.dateTimeFormat(day[0], "ddd, D MMM")
          self.totalDuration += parseInt(day[1]);
          self.chartData.push([formattedDay, self.$appFormatters.secondsToMinutes(day[1])]);
        });
        self.totalTime = self.$appFormatters.duration(self.totalDuration, 'seconds');
        self.loading = false;
      });
    },
  }
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

</style>
