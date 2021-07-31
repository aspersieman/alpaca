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
              @click="logout()"
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
import AvatarMixin from '@/mixins/AvatarMixin';

export default {
  mixins: [
    AvatarMixin,
  ],
  data: () => ({
    user: {
      initials: '',
      name: '',
      email: '',
      color: ''
    }
  }),
  mounted() {
    if (this.$auth.check) {
      this.user.name = this.$auth.user.name;
      this.user.email = this.$auth.user.email;
      this.user.initials = this.getInitials(this.user.name);
      this.user.color = this.getAvatarColour(this.user.name);
    }
  },
  methods: {
    logout() {
      this.$store.dispatch('auth/logout')
      this.$router.push("/");
    }
  }
}
</script>

<style scoped lang="scss">
.avatar-menu {
  margin-top: 7px;
}
</style>
