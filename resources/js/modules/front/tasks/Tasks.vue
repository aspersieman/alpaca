<template>
  <div class="tasks">
    <v-container class="my-2">
      <v-layout 
        row 
        class="mb-3" 
        wrap
      >
        <h1 class="subheading grey--text ml-4">
          Tasks
        </h1>
        <v-spacer />
        <v-flex
          xs2
          md1
          lg1
        >
          <base-btn
            v-shortkey.once="['ctrl', 'alt', 't']"
            class="mr-0"
            square
            title="Add new Task"
            @shortkey="openTaskDialog()" 
            @click="openTaskDialog()"
          >
            <v-icon>add</v-icon>
          </base-btn>
        </v-flex>
      </v-layout>
      <v-tabs
        v-model="activeTab"
        background-color="transparent"
        light
      >
        <v-tab>
          Active
        </v-tab>
        <v-tab>
          Complete
        </v-tab>
      </v-tabs>
      <v-tabs-items v-model="activeTab">
        <v-tab-item>
          <v-layout 
            row 
            class="mb-1 mt-1" 
            wrap
          >
            <v-btn 
              v-shortkey.once="['ctrl', 'alt', '/']"
              style="display: none;"
              @shortkey="searchFocus()"
            >
              Search focus
            </v-btn>
            <v-flex 
              class="search-input-container"
              xs3
              md3
              lg3
            >
              <v-text-field
                ref="searchInput"
                v-model="search"
                outlined
                clearable
                dense
                label="Search"
                class="search-input"
                type="text"
              />
            </v-flex>
            <v-flex
              xs6
              md6
              lg5
            >
              <div class="cmb-statuses">
                <v-combobox
                  v-model="status"
                  outlined
                  clearable
                  dense
                  chips
                  :items="statuses"
                  multiple
                  label="Status"
                  class="search-input"
                  @change="searchByStatus()"
                >
                  <template
                    #selection="data"
                  >
                    <v-chip
                      :key="JSON.stringify(data.item)"
                      :small="$vuetify.breakpoint.mdAndUp"
                      :x-small="$vuetify.breakpoint.smAndDown"
                      :class="`status ${data.item}`"
                      :input-value="data.selected"
                      :disabled="data.disabled"
                      v-bind="data.attrs"
                      @click:close="data.parent.selectItem(data.item)"
                    >
                      {{ data.item }}
                    </v-chip>
                  </template>
                </v-combobox>
              </div>
            </v-flex>
          </v-layout>

          <v-layout
            v-if="tasks.length == 0"
            row
            justify-center
          >
            <v-layout
              column 
              align-center
            >
              <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here </span>
              <v-img 
                src="img/logo/llama_emoji.png" 
                width="120px"
              />
              <v-btn
                text
                color="primary"
                class="display-1"
                @click="openTaskDialog()"
              >
                Add new Task
              </v-btn>
            </v-layout>
          </v-layout>

          <draggable
            v-if="tasks.length > 0"
            v-model="tasks"
            handle=".handle"
            :disabled="!todos"
            @change="saveOrder"
          >
            <v-card
              v-for="(task, index) in searchTasks"
              :key="task.id"
              text
              flat
            >
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
                    v-if="todos"
                    class="caption grey--text ml-3"
                  >
                    Task Title
                  </div>
                  <div
                    v-if="!todos"
                    class="caption grey--text"
                  >
                    Task Title
                  </div>
                  <div
                    v-if="!editMode || index != indexToEdit" 
                    :class="{'mt-0 mb-1': $vuetify.breakpoint.smAndDown}"
                    @click="editMode = !editMode; indexToEdit = index; newName = task.name;"
                  >
                    <div class="ellipsis">
                      <v-icon
                        v-if="todos"
                        size="20"
                        class="handle"
                      >
                        drag_indicator
                      </v-icon>
                      {{ task.name }}
                    </div>
                  </div>
                  <v-form @submit.prevent="updateName(index)">
                    <v-text-field
                      v-if="editMode && indexToEdit == index"
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
                      @click="changeStatus(index)"
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
                      @click="deleteTask(index)"
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
                      @click="openTimerDialog(task);"
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
                          @click="deleteTask(index)"
                        >
                          <v-list-item-icon class="horizontal-center">
                            <v-icon>delete</v-icon>
                          </v-list-item-icon>
                          <v-list-item-title>Delete</v-list-item-title>
                        </v-list-item>
                        <v-list-item
                          v-if="$store.state.activeTimerCounter.seconds == 0"
                          :class="`${task.status}`"
                          @click="openTimerDialog(task);"
                        >
                          <v-list-item-icon class="horizontal-center">
                            <v-icon>more_time</v-icon>
                          </v-list-item-icon>
                          <v-list-item-title>Add timer</v-list-item-title>
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
          </draggable>
        </v-tab-item>
        <v-tab-item>
          Other things
        </v-tab-item>
      </v-tabs-items>
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
      activeTab: null
    }
  },
  computed: {
    searchTasks() {
      return this.tasks.filter(task => {
        return task.name.toLowerCase().includes(this.search);
      });
    }
  },
  mounted() {
    const self = this;
    this.getDashboardSettings();
    self.$store.commit('setBreadcrumbs',[
      {label:'Tasks',to:{name:'tasks'}},
    ]);
    self.$eventBus.$on(['task-added','task-updated','task-deleted','timer-added', 'timer-updated'],()=>{
      self.loadTasks();  
    });
  },
  created() {
    const self = this;
    self.loadTasks();
  },
  methods: {
    openTaskDialog(task) {
      const self = this;
      if (task) {
        self.$store.commit('setSelectedTask', task);
        self.$eventBus.$emit('task-update-dialog', true)
      } else {
        self.$store.commit('setSelectedTask', null);
        self.$eventBus.$emit('task-add-dialog', true)
      }
    },
    openTimerDialog(task) {
      const self = this;
      self.$store.commit('setSelectedTask', task);
      self.$eventBus.$emit('timer-add-dialog', true)
    },
    loadTasks(statuses = 'ongoing,overdue') {
      console.log("loadTasks");
      console.log(statuses);
      const self = this;
      window.axios.get('/api/tasks/'+statuses).then(response => {
        self.tasks = response.data.data;
        self.$store.commit('setTasks', response.data.data);
        self.tasksCopy = response.data.data;
        window.axios.get('/api/task/timers/active').then(response => {
          if (response.data.data.id !== undefined) {
            self.$store.commit('setActiveTimer', response.data.data);
            self.$eventBus.$emit('timer-start', response.data.data)
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
            .then()
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.data.message,
              color: 'error',
              duration: 3000
          });
        });
        currentTask.status = newStatus
        this.btnColor = btnColor
        this.searchByStatus();
      }
    },

    deleteTask(index) {
      const self = this;

      self.$store.commit('showDialog',{
        type: "confirm",
        icon: 'warning',
        title: "Confirm deletion",
        message: "Are you sure you want to delete this task?",
        okCb: ()=>{
          const currentTask = self.tasks[index]
          console.log(currentTask);
          window.axios.delete('/api/tasks/'+parseInt(currentTask.id))
            .then(function(response) {
              self.tasks = self.tasks
                .filter(function(element) {
                  return element.id !== currentTask.id;
                });
              self.$store.commit('showSnackbar',{
                message: response.data.message,
                color: 'success',
                duration: 3000
              });
              self.$eventBus.$emit('task-deleted');
            })
            .catch(error => {
              self.$store.commit('hideLoader');
              if (error.response) {
                self.$store.commit('showSnackbar',{
                  message: error.response.data.data.message,
                  color: 'error',
                  duration: 10000
                });
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log('Error', error.message);
              }
            });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },

    saveOrder() {
      var tasks = [];
      for (let index = 0; index < this.tasks.length; index++) {
        const currentTask = this.tasks[index];
        tasks.push({ "id": currentTask.id, "order": index });
      }
      if (tasks.length > 1) {
        window.axios.post(`/api/tasks/order`, { "tasks": tasks })
            .then()
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.data.message,
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
            currentTask.name = this.newName;
            return response;
          })
            .catch(error => {
            console.log(error);
            this.$store.commit('showSnackbar', {
              message: error.response.data.data.message,
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
  }
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
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
