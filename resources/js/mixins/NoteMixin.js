export default {
  methods: {
    getNotes(payload) {
      return this.$store.dispatch('note/getNotes', payload)
        .then(() => this.$eventBus.notesLoaded());
    },

    saveNote(payload) {
      return this.$store.dispatch('note/saveNote', payload)
        .then(() =>{
          this.$eventBus.noteAdded(payload);
          this.$flash.success({
            text: 'Note added successfully'
          });
          if (payload.task_id) {
            this.$store.dispatch('task/getTask', payload.task_id);
          }
        })
        .catch(error => {
          console.error(error);
          this.$flash.error({
            text: 'Error adding note'
          });
        });
    },

    updateNote(payload) {
      return this.$store.dispatch('note/updateNote', payload)
        .then(() =>{
          this.$flash.success({
            text: 'Note updated successfully'
          });
          this.$eventBus.noteUpdated(payload);
          if (payload.task_id) {
            this.$store.dispatch('task/getTask', payload.task_id);
          }
        })
        .catch(error => {
          const message = error?.response?.data?.message || 'ERROR';
          console.error(message);
          this.$flash.error({
            text: 'Error updating note'
          });
        });
    },

    deleteNote(payload) {
      this.$store.dispatch('dialog/dialogShow',{
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this note?',
        okCb: ()=>{
          this.$store.dispatch('note/deleteNote', payload)
            .then(() => {
              this.$flash.success({
                text: 'Note deleted successfully'
              });
              if (payload.task_id) {
                this.$store.dispatch('task/getTask', payload.task_id);
              }
              this.$eventBus.noteDeleted(payload);
              this.$store.dispatch('dialog/dialogHide');
            }).catch((error) => {
              console.error(error)
              this.$flash.error({
                text: 'Error occurred deleting note'
              });
              this.$store.dispatch('dialog/dialogHide');
              reject(error);
            })
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      });
    },

    openNoteDialog(note) {
      if (note) {
        this.$eventBus.noteDialogUpdate(true, note);
      } else {
        this.$eventBus.noteDialogAdd(true);
      }
    },
  },
}
