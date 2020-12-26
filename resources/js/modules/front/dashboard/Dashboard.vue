<template>
  <div
    id="dasboard"
    class="page_wrap_vue pa-3"
  >
    <h1 class="subheading grey--text ml-4">
      Dashboard
    </h1>
    <v-container 
      fluid
      class="dashboard-container"
    >
      <v-row>
        <v-col>
          <timeline />
        </v-col>
        <v-col>
          <chart-bar-time-by-day />
        </v-col>
        <v-col>
          <pie-chart
            :download="true" 
            :data="[['Blueberry', 44], ['Strawberry', 23]]"
          />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>

import ChartBarTimeByDay from './components/ChartBarTimeByDay';
import Timeline from './components/Timeline';

export default {
  name: 'Home',
  components: {
    'chart-bar-time-by-day': ChartBarTimeByDay,
    Timeline
  },
  mounted() { },
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";

</style>
