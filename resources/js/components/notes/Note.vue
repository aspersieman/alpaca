<template>
  <v-card
    outlined
    class="note-card"
  >
    <div 
      v-show="!showEditForm"
    >
      <v-card-title
        class="text-subtitle-2"
      >
        {{ note.name }}
      </v-card-title>
      <v-card-text>
        <div class="text-sm-caption note-content">{{ note.content }}</div>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          class="text-sm-caption"
          icon
          x-small
          @click="formShow()"
        >
          <v-icon>
            edit
          </v-icon>
        </v-btn>
        <v-btn
          class="text-sm-caption"
          icon
          x-small
          @click="deleteNote(note)"
        >
          <v-icon>
            delete
          </v-icon>
        </v-btn>
      </v-card-actions>
    </div>
    <div
      v-show="showEditForm"
    >
      <v-card-text>
        <v-container>
          <v-row>
            <v-col>
              <v-text-field
                v-model="noteName"
                dense
                :loading="loading"
                label="Title"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-textarea
                v-model="noteContent"
                dense
                :loading="loading"
                label="Content"
                rows="3"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-btn
                class="success mx-0 mt-3"
                :loading="loading"
                @click="updateNote()"
              >
                Done
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </div>
  </v-card>
</template>

<script type="text/javascript">
export default {
  props: {
    note: {
      required: true,
      type: Object,
      default: function() {
        return {
          id: null,
          name: null,
          content: null,
          task_id: null
        }
      }
    }
  },
  data() {
    return {
      noteName: '',
      noteContent: '',
      showEditForm: false,
      loading: false
    };
  },
  methods: {
    deleteNote(note) {
      const self = this;

      self.$store.commit('showDialog',{
        type: "confirm",
        icon: 'warning',
        title: "Confirm deletion",
        message: "Are you sure you want to delete this note?",
        okCb: ()=>{
          const currentTask = self.task;
          window.axios.delete('/api/notes/'+parseInt(note.id))
            .then(function(response) {
              self.$store.commit('setSelectedTask', currentTask);
              self.$store.commit('showSnackbar',{
                message: response.data.message,
                color: 'success',
                duration: 3000
              });
              self.noteName =  '';
              self.noteContent = '';
              self.$eventBus.$emit('note-deleted', note);
            })
            .catch(error => {
              self.$store.commit('hideLoader');
              if (error.response) {
                self.$store.commit('showSnackbar',{
                  message: error.response.data.data.message,
                  color: 'error',
                  duration: 10000
                });
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log('Error', error.message);
              }
            });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },

    formShow() {
      this.formReset();
      this.noteName = this.note.name;
      this.noteContent = this.note.content;
      this.showEditForm = true;
    },

    formReset() {
      this.noteName = '';
      this.noteContent = '';
    },

    updateNote() {
      const note = {
        id: this.note.id,
        name: this.noteName,
        content: this.noteContent
      };
      if(note.name.length > 0) {
        this.loading = true;
        
        window.axios.put(`/api/notes/`+parseInt(note.id), note)
          .then(response => {
          this.loading = false;
          this.formReset();
          this.$eventBus.$emit('note-updated', note);
          this.$store.commit('showSnackbar',{
            message: "Note updated",
            color: 'success',
            duration: 1000
          });
          this.showEditForm = false;
          return response;
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
  },
};
</script>

<style scoped>
.italic {
  font-style: italic;
}
.note-card {
  margin-bottom:3px;
}
.note-content {
  white-space: pre-wrap; 
  width: 100%;
}
</style>
