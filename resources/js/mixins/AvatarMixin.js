export default {
  data(){
    return {
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


