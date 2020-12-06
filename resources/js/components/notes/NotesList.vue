<template>
  <div>
    <note
      v-for="note in notes"
      :key="note.title"
      :note="note"
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
  created() { },
  mounted() { },
  methods: { },
};
</script>
