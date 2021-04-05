<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    scrollable
    persistent
    :fullscreen="$vuetify.breakpoint.xs"
    :retain-focus="false"
    :loading="loading"
  >
    <v-card>
      <v-card-title class="grey--text">
        <span v-if="mode == 'ADD'">
          Add a New Task
        </span>
        <span v-if="mode == 'UPDATE'">
          Update Task
        </span>
      </v-card-title>
      <v-divider />
      <v-card-text>
        <v-form
          v-model="valid"
          lazy-validation
        >
          <v-text-field
            v-model="name"
            :rules="nameRules"
            label="Title"
            prepend-icon="edit"
            autofocus
          />
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px"
          >
            <template #activator="{ on, attrs }">
              <v-text-field
                v-model="due"
                :rules="dateRules"
                :value="due"
                label="Due date"
                prepend-icon="event"
                readonly
                v-bind="attrs"
                v-on="on"
              />
            </template>
            <v-date-picker
              v-model="due"
              @input="menu2 = false"
            />
          </v-menu>
          <v-combobox
            v-model="priority"
            class="cmb-priority"
            :items="priorities"
            item-text="name"
            item-value="id"
            label="Priority"
          >
            <template
              #prepend
            >
              <v-icon
                :class="`priority-${priority.id}`"
              >
                priority_high
              </v-icon>
            </template>
          </v-combobox>
          <div v-if="mode == 'UPDATE'">
            <v-combobox
              v-model="status"
              class="cmb-statuses"
              :items="statuses"
              label="Status"
              chips
            >
              <template
                #selection="data"
                class="cmb-statuses"
              >
                <v-chip
                  :key="JSON.stringify(data.item)"
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  :disabled="data.disabled"
                  :class="`status ${data.item}`"
                  @click:close="data.parent.selectItem(data.item)"
                >
                  {{ data.item }}
                </v-chip>
              </template>
            </v-combobox>
            <div
              v-if="task.timers.length > 0"
              class="timers"
            >
              <div class="caption grey--text">
                Timers
              </div>
              <timer-list
                :only-active="onlyActive"
                :task="task"
              />
              <v-card 
                text 
                flat 
              >
                <div>
                  <v-layout
                    row
                    wrap
                    class="border-top"
                  >
                    <v-flex 
                      xs1
                      sm1
                      md1
                      lg1
                    >
                      &nbsp;
                    </v-flex>
                    <v-flex
                      xs6
                      sm3
                      md3
                      lg3
                      class="ml-2 strong"
                    >
                      Total:
                    </v-flex>
                    <v-flex
                      v-if="$vuetify.breakpoint.smAndUp"
                      sm4
                      md4
                      lg4
                    >
                      &nbsp;
                    </v-flex>
                    <v-spacer />
                    <v-flex
                      xs4
                      sm3
                      md3
                      lg3 
                      class="fs-90"
                    >
                      <v-layout 
                        row
                      >
                        <v-flex
                          class="horizontal-center fs-90 strong"
                        >
                          {{ getTotalTime(task) }}
                        </v-flex>
                      </v-layout>
                    </v-flex>
                  </v-layout>
                </div>
              </v-card>
            </div>
          </div>
        </v-form>
      </v-card-text>
      <v-divider />
      <v-card-actions>
        <v-btn
          v-if="mode == 'ADD'"
          text
          class="success mx-0 mt-3"
          :disabled="!valid || loading"
          @click="save"
        >
          Add
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success ml-1 mx-0 mt-3"
          :disabled="!valid || loading"
          @click="update"
        >
          Update
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success ml-1 mx-0 mt-3"
          :disabled="!valid || loading"
          @click="openTimerDialog(task);"
        >
          Add timer
        </v-btn>
        <v-spacer />
        <v-btn
          text
          class="error mx-0 mt-3"
          :disabled="loading"
          @click="close();"
        >
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapActions, mapMutations, mapGetters } from 'vuex';
import TimerList from '@/modules/front/tasks/components/TimerList';
import moment from 'moment';
import TaskMixin from '@/mixins/TaskMixin';
import EventNames from '@/common/EventNames';

export default {
  components: {
    TimerList
  },
  mixins: [ 
    TaskMixin
  ],
  data() {
    return {
      valid: false,
      dateRules: [
        (v) => (v.length >= 4) || 'Invalid date format',
        (v) => !!v || 'Date is required',
      ],
      nameRules: [
        (v) => (v.length >= 2) || 'Cannot be shorter than 2 characters',
        (v) => !!v || 'Name is required',
      ],
      menu: false,
      activeTimerString: 'Timing...',
      counter: { seconds: 0, timer: null },
      onlyActive: false,
    }
  },
  computed: {
    ...mapGetters('task', [
      'task',
      'loading',
      'statuses',
      'priorities',
    ]),
    name: {
      get () {
        return this.task.name;
      },
      set (val) {
        let task = this.task;
        task.name = val;
        this.$store.commit('task/TASK', task);
      },
    },
    due:{
      get () {
        return this.task.due || moment().format('YYYY-MM-DD');
      },
      set (val) {
        let task = this.task;
        task.due = moment(val).format('YYYY-MM-DD');
        this.$store.commit('task/TASK', task);
      },
    },
    status: {
      get () {
        return this.task.status || 'ongoing';
      },
      set (val) {
        let task = this.task;
        task.status = val;
        this.$store.commit('task/TASK', task);
      },
    },
    priority: {
      get () {
        return this.getPriorityById(this.task.priority);
      },
      set (val) {
        let task = this.task;
        task.priority = val.id;
        this.$store.commit('task/TASK', task);
      },
    },
    dialog: function() {
      return this.$store.getters['task/dialogAdd'] === true || this.$store.getters['task/dialogUpdate'] === true;
    },
    mode: function() {
      return this.$store.getters['task/dialogUpdate'] ? 'UPDATE' : 'ADD';
    },
    timers: function() {
      return this.task.timers || [];
    },
  },
  created() {
    this.$store.commit('task/LOADING', false);
  },
  methods: {
    ...mapActions({
        saveTask: 'task/saveTask',
        updateTask: 'task/updateTask',
        openTimerDialog: 'timer/openTimerDialog',
    }),
    ...mapMutations('task', ['TASK']),
    save() {
      if (this.valid) {
        this.saveTask(this.task)
          .then(this.close());
      }
    },
    update() {
      if (this.valid) {
        this.updateTask(this.task)
          .then(this.close());
      }
    },
    close() {
      this.$store.dispatch('task/closeTaskDialog');
      this.formReset();
    },
    formReset() {
      this.due = moment().format('YYYY-MM-DD');
      this.task = {
        id: null,
        name: null,
        due: null,
        status: null,
        priority: null,
        order: null,
        timers: []
      };
      this.name = null;
      this.timers = [];
      this.status = 'ongoing';
      this.priority = { id: 0, name: '' };
    },

  },
}
</script>
<style scoped lang="scss">
@import "../../../../../sass/index";
@import "../../../../../sass/_variables";
.v-list {
  text-transform: uppercase;
}
.theme--light.v-chip.status {
  text-transform: uppercase;
}
.theme--light.v-chip.status.complete {
  color: white;
  background-color: $task-status-complete;
}
.theme--light.v-chip.status.ongoing {
  color: white;
  background-color: $task-status-ongoing;
}
.theme--light.v-chip.status.overdue {
  color: white;
  background-color: $task-status-overdue;
}
.strong {
  font-weight: bold;
}
.border-top {
  border-top: 1px solid $border-dark-grey;
}
.timers {
  max-width: 500px;
}
</style>
