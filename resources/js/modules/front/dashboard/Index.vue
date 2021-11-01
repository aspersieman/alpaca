<template>
  <v-container
    id="dashboard-view"
    fluid
    tag="section"
  >
    <v-row>
      <v-col>
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
    <v-row>
      <v-col cols="12">
        <v-row>
          <v-col
            cols="12"
            md="6"
            lg="4"
          >
            <material-chart-card
              :color="chartByDay.color"
              :data="chartByDayData"
              :options="chartByDay.options"
              :responsive-options="chartByDay.responsiveOptions"
              :title="chartByDay.title"
              :type="chartByDay.type"
            >
              <template #subtitle>
                <div class="font-weight-light text--secondary">
                  <!-- eslint-disable vue/no-v-html -->
                  <div v-html="chartByDay.subtitle" />
                  <!--eslint-enable-->
                </div>
              </template>

              <template #actions>
                <v-icon
                  class="mr-1"
                  small
                >
                  mdi-clock-outline
                </v-icon>

                <span
                  class="text-caption grey--text font-weight-light"
                  v-text="chartByDayAgeReadable"
                />
              </template>
            </material-chart-card>
          </v-col>
          <v-col
            cols="12"
            md="6"
            lg="4"
          >
            <material-chart-card
              :color="chartByTag.color"
              :data="chartByTagData"
              :options="chartByTag.options"
              :responsive-options="chartByTag.responsiveOptions"
              :title="chartByTag.title"
              :type="chartByTag.type"
            >
              <template #subtitle>
                <div class="font-weight-light text--secondary">
                  <!-- eslint-disable vue/no-v-html -->
                  <div v-html="chartByTag.subtitle" />
                  <!--eslint-enable-->
                </div>
              </template>

              <template #actions>
                <v-icon
                  class="mr-1"
                  small
                >
                  mdi-clock-outline
                </v-icon>

                <span
                  class="text-caption grey--text font-weight-light"
                  v-text="chartByTagAgeReadable"
                />
              </template>
            </material-chart-card>
          </v-col>
          <v-col
            cols="12"
            md="6"
            lg="4"
          >
            <material-chart-card
              :color="chartByCompleted.color"
              :data="chartByCompletedData"
              :options="chartByCompleted.options"
              :responsive-options="chartByCompleted.responsiveOptions"
              :title="chartByCompleted.title"
              :type="chartByCompleted.type"
            >
              <template #subtitle>
                <div class="font-weight-light text--secondary">
                  <!-- eslint-disable vue/no-v-html -->
                  <div v-html="chartByCompleted.subtitle" />
                  <!--eslint-enable-->
                </div>
              </template>

              <template #actions>
                <v-icon
                  class="mr-1"
                  small
                >
                  mdi-clock-outline
                </v-icon>

                <span
                  class="text-caption grey--text font-weight-light"
                  v-text="chartByCompletedAgeReadable"
                />
              </template>
            </material-chart-card>
          </v-col>
        </v-row>
      </v-col>

      <v-col
        cols="12"
        md="6"
        lg="3"
      >
        <material-stat-card
          :value="chartStatsTotalTasksCount.toString()"
          :color="statsTotalTasks.color"
          :icon="statsTotalTasks.icon"
          :title="statsTotalTasks.title"
        >
          <template #actions>
            <v-icon
              class="mr-2"
              small
              v-text="statsTotalTasks.actionIcon"
            />
            <div class="text-truncate">
              {{ statsTotalTasks.actionText }}
            </div>
          </template>
        </material-stat-card>
      </v-col>
      <v-col
        cols="12"
        md="6"
        lg="3"
      >
        <material-stat-card
          :value="chartStatsDueTasksCount.toString()"
          :color="statsDueTasks.color"
          :icon="statsDueTasks.icon"
          :title="statsDueTasks.title"
        >
          <template #actions>
            <v-icon
              class="mr-2"
              small
              v-text="statsDueTasks.actionIcon"
            />
            <div class="text-truncate">
              {{ statsDueTasks.actionText }}
            </div>
          </template>
        </material-stat-card>
      </v-col>
      <v-col
        cols="12"
        md="6"
        lg="3"
      >
        <material-stat-card
          :value="chartStatsTotalTimerDuration.toString()"
          :color="statsTotalTimers.color"
          :icon="statsTotalTimers.icon"
          :title="statsTotalTimers.title"
        >
          <template #actions>
            <v-icon
              class="mr-2"
              small
              v-text="statsTotalTimers.actionIcon"
            />
            <div class="text-truncate">
              {{ statsTotalTimers.actionText }}
            </div>
          </template>
        </material-stat-card>
      </v-col>
      <v-col
        cols="12"
        md="6"
        lg="3"
      >
        <material-stat-card
          :value="chartStatsTotalNotesCount.toString()"
          :color="statsTotalNotes.color"
          :icon="statsTotalNotes.icon"
          :title="statsTotalNotes.title"
        >
          <template #actions>
            <v-icon
              class="mr-2"
              small
              v-text="statsTotalNotes.actionIcon"
            />
            <div class="text-truncate">
              {{ statsTotalNotes.actionText }}
            </div>
          </template>
        </material-stat-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>

import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';

import MaterialChartCard from '@/components/core/MaterialChartCard';
import MaterialStatCard from '@/components/core/MaterialStatsCard';

import Chartist from "chartist";
require('chartist-plugin-legend');
require('chartist-plugin-tooltips');

