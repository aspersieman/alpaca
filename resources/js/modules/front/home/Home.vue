<template>
  <div id="home">
    <h1 class="subheading grey--text ml-4">Home</h1>
    <v-container class="my-2">
    </v-container>
  </div>
</template>
<script>

export default {
  name: 'Home',
  components: { },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs',[
      {label:'',name:''}
    ]);
  },
}
</script>
