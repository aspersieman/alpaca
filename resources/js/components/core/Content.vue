<template>
  <v-content>
    <v-container fluid>
      <router-view />
    </v-container>
  </v-content>
</template>

<script>
export default {
  name: 'CoreContent',
  computed: {
    getBreadcrumbs() {
      return this.$store.getters.getBreadcrumbs
    },
  }
}
</script>

