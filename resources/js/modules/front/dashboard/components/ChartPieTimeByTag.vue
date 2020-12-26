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
            pie_chart
          </v-icon>
        </v-avatar>
        <h3 class="title">
          Time by tag
        </h3>
      </v-card-title>
      <v-card-subtitle
        class="pa-2 grey lighten-3"
      >
        Total time logged by tag
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
      <pie-chart
        :download="true" 
        :data="chartData"
      />
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      Total tags: {{ totalTags }}
    </v-card-subtitle>
  </v-card>
</template>
<script>
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
      userIds: [window.APP_GLOBALS.AUTH_USER],
      loading: false,
      chartData: [],
      totalTags: 0,
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
  methods: {
    getData() {
      const self = this;
      self.loading = true;
      const params = {
        start: self.dates[0],
        end: self.dates[1],
        userIds: self.userIds
      };
      window.axios.post('/api/chart/bytag', params).then(response => {
        let tags = response.data;
        tags.forEach(tag => {
          self.chartData.push([tag[0], self.$appFormatters.secondsToMinutes(tag[1])]);
        });
        self.totalTags = this.chartData.length;
        self.loading = false;
      });
    },
  }
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

</style>
