<template>
  <div class="tasks">
    <note-drawer />
    <v-container class="my-2">
      <v-row>
        <v-col
          class="mb-3" 
        >
          <v-layout 
            row 
            wrap
          >
            <v-avatar>
              <v-icon>
                task_alt
              </v-icon>
            </v-avatar>
            <h1 class="subheading grey--text ml-4">
              Tasks
            </h1>
          </v-layout>
          <v-layout 
            v-if="$vuetify.breakpoint.mdAndUp"
            row 
            wrap
          >
            <h4 class="subheading grey--text ml-4">
              Active tasks and tasks completed today
            </h4>
          </v-layout>
        </v-col>
        <v-col
          class="filter-toggle-container"
        >
          <span
            v-if="$store.getters.isLoggedIn && $vuetify.breakpoint.smAndDown"
          >
            <v-badge
              :content="filtersCount"
              :value="filtersCount"
              bordered
              overlap
            >
              <a
                title="Filters"
                @click="toggleFilters()"
              >
                Filters
                <v-icon
                  v-if="!showFilters"
                  right
                >
                  arrow_drop_down
                </v-icon>
                <v-icon
                  v-if="showFilters"
                  right
                >
                  arrow_drop_up
                </v-icon>
              </a>
            </v-badge>
          </span>
        </v-col>
      </v-row>
      <v-fade-transition leave-absolute>
        <v-layout 
          v-if="$vuetify.breakpoint.mdAndUp || (showFilters && $vuetify.breakpoint.smAndDown)"
          row 
          class="mb-1 mt-1 " 
          wrap
          transition="scroll-y-transition"
        >
          <v-btn 
            v-shortkey.once="['ctrl', 'alt', '/']"
            style="display: none;"
            @shortkey="searchFocus()"
          >
            Search focus
          </v-btn>
          <v-text-field
            ref="searchInput"
            v-model="search"
            outlined
            :clearable="clearable"
            dense
            label="Search"
            class="search-input"
            :class="{ 'search-input-max-width': $vuetify.breakpoint.mdAndUp }"
            type="text"
          >
            <v-icon slot="prepend">
              search
            </v-icon>
          </v-text-field>
          <v-combobox
            v-model="status"
            outlined
            :clearable="clearable"
            dense
            chips
            :items="statuses"
            multiple
            label="Status"
            class="search-input"
            :class="{ 'search-input-max-width': $vuetify.breakpoint.mdAndUp }"
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
          <date-range-picker 
            v-model="dates"
            class="search-input"
            :value="dates"
            :clearable="clearable"
            :class="{ 'search-input-max-width': $vuetify.breakpoint.mdAndUp }"
          />
          <v-combobox
            v-model="priority"
            class="search-input"
            :class="{ 'search-input-max-width': $vuetify.breakpoint.mdAndUp }"
            :items="priorities"
            :clearable="clearable"
            item-text="name"
            item-value="id"
            label="Priority"
            outlined
            dense
          />
          <v-spacer />
          <v-combobox
            v-model="sort"
            outlined
            :clearable="clearable"
            dense
            :items="sortList"
            label="Sort"
            class="search-input sort-input"
            :class="{ 'search-input-max-width': $vuetify.breakpoint.mdAndUp }"
            prepend-icon="filter_list"
            @change="sortTasks()"
          />
        </v-layout>
      </v-fade-transition>
    </v-container>
    <v-container
      class="tasks-container"
    >
      <v-row>
        <v-col
          class="col-12"
        >
          <v-layout
            v-if="$store.getters['task/tasks'].length == 0"
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
          <task
            v-for="(task, index) in searchTasks"
            :key="index"
            :task="task" 
          />
          <v-btn
            v-if="$store.getters.isLoggedIn && $vuetify.breakpoint.smAndDown"
            fab
            dark
            small
            class="task-add-button-mobile primary hidden-lg-and-up"
            @click="openTaskDialog()"
          >
            <v-icon>add</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import Task from './components/Task';
import NoteDrawer from '../../../components/notes/NoteDrawer';
import DateRangePicker from '../../../components/core/DateRangePicker';
import TaskMixin from '../../../mixins/TaskMixin';
import TimerMixin from '../../../mixins/TimerMixin';
import EventNames from '../../../common/EventNames';

