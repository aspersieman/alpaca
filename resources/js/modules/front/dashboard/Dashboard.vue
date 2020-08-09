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
        <v-btn icon @click="tasks = projectsCopy; todos = true">
          <v-icon color="primary">dashboard</v-icon>
        </v-btn>
        <v-btn icon @click="filterTasks('complete'); todos = false">
          <v-icon color="success">check_circle</v-icon>
        </v-btn>
        <v-btn icon @click="filterTasks('ongoing'); todos = false">
          <v-icon color="warning">work</v-icon>
        </v-btn>
        <v-btn icon @click="filterTasks('overdue'); todos = false">
          <v-icon color="error">watch_later</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
         <v-btn icon @click="openDialog()">
          <v-icon color="primary">add_circle</v-icon>
        </v-btn>
      </v-layout>

      <v-layout row justify-center v-if="this.tasks.length == 0">
        <v-layout column align-center>
          <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here 🦙</span>
          <v-btn text color="primary" class="display-1" @click="openDialog">Add new Task</v-btn>
        </v-layout>
      </v-layout>

      <draggable v-model="tasks" @change="saveOrder" handle=".handle" :disabled="!todos" v-if="this.tasks.length > 0">
      <v-card text flat v-for="(task, index) in tasks" :key="task.name">
        <v-layout row wrap :class="`pa-2 task ${task.status}`">
          <v-flex xs12 md6>
            <div class="caption grey--text ml-3" v-if="todos">Task Title</div>
            <div class="caption grey--text" v-if="!todos">Task Title</div>
            <div :class="{'mt-0 mb-1': $vuetify.breakpoint.smAndDown}" v-if="!editMode || index != indexToEdit">
              <v-icon size="20" class="handle" v-if="todos">drag_indicator</v-icon>
              {{ task.name }}
            </div>
            <v-form @submit.prevent="updateName(index)">
            <v-text-field autofocus :color="`${task.status}s`" v-model="newName" v-if="editMode && indexToEdit == index" class="ma-0 pa-0"></v-text-field>
            </v-form>
          </v-flex>
          <v-flex xs5 sm6 md2>
            <div class="caption grey--text">Due by</div>
            <div :class="{'mt-0 mx-0 px-0': $vuetify.breakpoint.smAndDown}">{{ task.due }}</div>
          </v-flex>
          <v-flex xs3 sm3 md2 class="pt-0">
            <div class="mt-0 pt-0">
              <v-btn @click="changeStatus(index)" text small rounded :class="`${task.status} white--text px-0 mx-0 status v-btn--round`">
                {{ task.status }}
              </v-btn>
            </div>
          </v-flex>
          <v-spacer></v-spacer>

          <v-flex xs3 sm3 md2>
            <div class="mt-0 pt-0">
              <v-btn icon :class="`${task.status}`" @click="editMode = !editMode; indexToEdit = index; newName = task.name;" class="pa-0 ma-0"><v-icon>edit</v-icon></v-btn>
              <v-btn icon :class="`${task.status}`" @click="snackbar = true; indexToEdit = index;" class="pa-0 ma-0"><v-icon>delete</v-icon></v-btn>
            </div>

          </v-flex>
        </v-layout>
        <v-divider></v-divider>
      </v-card>
      </draggable>
    </v-container>
    <AddTaskDialog @projectAdded="snackbar=true" />
  </div>
</template>

<script>
import AddTaskDialog from './components/AddTaskDialog';
import draggable from 'vuedraggable';

export default {
  components: {
    draggable,
    AddTaskDialog
  },
  data() {
    return {
      tasks: [],
      projectsCopy: [],
      todos: true,
      userId: '',
      btnColor: '',
      snackbar: false,
      editMode: false,
      newName: "",
      indexToEdit: 0,
      drag: true,
      snackbar: false,
      // CLEAN UP
      tasksOld: null,
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
    self.loadTasks();
  },
  methods: {
    openDialog() {
      const self = this;
      self.$eventBus.$emit('dialog', true)
    },
    loadTasks() {
      console.log("loading tasks");
      window.axios.get('/api/tasks').then(response => {
        this.tasks = response.data;
      /*
        window.axios.get('/task/timers/active').then(response => {
          if (response.data.id !== undefined) {
            this.startTimer(response.data.task, response.data);
            // Show the timer
            this.$root.$emit('bv::toggle::collapse', 'task-'+response.data.task.id);
          }
        })
      */
      });
    },
    filterTasks(status){
      this.tasks = this.projectsCopy

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
        this.projectsCopy.splice(index, 1);
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
        const started = moment(timer.started_at)
        const stopped = moment(timer.stopped_at)
        const time = this._readableTimeFromSeconds(
          parseInt(moment.duration(stopped.diff(started)).asSeconds())
        )
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
      const started = moment(timer.started_at)

      this.counter.timer = timer
      this.counter.timer.task = task
      this.counter.seconds = parseInt(moment.duration(moment().diff(started)).asSeconds())
      this.counter.ticker = setInterval(() => {
        const time = this._readableTimeFromSeconds(++this.counter.seconds)
        this.activeTimerString = `${time.hours}:${time.minutes}:${time.seconds}`
      }, 1000)
    },

    /**
     * Stop the timer from the API and then from the local counter.
     */
    stopTimer: function () {
      window.axios.post(`/api/tasks/${this.counter.timer.id}/timers/stop`)
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
     * Create a new timer.
     */
    createTimer: function (task) {
      window.axios.post(`/api/tasks/${task.id}/timers`, {name: this.newTimerName})
        .then(response => {
          task.timers.push(response.data)
          this.startTimer(response.data.task, response.data)
          // Show the timer
          this.$root.$emit('bv::toggle::collapse', 'task-'+response.data.task.id);
        })
      this.newTimerName = ''
      $('#timerCreate').modal('hide');
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
    }
    /* REFACTOR }*/
  },
  created() {
    const user = {}; //firebase.auth().currentUser;
    if (user) {
      var userId = user.uid
      this.userId = userId
    } else {
      // not logged
    }

    var projectsRef = {}; //db.collection('users/'+userId+'/tasks')

    // ORDER DATA BY PRIORITY
    projectsRef = {}; //projectsRef.orderBy("priority", "desc")

    // GET DATA
    /*
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
