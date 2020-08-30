<template>
  <v-dialog max-width="600px" :fullscreen="$vuetify.breakpoint.mdAndDown" v-model="dialog">
    <v-card>
      <v-card-title class="grey--text">
        <h2 v-if="mode == 'ADD'">Add a New Timer</h2>
        <h2 v-if="mode == 'UPDATE'">Update Timer</h2>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-container>
            <v-row>
              <v-col>
                <v-menu
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="name"
                      label="Description"
                      v-bind="attrs"
                      autofocus
                      v-on="on">
                    </v-text-field>
                  </template>
                </v-menu>
              </v-col>
            </v-row>
            <v-row v-if="mode == 'UPDATE'">
              <v-col>
                <v-menu
                  v-model="dateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :rules="dateRules"
                      :value="formattedDate"
                      v-model="startDate"
                      label="Date"
                      prepend-icon="event"
                      readonly
                      v-bind="attrs"
                      v-on="on">
                    </v-text-field>
                  </template>
                  <v-date-picker v-model="startDate" @input="menu = false"></v-date-picker>
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
                  offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="startTime"
                      label="Start time"
                      prepend-icon="access_time"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="startTimeMenu"
                    v-model="startTime"
                    full-width
                    @click:minute="$refs.menu.save(startTime)"
                    ></v-time-picker>
                </v-menu>
              </v-col>
              <v-col>
                <v-text-field
                  v-model="duration"
                  label="Duration"
                  type="number"
                  hint="minutes"
                  persistent-hint>
                </v-text-field>
              </v-col>
            </v-row>
            <v-spacer></v-spacer>
            <v-layout row wrap width="100%">
              <v-btn text class="success mx-0 mt-3" v-if="mode == 'ADD'" @click="addTimer" :loading="loading">Add timer</v-btn>
              <v-btn text class="success mx-0 mt-3" v-if="mode == 'UPDATE'" @click="updateTimer" :loading="loading">Update timer</v-btn>
              <v-spacer></v-spacer>
              <v-btn text class="error mx-0 mt-3" @click="close();" :loading="loading">Cancel</v-btn>
            </v-layout>
          </v-container>
        </v-form>
      </v-card-text>

    </v-card>
  </v-dialog>
</template>

<script>

export default {

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
    }
  },
  methods: {
    addTimer() {
      const task = this.$store.state.task;
      const self = this;
      if(this.name.length > 0) {
        const timer = {
          name: this.name,
          task_id: this.task_id,
          tz: Intl.DateTimeFormat().resolvedOptions().timeZone
        };
        window.axios.post(`/api/tasks/${task.id}/timers`, timer)
          .then(response => {
            self.$store.commit('setActiveTimer', response.data);
            this.$eventBus.$emit('TIMER_ADDED');
            this.loading = false;
            this.dialog = false;
            this.formReset();
            this.$store.commit('showSnackbar',{
              message: "Timer added",
              color: 'success',
              duration: 1000
            });
          })
          .catch(error => {
          console.log(error);
          self.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
          });
        this.loading = true;
      }
    },
    updateTimer() {
      if(this.name.length > 0 && this.startDate.length >= 4) {
        this.loading = true;
        
        const started_at = moment(this.startDate + " " + this.startTime, "YYYY-MM-DD HH:mm:ss");
        const timer = {
          name: this.name,
          started_at: started_at.format("YYYY-MM-DD HH:mm:ss"),
          stopped_at: started_at.add(this.duration, 'minutes').format("YYYY-MM-DD HH:mm:ss")
        };
        console.log(this.duration);
        console.log(timer);

        window.axios.put(`/api/tasks/${this.$store.state.task.id}/timer/${this.$store.state.timer.id}`, timer)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.$eventBus.$emit('TIMER_UPDATED');
          this.$store.commit('showSnackbar',{
            message: "Timer updated",
            color: 'success',
            duration: 1000
          });
        })
          .catch(error => {
          console.log(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });
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
    },
  },
  mounted() { },
  created() {
    const self = this;
    this.$eventBus.$on('TIMER_ADD_DIALOG', (value) => {
      this.dialog = value;
      this.mode = 'ADD';
      this.formReset();
    });
    this.$eventBus.$on('TIMER_UPDATE_DIALOG', (value) => {
      this.timer = this.$store.state.timer;
      this.name = this.timer.name;
      this.startDate = this.timer.started_at ? moment(this.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('YYYY-MM-DD') : '';
      this.startTime = this.timer.started_at ? moment(this.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      this.endTime = this.timer.stopped_at ? moment(this.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').format('HH:mm:ss') : '';
      this.duration = moment(this.timer.stopped_at, 'YYYY-MM-DDTHH:mm:ss.SSS').diff(moment(this.timer.started_at, 'YYYY-MM-DDTHH:mm:ss.SSS'), 'minutes')
      this.dialog = value;
      this.mode = 'UPDATE';
    });
  },
  computed: {
    formattedDate() {
      return this.startDate ? moment(this.startDate).format('YYYY-MM-DD') : ''
    }
  }
}
</script>
<style scoped>
.mw-150 {
  max-width: 150px;
}
</style>

