<template>
  <div
    class="notes-container flex-wrap d-flex flex-row justify-space-between"
    :class="{ 'notes-container-large': $vuetify.breakpoint.mdAndUp, 'notes-container-medium': $vuetify.breakpoint.smAndDown }"
  >
    <note
      v-for="note in notes"
      :key="note.title"
      :note="note"
      :show-task="showTask"
      :width="width"
      :height="height"
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
    },
    width: {
      required: false,
      type: Number,
      default: function() {
        return 418;
      }
    },
    height: {
      required: false,
      type: Number,
      default: function() {
        return 418;
      }
    },
    showTask: {
      required: false,
      type: Boolean,
      default: function() {
        return false;
      }
    },
  },
  data() {
    return {
      listNote: false,
    };
  },
};
</script>
<style scoped>
.notes-container {
  flex-flow: row wrap;
}
.notes-container-large {
  padding: 0.1em;
  margin: 0.1em auto;
}
.notes-container-medium {
  padding: 0.01em;
  margin: 0.01em auto;
}
.notes-container::after {
  content: " ";
  flex: auto;
  padding: 0.1em;
  margin: 0.1em auto;
}
</style>
