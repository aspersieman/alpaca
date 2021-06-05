<template>
  <v-card
    outlined
    class="note-card mb-5 mx-auto"
    :class="{ 'ml-5': $vuetify.breakpoint.mdAndUp }"
    :width="width > 0 ? width : ''"
    :max-width="width > 0 ? width : ''"
    :height="height > 0 ? height : ''"
  >
    <v-card-title
      class="note-title"
    >
      {{ note.name }}
      <div class="note-details">
        {{ $appFormatters.formatDate(note.created_at) }}
      </div>
      <v-list-item-subtitle
        v-if="showTask && note.task"
      >
        <v-icon small>
          task_alt
        </v-icon>
        {{ note.task.name }}
        <v-chip
          small
          :class="`uppercase status ${note.task.status}`"
        >
          {{ note.task.status }}
        </v-chip>
      </v-list-item-subtitle>
    </v-card-title>
    <v-divider class="mx-1" />
    <v-card-text
      class="note-text"
    >
      <div class="text-sm-caption note-content">{{ note.content }}</div>
    </v-card-text>
    <v-card-actions class="note-actions">
      <v-spacer />
      <v-btn
        class="text-sm-caption"
        icon
        x-small
        @click="openNoteDialog(note)"
      >
        <v-icon>
          edit
        </v-icon>
      </v-btn>
      <v-btn
        class="text-sm-caption"
        icon
        x-small
        @click="$store.dispatch('note/deleteNote', note)"
      >
        <v-icon>
          delete
        </v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script type="text/javascript">
import TaskMixin from '@/mixins/TaskMixin';

export default {
  mixins: [
    TaskMixin,
  ],
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
    },
    width: {
      required: false,
      type: Number,
      default: function() {
        return 0;
      }
    },
    height: {
      required: false,
      type: Number,
      default: function() {
        return 0;
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
      noteName: '',
      noteContent: '',
      loading: false
    };
  },
};
</script>

<style scoped lang="scss">
@import "../../../../../sass/index";
.note-card {
  display: flex !important;
  flex-direction: column;
  overflow: hidden !important;
  align-self: flex-start;
}
.note-title {
  padding-top: 5px;
  padding-bottom: 5px;
}
.note-details {
  margin-top: -10px;
  width: 100%;
  font-weight: normal;
  font-style: italic;
  font-size: .75rem;
}
.note-text {
  flex-grow: 1;
  overflow: auto;
}
.note-content {
  white-space: pre-wrap;
  font-size: .75rem;
  width: 100%;
}
.note-actions {
  width: 100%;
  background-color: $theme-light-background;
  position: absolute;
  bottom: 0;
}
.italic {
  font-style: italic;
}
</style>
