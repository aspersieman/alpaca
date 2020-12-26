<template>
  <div>
    <v-card
      text
      flat
      class="transparent"
    >
      <v-icon
        size="20"
        class="handle"
      >
        drag_indicator
      </v-icon>
      <v-layout
        row
        wrap
        :class="`pa-2 task ${task.status}`"
      >
        <v-flex
          xs12
          sm12
          md4
          lg4
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
          xs5
          sm5
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
          xs5
          sm5
          md3
          lg3 
          class="pt-0"
        >
          <div class="mt-0 pt-0">
            <v-btn
              text
              small
              rounded
              :class="`${task.status} white--text px-0 mx-0 status v-btn--round`"
              @click="changeStatus()"
            >
              {{ task.status }}
            </v-btn>
          </div>
        </v-flex>
        <v-spacer />
        <v-flex
          v-if="$vuetify.breakpoint.mdAndUp"
          md3
          lg3
        >
          <div class="mt-0 pt-0">
            <v-btn
              icon
              class="pa-0 ma-0"
              :class="`${task.status}`"
              @click="openTaskDialog(task);"
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
              @click="deleteTask()"
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
              v-if="$store.state.activeTimerCounter.seconds == 0"
              icon
              :class="`${task.status}`"
              class="pa-0 ma-0"
              @click="openTimerAddDialog(task);"
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
              @click="openNotesDrawer()"
            >
              <v-tooltip bottom>
                <template #activator="{ on }">
                  <v-icon v-on="on">
                    note
                  </v-icon>
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
                  @click="openTaskDialog(task);"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>edit</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Edit</v-list-item-title>
                </v-list-item>
                <v-list-item
                  :class="`${task.status}`"
                  @click="deleteTask()"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Delete</v-list-item-title>
                </v-list-item>
                <v-list-item
                  v-if="$store.state.activeTimerCounter.seconds == 0"
                  :class="`${task.status}`"
                  @click="openTimerAddDialog(task);"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>more_time</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Add timer</v-list-item-title>
                </v-list-item>
                <v-list-item
                  :class="`${task.status}`"
                  @click.stop="openNotesDrawer()"
                >
                  <v-list-item-icon class="horizontal-center">
                    <v-icon>note_add</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>Notes</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </v-flex>
        <v-flex
          v-if="task.timers.length > 0"
          xs12
          sm12
          md12
          lg12
          class="ml-3"
        >
          <div class="caption grey--text">
            Timers
          </div>
          <div>
            <timer :task="task" />
          </div>
        </v-flex>
      </v-layout>
    </v-card>
  </div>
</template>
<script>

import Timer from './Timer';
import TaskMixin from '../../../../mixins/TaskMixin';
import TimerMixin from '../../../../mixins/TimerMixin';

export default {
  components: {
    Timer
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
    }
  },
  mounted() { },
  methods: {
    openNotesDrawer() {
      const self = this;
      self.$store.commit('setNotes', self.task.notes);
      self.$store.commit('setSelectedTask', self.task);
      self.$eventBus.$emit('notes-drawer-toggle', true);
    }
  },
}
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";

.task {
  border-bottom: 1px solid $border-light-grey;
}
.task.complete {
  border-left: 4px solid $task-status-complete;
}

.task.ongoing {
  border-left: 4px solid $task-status-ongoing;
}

.task.overdue {
  border-left: 4px solid $task-status-overdue;
}

.task button.status {
  min-width: 88px;
  height: 28px;
}

.theme--light.v-btn:not(.v-btn--icon).complete {
  background-color: $task-status-complete;
}

.theme--light.v-btn:not(.v-btn--icon).ongoing {
  background-color: $task-status-ongoing;
}

.theme--light.v-btn:not(.v-btn--icon).overdue {
  background-color: $task-status-overdue;
}

.theme--light.v-btn.complete {
  color: $task-status-complete;
}

.theme--light.v-btn.ongoing {
  color: $task-status-ongoing;
}

.theme--light.v-btn.overdue {
  color: $task-status-overdue;
}

.handle {
  position: absolute;
  left: -10px;
  top: 50%;
  bottom: 50%;
  vertical-align: middle;
  cursor: move;
}

.transparent {
  background-color: transparent;
}
</style>

