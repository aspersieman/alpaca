<template>
  <v-content>
    <v-container fluid>
      <v-breadcrumbs
        v-if="this.$store.getters.isLoggedIn && $vuetify.breakpoint.mdAndUp"
        :items="getBreadcrumbs">
        <template v-slot:item="props">
          <v-breadcrumbs-item
          exact
          :to="props.item.to"
          :key="props.item.label"
          :disabled="props.item.disabled">
            <template v-slot:divider>
              <v-icon>mdi-forward</v-icon>
            </template>
            {{ props.item.label }}
          </v-breadcrumbs-item>
        </template>
      </v-breadcrumbs>
      <v-divider
        v-if="this.$store.getters.isLoggedIn && $vuetify.breakpoint.mdAndUp">
      </v-divider>
      <router-view></router-view>
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

