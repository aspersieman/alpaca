<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    scrollable
    persistent
    :fullscreen="$vuetify.breakpoint.xs"
    :retain-focus="false"
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
          <v-text-field
            :value="completed_at"
            label="Completed"
            prepend-icon="task_alt"
            readonly
          />
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
              class="cmb-statuses uppercase"
              :items="statuses"
              label="Status"
              chips
            >
              <template
                #item="data"
              >
                <span class="uppercase">
                  {{ data.item.name }}
                </span>
              </template>
              <template
                #selection="data"
                class="cmb-statuses"
              >
                <v-chip
                  :key="JSON.stringify(data.item)"
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  :disabled="data.disabled"
                  :class="`uppercase status ${data.item.id}`"
                  @click:close="data.parent.selectItem(data.item)"
                >
                  {{ data.item.name }}
                </v-chip>
              </template>
            </v-combobox>
            <div
              v-if="task.timers && task.timers.length > 0"
            >
              <div class="caption grey--text">
                Timers
              </div>
              <timer-list
                :only-active="onlyActive"
                :task="task"
              />
              <v-container>
                <v-row>
                  <v-col>
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
                        class="horizontal-center fs-90 strong total-timer-time"
                      >
                        {{ getTaskTotalTime(task) }}
                      </v-flex>
                      <v-layout
                        row
                      >
                        <v-flex
                          class="horizontal-center fs-90"
                        >
                          &nbsp;
                        </v-flex>
                        <v-flex class="horizontal-center">
                          &nbsp;
                        </v-flex>
                      </v-layout>
                    </v-layout>
                  </v-col>
                </v-row>
              </v-container>
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
          v-if="mode == 'UPDATE' && isTimerRunning !== true"
          text
          class="success ml-1 mx-0 mt-3"
          :disabled="!valid || loading"
          @click="$store.dispatch('timer/openTimerDialog', {task: task, timer: null })"
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
import { mapGetters } from 'vuex';
import TimerList from '@/modules/front/tasks/components/timer/TimerList';
import moment from 'moment';
import TaskMixin from '@/mixins/TaskMixin';
import TimerMixin from '@/mixins/TimerMixin';

export default {
  components: {
    TimerList
  },
  mixins: [
    TaskMixin,
    TimerMixin
  ],
  data() {
    return {
      dialog: false,
      valid: false,
      loading: false,
      mode: '',
      name: '',
      status: { id: 'todo', name: 'To do' },
      completed_at: null,
      timers: [],
      due: moment().format('YYYY-MM-DD'),

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
      priority: { id: 0, name: '' },
    }
  },
  computed: {
    ...mapGetters('task', [
      'task',
      'statuses',
      'priorities',
      'taskDialogAdd',
      'taskDialogUpdate'
    ]),
    ...mapGetters('timer', [
      'isTimerRunning',
    ]),
  },
  created() {
    const self = this;
    this.$store.subscribe((mutation) => {
      if (mutation.type === 'task/TASK_DIALOG_ADD') {
        self.formReset();
        self.dialog = self.taskDialogAdd;
        if (self.taskDialogAdd === true) {
          self.mode = 'ADD';
        }
      }
      if (mutation.type === 'task/TASK_DIALOG_UPDATE') {
        self.formReset();
        self.dialog = self.taskDialogUpdate;
        if (self.taskDialogUpdate === true) {
          self.name = self.task.name;
          self.due = self.task.due;
          self.completed_at = self.$appFormatters.dateTimeFormat(self.task.completed_at, 'YYYY-MM-DD');
          self.timers = self.task.timers || [];
          self.status = self.statuses.find(status => status.id === self.task.status);
          self.$store.dispatch('task/getTaskPriorityById', self.task.priority)
            .then((value) => self.priority = value);
          self.mode = 'UPDATE';
        }
      }
    });
  },
  methods: {
    save() {
      if (this.valid) {
        const task = {
          name: this.name,
          due: moment(this.due).format('YYYY-MM-DD'),
          priority: this.priority.id,
          status: this.status,
        };
        this.$overlay.show(true);
        this.$store.dispatch('task/saveTask', task)
          .then(() => {
            this.close();
            this.$overlay.show(false);
          });
      }
    },
    update() {
      if (this.valid) {
        const task = {
          id: this.task.id,
          name: this.name,
          due: this.due,
          priority: this.priority.id,
          status: this.status,
        };
        this.$store.dispatch('task/updateTask', task)
          .then(this.close());
      }
    },
    close() {
      this.dialog = false;
    },
    formReset() {
      this.due = moment().format('YYYY-MM-DD');
      this.name = '';
      this.timers = [];
      this.status = 'todo';
      this.priority = { id: 0, name: '' };
    },

  },
}
</script>
<style scoped lang="scss">
@import "../../../../../sass/index";
@import "../../../../../sass/_variables";
.strong {
  font-weight: bold;
}
.border-top {
  border-top: 1px solid $border-dark-grey;
}
.total-timer-time {
  margin-right: 4px;
}
</style>
