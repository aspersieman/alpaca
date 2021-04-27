<template>
  <div class="drawer-buttons">
    <v-btn
      class="drawer-button"
      @click="openNoteDialog()"
    >
      New Note
    </v-btn>
  </div>
</template>

<script>
import NoteMixin from '@/mixins/NoteMixin';

export default {
  mixins: [ NoteMixin ],
  data() {
    return { };
  },
  methods: { },
};
</script>
<style scoped lang="scss">
@import "../../../../../sass/index";
.drawer-buttons {
  width: 100%;
  border-top: 1px solid $border-light-grey;
  text-align: center;
}

.drawer-button {
  width: 100px;
  margin: 3px;
}

.note-content {
  font-size: .75rem;
}
</style>
