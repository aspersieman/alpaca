<template>
  <div class="ui basic content center aligned segment">
    <button
      v-show="!isCreating"
      class="ui basic button icon"
      @click="openForm"
    >
      <v-icon>plus</v-icon>
      New note
    </button>
    <div
      v-show="isCreating"
      class="ui centered card"
    >
      <div class="content">
        <div class="ui form">
          <div class="field">
            <label>Title</label>
            <input
              v-model="titleText"
              type="text"
            >
          </div>
          <div class="field">
            <label>Description</label>
            <input
              v-model="descriptionText"
              type="text"
            >
          </div>
          <div class="ui two buttons">
            <button
              class="ui basic green button"
              @click="sendForm()"
            >
              Create
            </button>
            <button
              class="ui basic black button"
              @click="closeForm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      titleText: '',
      descriptionText: '',
      isCreating: false,
    };
  },
  methods: {
    openForm() {
      this.isCreating = true;
    },
    closeForm() {
      this.isCreating = false;
    },
    sendForm() {
      if (this.titleText.length > 0 && this.descriptionText.length > 0) {
        const title = this.titleText;
        const description = this.descriptionText;
        this.$emit('create-note', {
          title,
          description,
        });
        // Clean fields after submit
        this.titleText = '';
        this.descriptionText = '';
        this.isCreating = false;
      }
    },
  },
};
</script>
