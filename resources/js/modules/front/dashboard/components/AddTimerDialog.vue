<template>
  <v-dialog max-width="600px" v-model="dialog">
    <v-card>
      <v-card-title class="grey--text">
        <h2>Add a New Timer</h2>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px">
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
          <v-spacer></v-spacer>
          <v-btn text class="success mx-0 mt-3" @click="submit" :loading="loading">Add timer</v-btn>
        </v-form>
      </v-card-text>

    </v-card>
  </v-dialog>
</template>

<script>

export default {

  data() {
    return {
      dialog: false,
      name: '',
      userId: '',
      loading: false,
      menu: false,
    }
  },
  methods: {
    
    submit() {
      const task = this.$store.state.task;
      if(this.name.length > 0) {
        const timer = {
          name: this.name,
          task_id: this.task_id
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
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
          })
        this.newTimerName = ''
        this.loading = true;
      }
    },

    formReset() {
      this.name = '';
    },
  },
  mounted() {
    this.$eventBus.$on('TIMER_DIALOG', (value) => {
      this.dialog = value
    })
  },
  created() {
    const user = {}; //firebase.auth().currentUser;
    if (user){
      var userId = user.uid;
    } else {
      //
    }
    this.userId = userId;
  }
}
</script>
