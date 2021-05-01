export default {
  methods: {
    setSetting(name, value) {
      const parsed = JSON.stringify(value);
      localStorage.setItem(name, parsed);
    },
    getSetting(name) {
      let value = null;
      if (localStorage.getItem(name)) {
        try {
          value = JSON.parse(localStorage.getItem(name));
        } catch(e) {
          localStorage.removeItem(name);
        }
      }
      return value;
    },
    getSettings() {
    }
  }
}
