<template>
  <div class="tasks">
    <note-drawer />
    <v-container>
      <v-row>
        <v-col>
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
          <timer-running />
          <add-fab />
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
                <v-icon>
                  search
                </v-icon>
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
      <v-row>
        <v-col>
          <v-fade-transition leave-absolute>
            <v-card
              v-if="$vuetify.breakpoint.mdAndUp || (showFilters && $vuetify.breakpoint.smAndDown)"
            >
              <v-card-title
                v-if="$vuetify.breakpoint.mdAndUp"
                class="grey--text"
              >
                Filters
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row
                    class="filters-row"
                  >
                    <v-col
                      class="filters-column"
                      cols="12"
                      sm="12"
                      md="4"
                      lg="2"
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
                        label="Title"
                        type="text"
                        @change="setTasksSettings()"
                      />
                    </v-col>
                    <v-col
                      class="filters-column"
                      cols="12"
                      sm="12"
                      md="4"
                      lg="3"
                    >
                      <v-combobox
                        v-model="status"
                        outlined
                        :clearable="clearable"
                        dense
                        chips
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        multiple
                        label="Status"
                        class="status-list-filter"
                        @change="setTasksSettings()"
                        @input="searchTasks()"
                      >
                        <template
                          #selection="data"
                        >
                          <v-chip
                            :key="JSON.stringify(data.item.id)"
                            :small="$vuetify.breakpoint.mdAndUp"
                            :x-small="$vuetify.breakpoint.smAndDown"
                            :class="`status ${data.item.id} status-chip`"
                            :input-value="data.selected"
                            :disabled="data.disabled"
                            v-bind="data.attrs"
                            @click:close="data.parent.selectItem(data.item)"
                          >
                            {{ data.item.name }}
                          </v-chip>
                        </template>
                      </v-combobox>
                    </v-col>
                    <v-col
                      class="filters-column"
                      cols="12"
                      sm="12"
                      md="4"
                      lg="3"
                    >
                      <date-range-picker
                        v-model="dates"
                        :label="dateRangeLabel"
                        :value="dates"
                        :clearable="clearable"
                      />
                    </v-col>
                    <v-col
                      class="filters-column"
                      cols="12"
                      sm="12"
                      md="4"
                      lg="2"
                    >
                      <v-combobox
                        v-model="priority"
                        :items="priorities"
                        :clearable="clearable"
                        item-text="name"
                        item-value="id"
                        label="Priority"
                        outlined
                        dense
                        class="priority-list-filter"
                        @change="setTasksSettings()"
                        @input="searchTasks()"
                      />
                    </v-col>
                    <v-col
                      v-if="$vuetify.breakpoint.mdOnly"
                      md="4"
                    />
                    <v-col
                      class="filters-column"
                      cols="12"
                      sm="12"
                      md="4"
                      lg="2"
                    >
                      <v-spacer />
                      <v-combobox
                        v-model="sort"
                        outlined
                        :clearable="clearable"
                        dense
                        :items="sortList"
                        label="Sort"
                        prepend-icon="filter_list"
                        class="sort-filter"
                        @change="sortTasks()"
                      />
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
            </v-card>
          </v-fade-transition>
        </v-col>
      </v-row>
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
import AddFab from '@/components/core/AddFab';
import TimerRunning from '@/modules/front/tasks/components/timer/TimerRunning';

export default {
  components: {
    Task,
    NoteDrawer,
    DateRangePicker,
    AddFab,
    TimerRunning,
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
      dateRangeLabel: 'Due dates',
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
      query: {
        name: '',
        status: '',
        dueFrom: '',
        dueTo: '',
        priority: '',
        userId: '',
        default: true
      },
      debounce: null,
    }
  },
  computed: {
    ...mapGetters('task', [
      'tasks',
      'statuses',
      'priorities',
    ]),
  },
  watch: {
    search: function() {
      this.countFilters();
      this.searchTasks(true);
    },
    status: function() {
      this.countFilters();
    },
    dates: function() {
      this.setTasksSettings();
      this.countFilters();
      this.searchTasks(true);
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
          'timer/TIMER_STOPPED',
          'note/ADDED',
          'note/UPDATED',
          'note/DELETED',
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

    searchTasks(debounce) {
      if (debounce) {
        let bouncer;
        clearTimeout(bouncer);
        bouncer = setTimeout(() => {
          this.prepareQuery();
          this.$store.dispatch('task/getTasks', this.query)
            .then(() => this.sortTasks())
            .then(() => this.$overlay.show(false))
        }, 700);
      } else {
        this.prepareQuery();
        this.$store.dispatch('task/getTasks', this.query)
          .then(() => this.sortTasks())
          .then(() => this.$overlay.show(false))
      }
    },

    prepareQuery() {
      this.resetQuery();
      this.setQueryValue('name', this.search || null);
      let statuses = [];
      this.status.forEach((s) => statuses.push(s.id));
      const status = statuses.join(',');
      this.setQueryValue('status', status || null);
      if (this.dates) {
        if ((new Date(this.dates[1])) < (new Date(this.dates[0]))) {
          this.dates = [this.dates[1], this.dates[0]];
        }
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
.status-chip {
  min-width: 100px;
  max-width: 130px;
  justify-content: center;
  margin: 5px;
  margin-top: 3px !important;
}
.theme--light.v-chip.status {
  text-transform: uppercase;
}
.theme--light.v-chip.status.complete {
  color: white;
  background-color: $task-status-complete;
}
.theme--light.v-chip.status.inprogress {
  color: white;
  background-color: $task-status-inprogress;
}
.theme--light.v-chip.status.todo {
  color: white;
  background-color: $task-status-todo;
}

.tasks-container {
  @media (max-width: 960px) {
    padding: 0px !important;
    padding-right: 5px !important;
  }
}
.filter-toggle-container { text-align: right;
  margin-top: 55px;
}
.filter-toggle-container i {
  color: #ff4f4f;
}
.filters-row {
  margin-bottom: -35px;
}
.filters-column {
  padding: 0px;
  @media (min-width: 960px) {
    padding-right: 5px !important;
  }
}
.status-list-filter {
  min-width: 170px;
}
.priority-list-filter {
  max-width: 269px;
}
.sort-filter {
  max-width: 120px;
}
</style>
