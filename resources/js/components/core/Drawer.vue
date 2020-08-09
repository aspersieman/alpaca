<template>
  <div>
    <v-navigation-drawer
       v-if="$vuetify.breakpoint.smAndDown || $store.getters.menu.length > 0"
       v-model="drawer"
       app
       clipped
       left>
      <v-list dense>
        <v-list-item
          to="/">
          <v-list-item-action>
            <v-icon>home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Home</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <div v-for="item of $store.getters.menu" :key="item.label">
          <v-divider 
            v-if="item.navType == 'divider'"
            horizontal>
          </v-divider>
          <v-list-item
            v-if="item.navType != 'divider'"
            v-bind:data="item"
            v-bind:key="item.routeName"
            :to="{name: item.routeName}">
            <v-list-item-action>
              <v-icon>{{item.icon}}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{item.label}}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </div>
        <v-divider horizontal></v-divider>
        <v-list-item
          v-if="!this.$store.getters.isLoggedIn"
          v-on:click="clickLogin('/login')">
          <v-list-item-action>
            <v-icon>meeting_room</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Login</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item
          v-if="this.$store.getters.isLoggedIn"
          v-on:click="clickLogout('/logout','/')">
          <v-list-item-action>
            <v-icon>no_meeting_room</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
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
  name: 'CoreDrawer',
  mounted() {
    var self = this;
    self.clickLogin = methods.clickLogin;
    self.clickLogout = methods.clickLogout;
  },
  computed: {
    drawer: {
      get () {
        return this.$store.state.drawer
      },
      set (val) {
        this.setDrawer(val)
      },
    },
  },
  methods: {
    ...mapMutations(['setDrawer']),
  },
}
</script>