export default {
  components: {
    Task,
    NoteDrawer,
    DateRangePicker
  },
  mixins: [ 
    TaskMixin,
    TimerMixin
  ],
  data() {
    return {
      tasks: [],
      tasksCopy: [],
      userId: '',
      btnColor: '',
      newName: '',
      indexToEdit: 0,
      drag: true,
      snackbar: false,
      search: '',
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
      dates: [],
      showFilters: false,
      sort: '',
      sortList: [
        'Due date',
        'Priority',
        'Status'
      ],
      filtersCount: 0,
      clearable: true,
      priority: null,
      priorities: [
        { id: 1, name: 'High' },
        { id: 2, name: 'Medium' },
        { id: 3, name: 'Low' },
      ],
    }
  },
  computed: {
    searchTasks() {
      const self = this;
      self.setTasksSettings();
      return this.$store.getters['task/tasks'].filter(task => {
        let searchFound = true;
        if (self.search != '' && self.search != null) {
          searchFound = task.name.toLowerCase().includes(self.search);
        }
        let statusFound = true;
        if (self.status.length > 0) {
          statusFound = self.status.includes(task.status);
        }
        let priorityFound = true;
        if (self.priority) {
          priorityFound = task.priority === self.priority.id;
        }
        let datesFound = true;
        if (self.dates && self.dates.length > 0) {
          let fromDate = self.$appFormatters.dateOnly(self.dates[0])
          let toDate = fromDate;
          if (self.dates.length > 1) {
            toDate = self.$appFormatters.dateOnly(self.dates[1]);
          }

          fromDate = fromDate.getTime();
          toDate = toDate.getTime();
          // check without time, i.e. as date only
          const dueDate = self.$appFormatters.dateOnly(task.due);
          datesFound = (dueDate >= fromDate) && (dueDate <= toDate);
        }
        if (searchFound && statusFound && datesFound && priorityFound) {
          return task;
        }
      });
    },
  },
  watch: {
    search: function() {
      this.countFilters();
    },
    status: function() {
      this.countFilters();
    },
    dates: function() {
      this.countFilters();
    },
    priority: function() {
      this.countFilters();
    }
  },
  mounted() {
    const self = this;
    self.getTasksSettings();
    self.$eventBus.$on(['task-added', 'task-updated', 'timer-added', 'timer-updated', 'timer-deleted', 'note-deleted'],()=>{
      self.getTasks();  
    });
    self.$eventBus.$on(['tasks-loaded'],()=>{
      self.sortTasks();  
    });
    self.$eventBus.$on(['task-deleted'], ()=> {
      self.tasks = self.tasks
        .filter(function(element) {
          return element.id !== self.$store.state.task.id;
        });
    });
  },
  created() {
    this.getTasksSettings();
    this.getTasks();
    this.setActiveTimer();
  },
  methods: {
    ...mapActions({
        setActiveTimer: 'timer/setRunningTimer',
    }),
    searchFocus() {
      this.$refs.searchInput.focus();
    },

    setTasksSettings() {
      const parsedSearch = JSON.stringify(this.search);
      localStorage.setItem('tasks-search', parsedSearch);
      const parsedStatus = JSON.stringify(this.status);
      localStorage.setItem('tasks-status', parsedStatus);
      const parsedDateRange = JSON.stringify(this.dates);
      localStorage.setItem('tasks-date-range', parsedDateRange);
      const parsedPriority = JSON.stringify(this.priority);
      localStorage.setItem('tasks-priority', parsedPriority);
      const parsedSort = JSON.stringify(this.sort);
      localStorage.setItem('tasks-sort', parsedSort);
    },

    getTasksSettings() {
      if (localStorage.getItem('tasks-search')) {
        try {
          this.search = JSON.parse(localStorage.getItem('tasks-search'));
        } catch(e) {
          localStorage.removeItem('tasks-search');
        }
      }
      if (localStorage.getItem('tasks-status')) {
        try {
          this.status = JSON.parse(localStorage.getItem('tasks-status'));
        } catch(e) {
          localStorage.removeItem('tasks-status');
        }
      }
      if (localStorage.getItem('tasks-date-range')) {
        try {
          this.dates = JSON.parse(localStorage.getItem('tasks-date-range'));
        } catch(e) {
          localStorage.removeItem('tasks-date-range');
        }
      }
      if (localStorage.getItem('tasks-priority')) {
        try {
          this.priority = JSON.parse(localStorage.getItem('tasks-priority'));
        } catch(e) {
          localStorage.removeItem('tasks-priority');
        }
      }
      if (localStorage.getItem('tasks-sort')) {
        try {
          this.sort = JSON.parse(localStorage.getItem('tasks-sort'));
        } catch(e) {
          localStorage.removeItem('tasks-sort');
        }
      }
    },
    toggleFilters() {
      this.showFilters = !this.showFilters;
    },
    countFilters() {
      let activeFilters = 0;
      if (this.search != '' && this.search != null) {
        activeFilters += 1;
      }
      if (this.status.length > 0) {
        activeFilters += 1;
      }
      if (this.dates && this.dates.length > 0) {
        activeFilters += 1;
      }
      if (this.priority) {
        activeFilters += 1;
      }
      this.filtersCount = activeFilters;
    },

    sortTasks() {
      switch (this.sort) {
        case 'Due date':
          this.tasks.sort((a, b) => {
            let dateA = new Date(a.due);
            let dateB = new Date(b.due);
            return dateA - dateB;
          });
          break;
        case 'Priority':
          this.tasks.sort((a, b) => {
            let priorityA = a.priority == 0 ? 4 : a.priority;
            let priorityB = b.priority == 0 ? 4 : b.priority;
            if (priorityA > priorityB) return 1;
            if (priorityB > priorityA) return -1;
            return 0;
          });
          break;
        case 'Status':
          this.tasks.sort((a, b) => a.status.localeCompare(b.status));
          break;
        case 'id':
          this.tasks.sort((a, b) => a.status.localeCompare(b.status));
          break;
        default:
          this.tasks.sort((a, b) => {
            let idA = a.id == 0 ? 4 : a.id;
            let idB = b.id == 0 ? 4 : b.id;
            if (idA > idB) return 1;
            if (idB > idA) return -1;
            return 0;
          });
          break;
      }
      this.setTasksSettings();
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

.search-input {
  margin-left: 5px;
}

.task-add-button-mobile {
  bottom: 10px;
  right: 0;
  position: sticky;
  margin-left: 10px;
  float: right;
  z-index: 10;
}

.filter-toggle-container {
  text-align: right;
  margin-top: 15px;
}

.search-input-max-width {
  max-width: 220px;
}

.tasks-container {
  margin-top: -10px;
}

.sort-input {
  max-width: 165px;
}
</style>
