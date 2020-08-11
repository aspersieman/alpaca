<template>
  <div class="dashboard">
    <h1 class="subheading grey--text ml-4">Dashboard</h1>
    <v-snackbar v-model="snackbar" top color="warning" text>
      <span>Are you sure?</span>
      <v-btn text color="white" @click="snackbar = false; deleteTask()">Yes</v-btn>
      <v-btn text color="white" @click="snackbar = false; ">No</v-btn>
    </v-snackbar>
    <v-container class="my-2">
      <v-layout row class="mb-3" wrap>
        <v-btn icon @click="tasks = tasksCopy; todos = true">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="primary" v-on="on">dashboard</v-icon>
            </template>
            All tasks
          </v-tooltip>
        </v-btn>
        <v-btn icon @click="searchByStatus('complete'); todos = false">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="success" v-on="on">check_circle</v-icon>
            </template>
            Completed tasks
          </v-tooltip>
        </v-btn>
        <v-btn icon @click="searchByStatus('ongoing'); todos = false">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="warning" v-on="on">work</v-icon>
            </template>
            Ongoing tasks
          </v-tooltip>
        </v-btn>
        <v-btn icon @click="searchByStatus('overdue'); todos = false">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-icon color="error" v-on="on">watch_later</v-icon>
            </template>
            Overdue tasks
          </v-tooltip>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn 
          style="display: none;"
          v-shortkey.once="['ctrl', 'alt', '/']"
          @shortkey="searchFocus()">
          Search focus
        </v-btn>
        <v-text-field
          v-model="search"
          outlined
          clearable
          label="Search"
          dense
          ref="searchInput"
          type="text">
        </v-text-field>
        <v-spacer></v-spacer>
        <v-btn 
          icon
          v-shortkey.once="['ctrl', 'alt', 't']"
          @shortkey="openTaskDialog()" 
          @click="openTaskDialog()">
          <v-icon color="primary">add_circle</v-icon>
        </v-btn>
      </v-layout>

      <v-layout row justify-center v-if="this.tasks.length == 0">
        <v-layout column align-center>
          <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here 🦙</span>
          <v-btn text color="primary" class="display-1" @click="openTaskDialog">Add new Task</v-btn>
        </v-layout>
      </v-layout>

      <draggable v-model="tasks" @change="saveOrder" handle=".handle" :disabled="!todos" v-if="this.tasks.length > 0">
      <v-card text flat v-for="(task, index) in searchTasks" :key="task.id">
        <v-layout row wrap :class="`pa-2 task ${task.status}`">
          <v-flex xs12 md3>
            <div class="caption grey--text ml-3" v-if="todos">Task Title</div>
            <div class="caption grey--text" v-if="!todos">Task Title</div>
            <div :class="{'mt-0 mb-1': $vuetify.breakpoint.smAndDown}" v-if="!editMode || index != indexToEdit" @click="editMode = !editMode; indexToEdit = index; newName = task.name;">
              <v-icon size="20" class="handle" v-if="todos">drag_indicator</v-icon>
              {{ task.name }}
            </div>
            <v-form @submit.prevent="updateName(index)">
            <v-text-field autofocus :color="`${task.status}s`" v-model="newName" v-if="editMode && indexToEdit == index" class="ma-0 pa-0"></v-text-field>
            </v-form>
          </v-flex>
          <v-flex xs2 sm3 md1>
            <div class="caption grey--text">Due by</div>
            <div :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}">{{ task.due }}</div>
          </v-flex>
          <v-flex xs5 sm6 md2 v-if="task.tagged && $vuetify.breakpoint.mdAndUp">
            <div 
              :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}">
              <v-chip
                v-for="tag in task.tagged"
                :key="tag.id"
                class="ma-2"
                x-small>
                {{tag.tag_name}}
              </v-chip>
            </div>
          </v-flex>
          <v-flex xs3 sm3 md2 class="pt-0">
            <div class="mt-0 pt-0">
              <v-btn @click="changeStatus(index)" text small rounded :class="`${task.status} white--text px-0 mx-0 status v-btn--round`">
                {{ task.status }}
              </v-btn>
            </div>
          </v-flex>

          <v-flex xs3 sm3 md2 v-if="$vuetify.breakpoint.mdAndUp">
            <v-list dense two-line subheader v-if="task.timers.length > 0">
              <v-list-item v-for="timer in task.timers" :key="timer.id" class="list-group-item timer-item" v-bind:class="{ active: showTimerForTask(task, timer) }">
                <v-list-item-icon>
                  <v-icon>access_time</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="timer.name">{{timer.name}}</v-list-item-title>
                  <v-list-item-subtitle>
                    <div v-if="showTimerForTask(task, timer)">
                      {{ activeTimerString }}
                      <v-btn icon v-if="showTimerForTask(task, timer)" class="btn btn-sm btn-secondary" @click="stopTimer()">
                        <v-tooltip bottom>
                          <template v-slot:activator="{ on }">
                            <v-icon v-on="on">stop</v-icon>
                          </template>
                          Stop timer
                        </v-tooltip>
                      </v-btn>
                    </div>
                    <div v-else>
                      {{ calculateTimeSpent(timer) }}
                      <v-btn icon @click="deleteTimer(task, timer)">
                        <v-tooltip bottom>
                          <template v-slot:activator="{ on }">
                            <v-icon v-on="on">delete</v-icon>
                          </template>
                          Delete timer
                        </v-tooltip>
                      </v-btn>
                    </div>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
            <p v-else>No work logged yet.</p>
          </v-flex>
          <v-spacer></v-spacer>
          <v-flex xs3 sm3 md2 v-if="$vuetify.breakpoint.mdAndUp">
            <div class="mt-0 pt-0">
              <v-btn icon :class="`${task.status}`" @click="editMode = !editMode; indexToEdit = index; newName = task.name;" class="pa-0 ma-0">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">edit</v-icon>
                  </template>
                  Edit
                </v-tooltip>
              </v-btn>
              <v-btn icon :class="`${task.status}`" @click="snackbar = true; indexToEdit = index;" class="pa-0 ma-0">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">delete</v-icon>
                  </template>
                  Delete
                </v-tooltip>
              </v-btn>
              <v-btn icon :class="`${task.status}`" @click="openTimerDialog(task);" class="pa-0 ma-0">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">more_time</v-icon>
                  </template>
                  Add timer
                </v-tooltip>
              </v-btn>
            </div>
          </v-flex>
          <v-flex xs3 sm3 md2 v-if="$vuetify.breakpoint.smAndDown">
            small
          </v-flex>
        </v-layout>
      </v-card>
      </draggable>
    </v-container>
    <AddTaskDialog @taskAdded="snackbar=true" />
    <AddTimerDialog @timerAdded="snackbar=true" />
  </div>
