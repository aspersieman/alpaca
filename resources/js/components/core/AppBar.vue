<template>
  <div
    id="mainAppBar"
    ref="mainAppBar"
  >
    <v-app-bar 
      app 
      light
      clipped-left
    >
      <v-app-bar-nav-icon
        v-if="$vuetify.breakpoint.smAndDown || this.$store.getters.menu.length > 0"
        @click="toggleDrawer" 
      />
      <router-link to="/">
        <v-img
          :src="require('../../../img/logo/llama_emoji.png')"
          class="mr-5"
          contain
          height="48"
          width="48"
          max-width="48"
        />
      </router-link>
      <v-toolbar-title
        v-if="$vuetify.breakpoint.mdAndUp && !isSmall"
        class="toolbar-title"
      >
        <router-link to="/">
          {{ this.$store.state.appName }}
        </router-link>
      </v-toolbar-title>
      <v-container class="mx-auto py-0 main-container">
        <v-row align="center">
          <v-btn
            v-if="$store.getters.isLoggedIn && !isSmall"
            class="d-none d-sm-flex appbar-button"
            text
            to="/dashboard"
          >
            Dashboard
          </v-btn>
          <v-btn
            v-if="$store.getters.isLoggedIn && !isSmall"
            class="d-none d-sm-flex appbar-button"
            text
            to="/tasks"
          >
            Tasks
          </v-btn>
          <v-btn
            v-if="this.$store.getters.isLoggedIn && !isSmall"
            class="d-none d-sm-flex appbar-button"
            text
            to="/reports"
          >
            Reports
          </v-btn>
          <v-spacer />
          <base-btn-primary
            v-if="this.$store.getters.isLoggedIn && !isSmall"
            v-shortkey.once="['ctrl', 'alt', 't']"
            class="hidden-sm-and-down"
            dark
            title="Add new Task"
            icon="add"
            @shortkey="openTaskDialog()" 
            @click="openTaskDialog()"
          >
            New task
          </base-btn-primary>
          <timer-active />
          <v-btn
            v-if="!this.$store.getters.isLoggedIn && !isSmall"
            class="d-none d-sm-flex"
            text
            link
            @click="clickLogin('/login')"
          >
            Login
          </v-btn>
          <v-spacer />
          <avatar 
            v-if="this.$store.getters.isLoggedIn && !isSmall"
            class="d-none d-sm-flex"
          />
        </v-row>
        <task-dialog @taskAdded="snackbar=true" />
        <timer-dialog @timerAdded="snackbar=true" />
      </v-container>
    </v-app-bar>
  </div>
</template>
<script>
// Utilities
import {
  mapMutations,
} from 'vuex';

import TimerActive from '../timer/TimerActive';
import TaskDialog from '../../modules/front/tasks/components/TaskDialog';
import TimerDialog from '../../modules/front/tasks/components/TimerDialog';
import Avatar from './Avatar';
import methods from '../../common/Methods';
import TaskMixin from '../../mixins/TaskMixin';
import EventNames from '../../common/EventNames';

export default {
  name: 'CoreAppBar',
  components: {
    TimerActive,
    TaskDialog,
    TimerDialog,
    Avatar
  },
  mixins: [ 
    TaskMixin
  ],
  data() {
    return {
      breakpoint: false
    }
  },
  computed: {
    isSmall: function() {
      return this.breakpoint == 'xs' || this.breakpoint == 'sm';
    }
  },
  mounted() {
    var self = this;
    self.clickLogin = methods.clickLogin;
    self.$eventBus.$on(EventNames.NOTE_DRAWER_RESIZE, (drawerWidth) => {
      this.relativeBreakpoint(drawerWidth);
    });
  },
  methods: {
    ...mapMutations(['toggleDrawer']),
    onClick (e, item) {
      e.stopPropagation()
      if (item.to || !item.href) return
      this.$vuetify.goTo(item.href.endsWith('!') ? 0 : item.href)
    },
    relativeBreakpoint(drawerWidth) {
      const appBar = this.$refs.mainAppBar;
      let computedWidth = appBar.offsetWidth - drawerWidth;
      this.breakpoint = methods.getBreakpoint(computedWidth)
      this.$store.commit('setAppBarBreakpoint', this.breakpoint);
    }
  },
}
</script>
<style scoped>
.toolbar-title {
  width: 100px;
  min-width: 100px;
} 
.main-container {
  margin-left: unset !important;
}

.appbar-button {
  margin-left: 5px;
}
</style>
