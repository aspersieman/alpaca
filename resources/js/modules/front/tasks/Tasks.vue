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
        @change="saveOrder"
      >
        <task
          v-for="(task, index) in searchTasks"
          :key="index"
          :task="task" 
        />
      </draggable>
    </v-container>
    <task-dialog @taskAdded="snackbar=true" />
    <timer-dialog @timerAdded="snackbar=true" />
  </div>
</template>

<script>
import TaskDialog from './components/TaskDialog';
import TimerDialog from './components/TimerDialog';
import Task from './components/Task';
import draggable from 'vuedraggable';
import TaskMixin from './mixins/TaskMixin';

export default {
  components: {
    draggable,
    TaskDialog,
    TimerDialog,
    Task
  },
  mixins: [ TaskMixin ],
  data() {
    return {
      tasks: [],
      tasksCopy: [],
      userId: '',
      btnColor: '',
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
    self.getDashboardSettings();
    self.$store.commit('setBreadcrumbs',[
      {
        label: 'Tasks',
        to: { name:'tasks' }
      },
    ]);
    self.$eventBus.$on(['task-added', 'task-updated', 'timer-added', 'timer-updated'],()=>{
      self.loadTasks();  
    });
    self.$eventBus.$on(['task-deleted'], ()=> {
      self.tasks = self.tasks
        .filter(function(element) {
          return element.id !== self.$store.state.task.id;
        });
    });
    self.$eventBus.$on(['task-search-by-status'],()=>{
      self.searchByStatus();  
    });
  },
  created() {
    const self = this;
    self.loadTasks();
  },
  methods: {
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

    searchByStatus(status) {
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
