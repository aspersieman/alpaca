<template>
  <div>
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
  </div>
</template>

<script>
export default {
  components: { },
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
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";
</style>
