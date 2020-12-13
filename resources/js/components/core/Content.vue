<template>
  <v-content>
    <v-container fluid>
      <transition name="fade">
        <router-view />
      </transition>
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

