<template>
  <div>
    <v-navigation-drawer
      v-if="$vuetify.breakpoint.smAndDown || $store.getters.menu.length > 0"
      v-model="drawer"
      app
      clipped
      left
    >
      <template 
        v-if="$store.getters.isLoggedIn && ($vuetify.breakpoint.smAndDown || $store.getters.getAppBarBreakpoint == 'sm' || $store.getters.getAppBarBreakpoint == 'sx')"
        #prepend
      >
        <v-list-item two-line>
          <avatar />
          <v-list-item-content>
            <v-list-item-title>{{ user.name }}</v-list-item-title>
            <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-divider 
        v-if="$store.getters.isLoggedIn && $vuetify.breakpoint.smAndDown"
      />
      <v-list dense>
        <v-list-item
          to="/"
        >
          <v-list-item-action>
            <v-icon>home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>
              Home
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <div
          v-for="item of $store.getters.menu"
          :key="item.label"
        >
          <v-divider 
            v-if="item.navType == 'divider'"
            horizontal
          />
          <v-list-item
            v-if="item.navType != 'divider'"
            :key="item.routeName"
            :data="item"
            :to="{name: item.routeName}"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ item.label }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </div>
        <v-divider horizontal />
        <v-list-item
          v-if="!$store.getters.isLoggedIn"
          @click="clickLogin('/login')"
        >
          <v-list-item-action>
            <v-icon>meeting_room</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Login</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item
          v-if="$store.getters.isLoggedIn"
          @click="clickLogout('/logout','/')"
        >
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
import {
  mapMutations,
} from 'vuex';
import methods from '../../common/Methods';
import Avatar from './Avatar';
import AvatarMixin from '../../mixins/AvatarMixin';

export default {
  name: 'CoreDrawer',
  components: {
    Avatar
  },
  mixins: [ 
    AvatarMixin,
  ],
  data: () => ({
    user: {
      initials: '',
      name: '',
      email: '',
    },
  }),
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
  created() {
  },
  mounted() {
    this.clickLogin = methods.clickLogin;
    this.clickLogout = methods.clickLogout;
    if (this.$store.getters.isLoggedIn) {
      this.user.name = window.APP_GLOBALS.AUTH_USER.name;
      this.user.email = window.APP_GLOBALS.AUTH_USER.email;
      this.user.initials = this.getInitials(this.user.name);
      this.user.color = this.getAvatarColour(this.user.name);
    }
  },
  methods: {
    ...mapMutations(['setDrawer']),
    getInitials(name) {
      var initials = name.match(/\b\w/g) || [];
      return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
    },
  },
}
</script>
