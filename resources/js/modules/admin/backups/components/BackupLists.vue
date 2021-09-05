<template>
  <div class="component-wrap">
    <v-card class="pt-3">
      <div class="d-flex flex-row">
        <div class="flex-grow-1 pa-2">
          <v-btn
            class="primary lighten-1"
            dark
            @click="saveBackup()"
          >
            Backup DB
            <v-icon
              right
              dark
            >
              add
            </v-icon>
          </v-btn>
          <v-btn
            v-if="isGoogleDriveAuthenticated === false"
            class="primary lighten-1"
            dark
            @click="googleConnect()"
          >
            Google Connect
            <v-icon
              right
              dark
            >
              mdi-google-drive
            </v-icon>
          </v-btn>
          <v-btn
            v-if="isGoogleDriveAuthenticated === true && backups.length > 0"
            class="primary lighten-1"
            :enabled="!sendingToDrive"
            :loading="sendingToDrive"
            dark
            @click="googleUpload()"
          >
            Upload to Google Drive
            <v-icon
              right
              dark
            >
              mdi-google-drive
            </v-icon>
          </v-btn>
        </div>
        <div class="flex-grow-1 pa-2" />
      </div>
      <div class="d-flex flex-lg-row flex-sm-column">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.name"
            filled
            prepend-icon="search"
            label="Filter by name"
            outlined
            dense
            clearable
            class="filter-input"
          />
        </div>
      </div>
    </v-card>
    <v-divider class="pb-2" />
    <v-data-table
      hide-default-header
      :headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="backupsTotal"
      class="elevation-1"
    >
      <template #header="{props:{headers}}">
        <thead>
          <tr>
            <th
              v-for="header in headers"
              :key="header.text"
            >
              <div
                v-if="header.value=='name'"
                :class="`text-${header.align}`"
              >
                <v-icon>mdi-file-restore</v-icon>
                {{ header.text }}
              </div>
              <div
                v-else
                :class="`text-${header.align}`"
              >
                {{ header.text }}
              </div>
            </th>
          </tr>
        </thead>
      </template>
      <template #body="{items}">
        <tbody>
          <tr
            v-for="item in items"
            :key="item.name"
          >
            <td>
              <v-btn
                class="ma-2"
                outlined
                fab
                small
                icon
                color="red"
                @click="$store.dispatch('backup/deleteBackup', item.name)"
              >
                <v-icon>
                  mdi-delete
                </v-icon>
              </v-btn>
            </td>
            <td>{{ item.name }}</td>
            <td>{{ item.size }}</td>
          </tr>
        </tbody>
      </template>
    </v-data-table>
    <v-divider class="py-5" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
window._ = require('lodash');

export default {
  data () {
    return {
      headers: [
        { text: 'Action', value: false, align: 'left', sortable: false },
        { text: 'Name', value: 'name', align: 'left', sortable: true },
        { text: 'Size', value: 'size', align: 'left', sortable: true },
      ],
      items: [],
      pagination: {
        itemsPerPage: 10
      },

      filters: {
        name: '',
      },
    }
  },
  computed: {
    ...mapGetters('backup', [
      'backups',
      'backupsTotal',
      'isGoogleDriveAuthenticated',
      'googleDriveLoginURL',
      'sendingToDrive',
    ]),
  },
  watch: {
    pagination: {
      handler () {
        this.loadBackups(()=>{});
      },
    },
    'filters.name': window._.debounce(function(){
      const self = this;
      self.loadBackups(()=>{});
    },700),
  },
  mounted() {
    this.$store.dispatch('backup/isGoogleDriveAuthenticated');
    this.$store.commit('backup/SENDING_TO_DRIVE', false);
    this.$store.subscribe((mutation) => {
      if (
        [
          'backup/ADDED',
          'backup/DELETED',
        ].includes(mutation.type)
      ) {
        this.loadBackups(()=>{});
      }
    });
  },
  methods: {
    loadBackups(cb) {
      let params = {
        name: this.filters.name
      };

      this.$store.dispatch('backup/getBackups', params)
        .then(() => {
          this.items = this.backups;
          this.pagination.totalItems = this.backupsTotal;
        });
    },

    saveBackup() {
      this.$store.dispatch('backup/saveBackup');
    },

    googleConnect() {
      window.location = this.googleDriveLoginURL;
    },

    googleUpload() {
      this.$store.dispatch('backup/sendToDrive');
    },
  }
}
</script>

<style scoped>
  .filter-input {
    max-width: 250px;
  }
</style>
