<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    :fullscreen="$vuetify.breakpoint.xs"
  >
    <v-card>
      <v-card-title class="grey--text">
        <h2 v-if="mode == 'ADD'">
          Add a New Timer
        </h2>
        <h2 v-if="mode == 'UPDATE'">
          Update Timer
        </h2>
      </v-card-title>
      <v-card-text>
        <v-form
          ref="form"
          class="px-3"
        >
          <v-container>
            <v-row>
              <v-col>
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
                      label="Description"
                      v-bind="attrs"
                      autofocus
                      v-on="on"
                    />
                  </template>
                </v-menu>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
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
              </v-col>
            </v-row>
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
            <v-spacer />
            <v-layout
              row
              wrap 
              width="100%"
            >
              <v-btn
                v-if="mode == 'ADD'"
                text
                class="success mx-0 mt-3"
                :loading="loading"
                @click="addTimer"
              >
                Add timer
              </v-btn>
              <v-btn
                v-if="mode == 'UPDATE'"
                text
                class="success mx-0 mt-3"
                :loading="loading"
                @click="updateTimer"
              >
                Update timer
              </v-btn>
              <v-spacer />
              <v-btn
                text
                class="error mx-0 mt-3"
                :loading="loading"
                @click="close();"
              >
                Cancel
              </v-btn>
            </v-layout>
          </v-container>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>

import TimerMixin from '../../../../mixins/TimerMixin';

export default {
  mixins: [ TimerMixin ],
  data() {
    return {
      mode: 'ADD',
      dialog: false,
      name: '',
      startDate: '',
      startTime: '',
      endTime: '',
      duration: '',
      userId: '',
      loading: false,
      dateMenu: false,
      startTimeMenu: false,
      dateRules: [
        (v) => (v.length >= 4) || 'Invalid date format',
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
    this.$eventBus.$on('timer-add-dialog', (value) => {
      self.dialog = value;
      self.mode = 'ADD';
      self.formReset();
    });
    self.$eventBus.$on('timer-update-dialog', (value) => {
      self.timer = self.$store.state.timer;
      console.log(self.timer);
      self.name = self.timer.name;
      self.startDate = self.timer.started_at ? window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('YYYY-MM-DD') : '';
      self.startTime = self.timer.started_at ? window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      self.endTime = self.timer.stopped_at ? window.moment(self.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      self.duration = window.moment(self.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').diff(window.moment(self.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS'), 'minutes')
      self.dialog = value;
      self.mode = 'UPDATE';
      const tags = self.$store.state.timer.tagged.map(function (item) {
        return item.tag_name;
      });
      self.tags = tags;
    });
  },
  methods: {
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

