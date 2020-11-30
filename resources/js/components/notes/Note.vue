<template>
  <v-card
    outlined
    class="note-card"
  >
    <div 
      v-show="!showEditForm"
      class="content"
    >
      <v-card-title
        class="text-subtitle-2"
      >
        {{ note.title }}
      </v-card-title>
      <div class="meta italic">
        {{ note.description }}
      </div>
      <div class="extra content">
        <span
          class="right floated edit icon"
          @click="showForm"
        >
          <v-icon>edit</v-icon>
        </span>
        <span
          class="right floated trash icon"
          @click="deleteNote(note)"
        >
          <v-icon>trash</v-icon>
        </span>
      </div>
    </div>
    <div
      v-show="showEditForm"
      class="content"
    >
      <div class="ui form">
        <div class="field">
          <label>Title</label>
          <input
            v-model="noteTitle"
            type="text"
            value="note.title"
          >
        </div>
        <div class="field">
          <label>Description</label>
          <input
            v-model="noteDescription"
            type="text"
            value="note.description"
          >
        </div>
        <div class="ui two buttons">
          <button
            class="ui basic button"
            @click="hideForm"
          >
            Close X
          </button>
        </div>
      </div>
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
          title: null,
          description: null
        }
      }
    }
  },
  data() {
    return {
      noteTitle: '',
      noteDescription: '',
      showEditForm: false,
    };
  },
  methods: {
    deleteNote(note) {
      this.$emit('delete-note', note);
    },
    showForm() {
      this.showEditForm = true;
    },
    hideForm() {
      this.showEditForm = false;
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
</style>
