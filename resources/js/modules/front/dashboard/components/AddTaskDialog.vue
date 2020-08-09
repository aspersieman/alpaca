<template>
  <v-dialog max-width="600px" v-model="dialog">
    <v-card>
      <v-card-title>
        <h2>Add a New Task</h2>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-text-field label="Title" v-model="name" prepend-icon="edit"></v-text-field>
          <!-- Date Picker -->
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                :rules="dateRules"
                :value="formattedDate"
                v-model="due"
                label="Due date"
                prepend-icon="event"
                readonly
                v-bind="attrs"
                v-on="on">
              </v-text-field>
            </template>
            <v-date-picker v-model="due" @input="menu2 = false"></v-date-picker>
          </v-menu>

          <v-spacer></v-spacer>

          <v-btn text class="success mx-0 mt-3" @click="submit" :loading="loading">Add task</v-btn>
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
      content: '',
      due: moment().format('YYYY-MM-DD'),
      userId: '',
      dateRules: [
        (v) => (v.length >= 4) || 'Invalid date format',
        (v) => !!v || 'Date is required',
      ],
      loading: false,
      menu: false,
      tags: []
    }
  },
  methods: {
    
    submit() {
      if(this.name.length > 0 && this.due.length >= 4) {
        this.loading = true;
        
        const task = {
          name: this.name,
          content: this.content,
          due: moment(this.due).format('YYYY-MM-DD'),
          status: 'ongoing',
          priority: 100,
          tags: this.tags
        };

        window.axios.post('/api/tasks', task)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.formReset();
          this.name = ''
          this.tags = [];
          this.$eventBus.$emit('task-added');
          this.$store.commit('showSnackbar',{
            message: "Task added",
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

    formReset() {
      this.name = '';
      this.content = '';
      this.due = moment().format('YYYY-MM-DD');
    },
  },
  computed: {
    formattedDate() {
      return this.due ? moment(this.due).format('YYYY-MM-DD') : ''
    }
  },

  mounted() {
    this.$eventBus.$on('dialog', (value) => {
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
