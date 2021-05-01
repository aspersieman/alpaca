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
          <v-spacer />
          <base-btn-primary
            v-if="$auth.check"
            v-shortkey.once="['ctrl', 'alt', 't']"
            class="hidden-sm-and-down"
            dark
            title="Add new Task"
            icon="add"
            @shortkey="taskDialogAdd()"
            @click="taskDialogAdd()"
          >
            New task
          </base-btn-primary>
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
import NoteMixin from '@/mixins/NoteMixin';

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
    NoteMixin
  ],
  data: () => ({
    drawer: false,
  }),
  computed: {
    ...mapGetters('menu', ['menu'])
  },
  created() {
    this.$overlay.show(true);
    this.$store.dispatch('note/getNotes')
      .then(() => this.$eventBus.notesLoaded());
    this.getTasks().then(() => this.$overlay.show(false));
  },
  methods: {
    onClick (e, item) {
      e.stopPropagation()
      if (item.to || !item.href) return
      this.$vuetify.goTo(item.href.endsWith('!') ? 0 : item.href)
    },
    taskDialogAdd() {
      this.$eventBus.taskDialogAdd(true);
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
