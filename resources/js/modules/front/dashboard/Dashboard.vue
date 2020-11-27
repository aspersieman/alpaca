<template>
  <div id="home">
    <h1 class="subheading grey--text ml-4">
      Dashboard
    </h1>
    <v-container class="my-2">
      <chart-bar-time-by-day />
      <pie-chart
        :download="true" 
        :data="[['Blueberry', 44], ['Strawberry', 23]]"
      />
    </v-container>
  </div>
</template>
<script>

import ChartBarTimeByDay from './components/ChartBarTimeByDay';

export default {
  name: 'Home',
  components: {
    'chart-bar-time-by-day': ChartBarTimeByDay,
  },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs',[
      {label:'Dashboard',name:'dashboard'}
      ]);
  },
}
</script>
