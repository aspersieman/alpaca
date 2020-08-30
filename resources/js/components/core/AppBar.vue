<template>
  <div>
    <v-app-bar 
      app 
      light
      clipped-left>
      <v-app-bar-nav-icon
        v-if="$vuetify.breakpoint.smAndDown || this.$store.getters.menu.length > 0"
        @click="this.toggleDrawer" />
      <v-toolbar-title
        class="toolbar-title">
        <router-link to="/">
          {{this.$store.state.appName}}
        </router-link>
      </v-toolbar-title>
      <v-container class="mx-auto py-0 main-container">
        <v-row align="center">
          <router-link to="/">
            <v-img
              :src="require('../../../img/logo/llama_emoji.png')"
              class="mr-5"
              contain
              height="48"
              width="48"
              max-width="48" />
          </router-link>
          <v-btn
            class="d-none d-sm-flex appbar-button"
            text
            to="/dashboard">
            Dashboard
          </v-btn>
          <v-btn
            class="d-none d-sm-flex appbar-button"
            text
            to="/tasks">
            Tasks
          </v-btn>
          <v-spacer />
          <v-text-field
            class="hidden-sm-and-down"
            append-icon="mdi-magnify"
            v-if="canSearch"
            flat
            hide-details
            outlined
            dense
            style="max-width: 300px;"
          />
          &nbsp;
          <v-btn
            v-if="!this.$store.getters.isLoggedIn"
            class="d-none d-sm-flex"
            text
            link
            v-on:click="clickLogin('/login')">
            Login
          </v-btn>
          <v-btn
            v-if="this.$store.getters.isLoggedIn"
            class="d-none d-sm-flex"
            text
            v-on:click="clickLogout('/logout','/')">
            Logout
          </v-btn>
        </v-row>
      </v-container>
    </v-app-bar>
  </div>
</template>
<script>
// Utilities
import {
  mapGetters,
  mapMutations,
} from 'vuex';

import methods from '../../common/Methods';

export default {
  name: 'CoreAppBar',
  data() {
    return {
      canSearch: false,
      canCards: false,
    }
  },
  mounted() {
    var self = this;
    self.clickLogin = methods.clickLogin;
    self.clickLogout = methods.clickLogout;
  },
  methods: {
    ...mapMutations(['toggleDrawer']),
    onClick (e, item) {
      e.stopPropagation()
      if (item.to || !item.href) return
      this.$vuetify.goTo(item.href.endsWith('!') ? 0 : item.href)
    },
  },
}
</script>
<style scoped>
.toolbar-title {
  width: 150px;
  min-width: 150px;
  max-width: 150px;
} 
.main-container {
  margin-left: unset !important;
}

.appbar-button {
  margin-left: 5px;
}
</style>
