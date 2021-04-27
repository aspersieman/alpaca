export default {
  data(){
    return {
    }
  },
  methods: {
    getNotes(payload) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('note/getNotes', payload)
          .then(() =>{
            this.$eventBus.notesLoaded();
            resolve()
          })
          .catch(error => reject(error))
      })
    },
    saveNote(note) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('note/saveNote', note)
          .then(() =>{
            this.$eventBus.taskAdded();
            this.$flash.success({
              text: 'Note added successfully'
            });
            this.getNotes();
            resolve()
          })
          .catch(error => {
            const message = error?.response?.data?.message || 'ERROR';
            console.error(message);
            this.$flash.error({
              text: 'Error adding note'
            });
            reject(error);
          });
      });
    },
    updateNote(note) {
      return new Promise((resolve, reject) => {
        this.$store.dispatch('note/updateNote', note)
          .then(() =>{
            this.$flash.success({
              text: 'Note updated successfully'
            });
            this.getNotes();
            this.$eventBus.noteUpdated();
            resolve()
          })
          .catch(error => {
            const message = error?.response?.data?.message || 'ERROR';
            console.error(message);
            this.$flash.error({
              text: 'Error updating note'
            });
            reject(error);
          });
      });
    },
    deleteNote(noteId) {
      this.$store.dispatch('dialog/dialogShow',{
        type: 'confirm',
        icon: 'warning',
        title: 'Confirm deletion',
        message: 'Are you sure you want to delete this note?',
        okCb: ()=>{
          new Promise((resolve, reject) => {
            this.$store.dispatch('note/deleteNote', noteId)
              .then(({data}) => {
                this.getNotes();
                this.$flash.success({
                  text: 'Note deleted successfully'
                });
                this.$eventBus.noteDeleted();
                this.$store.dispatch('dialog/dialogHide');
                resolve(data)
              }).catch((error) => {
                console.error(error)
                this.$flash.error({
                  text: 'Error occurred deleting note'
                });
                this.$store.dispatch('dialog/dialogHide');
                reject(error);
              })
          });
        },
        cancelCb: ()=>{
          this.$store.dispatch('dialog/dialogHide');
        }
      });
    },
    openNoteDialog(note) {
      if (note) {
        this.$store.commit('note/NOTE', note);
        this.$eventBus.noteDialogUpdate(true);
      } else {
        this.$store.commit('note/NOTE', null);
        this.$eventBus.noteDialogAdd(true);
      }
    },
  },
}
