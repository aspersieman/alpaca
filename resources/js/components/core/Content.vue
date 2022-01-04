<template>
  <v-main>
    <v-container
      fluid
      class="pl-0"
    >
      <transition name="fade">
        <router-view />
      </transition>
    </v-container>
  </v-main>
</template>

<script>
export default {
  name: 'CoreContent',

  computed: {
    getBreadcrumbs() {
      return this.$store.getters.getBreadcrumbs
    },
  },
}
</script>

