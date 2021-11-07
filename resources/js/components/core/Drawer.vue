<template>
  <div>
    <v-navigation-drawer
      v-if="$vuetify.breakpoint.smAndDown || menu.length > 0"
      temporary
      left
      app
      :value="value"
      @input="$emit('input', $event)"
    >
      <template
        #prepend
      >
        <div
          v-if="$auth.check && ($vuetify.breakpoint.smAndDown || $store.getters.getAppBarBreakpoint == 'sm' || $store.getters.getAppBarBreakpoint == 'sx')"
        >
          <v-list-item two-line>
            <avatar />
            <v-list-item-content>
              <v-list-item-title>{{ user.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </div>
        <div
          v-if="$auth.check && ($vuetify.breakpoint.mdAndUp && $store.getters.getAppBarBreakpoint != 'sm' && $store.getters.getAppBarBreakpoint != 'sx')"
        >
          <v-list-item two-line>
            <v-list-item-content>
              <router-link to="/">
                <v-img
                  src="/img/logo/logo-and-alpaca-text-primary-button.png"
                  height="48"
                  contain
                />
              </router-link>
            </v-list-item-content>
          </v-list-item>
        </div>
      </template>

      <v-divider />
      <v-list dense>
        <v-list-item
          to="/"
        >
          <v-list-item-action>
            <v-icon>home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>
              Home
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <div
          v-for="item of menu"
          :key="item.label"
        >
          <v-divider
            v-if="item.navType == 'divider'"
            horizontal
          />
          <v-list-item
            v-if="item.navType != 'divider' && item.routeType == 'vue'"
            :key="item.routeName"
            :data="item"
            :to="{name: item.routeName}"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ item.label }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item
            v-if="item.navType != 'divider' && item.routeType == 'laravel'"
            :key="item.routeName"
            :data="item"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <a :href="item.routeName">
                <v-list-item-title>{{ item.label }}</v-list-item-title>
              </a>
            </v-list-item-content>
          </v-list-item>
        </div>
        <v-divider horizontal />
        <v-list-item
          v-if="!$auth.check"
          @click="$store.dispatch('auth/login')"
        >
          <v-list-item-action>
            <v-icon>meeting_room</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Login</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item
          v-if="$auth.check"
          @click="$store.dispatch('auth/logout')"
        >
          <v-list-item-action>
            <v-icon>no_meeting_room</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import Avatar from '@/components/core/Avatar';
import AvatarMixin from '@/mixins/AvatarMixin';

export default {
  name: 'CoreDrawer',
  components: {
    Avatar
  },
  mixins: [ AvatarMixin ],
  props: {
    value: {
      type: Boolean,
      default: false
    }
  },
  emits: [ 'input' ],
  data: () => ({
    user: {
      initials: '',
      name: '',
      email: '',
    },
  }),
  computed: {
    ...mapGetters('menu', ['menu']),
  },
  created() {
    this.$store.dispatch('menu/getMenu');
  },
  mounted() {
    if (this.$auth.check) {
      this.user.name = this.$auth.user.name;
      this.user.email = this.$auth.user.email;
      this.user.initials = this.getInitials(this.user.name);
      this.user.color = this.getAvatarColour(this.user.name);
    }
  },
  methods: {
    getInitials(name) {
      var initials = name.match(/\b\w/g) || [];
      return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
    },
  },
}
</script>
