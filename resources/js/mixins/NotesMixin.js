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
          name: note.name.trim(),
          content: note.content.trim(),
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
          this.$flash.success({
            title: "Note added",
            text: "Note added successfully",
          });
          this.$emit(EventNames.NOTE_ADDED, false);
          return response;
        })
          .catch(error => {
          console.error(error);
          this.$flash.error({
            title: "Note added",
            text: "Error adding note",
          });
          this.loading = false;
        });
      }
    },
    updateNote(note) {
      if(note && note.name.length > 0) {
        this.loading = true;
        const updateNote = {
          id: note.id,
          name: note.name.trim(),
          content: note.content.trim()
        };
        
        window.axios.put(`/api/notes/`+ parseInt(note.id), updateNote)
          .then(response => {
          this.loading = false;
          this.formReset();
          this.$eventBus.$emit(EventNames.NOTE_UPDATED, note);
          this.$flash.success({
            title: "Note update",
            text: "Note updated successfully",
          });
          this.showEditForm = false;
          this.$emit(EventNames.NOTE_UPDATED, false);
          return response;
        })
          .catch(error => {
          console.log(error);
          this.$flash.success({
            title: "Note update",
            text: "Error updating note",
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
            .then(function() {
              self.$store.commit('setSelectedTask', currentTask);
              this.$flash.success({
                title: "Note delete",
                text: "Note deleted successfully",
              });
              self.noteName =  '';
              self.noteContent = '';
              self.$eventBus.$emit(EventNames.NOTE_DELETED, note);
            })
            .catch(error => {
              if (error.response) {
                console.error(error);
                this.$flash.success({
                  title: "Note delete",
                  text: "Error deleting note",
                });
              }
            });
        },
        cancelCb: ()=>{
          console.log("CANCEL");
        }
      });
    },
    openNoteDialog(note) {
      const self = this;
      if (note) {
        self.$store.commit('setSelectedNote', note);
        self.$eventBus.$emit(EventNames.NOTE_UPDATE_DIALOG, true)
      } else {
        self.$store.commit('setSelectedNote', null);
        self.$eventBus.$emit(EventNames.NOTE_ADD_DIALOG, true)
      }
    },
  },
}
