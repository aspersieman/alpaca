<template>
  <section id="feed">
    <feed>
      <slot />
    </feed>
  </section>
</template>

<script>
import Feed from '../../../components/core/Feed.vue';
export default {
  name: 'HomePictures',

  components: {
    Feed
  },
}
</script>