</template>

<script>
import AddTaskDialog from './components/AddTaskDialog';
import AddTimerDialog from './components/AddTimerDialog';
import draggable from 'vuedraggable';

export default {
  components: {
    draggable,
    AddTaskDialog,
    AddTimerDialog
  },
  data() {
    return {
      tasks: [],
      tasksCopy: [],
      todos: true,
      userId: '',
      btnColor: '',
      snackbar: false,
      editMode: false,
      newName: "",
      indexToEdit: 0,
      drag: true,
      snackbar: false,
      search: "",
      searching: false,
      // CLEAN UP
      newTimerName: '',
      newTaskName: '',
      newTaskTag: '',
      newTaskTags: [],
      activeTimerString: 'Timing...',
      counter: { seconds: 0, timer: null },
      modal: false,
      newValues: [],
      tasks: [],
    }
  },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs',[
      {label:'Tasks',to:{name:'dashboard'}},
    ]);
    self.$eventBus.$on(['TASK_ADDED','TASK_UPDATED','TASK_DELETED','TIMER_ADDED'],()=>{
      self.saveOrder();
      self.loadTasks(()=>{});
    });
  },
  methods: {
    openTaskDialog() {
      const self = this;
      self.$eventBus.$emit('TASK_DIALOG', true)
    },
    openTimerDialog(task) {
      const self = this;
      self.$store.commit('setActiveTask', task);
      self.$eventBus.$emit('TIMER_DIALOG', true)
    },
    loadTasks() {
      const self = this;
      window.axios.get('/api/tasks').then(response => {
        self.tasks = response.data;
        self.tasksCopy = self.tasks;
        window.axios.get('/api/task/timers/active').then(response => {
          if (response.data.id !== undefined) {
            self.$store.commit('setActiveTimer', response.data);
            self.startTimer(response.data.task, response.data);
          }
        })
      });
    },
    searchByStatus(status){
      if (status === "complete") {
        this.tasks = this.tasks.filter(function(item) {
          return item.status === "complete";
        });

      } else if (status === "ongoing") {
        this.tasks = this.tasks.filter(function(item) {
          return item.status === "ongoing";
        });

      } else if (status === "overdue") {
        this.tasks = this.tasks.filter(function(item) {
          return item.status === "overdue";
        });
      }  
    },

    changeStatus(index) {
      const currentTask = this.tasks[index]

      var oldStatus = currentTask.status;
      var newStatus = null;
      var btnColor = null;
      switch (currentTask.status) {
        case 'ongoing':
          newStatus = 'complete';
          btnColor = 'success';
          break;
        case 'complete':
          newStatus = 'overdue';
          btnColor = 'error';
          break;
        case 'overdue':
          newStatus = 'ongoing';
          btnColor = 'info';
          break;
        default:
          break;
      }

      if (newStatus) {
        window.axios.post(`/api/tasks/${currentTask.id}/status`, { status: newStatus })
            .then(response => {
          })
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.message,
              color: 'error',
              duration: 3000
          });
        });
        currentTask.status = newStatus
        this.btnColor = btnColor
        this.searchByStatus(oldStatus);
      }
    },

    deleteTask() {
      var index = this.indexToEdit;
      const currentTask = this.tasks[index]

      window.axios.delete('/api/tasks/'+parseInt(currentTask.id))
        .then(
          response => {
            this.tasks = this.tasks
              .filter(function(element) {
                return element.id !== task.id;
              });
          });

      if (index > -1) {
        this.tasks.splice(index, 1);
        this.tasksCopy.splice(index, 1);
      }

      this.saveOrder();
    },

    saveOrder() {
      var tasks = [];
      for (let index = 0; index < this.tasks.length; index++) {
        const currentTask = this.tasks[index];
        tasks.push({ "id": currentTask.id, "order": index });
      }
      if (tasks.length > 1) {
        window.axios.post(`/api/tasks/order`, { "tasks": tasks })
            .then(response => {})
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.message,
              color: 'error',
              duration: 3000
          });
        });
      }
    },

    updateName(index) {
      const currentTask = this.tasks[index]

      if (currentTask.name != this.newName) {
        window.axios.post(`/api/tasks/${currentTask.id}/name`, { name: this.newName })
            .then(response => {
            currentTask.name = this.newName
          })
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.message,
              color: 'error',
              duration: 3000
          });
        });
      }

      this.editMode = false;
    },

    /* REFACTOR {*/
    /**
     * Conditionally pads a number with "0"
     */
    _padNumber: number =>  (number > 9) ? number : "0" + number,

    /**
     * Splits seconds into hours, minutes, and seconds.
     */
    _readableTimeFromSeconds: function(seconds) {
      seconds = parseInt(seconds);
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60),
      }
    },

    /**
     * Redirects user back to login page if logged out
     */
    authenticationCheck: function(statusCode) {
      if (parseInt(statusCode) == 419) {
        // Logged out
        this.$router.go("/");
      }
    },

    /**
     * Calculate the amount of time spent on the task using the timer object.
     */
    calculateTimeSpent: function (timer) {
      if (timer.stopped_at) {
        const started = moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const stopped = moment(timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS')
        const seconds = parseInt(moment.duration(stopped.diff(started)).asSeconds())
        const time = this._readableTimeFromSeconds(seconds);
        return `${time.hours}:${time.minutes}:${time.seconds}`; // for syntax highlighting: `
      }
      return '';
    },

    /**
     * Determines if there is an active timer and whether it belongs to the task
     * passed into the function.
     */
    showTimerForTask: function (task, timer) {
      return this.counter.timer &&
        this.counter.timer.id === timer.id &&
        this.counter.timer.task.id === task.id
    },

    /**
     * Start counting the timer. Tick tock.
     */
    startTimer: function (task, timer) {
      // remove 'z' from timezone, moment doesn't like it
      // when doing maths
      const started = moment(timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS');
      const now = moment();

      this.counter.timer = timer
      this.counter.timer.task = task
      this.counter.seconds = parseInt(moment.duration(now.diff(started)).asSeconds())
      this.counter.ticker = setInterval(() => {
        const time = this._readableTimeFromSeconds(++this.counter.seconds)
        this.activeTimerString = `${time.hours}:${time.minutes}:${time.seconds}`
      }, 1000)
    },

    /**
     * Stop the timer from the API and then from the local counter.
     */
    stopTimer: function () {
      const timer = {
        tz: Intl.DateTimeFormat().resolvedOptions().timeZone
      };
      window.axios.post(`/api/tasks/${this.counter.timer.id}/timers/stop`, timer)
        .then(response => {
          // Loop through the tasks and get the right task...
          this.tasks.forEach(task => {
            if (task.id === parseInt(this.counter.timer.task.id)) {
              // Loop through the timers of the task and set the `stopped_at` time
              return task.timers.forEach(timer => {
                if (timer.id === parseInt(this.counter.timer.id)) {
                  return timer.stopped_at = response.data.stopped_at
                }
              })
            }
          });

          // Stop the ticker
          clearInterval(this.counter.ticker)

          // Reset the counter and timer string
          this.counter = { seconds: 0, timer: null }
          this.activeTimerString = 'Timing...'
        })
    },

    /**
     * Delete a timer.
     */
    deleteTimer: function (task, timer) {
      window.axios.delete(`/api/tasks/${task.id}/timer/${timer.id}`)
        .then(
          response => {
            task.timers = task.timers
              .filter(function(element) {
                return element.id !== timer.id;
              });
          })
        .catch(error => {
          this.authenticationCheck(error.response.status);
        });
      this.newTimerName = ''
    },

    handleDelete(eventIndex) {
      this.events.splice(eventIndex, 1);
    },
    handleInput(val, type) {
      this.newValues[type] = val;
    },
    addEvent() {
      this.events.push({
        time: this.newValues["time"],
        name: this.newValues["name"],
        location: this.newValues["location"],
        description: this.newValues["description"]
      });
    },
    /* REFACTOR }*/
    searchFocus() {
      this.$refs.searchInput.focus();
    }
  },
  computed: {
    searchTasks() {
      if (this.search === null) this.search = "";
      return this.tasks.filter(task => {
        return task.name.toLowerCase().includes(this.search);
      });
    }
  },
  created() {
    const self = this;
    const user = {}; //firebase.auth().currentUser;
    if (user) {
      var userId = user.uid
      this.userId = userId
    } else {
      // not logged
    }

    /*
    var projectsRef = {}; //db.collection('users/'+userId+'/tasks')

    // ORDER DATA BY PRIORITY
    projectsRef = {}; //projectsRef.orderBy("priority", "desc")

    // GET DATA
    projectsRef.onSnapshot(res => {
      const changes = res.docChanges();

      changes.forEach(change => {
        if (change.type === 'added') {
          this.tasks.unshift({ // UNSHIF FOR ADD AT THE BEGINNING
            ...change.doc.data(),
            id: change.doc.id
            })
          }
        })
    })
    */
    self.loadTasks();
  }
}
</script>

<style>

  .task.complete {
    border-left: 4px solid #3cd1c2;
  }

  .task.ongoing {
    border-left: 4px solid #ffaa2c;
  }

  .task.overdue {
    border-left: 4px solid #f83e70;
  }

  .task button.status {
    min-width: 88px;
    height: 28px;
  }

  .theme--light.v-btn:not(.v-btn--icon).complete {
    background-color: #3cd1c2;
  }

  .theme--light.v-btn:not(.v-btn--icon).ongoing {
    background-color: #ffaa2c;
  }

  .theme--light.v-btn:not(.v-btn--icon).overdue {
    background-color: #f83e70;
  }

  .theme--light.v-btn.complete {
    color: #3cd1c2;
  }

  .theme--light.v-btn.ongoing {
    color: #ffaa2c;
  }

  .theme--light.v-btn.overdue {
    color: #f83e70;
  }

  .handle {
    cursor: move;
  }

  .row {
    margin-left: unset;
    border-radius: unset !important;
  }
</style>
