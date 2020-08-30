<template>
  <div id="home">
    <h1 class="subheading grey--text ml-4">Dashboard</h1>
    <v-container class="my-2">
      <pie-chart :download="true" :data="[['Blueberry', 44], ['Strawberry', 23]]"></pie-chart>
      <column-chart :data="[['Sun', 32], ['Mon', 46], ['Tue', 28]]"></column-chart>
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
      {label:'Dashboard',name:'dashboard'}
    ]);
  },
}
</script>
