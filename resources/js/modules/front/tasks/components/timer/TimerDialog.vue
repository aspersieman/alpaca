<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    scrollable
    :fullscreen="$vuetify.breakpoint.xs"
    class="form-dialog"
  >
    <v-card>
      <v-card-title class="grey--text">
        Timer
        <v-spacer />
        <span
          class="text-right"
        >
          <v-btn
            class="text-sm-caption text-right"
            icon
            text
            x-small
            light
            color="error"
            @click="close()"
          >
            <v-icon>
              close
            </v-icon>
          </v-btn>
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
                  transition="scale-transition"
                  offset-y
                  @update:name="val => startTime = val"
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
                    format="24hr"
                    @click:minute="$refs.menu.save(startTime)"
                  />
                </v-menu>
              </v-col>
              <v-col>
                <v-menu
                  ref="menu"
                  v-model="endTimeMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  @update:name="val => startTime = val"
                >
                  <template #activator="{ on, attrs }">
                    <v-text-field
                      v-model="endTime"
                      label="End time"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    />
                  </template>
                  <v-time-picker
                    v-if="endTimeMenu"
                    v-model="endTime"
                    width="290"
                    format="24hr"
                    @click:minute="$refs.menu.save(endTime)"
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
                  disabled
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
          :disabled="!valid"
          @click="save()"
        >
          Start
        </v-btn>
        <v-btn
          v-if="mode == 'UPDATE'"
          text
          class="success mx-0 mt-3"
          :disabled="!valid"
          @click="update()"
        >
          Save
        </v-btn>
        <success-checkmark
          v-if="updated"
          class="success-checkmark"
        />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import SuccessCheckmark from '@/components/core/SuccessCheckmark';
import TimerMixin from '@/mixins/TimerMixin';
import TaskMixin from '@/mixins/TaskMixin';

export default {
  components: {
    SuccessCheckmark
  },
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
      subtitle: '',
      loading: false,
      dateMenu: false,
      startTimeMenu: false,
      endTimeMenu: false,
      updated: false,
      nameRules: [
        (v) => (v.length >= 2) || 'Cannot be shorter than 2 characters',
        (v) => !!v || 'Name is required',
      ],
      dateRules: [
        (v) => (v.length >= 4) || 'Invalid date format',
        (v) => !!v || 'Date is required',
      ],
      tags: [],
      tagItems: [],
    }
  },
  computed: {
    formattedDate() {
      return this.startDate ? window.moment(this.startDate).format('YYYY-MM-DD') : ''
    },
    ...mapGetters('timer', [
      'timer',
      'timerDialogUpdate',
      'timerDialogAdd'
    ]),
    ...mapGetters('task', [
      'task'
    ]),
    duration() {
      return window.moment(this.startDate + ' ' + this.endTime, 'YYYY-MM-DDTHH:mm:ss.SSS').diff(window.moment(this.startDate + ' ' + this.startTime, 'YYYY-MM-DDTHH:mm:ss.SSS'), 'minutes')
    }
  },
  created() {
    this.$store.subscribe((mutation) => {
      if (mutation.type === 'timer/TIMER_DIALOG_ADD') {
        this.formReset();
        this.dialog = this.timerDialogAdd;
        if (this.timerDialogAdd === true) {
          this.mode = 'ADD';
          this.subtitle = this.task.name || '';
          this.name = this.task.name || '';
          this.task_id = this.task.id;
        }
      }
      if (mutation.type === 'timer/TIMER_DIALOG_UPDATE') {
        this.formReset();
        this.dialog = this.timerDialogUpdate;
        if (this.timerDialogUpdate === true) {
          this.name = this.timer.name;
          this.task_id = this.timer.task_id;
          this.startDate = this.timer.started_at ? window.moment(this.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('YYYY-MM-DD') : '';
          this.startTime = this.timer.started_at ? window.moment(this.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm') : '';
          this.endTime = this.timer.stopped_at ? window.moment(this.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm') : '';
          this.subtitle = this.task.name || '';
          this.mode = 'UPDATE';
          const tags = this.timer.tagged.map(function (item) {
            return item.tag_name;
          });
          this.tags = tags;
        }
      }
      if (mutation.type === 'timer/UPDATED') {
        this.updated = true;
        setTimeout(function () {
          this.updated = false;
        }, 3000);
      }
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
        this.$store.dispatch('timer/saveTimer', timer)
          .then(this.close());
      }
    },
    update() {
      if (this.valid) {
        const started_at = window.moment(this.startDate + ' ' + this.startTime, 'YYYY-MM-DD HH:mm:ss');
        const timer = {
          id: this.timer.id,
          name: this.name,
          task_id: this.task_id,
          started_at: started_at.format('YYYY-MM-DD HH:mm:ss'),
          stopped_at: started_at.add(this.duration, 'minutes').format('YYYY-MM-DD HH:mm:ss'),
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
          tags: this.tags
        };
        this.$store.dispatch('timer/updateTimer', timer);
      }
    },
    close() {
      this.dialog = false;
      this.$store.commit('timer/TIMER_DIALOG_UPDATE', false);
      this.$store.commit('timer/TIMER_DIALOG_ADD', false);
    },
    formReset() {
      this.name = '';
      this.startDate = '';
      this.startTime = '';
      this.endTime = '';
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
