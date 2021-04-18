<template>
  <v-app>
    <div id="section-id-1-" data-element-type="section" class="position-fixed">
      <div id="container-id-2-" data-element-type="container">
         <a id="button-id-25-" data-element-type="image" href="/" class="clickable">
           <v-img
             src="/img/logo/logo-and-alpaca-text-primary-button.png"
             class="mr-5"
             contain
             height="48"
           />
         </a>
         <div 
           v-if="$auth.check"
           id="container-id-77-" 
           data-element-type="container"
          >
            <div id="container-id-78-" data-element-type="container">
              <base-btn-primary
                to="/dashboard"
              >
                My Dashboard
              </base-btn-primary>
            </div>
         </div>
         <div 
           v-else
           id="container-id-4-" 
           data-element-type="container"
          >
            <div id="container-id-5-" data-element-type="container">
              <core-login-sign-up-dialog />
            </div>
            <base-btn-primary>
              Sign up
            </base-btn-primary>
         </div>
      </div>
    </div>
  </v-app>
</template>
<script>
import CoreLoginSignUpDialog from '@/components/core/LoginSignUpDialog';

export default {
  components: { 
    'core-login-sign-up-dialog': CoreLoginSignUpDialog,
  },
  data: () => ({
    loginSignUpDialog: false
  }),
}
</script>
<style scoped lang="scss">
@import "../../../../sass/home";
</style>

