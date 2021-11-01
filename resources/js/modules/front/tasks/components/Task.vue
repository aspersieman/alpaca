<template>
  <div
    class="task-container"
  >
    <v-card
      text
      flat
      class="transparent"
    >
      <v-layout
        row
        wrap
        :class="`pa-2 task ${task.status}`"
      >
        <v-flex
          xs12
          sm12
          md5
          lg5
        >
          <div
            class="caption grey--text ml-2"
          >
            Task Title
          </div>
          <div
            v-if="!editMode"
            :class="{'mt-0 mb-1': $vuetify.breakpoint.smAndDown}"
          >
            <div class="ellipsis ml-2">
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-icon
                    :class="`priority-${task.priority}`"
                    size="20"
                    v-on="on"
                  >
                    priority_high
                  </v-icon>
                </template>
                Priority: {{ getTaskPriorityDescription(task.priority) }}
              </v-tooltip>
              {{ task.name }}
            </div>
          </div>
          <v-form @submit.prevent="updateName()">
            <v-text-field
              v-if="editMode"
              v-model="newName"
              autofocus
              :color="`${task.status}s`"
              class="ma-0 pa-0"
            />
          </v-form>
        </v-flex>
        <v-flex
          xs7
          sm7
          md2
          lg2
        >
          <div
            class="caption grey--text"
            :class="{'ml-3': $vuetify.breakpoint.smAndDown}"
          >
            Due by
          </div>
          <div :class="{'mt-0 mx-0 px-0 ml-3': $vuetify.breakpoint.smAndDown}">
            {{ task.due }}
          </div>
        </v-flex>
        <v-flex
          v-if="$vuetify.breakpoint.mdAndUp"
          md2
          lg2
          class="pt-0"
        >
          <div
            v-if="getTaskTotalTime(task) != '00:00:00'"
            class="caption grey--text"
            :class="{'ml-3': $vuetify.breakpoint.smAndDown}"
          >
            Time
          </div>
          <div
            v-if="getTaskTotalTime(task) != '00:00:00'"
            class="mt-0 pt-0"
          >
            {{ getTaskTotalTime(task) }}
          </div>
        </v-flex>
        <v-spacer
          v-if="$vuetify.breakpoint.mdAndUp"
        />
        <v-flex
          xs4
          sm4
          md1
          lg1
          class="pt-0"
        >
          <div class="mt-0 pt-0">
            <v-btn
              text
              small
              rounded
              :class="`${task.status} white--text px-0 mx-0 status v-btn--round`"
              @click="$store.dispatch('task/changeTaskStatus', task)"
            >
              {{ task.status }}
            </v-btn>
          </div>
        </v-flex>
        <v-flex
          v-if="$vuetify.breakpoint.mdAndUp"
          md2
          lg2
          class="align-right"
        >
          <div class="mt-0 pt-0">
            <v-btn
              icon
              class="pa-0 ma-0"
              :class="`${task.status}`"
              @click="$store.dispatch('task/openTaskDialog', task)"
            >
              <v-tooltip bottom>
                <template
                  #activator="{ on }"
                >
                  <v-icon v-on="on">
                    edit
                  </v-icon>
                </template>
                Edit
              </v-tooltip>
            </v-btn>
            <v-btn
              icon
              :class="`${task.status}`"
              class="pa-0 ma-0"
              @click="$store.dispatch('task/deleteTask', task)"
            >
              <v-tooltip bottom>
                <template
                  #activator="{ on }"
                >
                  <v-icon v-on="on">
                    delete
                  </v-icon>
                </template>
                Delete
              </v-tooltip>
            </v-btn>
            <v-btn
              v-if="getTaskTimers(task, onlyActive).length > 0"
              icon
            >
              <timer-running
                :small="small"
              />
            </v-btn>
            <v-btn
              v-if="isTimerRunning !== true"
              icon
              :class="`${task.status}`"
              class="pa-0 ma-0"
              @click="$store.dispatch('timer/openTimerDialog', {task: task, timer: null })"
            >
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-icon v-on="on">
                    more_time
                  </v-icon>
                </template>
                Add timer
              </v-tooltip>
            </v-btn>
            <v-btn
              icon
              :class="`${task.status}`"
              class="pa-0 ma-0"
              @click="openNoteDrawer()"
            >
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-badge
                    :content="countNotes(task)"
                    :value="countNotes(task)"
                    bordered
                    overlap
                    color="grey lighten-1"
                  >
                    <v-icon v-on="on">
                      note
                    </v-icon>
                  </v-badge>
                </template>
                Notes
              </v-tooltip>
            </v-btn>
          </div>
        </v-flex>
        <v-flex
          v-if="$vuetify.breakpoint.smAndDown"
          xs1
          sm1
        >
          <div>
            <v-menu
              bottom
              left
              close-on-content-click
            >
              <template #activator="{ on, attrs }">
                <v-btn
                  light
                  icon
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                  :class="`${task.status}`"
                  @click="$store.dispatch('task/openTaskDialog', task)"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>edit</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Edit</v-list-item-title>
                </v-list-item>
                <v-list-item
                  :class="`${task.status}`"
                  @click="$store.dispatch('task/deleteTask', task)"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Delete</v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="getTaskTimers(task, onlyActive).length > 0"
                  :class="`${task.status}`"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-btn
                      icon
                    >
                      <timer-running
                        :small="small"
                      />
                    </v-btn>
                  </v-list-item-icon>
                  <v-list-item-title>Timer</v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="$store.getters['timer/timerRunning'].seconds == 0"
                  :class="`${task.status}`"
                  @click="$store.dispatch('timer/openTimerDialog', {task: task, timer: null })"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>more_time</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Add timer</v-list-item-title>
                </v-list-item>
                <v-list-item
                  :class="`${task.status}`"
                  @click.stop="openNoteDrawer()"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-badge
                      :content="countNotes(task)"
                      :value="countNotes(task)"
                      bordered
                      overlap
                      color="grey lighten-1"
                    >
                      <v-icon>note</v-icon>
                    </v-badge>
                  </v-list-item-icon>
                  <v-list-item-title>Notes</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </v-flex>
      </v-layout>
    </v-card>
  </div>
