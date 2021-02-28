<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    scrollable
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
        <v-text-field
          v-model="name"
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
              :value="formattedDate"
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
      </v-card-text>
      <v-divider />
      <v-card-actions>
        <v-btn
          v-if="mode == 'ADD'"
          text
          class="success mx-0 mt-3"
          :loading="loading"
          @click="addTask"
        >
          Add
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success ml-1 mx-0 mt-3"
          :loading="loading"
          @click="updateTask"
        >
          Update
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success ml-1 mx-0 mt-3"
          :loading="loading"
          @click="openTimerDialog();"
        >
          Add timer
        </v-btn>
        <v-spacer />
        <v-btn
          text
          class="error mx-0 mt-3"
          :loading="loading"
          @click="close();"
        >
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import TimerList from './TimerList';
import moment from 'moment';
import TaskMixin from '../../../../mixins/TaskMixin';
import EventNames from '../../../../common/EventNames';

export default {
  components: {
    TimerList
  },
  mixins: [ 
    TaskMixin
  ],
  data() {
    return {
      dialog: false,
      task: {
        id: null,
        name: null,
        due: null,
        status: null,
        order: null,
        timers: []
      },
      mode: '',
      name: '',
      due: moment().format('YYYY-MM-DD'),
      userId: '',
      dateRules: [
        (v) => (v.length >= 4) || 'Invalid date format',
        (v) => !!v || 'Date is required',
      ],
      loading: false,
      menu: false,
      timers: [],
      activeTimerString: 'Timing...',
      counter: { seconds: 0, timer: null },
      status: '',
      statuses: [
        'complete',
        'ongoing',
        'overdue'
      ],
      priority: { id: 0, name: '' },
      priorities: [
        { id: 0, name: '' },
        { id: 1, name: 'High' },
        { id: 2, name: 'Medium' },
        { id: 3, name: 'Low' },
      ],
      onlyActive: false
    }
  },
  computed: {
    formattedDate() {
      return this.due ? moment(this.due).format('YYYY-MM-DD') : ''
    }
  },
  created() {
    const self = this;
    self.$eventBus.$on(EventNames.TASK_ADD_DIALOG, (value) => {
      self.formReset();
      self.dialog = value;
      self.mode = 'ADD';
    });
    self.$eventBus.$on(EventNames.TASK_UPDATE_DIALOG, (value) => {
      self.formReset();
      self.task = self.$store.state.task;
      self.name = self.$store.state.task.name;
      self.due = self.$store.state.task.due;
      self.timers = self.$store.state.task.timers || [];
      self.status = self.$store.state.task.status;
      self.priority = self.getPriorityById(self.$store.state.task.priority);

      self.dialog = value;
      self.mode = 'UPDATE';
    });
  },
  methods: {
    addTask() {
      const self = this;
      if(self.name && self.due && self.name.length > 0 && self.due.length >= 4) {
        self.loading = true;
        
        const task = {
          name: self.name,
          due: moment(self.due).format('YYYY-MM-DD'),
          status: 'ongoing',
          order: null,
        };

        window.axios.post('/api/tasks', task)
          .then(response => {
          self.loading = false;
          self.dialog = false;
          self.formReset();
          self.$eventBus.$emit(EventNames.TASK_ADDED);
          self.$store.commit('showSnackbar',{
            message: "Task added",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.log(error);
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          self.loading = false;
        });

      }
    },
    updateTask() {
      const self = this;
      if(self.name.length > 0 && self.due.length >= 4) {
        self.loading = true;
        
        const task = {
          name: self.name,
          due: self.due,
          status: self.status,
          priority: self.priority.id || 0
        };

        window.axios.put(`/api/tasks/${self.task.id}`, task)
          .then(response => {
          self.loading = false;
          self.dialog = false;
          self.formReset();
          self.$eventBus.$emit(EventNames.TASK_UPDATED);
          self.$store.commit('showSnackbar',{
            message: "Task updated",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.log(error);
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          self.loading = false;
        });
      }
    },
    close() {
      this.dialog = false;
    },

    openTimerDialog() {
      const self = this;
      self.$store.commit('setSelectedTask', this.task);
      self.$eventBus.$emit(EventNames.TIMER_ADD_DIALOG, true)
    },

    formReset() {
      this.due = moment().format('YYYY-MM-DD');
      this.task = {
        id: null,
        name: null,
        due: null,
        status: null,
        order: null,
        timers: []
      };
      this.name = null;
      this.timers = [];
      this.status = 'ongoing';
      this.priority = { id: 0, name: '' };
    },

    getPriorityById(priorityId) {
      let priority = this.priorities
        .filter(function(element) {
          return element.id == priorityId;
        })[0];
      priority = priority || { id: 0, name: '' };
      return priority;
    }
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
