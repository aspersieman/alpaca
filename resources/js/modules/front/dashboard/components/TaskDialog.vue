<template>

  <v-dialog max-width="600px" v-model="dialog">
    <v-card>
      <v-card-title class="grey--text">
        <h2 v-if="mode == 'ADD'">Add a New Task</h2>
        <h2 v-if="mode == 'UPDATE'">Update Task</h2>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-text-field label="Title" v-model="name" prepend-icon="edit" autofocus></v-text-field>
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                :rules="dateRules"
                :value="formattedDate"
                v-model="due"
                label="Due date"
                prepend-icon="event"
                readonly
                v-bind="attrs"
                v-on="on">
              </v-text-field>
            </template>
            <v-date-picker v-model="due" @input="menu2 = false"></v-date-picker>
          </v-menu>
          <v-combobox
            v-model="tags"
            :items="tagItems"
            chips
            clearable
            label="Categories"
            multiple
            prepend-icon="category"
            solo>
            <template v-slot:selection="{ attrs, item, select, selected }">
              <v-chip
                v-bind="attrs"
                :input-value="selected"
                close
                @click="select"
                @click:close="removeCategory(item)">
                <strong>{{ item }}</strong>&nbsp;
              </v-chip>
            </template>
          </v-combobox>
          <div v-if="mode == 'UPDATE'">
            <v-combobox
              v-model="status"
              :items="statuses"
              label="Status"
              chips>
              <template v-slot:selection="data">
                <v-chip
                  :key="JSON.stringify(data.item)"
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  :disabled="data.disabled"
                  @click:close="data.parent.selectItem(data.item)"
                  :class="`status ${data.item}`">
                  {{ data.item }}
                </v-chip>
              </template>
            </v-combobox>
            <timer :dense="true" :two-line="true" :task="task"></timer>
          </div>

          <v-spacer></v-spacer>

          <v-btn text class="success mx-0 mt-3" v-if="mode == 'ADD'" @click="addTask" :loading="loading">Add task</v-btn>
          <v-btn text class="success mx-0 mt-3" v-if="mode == 'UPDATE'" @click="updateTask" :loading="loading">Update task</v-btn>
          <v-btn text class="success mx-0 mt-3" v-if="mode == 'UPDATE'" @click="openTimerDialog();"  :loading="loading">Add timer</v-btn>
        </v-form>
      </v-card-text>

    </v-card>
  </v-dialog>
</template>

<script>
import timer from './Timer';
import TimerDialog from './TimerDialog';

export default {
  components: {
    timer
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
        tags: []
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
      tags: [],
      tagItems: [],
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
  mounted() { },
  created() {
    this.$eventBus.$on('TASK_ADD_DIALOG', (value) => {
      this.dialog = value;
      this.mode = 'ADD';
      this.task = null;
      this.name = null;
      this.tags = [];
      this.due = moment().format('YYYY-MM-DD');
      this.timers = [];
      this.status = 'ongoing';
    });
    this.$eventBus.$on('TASK_UPDATE_DIALOG', (value) => {
      this.task = this.$store.state.task;
      this.name = this.$store.state.task.name;
      const tags = this.$store.state.task.tagged.map(function (item) {
        return item.tag_name;
      });
      this.tags = tags;
      this.due = this.$store.state.task.due;
      this.timers = this.$store.state.task.timers;
      this.status = this.$store.state.task.status;

      this.dialog = value;
      this.mode = 'UPDATE';
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
          tags: this.tags
        };

        window.axios.post('/api/tasks', task)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.name = ''
          this.tags = [];
          this.$eventBus.$emit('TASK_ADDED');
          this.$store.commit('showSnackbar',{
            message: "Task added",
            color: 'success',
            duration: 1000
          });
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
          tags: this.tags
        };

        window.axios.put(`/api/tasks/${this.task.id}`, task)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.name = ''
          this.tags = [];
          this.$eventBus.$emit('TASK_UPDATED');
          this.$store.commit('showSnackbar',{
            message: "Task updated",
            color: 'success',
            duration: 1000
          });
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
    openTimerDialog() {
      const self = this;
      self.$store.commit('setActiveTask', this.task);
      self.$eventBus.$emit('TIMER_ADD_DIALOG', true)
    },

    formReset() {
      this.name = '';
      this.due = moment().format('YYYY-MM-DD');
    },
    removeCategory(item) {
      this.tags.splice(this.tags.indexOf(item), 1)
      this.tags = [...this.tags]
    },
  },
  computed: {
    formattedDate() {
      return this.due ? moment(this.due).format('YYYY-MM-DD') : ''
    }
  },

}
</script>
<style scoped lang="scss">
@import "../../../../../sass/_variables";
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
