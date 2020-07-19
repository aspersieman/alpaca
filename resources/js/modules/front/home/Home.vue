<template>
  <div id="home">
    <pictures>
    </pictures>
  </div>
</template>
<script>
import Pictures from './Pictures.vue';

export default {
  name: 'Home',
  components: {
    Pictures,
  },
  mounted() {
    console.log("index.home.Home.vue");
    const self = this;
    self.$store.commit('setBreadcrumbs',[
      {label:'',name:''}
    ]);
  },
}
</script>
