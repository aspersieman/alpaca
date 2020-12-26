<template>
  <div>
    <v-menu
      bottom
      left
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
    }
  }),

  mounted() {
    var self = this;
    self.clickLogout = methods.clickLogout;
    if (this.$store.getters.isLoggedIn) {
      self.user.name = window.APP_GLOBALS.AUTH_USER.name;
      self.user.email = window.APP_GLOBALS.AUTH_USER.email;
      self.user.initials = self.getInitials(self.user.name);
      self.user.color = self.getAvatarColour(self.user.name);
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
    },
    getInitials(name) {
      var initials = name.match(/\b\w/g) || [];
      return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
    },
    getAvatarColour(name) {
      return "#"+this.intToRGB(this.hashCode(name));
    }
  }
}
</script>

<style scoped lang="scss">
.avatar-menu {
  margin-top: 7px;
}
</style>
