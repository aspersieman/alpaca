<template>
  <div>
    <note
      v-for="note in notes"
      :key="note.title"
      :note="note"
      @delete-note="deleteNote"
    />
  </div>
</template>

<script type = "text/javascript" >
import Note from './Note';
export default {
  components: {
    Note
  },
  props: {
    notes: {
      required: true,
      type: Array,
      default: function() {
        return [
          Note
        ]
      }
    }
  },
  data() {
    return {
      listNote: false,
    };
  },
  mounted() {
    console.log(this.notes);
  },
  methods: {
    deleteNote(note) {
      var removeIt = confirm('Do you really wanna remove it?');
      if (removeIt) {
        const noteIndex = this.notes.indexOf(note);
        this.notes.splice(noteIndex, 1);
        alert('Note deleted!');
      }
    },
  },
};
</script>