</template>
<script>

import { mapGetters } from 'vuex';
import TimerRunning from '@/modules/front/tasks/components/timer/TimerRunning';
import TaskMixin from '@/mixins/TaskMixin';
import TimerMixin from '@/mixins/TimerMixin';

export default {
  components: {
    TimerRunning
  },
  mixins: [
    TaskMixin,
    TimerMixin
  ],
  props: {
    task: {
      required: true,
      type: Object,
      default: function() {
        return {
          id: null,
          name: null,
          due: null,
          status: null,
          order: null,
          timers: [],
        }
      }
    }
  },
  data() {
    return {
      newValues: [],
      timers: [],
      editMode: false,
      onlyActive: true,
      small: true
    }
  },
  computed: {
    ...mapGetters('timer', [
      'isTimerRunning',
      'timerRunning'
    ]),
  },
  methods: {
    openNoteDrawer() {
      this.$store.commit('task/TASK', this.task);
      this.$store.dispatch('drawer/toggleNoteDrawer');
    },
    countNotes(task) {
      return task?.notes?.length > 0 ? task?.notes?.length : '';
    },
    countTimers(task) {
      return task?.timers?.length > 0 ? task?.timers?.length : '';
    },
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";
.task-container {
  background-color: $theme-light-background;
  border-bottom: 1px solid $border-light-grey;
}
.task button.status {
  min-width: 88px;
  height: 28px;
}
.task.complete {
  border-left: 4px solid $task-status-complete;
}
.task.inprogress {
  border-left: 4px solid $task-status-inprogress;
}
.task.todo {
  border-left: 4px solid $task-status-todo;
}
.transparent {
  background-color: transparent;
}
.align-right {
  text-align: right;
}
.row {
  margin: 0px;
}
</style>
