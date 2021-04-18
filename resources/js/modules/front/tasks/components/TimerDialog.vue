<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    scrollable
    :fullscreen="$vuetify.breakpoint.xs"
    :loading="loading"
  >
    <v-card>
      <v-card-title class="grey--text">
        <span v-if="mode == 'ADD'">
          Add a New Timer
        </span>
        <span v-if="mode == 'UPDATE'">
          Update Timer
        </span>
      </v-card-title>
      <v-card-subtitle
        v-if="subtitle != '' && subtitle != null"
      >
        <v-icon small>
          task_alt
        </v-icon>
        {{ subtitle }}
      </v-card-subtitle>
      <v-divider />
      <v-form
        v-model="valid"
        lazy-validation
      >
        <v-card-text>
          <v-menu
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
          >
            <template
              #activator="{ on, attrs }"
            >
              <v-text-field
                v-model="name"
                :rules="nameRules"
                label="Description"
                v-bind="attrs"
                autofocus
                v-on="on"
              />
            </template>
          </v-menu>
          <v-combobox
            v-model="tags"
            :items="tagItems"
            chips
            clearable
            label="Categories"
            multiple
            prepend-icon="category"
            solo
          >
            <template
              #selection="{ attrs, item, select, selected }"
            >
              <v-chip
                v-bind="attrs"
                :input-value="selected"
                close
                @click="select"
                @click:close="removeCategory(item)"
              >
                <strong>{{ item }}</strong>&nbsp;
              </v-chip>
            </template>
          </v-combobox>
          <v-container>
            <v-row v-if="mode == 'UPDATE'">
              <v-col>
                <v-menu
                  v-model="dateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template #activator="{ on, attrs }">
                    <v-text-field
                      v-model="startDate"
                      :rules="dateRules"
                      :value="formattedDate"
                      label="Date"
                      prepend-icon="event"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    />
                  </template>
                  <v-date-picker
                    v-model="startDate"
                    @input="menu = false"
                  />
                </v-menu>
              </v-col>
              <v-col>
                <v-menu
                  ref="menu"
                  v-model="startTimeMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="startTime"
                  transition="scale-transition"
                  offset-y
                >
                  <template #activator="{ on, attrs }">
                    <v-text-field
                      v-model="startTime"
                      label="Start time"
                      prepend-icon="access_time"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    />
                  </template>
                  <v-time-picker
                    v-if="startTimeMenu"
                    v-model="startTime"
                    width="290"
                    @click:minute="$refs.menu.save(startTime)"
                  />
                </v-menu>
              </v-col>
              <v-col>
                <v-text-field
                  v-model="duration"
                  label="Duration"
                  type="number"
                  hint="minutes"
                  persistent-hint
                />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-form>
      <v-divider />
      <v-card-actions>
        <v-btn
          v-if="mode == 'ADD'"
          text
          class="success mx-0 mt-3"
          :disabled="!valid || loading"
          @click="save()"
        >
          Start
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success mx-0 mt-3"
          :disabled="!valid || loading"
          @click="update()"
        >
          Update timer
        </v-btn>
        <v-spacer />
        <v-btn
          text
          class="error mx-0 mt-3"
          @click="close();"
        >
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import TimerMixin from '@/mixins/TimerMixin';
import TaskMixin from '@/mixins/TaskMixin';

export default {
  mixins: [
    TimerMixin,
    TaskMixin
  ],
  data() {
    return {
      mode: 'ADD',
      dialog: false,
      valid: false,
      name: '',
      task_id: '',
      startDate: '',
      startTime: '',
      endTime: '',
      duration: '',
      subtitle: '',
      loading: false,
      dateMenu: false,
      startTimeMenu: false,
      nameRules: [
        (v) => (v.length >= 2) || 'Cannot be shorter than 2 characters',
        (v) => !!v || 'Name is required',
      ],
      tags: [],
      tagItems: [],
    }
  },
  computed: {
    formattedDate() {
      return this.startDate ? window.moment(this.startDate).format('YYYY-MM-DD') : ''
    }
  },
  created() {
    const self = this;
    self.$eventBus.$on('timer-dialog-add', (value) => {
      const task = self.getTaskActive();
      self.formReset();
      self.dialog = value;
      self.mode = 'ADD';
      self.subtitle = task.name || '';
      self.name = task.name || '';
      self.task_id = task.id;
    });
    self.$eventBus.$on('timer-dialog-update', (value) => {
      self.formReset();
      self.timer = self.$store.getters['timer/timer'];
      self.name = self.timer.name;
      self.task_id = task.id;
      self.startDate = self.timer.started_at ? window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('YYYY-MM-DD') : '';
      self.startTime = self.timer.started_at ? window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      self.endTime = self.timer.stopped_at ? window.moment(self.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      self.duration = window.moment(self.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').diff(window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS'), 'minutes')
      self.subtitle = self.getTaskActive().name || '';
      self.dialog = value;
      self.mode = 'UPDATE';
      const tags = self.$store.state.timer.tagged.map(function (item) {
        return item.tag_name;
      });
      self.tags = tags;
    });
  },
  methods: {
    save() {
      if (this.valid) {
        const timer = {
          name: this.name,
          task_id: this.task_id,
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
          tags: this.tags
        };
        this.saveTimer(timer)
          .then(this.close());
      }
    },
    update() {
      if (this.valid) {
        const timer = {
          id: this.timer.id,
          name: this.name,
          task_id: this.task_id,
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
          tags: this.tags
        };
        this.updateTimer(timer)
          .then(this.close());
      }
    },
    close() {
      this.dialog = false;
    },
    formReset() {
      this.name = '';
      this.startDate = '';
      this.startTime = '';
      this.endTime = '';
      this.duration = '';
      this.timer = null;
      this.tags = [];
      this.subtitle = '';
    },
    removeCategory(item) {
      this.tags.splice(this.tags.indexOf(item), 1)
      this.tags = [...this.tags]
    },
  }
}
</script>
<style scoped>
</style>

