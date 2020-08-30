<template>
  <div class="tasks">
    <h1 class="subheading grey--text ml-4">Tasks</h1>
    <v-snackbar v-model="snackbar" top color="warning" text>
      <span>Are you sure?</span>
      <v-btn text color="white" @click="snackbar = false; deleteTask()">Yes</v-btn>
      <v-btn text color="white" @click="snackbar = false; ">No</v-btn>
    </v-snackbar>
    <v-container class="my-2">
      <v-layout row class="mb-3" wrap>
        <v-btn 
          style="display: none;"
          v-shortkey.once="['ctrl', 'alt', '/']"
          @shortkey="searchFocus()">
          Search focus
        </v-btn>
        <v-flex class="search-input-container" xs3 md3 lg3>
          <v-text-field
            v-model="search"
            outlined
            clearable
            label="Search"
            dense
            class="search-input"
            ref="searchInput"
            type="text">
          </v-text-field>
        </v-flex>
        <v-flex xs6 md6 lg5>
          <div class="cmb-statuses">
            <v-combobox
              v-model="status"
              :items="statuses"
              multiple
              label="Status"
              class="search-input"
              @change="searchByStatus()"
              outlined
              clearable
              dense
              chips>
              <template v-slot:selection="data">
                <v-chip
                  :key="JSON.stringify(data.item)"
                  :small="$vuetify.breakpoint.mdAndUp"
                  :x-small="$vuetify.breakpoint.smAndDown"
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  :disabled="data.disabled"
                  @click:close="data.parent.selectItem(data.item)"
                  :class="`status ${data.item}`">
                  {{ data.item }}
                </v-chip>
              </template>
            </v-combobox>
          </div>
        </v-flex>
        <v-spacer></v-spacer>
        <v-flex xs2 md1 lg1>
          <base-btn
            class="mr-0"
            square
            title="Add new Task"
            v-shortkey.once="['ctrl', 'alt', 't']"
            @shortkey="openTaskDialog()" 
            @click="openTaskDialog()">
            <v-icon>add</v-icon>
          </base-btn>
        </v-flex>
      </v-layout>

      <v-layout row justify-center v-if="this.tasks.length == 0">
        <v-layout column align-center>
          <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here </span>
          <v-img src="img/logo/llama_emoji.png" width="120px"></v-img>
          <v-btn text color="primary" class="display-1" @click="openTaskDialog()">Add new Task</v-btn>
        </v-layout>
      </v-layout>

      <draggable v-model="tasks" @change="saveOrder" handle=".handle" :disabled="!todos" v-if="this.tasks.length > 0">
      <v-card text flat v-for="(task, index) in searchTasks" :key="task.id">
        <v-layout row wrap :class="`pa-2 task ${task.status}`">
          <v-flex xs12 sm12 md4 lg4 class="task-column">
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
          <v-flex xs4 sm4 md3 lg2 class="task-column">
            <div class="caption grey--text ml-3">Due by</div>
            <div :class="{'mt-0 mx-0 px-0 ml-3': $vuetify.breakpoint.smAndDown}">{{ task.due }}</div>
          </v-flex>
          <v-flex md3 lg2 v-if="$vuetify.breakpoint.mdAndUp" class="task-column">
            <div class="caption grey--text ml-3" v-if="task.tagged.length > 0">Categories</div>
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
          <v-flex xs4 sm4 md3 lg2 class="pt-0 task-column">
            <div class="mt-0 pt-0">
              <v-btn @click="changeStatus(index)" text small rounded :class="`${task.status} white--text px-0 mx-0 status v-btn--round`">
                {{ task.status }}
              </v-btn>
            </div>
          </v-flex>
          <v-spacer></v-spacer>
          <v-flex md3 lg2 v-if="$vuetify.breakpoint.mdAndUp" class="task-column">
            <div class="mt-0 pt-0">
              <v-btn icon :class="`${task.status}`" @click="openTaskDialog(task);" class="pa-0 ma-0">
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
              <v-btn
                v-if="$store.state.activeTimerCounter.seconds == 0"
                icon
                :class="`${task.status}`"
                @click="openTimerDialog(task);"
                class="pa-0 ma-0">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on">more_time</v-icon>
                  </template>
                  Add timer
                </v-tooltip>
              </v-btn>
            </div>
          </v-flex>
          <v-flex xs1 sm1 v-if="$vuetify.breakpoint.smAndDown" class="task-column">
            <div>
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    light
                    icon
                    v-bind="attrs"
                    v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item
                    :class="`${task.status}`"
                    @click="openTaskDialog(task);">
                    <v-list-item-icon class="horizontal-center">
                      <v-icon>edit</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Edit</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    :class="`${task.status}`"
                    @click="snackbar = true; indexToEdit = index;">
                    <v-list-item-icon class="horizontal-center">
                      <v-icon>delete</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Delete</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    :class="`${task.status}`"
                    v-if="$store.state.activeTimerCounter.seconds == 0"
                    @click="openTimerDialog(task);">
                    <v-list-item-icon class="horizontal-center">
                      <v-icon>more_time</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Add timer</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
          </v-flex>
          <v-flex xs12 sm12 md12 lg12 v-if="task.timers.length > 0" class="task-column">
            <div class="caption grey--text ml-3">Timers</div>
            <timer :dense="false" :two-line="true" :task="task"></timer>
          </v-flex>
        </v-layout>
      </v-card>
      </draggable>
    </v-container>
    <task-dialog @taskAdded="snackbar=true" />
    <timer-dialog @timerAdded="snackbar=true" />
  </div>
