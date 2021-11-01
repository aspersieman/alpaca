<template>
  <div
    id="mainAppBar"
    ref="mainAppBar"
  >
    <v-app-bar
      app
      light
      clipped-left
      clipped-right
    >
      <v-app-bar-nav-icon
        v-if="$vuetify.breakpoint.smAndDown || menu.length > 0"
        @click.stop="drawer = !drawer"
      />
      <router-link to="/">
        <v-img
          src="/img/logo/logo-and-alpaca-text-primary-button.png"
          :class="{ 'mr-5': $vuetify.breakpoint.mdAndUp }"
          height="48"
          contain
        />
      </router-link>
      <v-container class="mx-auto py-0 main-container">
        <v-row align="center">
          <v-btn
            v-if="$auth.check && $vuetify.breakpoint.mdAndUp"
            class="d-none d-sm-flex appbar-button"
            text
            to="/dashboard"
          >
            Dashboard
          </v-btn>
          <v-btn
            v-if="$auth.check && $vuetify.breakpoint.mdAndUp"
            class="d-none d-sm-flex appbar-button"
            text
            to="/tasks"
          >
            Tasks
          </v-btn>
          <v-btn
            v-if="$auth.check && $vuetify.breakpoint.mdAndUp"
            class="d-none d-sm-flex appbar-button"
            text
            to="/notes"
          >
            Notes
          </v-btn>
          <v-btn
            v-if="$auth.check && $vuetify.breakpoint.mdAndUp"
            class="d-none d-sm-flex appbar-button"
            text
            to="/reports"
          >
            Reports
          </v-btn>
          <v-speed-dial
            v-if="$auth.check"
            v-model="fab"
            :direction="`bottom`"
            :open-on-hover="openOnHover"
            :transition="`slide-y-reverse-transition`"
          >
            <template #activator>
              <v-btn
                v-model="fab"
                color="primary"
                fab
              >
                <v-icon v-if="fab">
                  mdi-close
                </v-icon>
                <v-icon v-else>
                  mdi-plus
                </v-icon>
              </v-btn>
            </template>
            <v-btn
              fab
              small
              dark
              color="#565555"
              @click="$store.dispatch('task/openTaskDialog')"
            >
              <v-icon>mdi-check-circle-outline</v-icon>
            </v-btn>
            <v-btn
              fab
              small
              dark
              color="#565555"
              @click="$store.dispatch('note/openNoteDialog')"
            >
              <v-icon>mdi-note-plus-outline</v-icon>
            </v-btn>
          </v-speed-dial>
          <timer-running />
          <v-btn
            v-if="!$auth.check"
            class="d-none d-sm-flex"
            text
            link
            go="/login"
          >
            Login
          </v-btn>
          <v-spacer />
        </v-row>
        <task-dialog @taskAdded="snackbar=true" />
        <timer-dialog @timerAdded="snackbar=true" />
      </v-container>
      <avatar
        v-if="$auth.check"
        class="d-none d-sm-flex"
      />
    </v-app-bar>
    <core-drawer v-model="drawer" />
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import CoreDrawer from '@/components/core/Drawer.vue';
import TimerRunning from '@/modules/front/tasks/components/timer/TimerRunning';
import TaskDialog from '@/modules/front/tasks/components/TaskDialog';
import TimerDialog from '@/modules/front/tasks/components/timer/TimerDialog';
import Avatar from '@/components/core/Avatar';
import TaskMixin from '@/mixins/TaskMixin';

export default {
  name: 'CoreAppBar',
  components: {
    CoreDrawer,
    TimerRunning,
    TaskDialog,
    TimerDialog,
    Avatar
  },
  mixins: [
    TaskMixin,
  ],
  data: () => ({
    drawer: false,
    fab: false,
    openOnHover: true
  }),
  computed: {
    ...mapGetters('menu', ['menu'])
  },
  methods: {
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
