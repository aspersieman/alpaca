<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    :fullscreen="$vuetify.breakpoint.xs"
  >
    <v-card>
      <v-card-title class="grey--text">
        <h2 v-if="mode == 'ADD'">
          Add a New Task
        </h2>
        <h2 v-if="mode == 'UPDATE'">
          Update Task
        </h2>
      </v-card-title>
      <v-card-text>
        <v-form
          ref="form"
          class="px-3"
        >
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
            <timer 
              v-if="task"
              :dense="true"
              :two-line="true"
              :task="task"
            />
          </div>

          <v-spacer />

          <v-layout
            row 
            wrap
            width="100%"
          >
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
          </v-layout>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import Timer from './Timer';
import moment from 'moment';

export default {
  components: {
    Timer
  },
  data() {
    return {
      dialog: false,
      task: {
        id: null,
        name: null,
        due: null,
        status: null,
        order: null,
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
      ]
    }
  },
  computed: {
    formattedDate() {
      return this.due ? moment(this.due).format('YYYY-MM-DD') : ''
    }
  },
  created() {
    const self = this;
    self.$eventBus.$on('task-add-dialog', (value) => {
      self.dialog = value;
      self.mode = 'ADD';
    });
    self.$eventBus.$on('task-update-dialog', (value) => {
      self.task = self.$store.state.task;
      self.name = self.$store.state.task.name;
      self.due = self.$store.state.task.due;
      self.timers = self.$store.state.task.timers || [];
      self.status = self.$store.state.task.status;

      self.dialog = value;
      self.mode = 'UPDATE';
    });
  },
  methods: {
    addTask() {
      if(this.name.length > 0 && this.due.length >= 4) {
        this.loading = true;
        
        const task = {
          name: this.name,
          due: moment(this.due).format('YYYY-MM-DD'),
          status: 'ongoing',
          order: null,
        };

        window.axios.post('/api/tasks', task)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.$eventBus.$emit('task-added');
          this.$store.commit('showSnackbar',{
            message: "Task added",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.log(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });

      }
    },
    updateTask() {
      if(this.name.length > 0 && this.due.length >= 4) {
        this.loading = true;
        
        const task = {
          name: this.name,
          due: this.due,
          status: this.status,
        };

        window.axios.put(`/api/tasks/${this.task.id}`, task)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.$eventBus.$emit('task-updated');
          this.$store.commit('showSnackbar',{
            message: "Task updated",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.log(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });

      }
    },
    close() {
      this.dialog = false;
    },
    openTimerDialog() {
      const self = this;
      self.$store.commit('setSelectedTask', this.task);
      self.$eventBus.$emit('timer-add-dialog', true)
    },

    formReset() {
      this.due = moment().format('YYYY-MM-DD');
      this.task = null;
      this.name = null;
      this.timers = [];
      this.status = 'ongoing';
    },
  },
}
</script>
<style scoped lang="scss">
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
</style>
