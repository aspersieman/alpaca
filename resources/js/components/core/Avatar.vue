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
            <v-img
              v-if="user.avatar"
              :src="avatarUrl"
            />
            <span
              v-else
              class="white--text headline"
            >
              {{ user.initials }}
            </span>
          </v-avatar>
        </v-btn>
      </template>
      <v-card>
        <v-list-item-content class="justify-center">
          <div class="mx-auto text-center">
            <v-avatar
              :color="user.color"
            >
              <v-img
                v-if="user.avatar"
                :src="avatarUrl"
                width="128"
              />
              <span
                v-else
                class="white--text headline"
              >
                {{ user.initials }}
              </span>
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
              to="/profile"
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
      id: '',
      initials: '',
      name: '',
      email: '',
      color: '',
      avatar: null
    }
  }),

  computed: {
    avatarUrl() {
      return this.user.avatar ? `/avatar/${this.user.id}?path=${this.user.avatar}` : null;
    }
  },

  mounted() {
    this.setUser(this.$auth.user);
    if (this.$auth.check) {
      this.$store.subscribe((mutation) => {
        if (
          [
            'auth/SET_AUTH_USER'
          ].includes(mutation.type)
        ) {
          this.setUser(this.$auth.user);
        }
      });
    }
  },

  methods: {
    logout() {
      this.$store.dispatch('auth/logout')
      this.$router.push("/");
    },

    setUser(user) {
      this.user.id = user.id;
      this.user.name = user.name;
      this.user.email = user.email;
      this.user.initials = this.getInitials(user.name);
      this.user.avatar = user.avatar;
      this.user.color = this.getAvatarColour(user.name);
    }
  }
}
</script>

<style scoped lang="scss">
.avatar-menu {
  margin-top: 7px;
}
</style>
