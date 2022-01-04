<template>
  <v-container class="my-2">
    <add-fab />
    <user-profile />
  </v-container>
</template>

<script>
import UserProfile from '@/modules/front/user/UserProfile';
import AddFab from '@/components/core/AddFab';

export default {
  components: {
    UserProfile,
    AddFab,
  },
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
</style>
