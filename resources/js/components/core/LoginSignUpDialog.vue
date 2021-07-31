<template>
  <div>
    <v-dialog
      v-model="dialog"
      max-width="600px"
      min-width="360px"
    >
      <template #activator="vdialog">
        <v-container
          v-if="$vuetify.breakpoint.mdAndUp"
        >
          <v-row>
            <v-col
              class="col-6"
            >
              <v-btn
                text
                link
                class="d-none d-sm-flex"
                v-bind="vdialog.attrs"
                v-on="vdialog.on"
                @click="goToTab(0)"
              >
                Log In
              </v-btn>
            </v-col>
            <v-col
              class="col-6"
            >
              <base-btn-primary
                v-bind="vdialog.attrs"
                v-on="vdialog.on"
                @click="goToTab(1)"
              >
                Sign Up
              </base-btn-primary>
            </v-col>
          </v-row>
        </v-container>
        <v-container
          v-else
        >
          <v-menu
            close-on-click
            bottom
            offset-y
          >
            <template #activator="vmenu">
              <v-btn
                icon
                x-large
                v-bind="vmenu.attrs"
                v-on="vmenu.on"
              >
                <v-icon
                  color="primary"
                >
                  account_circle
                </v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item>
                <v-list-item-title
                  v-bind="vdialog.attrs"
                  v-on="vdialog.on"
                  @click="goToTab(0)"
                >
                  Log In
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title
                  v-bind="vdialog.attrs"
                  v-on="vdialog.on"
                  @click="goToTab(1)"
                >
                  Sign Up
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-container>
      </template>
      <div>
        <v-tabs
          v-model="tab"
          show-arrows
          background-color="light"
          color="primary accent-4"
          icons-and-text
          grow
        >
          <v-tabs-slider />
          <v-tab
            v-for="i in tabs"
            :key="i.name"
          >
            <v-icon large>
              {{ i.icon }}
            </v-icon>
            <div class="caption py-1">
              {{ i.name }}
            </div>
          </v-tab>
          <v-tab-item>
            <v-card class="px-4">
              <v-card-text>
                <v-form
                  ref="loginForm"
                  v-model="valid"
                  lazy-validation
                >
                  <v-row>
                    <v-col cols="12">
                      <v-text-field
                        v-model="loginEmail"
                        :rules="loginEmailRules"
                        label="E-mail"
                        required
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="loginPassword"
                        :append-icon="show1?'visibility':'visibility_off'"
                        :rules="[rules.required, rules.min]"
                        :type="show1 ? 'text' : 'password'"
                        name="input-10-1"
                        label="Password"
                        hint="At least 8 characters"
                        counter
                        @click:append="show1 = !show1"
                      />
                    </v-col>
                    <v-col
                      class="d-flex"
                      cols="12"
                      sm="6"
                      xsm="12"
                    />
                    <v-spacer />
                    <v-col
                      class="d-flex"
                      cols="12"
                      sm="3"
                      xsm="12"
                      align-end
                    >
                      <v-btn
                        block
                        :disabled="!valid"
                        color="success"
                        @click="login"
                      >
                        Login
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card class="px-4">
              <v-card-text>
                <v-form
                  ref="registerForm"
                  v-model="valid"
                  lazy-validation
                >
                  <v-row>
                    <v-col
                      cols="12"
                      sm="6"
                      md="6"
                    >
                      <v-text-field
                        v-model="firstName"
                        :rules="[rules.required]"
                        label="First name"
                        maxlength="20"
                        required
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      sm="6"
                      md="6"
                    >
                      <v-text-field
                        v-model="lastName"
                        :rules="[rules.required]"
                        label="Last name"
                        maxlength="20"
                        required
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="email"
                        :rules="emailRules"
                        label="E-mail"
                        required
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="password"
                        :append-icon="show1 ? 'visibility' : 'visibility_off'"
                        :rules="[rules.required, rules.min]"
                        :type="show1 ? 'text' : 'password'"
                        name="input-10-1"
                        label="Password"
                        hint="At least 8 characters"
                        counter
                        @click:append="show1 = !show1"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="verify"
                        block
                        :append-icon="show1 ? 'visibility' : 'visibility_off'"
                        :rules="[rules.required, passwordMatch]"
                        :type="show1 ? 'text' : 'password'"
                        name="input-10-1"
                        label="Confirm password"
                        counter
                        @click:append="show1 = !show1"
                      />
                    </v-col>
                    <v-spacer />
                    <v-col
                      class="d-flex ml-auto"
                      cols="12"
                      sm="3"
                      xsm="12"
                    >
                      <v-btn
                        block
                        :disabled="!valid"
                        color="success"
                        @click="signup"
                      >
                        Register
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </div>
    </v-dialog>
  </div>
</template>
<script>

export default {
  data: () => ({
    dialog: false,
    tab: 0,
    tabs: [
      { name:"Login", icon:"mdi-account" },
      { name:"Register", icon:"mdi-account-outline" }
    ],
    valid: true,
    firstName: "",
    lastName: "",
    email: "",
    password: "",
    verify: "",
    loginPassword: "",
    loginEmail: "",
    loginEmailRules: [
      v => !!v || "Required",
      v => /.+@.+\..+/.test(v) || "E-mail must be valid"
    ],
    emailRules: [
      v => !!v || "Required",
      v => /.+@.+\..+/.test(v) || "E-mail must be valid"
    ],

    show1: false,
    rules: {
      required: value => !!value || "Required.",
      min: v => (v && v.length >= 8) || "Min 8 characters"
    }
  }),
  computed: {
    passwordMatch() {
      return () => this.password === this.verify || "Password must match";
    }
  },
  methods: {
    login() {
      const self = this;
      if (this.$refs['loginForm'].validate()) {
        let user = {
            email: this.loginEmail,
            password: this.loginPassword
        };
        this.$store.dispatch('auth/login', user)
          .then(() => { })
          .catch((error) => {
            console.log(error);
            this.$flash.error({
              text: 'Email or password incorrect'
            });
          })
      }
    },

    signup() {
      if (this.$refs['registerForm'].validate()) {
        let user = {
            email: this.loginEmail,
            password: this.loginPassword,
            name: this.firstName + ' ' + this.lastName,
        };
        this.$store.dispatch('auth/register', user)
          .then(() => { })
          .catch((error) => {
            console.log(error);
            this.$flash.error({
              text: 'Email or password incorrect'
            });
          })
      }
    },

    goToTab(number) {
      if (number > 1) {
        number = 1;
      }
      if (number < 0) {
        number = 0;
      }
      this.tab = number;
    }
  }
}
</script>
<style scoped lang="scss">
@import "../../../sass/index";
@import "../../../sass/home";
</style>