</template>

<script>
import TaskDialog from './components/TaskDialog';
import TimerDialog from './components/TimerDialog';
import Timer from './components/Timer';
import draggable from 'vuedraggable';

export default {
  components: {
    draggable,
    TaskDialog,
    TimerDialog,
    Timer
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
      activeTimerString: 'Timing...',
      counter: { seconds: 0, timer: null },
      modal: [],
      status: [],
      statuses: [
        'complete',
        'ongoing',
        'overdue'
      ],
      newValues: [],
    }
  },
  mounted() {
    const self = this;
    this.getDashboardSettings();
    self.$store.commit('setBreadcrumbs',[
      {label:'Tasks',to:{name:'tasks'}},
    ]);
    self.$eventBus.$on(['TASK_ADDED','TASK_UPDATED','TASK_DELETED','TIMER_ADDED', 'TIMER_UPDATED'],()=>{
      self.saveOrder();
      self.loadTasks();  
    });
  },
  methods: {
    openTaskDialog(task) {
      const self = this;
      if (task) {
        self.$store.commit('setActiveTask', task);
        self.$eventBus.$emit('TASK_UPDATE_DIALOG', true)
      } else {
        self.$store.commit('setActiveTask', null);
        self.$eventBus.$emit('TASK_ADD_DIALOG', true)
      }
    },
    openTimerDialog(task) {
      const self = this;
      self.$store.commit('setActiveTask', task);
      self.$eventBus.$emit('TIMER_ADD_DIALOG', true)
    },
    loadTasks() {
      const self = this;
      window.axios.get('/api/tasks').then(response => {
        self.tasks = response.data;
        self.$store.commit('setTasks', response.data);
        self.tasksCopy = response.data;
        window.axios.get('/api/task/timers/active').then(response => {
          if (response.data.id !== undefined) {
            self.$store.commit('setActiveTimer', response.data);
            self.$eventBus.$emit('TIMER_START', response.data)
          }
        });
        self.searchByStatus();
      });
    },
    searchByStatus(status){
      const self = this;
      self.tasks = self.$store.state.tasks;
      self.setDashboardSettings();
      if (status) {
        self.tasks = self.tasks.filter(function(item) {
          return item.status === status;
        });
      } else {
        if (self.status.length > 0) {
          self.tasks = self.tasks.filter(function(item) {
            return self.status.includes(item.status);
          });
        }
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
        this.searchByStatus();
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
    searchFocus() {
      this.$refs.searchInput.focus();
    },
    setDashboardSettings() {
      const parsed = JSON.stringify(this.status);
      localStorage.setItem('status', parsed);
    },
    getDashboardSettings() {
      if (localStorage.getItem('status')) {
        try {
          this.status = JSON.parse(localStorage.getItem('status'));
        } catch(e) {
          localStorage.removeItem('status');
        }
      }
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
<style scoped lang="scss">
@import "../../../../sass/_variables";
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
  cursor: move;
}

.row {
  margin-left: unset;
  border-radius: unset !important;
}

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

.search-input-container {
  min-width: 111px; 
}
.search-input {
  margin-left: 5px;
}
</style>
