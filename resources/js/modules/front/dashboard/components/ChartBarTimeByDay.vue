<template>
  <div>
    <column-chart :data="chartData"></column-chart>
  </div>
</template>
<script>

export default {
  name: 'ChartBarWeeklyTime',
  computed: { },
  data() {
    return {
      start: moment().subtract(7,'d').format('YYYY-MM-DD'),
      end: moment().format('YYYY-MM-DD'),
      userIds: [APP_GLOBALS.AUTH_USER],
      chartData: []
    }
  },
  created() {
    const self = this;
    self.getChartData();
  },
  methods: {
    getChartData() {
      const self = this;
      const params = {
        start: self.start,
        end: self.end,
        userIds: self.userIds
      };
      window.axios.post('/api/chart/byday', params).then(response => {
        self.chartData = self.secondsToMinutes(response.data);
      });
    },
    secondsToMinutes(data) {
      return data.map(function(item) {
        return [item[0], parseInt(item[1]) / 60];
      });
    }
  }
}
</script>

