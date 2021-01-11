import EventNames from '../common/EventNames';

export default {
  data(){
    return {
    }
  },
  methods: {
    getNotes() {
      return this.$store.state.notes;
    },
    addNote(note) {
      if(note.name != '') {
        this.loading = true;
        
        const newNote = {
          name: note.name,
          content: note.content,
          task_id: note.task_id
        };

        window.axios.post('/api/notes', newNote)
          .then(response => {
          this.loading = false;
          this.dialog = false;
          this.$eventBus.$emit(EventNames.NOTE_ADDED);
          let notes = this.getNotes();
          newNote.id = response.data.data.id;
          notes.push(newNote);
          this.$store.commit('setNotes', notes);
          this.$store.commit('showSnackbar',{
            message: "Note added",
            color: 'success',
            duration: 1000
          });
          return response;
        })
          .catch(error => {
          console.error(error);
          this.$store.commit('showSnackbar',{
            message: error.response.data.message,
            color: 'error',
            duration: 3000
          });
          this.loading = false;
        });
      }
    },
    updateNote() {
      const note = {
        id: this.note.id,
        name: this.noteName,
        content: this.noteContent
      };
      if(note.name.length > 0) {
        this.loading = true;
        
        window.axios.put(`/api/notes/`+ parseInt(note.id), note)
          .then(response => {
          this.loading = false;
          this.formReset();
          this.$eventBus.$emit(EventNames.NOTE_UPDATED, note);
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
              self.$eventBus.$emit(EventNames.NOTE_DELETED, note);
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
  }
}
