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
        <div class="text-sm-caption note-content">
          {{ note.content }}
        </div>
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
                class="note-content"
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
import NotesMixin from '../../mixins/NotesMixin';

export default {
  mixins: [ 
    NotesMixin
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
.note-content {
  font-size: .75rem;
}
</style>
