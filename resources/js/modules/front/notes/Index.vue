<template>
  <div class="reports">
    <v-container class="my-2">
      <v-layout 
        row 
        class="mb-3" 
        wrap
      >
        <v-avatar>
          <v-icon>
            note
          </v-icon>
        </v-avatar>
        <h1 class="subheading grey--text ml-4">
          Notes
        </h1>
      </v-layout>
      <v-layout 
        row 
        class="mb-1 mt-1" 
        wrap
      >
        <v-btn 
          v-shortkey.once="['ctrl', 'alt', '/']"
          style="display: none;"
          @shortkey="searchFocus()"
        >
          Search focus
        </v-btn>
        <v-flex 
          class="search-input-container"
          xs3
          md3
          lg3
        >
          <v-text-field
            ref="searchInput"
            v-model="search"
            outlined
            clearable
            dense
            label="Search"
            class="search-input"
            type="text"
          />
        </v-flex>
        <v-flex
          xs6
          md6
          lg5
        >
          <div class="cmb-statuses">
            <v-combobox
              v-model="status"
              outlined
              clearable
              dense
              chips
              :items="statuses"
              multiple
              label="Status"
              class="search-input"
              @change="searchByStatus()"
            >
              <template
                #selection="data"
              >
                <v-chip
                  :key="JSON.stringify(data.item)"
                  :small="$vuetify.breakpoint.mdAndUp"
                  :x-small="$vuetify.breakpoint.smAndDown"
                  :class="`status ${data.item}`"
                  :input-value="data.selected"
                  :disabled="data.disabled"
                  v-bind="data.attrs"
                  @click:close="data.parent.selectItem(data.item)"
                >
                  {{ data.item }}
                </v-chip>
              </template>
            </v-combobox>
          </div>
        </v-flex>
      </v-layout>

      <v-layout
        v-if="notes.length == 0"
        row
        justify-center
      >
        <v-layout
          column 
          align-center
        >
          <span :class="{'grey--text display-3': $vuetify.breakpoint.smAndUp, 'display-1 grey--text': $vuetify.breakpoint.xsOnly}">Nothing here </span>
          <v-img 
            src="img/logo/llama_emoji.png" 
            width="120px"
          />
        </v-layout>
      </v-layout>
    </v-container>
  </div>
</template>

<script>
export default {
  components: {
  },
  data() {
    return {
      search: '',
      notes: [],
    }
  },
  computed: {
    searchNotes() {
      const self = this;
      return this.notes.filter(note => {
        if (self.search != '' && self.search != null) {
          if (note.name.toLowerCase().includes(self.search)) {
            return note;
          }
          if (note.content.toLowerCase().includes(self.search)) {
            return note;
          }
        } else {
          return note;
        }
      });
    },
  },
  mounted() {
  },
  created() { },
  methods: {
  }
}
</script>
<style scoped lang="scss">
@import "../../../../sass/index";
</style>
