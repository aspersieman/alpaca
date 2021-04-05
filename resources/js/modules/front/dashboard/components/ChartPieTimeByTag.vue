<template>
  <v-card
    class="mx-auto timeline-card"
  >
    <v-card
      light
      flat
      :loading="chartByTagLoading"
    >
      <v-card-title class="pa-2 grey lighten-3">
        <v-avatar>
          <v-icon>
            pie_chart
          </v-icon>
        </v-avatar>
        <h3 class="title">
          Time by category
        </h3>
      </v-card-title>
      <v-card-subtitle
        class="pa-2 grey lighten-3"
      >
        Total time logged by category
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
        :data="chartByTagData"
      />
    </v-card-text>
    <v-card-subtitle
      class="chart-footer"
    >
      Total tags: {{ chartByTagTotalTags }}
    </v-card-subtitle>
  </v-card>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
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
    }
  },
  computed: {
    dateRange () {
      return this.dates.join(' ~ ')
    },
    ...mapGetters('chart', ['chartByTagLoading', 'chartByTagData', 'chartByTagTotalTags']),
  },
  created() {
    const self = this;
    self.getData();
  },
  mounted() {
    this.$eventBus.$on([ EventNames.CHART_PARAMETERS_CHANGED, EventNames.CHART_PIE_TIME_BY_TAG_PARAMETERS_CHANGED ], (values) => {
      this.getData(values);
    });
  },
  methods: {
    ...mapActions({
        getChartByTagData: 'chart/getChartByTagData',
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
        userIds: self.userIds
      };
      this.getChartByTagData(params);
    },
  }
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

</style>
