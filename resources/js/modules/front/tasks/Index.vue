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
            v-if="$auth.check && $vuetify.breakpoint.smAndDown"
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
          class="mb-1 mt-1"
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
            prepend-icon="search"
            @change="setTasksSettings"
          />
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
            @change="setTasksSettings"
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
            :label="dateRangeLabel"
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
            @change="setTasksSettings"
          />
          <base-btn-primary
            class="action-button"
            @click="searchTasks()"
          >
            Go
          </base-btn-primary>
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
              <v-btn
                text
                color="primary"
                class="display-1"
                @click="$eventBus.taskDialogAdd(true)"
              >
                Add new Task
              </v-btn>
            </v-layout>
          </v-layout>
          <task
            v-for="(task, index) in tasks"
            :key="index"
            :task="task"
          />
          <v-btn
            v-if="$auth.check && $vuetify.breakpoint.smAndDown"
            fab
            small
            dark
            fixed
            bottom
            right
            color="primary"
            class="task-add-button-mobile hidden-lg-and-up mr-12"
            @click="$store.dispatch('task/openTaskDialog')"
          >
            <v-icon>add</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Task from './components/Task';
import NoteDrawer from '@/modules/front/notes/components/NoteDrawer';
import DateRangePicker from '@/components/core/DateRangePicker';
import TaskMixin from '@/mixins/TaskMixin';
import TimerMixin from '@/mixins/TimerMixin';
import SettingMixin from '@/mixins/SettingMixin';

export default {
  components: {
    Task,
    NoteDrawer,
    DateRangePicker
  },
  mixins: [
    TaskMixin,
    TimerMixin,
    SettingMixin
  ],
  data() {
    return {
      userId: '',
      search: '',
      status: [],
      dates: [],
      dateRangeLabel: 'Due date range',
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
      query: {
        name: '',
        status: '',
        dueFrom: '',
        dueTo: '',
        priority: '',
        userId: '',
        default: true
      },
    }
  },
  computed: {
    ...mapGetters('task', [
      'tasks',
      'statuses'
    ]),
  },
  watch: {
    search: function() {
      this.countFilters();
    },
    status: function() {
      this.countFilters();
    },
    dates: function() {
      this.setTasksSettings();
      this.countFilters();
    },
    priority: function() {
      this.countFilters();
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.getTasksSettings();
      this.searchTasks();
    });
    this.$store.subscribe((mutation) => {
      if (
        [
          'task/ADDED',
          'task/UPDATED',
          'task/DELETED',
          'timer/ADDED',
          'timer/UPDATED',
          'timer/DELETED',
          'timer/TIMER_STOPPED'
        ].includes(mutation.type)
      ) {
        this.getTasksSettings();
        this.searchTasks();
      }
    });
  },
  methods: {
    searchFocus() {
      this.$refs.searchInput.focus();
    },

    setTasksSettings() {
      this.setSetting('tasks-search', this.search);
      this.setSetting('tasks-status', this.status);
      this.setSetting('tasks-date-range', this.dates);
      this.setSetting('tasks-priority', this.priority);
      this.setSetting('tasks-sort', this.sort);
    },

    getTasksSettings() {
      this.search = this.getSetting('tasks-search') ?? this.search;
      this.status = this.getSetting('tasks-status') ?? this.status;
      this.dates = this.getSetting('tasks-date-range') ?? this.dates;
      this.priority = this.getSetting('tasks-priority') ?? this.priority;
      this.sort = this.getSetting('tasks-sort') ?? this.priority;
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
      let tasks = this.$store.getters['task/tasks'];
      switch (this.sort) {
        case 'Due date':
          tasks.sort((a, b) => {
            let dateA = new Date(a.due);
            let dateB = new Date(b.due);
            return dateA - dateB;
          });
          break;
        case 'Priority':
          tasks.sort((a, b) => {
            let priorityA = a.priority == 0 ? 4 : a.priority;
            let priorityB = b.priority == 0 ? 4 : b.priority;
            if (priorityA > priorityB) return 1;
            if (priorityB > priorityA) return -1;
            return 0;
          });
          break;
        case 'Status':
          tasks.sort((a, b) => a.status.localeCompare(b.status));
          break;
        case 'id':
          tasks.sort((a, b) => a.id.localeCompare(b.id));
          break;
        default:
          tasks.sort((a, b) => {
            let idA = a.id == 0 ? 4 : a.id;
            let idB = b.id == 0 ? 4 : b.id;
            if (idA > idB) return 1;
            if (idB > idA) return -1;
            return 0;
          });
          break;
      }
      this.$store.commit('task/TASKS', tasks);
      this.setSetting('tasks-sort', this.sort);
    },

    searchTasks() {
      this.prepareQuery();
      this.$store.dispatch('task/getTasks', this.query)
        .then(() => this.sortTasks());
    },

    prepareQuery() {
      this.resetQuery();
      this.setQueryValue('name', this.search || null);
      const status = this.status.join(',');
      this.setQueryValue('status', status || null);
      if (this.dates) {
        this.setQueryValue('dueFrom', this.dates[0] || null);
        this.setQueryValue('dueTo', this.dates[1] || null);
      } else {
        this.setQueryValue('dueFrom', null);
        this.setQueryValue('dueTo', null);
      }
      this.setQueryValue('priority', this.priority?.id || null);
      this.setQueryValue('userId', this.$auth?.user?.id);
      this.setQueryValue('default', null);
      if (Object.keys(this.query).length === 0) {
        this.setQueryValue('default', true);
      }
    },

    setQueryValue(key, value) {
      if (value === null) {
        delete(this.query[key]);
      } else {
        this.query[key] = value;
      }
    },

    resetQuery(){
      this.query = {
        name: null,
        status: null,
        dueFrom: null,
        dueTo: null,
        priority: null,
        userId: null,
        default: true
      }
    },
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
