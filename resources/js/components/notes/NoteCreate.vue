<template>
  <div class="drawer-buttons">
    <v-btn
      v-show="!isCreating"
      class="drawer-button"
      @click="open"
    >
      New Note
    </v-btn>
    <v-card
      v-show="isCreating"
    >
      <v-card-text>
        <v-container>
          <v-row>
            <v-col>
              <v-text-field
                v-model="name"
                label="Title"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-textarea
                v-model="content"
                label="Content"
                rows="3"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-btn
                class="success mx-0 mt-3"
                @click="createNote()"
              >
                Add
              </v-btn>
            </v-col>
            <v-col>
              <v-btn
                class="error mx-0 mt-3"
                @click="close"
              >
                Cancel
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: '',
      content: '',
      isCreating: false,
    };
  },
  methods: {
    open() {
      this.isCreating = true;
    },

    close() {
      this.isCreating = false;
    },

    createNote() {
      if (this.name != '' && this.content != '') {
        const name = this.name;
        const content = this.content;
        const task_id = this.$store.state.task.id == 0 || typeof this.$store.state.task.id == 'undefined' ? null : this.$store.state.task.id;
        this.$emit('create-note', {
          task_id,
          name,
          content,
        });
        // Clean fields after submit
        this.name = '';
        this.content = '';
        this.isCreating = false;
      }
    },
  },
};
</script>
<style scoped lang="scss">
@import "../../../sass/index";
.drawer-buttons {
  width: 100%;
  border-top: 1px solid $border-light-grey;
  text-align: center;
}

.drawer-button {
  width: 100px;
  margin: 3px;
}
</style>