import 'chartist/dist/chartist.min.css'

Vue.use(require('vue-chartist'))

const lineSmooth = Vue.chartist.Interpolation.cardinal({
  tension: 0,
})

export default {
  name: 'Dashboard',

  components: {
    MaterialChartCard,
    MaterialStatCard,
  },

  data() {
    return {
      showDateRange: false,
      dates: [ new Date(Date.now() - 14 * 24 * 60 * 60 * 1000).toISOString().substr(0, 10), new Date().toISOString().substr(0, 10) ],
      chartByDay: {
        type: 'Bar',
        color: 'primary',
        title: 'Time logged',
        subtitle: 'Total time logged per day in minutes',
        options: {
          axisX: {
            showGrid: false,
          },
          low: 0,
          high: 200,
          chartPadding: {
            top: 0,
            right: 5,
            bottom: 0,
            left: 0,
          },
          plugins: [
            Chartist.plugins.tooltip()
          ],
        },
        responsiveOptions: [
          ['screen and (max-width: 640px)', {
            seriesBarDistance: 5,
            axisX: {
              labelInterpolationFnc: function (value) {
                return value[0]
              },
            },
          }],
        ],
      },
      chartByTag: {
        type: 'Pie',
        color: 'success',
        title: 'Time by category',
        subtitle: 'Total time logged by category',
        options: {
          showLabel: false,
          plugins: [
            Chartist.plugins.legend(),
            Chartist.plugins.tooltip()
          ]
        },
      },
      chartByCompleted: {
        type: 'Line',
        color: 'info',
        title: 'Completed Tasks',
        subtitle: 'Tasks recently completed',
        options: {
          lineSmooth,
          low: 0,
          high: 15, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0,
          },
          plugins: [
            Chartist.plugins.tooltip()
          ],
        },
      },
      statsTotalTasks: {
        actionIcon: 'mdi-check-circle-outline',
        actionText: '...and counting',
        color: 'warning',
        icon: 'mdi-check-circle-outline',
        title: 'Total tasks',
      },
      statsDueTasks: {
        actionIcon: 'mdi-information-outline',
        actionText: 'Tasks with a due date set that are due by today or earlier',
        color: 'primary',
        icon: 'mdi-calendar-today',
        title: 'Tasks due',
      },
      statsTotalTimers: {
        actionIcon: 'mdi-information-outline',
        actionText: 'Total time logged in hours',
        color: 'success',
        icon: 'mdi-clock-outline',
        title: 'Total time',
      },
      statsTotalNotes: {
        actionIcon: 'mdi-note-outline',
        actionText: '...and counting',
        color: 'info',
        icon: 'mdi-note-outline',
        title: 'Total notes',
      },
    }
  },

  computed: {
    ...mapGetters('chart', [
        'chartByDayData',
        'chartByDayHigh',
        'chartByDayAge',
        'chartByTagData',
        'chartByTagAge',
        'chartByCompletedData',
        'chartByCompletedHigh',
        'chartByCompletedAge',
        'chartStatsTotalTasksCount',
        'chartStatsDueTasksCount',
        'chartStatsTotalTimerDuration',
        'chartStatsTotalNotesCount',
    ]),
    chartByDayAgeReadable: function() { return 'updated ' + window.moment(this.chartByDayAge).fromNow(); },
    chartByTagAgeReadable: function() { return 'updated ' + window.moment(this.chartByTagAge).fromNow(); },
    chartByCompletedAgeReadable: function() { return 'updated ' + window.moment(this.chartByTagAge).fromNow(); },
  },

  created() {
    this.fetchChartByDayData();
    this.fetchChartByTagData();
    this.fetchChartByCompletedData();
    this.fetchChartStats();
  },

  mounted() {
    this.$store.subscribe((mutation) => {
      if (
        [
          'task/ADDED',
        ].includes(mutation.type)
      ) {
      }
    });
  },

  methods: {
    ...mapActions({
        getChartByDayData: 'chart/getChartByDayData',
        getChartByTagData: 'chart/getChartByTagData',
        getChartByCompletedData: 'chart/getChartByCompletedData',
        getChartStats: 'chart/getChartStats',
    }),

    fetchChartByDayData(parameters) {
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

    fetchChartByTagData(parameters) {
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
      this.getChartByTagData(params);
    },

    fetchChartByCompletedData(parameters) {
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
      this.getChartByCompletedData(params);
    },

    fetchChartStats(parameters) {
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
      this.getChartStats(params);
    },
  }
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
.row {
  margin-left: unset;
  border-radius: unset !important;
}
.charts-container {
  margin-top: -50px;
}
</style>
<style lang="scss">
.chartist-tooltip > br {
  display: none;
}
.chartist-tooltip {
  position: absolute;
  display: block;
  min-width: 5em;
  padding: 0em;
  background: rgba(0,0,0,0.2);
  color: #000000;
  font-weight: 700;
  text-align: center;
  pointer-events: none;
  z-index: 1;
  -webkit-transition: opacity .2s linear;
  -moz-transition: opacity .2s linear;
  -o-transition: opacity .2s linear;
  transition: opacity .2s linear;
  border-radius: 5px;
}
.chartist-tooltip-meta, .chartist-tooltip-value {
  background: rgba(0,0,0,0);
  color: #FFFFFF;
  font-size: 0.7rem;
  text-align: center;
  border-radius: 5px;
  padding: 3px;
}
</style>
