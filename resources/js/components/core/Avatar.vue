<template>
  <div>
    <v-menu
      bottom
      min-width="200px"
      rounded
      offset-y
    >
      <template #activator="{ on }">
        <v-btn
          icon
          x-large
          v-on="on"
        >
          <v-avatar
            :color="user.color"
            size="48"
          >
            <span class="white--text headline">{{ user.initials }}</span>
          </v-avatar>
        </v-btn>
      </template>
      <v-card>
        <v-list-item-content class="justify-center">
          <div class="mx-auto text-center">
            <v-avatar
              :color="user.color"
            >
              <span class="white--text headline">{{ user.initials }}</span>
            </v-avatar>
            <h3>{{ user.name }}</h3>
            <p class="caption mt-1">
              {{ user.email }}
            </p>
            <v-divider class="my-3" />
            <v-btn
              depressed
              rounded
              text
            >
              Edit Account
            </v-btn>
            <v-divider class="my-3" />
            <v-btn
              depressed
              rounded
              text
              @click="clickLogout('/logout','/')"
            >
              Logout
            </v-btn>
          </div>
        </v-list-item-content>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import methods from '../../common/Methods';

export default {
  data: () => ({
    user: {
      initials: '',
      name: '',
      email: '',
      color: ''
    },
  }),

  mounted() {
    var self = this;
    self.clickLogout = methods.clickLogout;
    if (this.$store.getters.isLoggedIn) {
      this.user.name = window.APP_GLOBALS.AUTH_USER.name;
      this.user.email = window.APP_GLOBALS.AUTH_USER.email;
      var initials = this.user.name.match(/\b\w/g) || [];
      this.user.initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
      this.user.color = "#"+this.intToRGB(this.hashCode(this.user.name));
      console.log(this.user.color);
    }
  },

  methods: {
    hashCode(str) {
      var hash = 0;
      for (var i = 0; i < str.length; i++) {
       hash = str.charCodeAt(i) + ((hash << 5) - hash);
      }
      return hash;
    },

    intToRGB(i) {
      var c = (i & 0x00FFFFFF)
        .toString(16)
        .toUpperCase();
      return "00000".substring(0, 6 - c.length) + c;
    }
  }
}
</script>
