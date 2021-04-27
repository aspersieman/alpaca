<template>
  <div class="reports">
    <v-container class="my-2">
      <v-layout
        row
        class="mb-3"
        wrap
      >
        <h1 class="subheading grey--text ml-4">
          Reports
        </h1>
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
        </v-layout>
      </v-layout>

      <task
        v-for="(task, index) in searchTasks"
        :key="index"
        :task="task"
      />
    </v-container>
  </div>
</template>

<script>
import TaskMixin from '../../../mixins/TaskMixin';

export default {
  components: {
  },
  mixins: [
    TaskMixin
  ],
  data() {
    return {
      tasks: [],
      tasksCopy: [],
      userId: '',
      btnColor: '',
      search: "",
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
  },
  created() { },
  methods: {
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

.task-add-button-mobile {
  bottom: 5px;
  right: 0;
  position: sticky;
  margin-left: 10px;
  float: right;
}
</style>
