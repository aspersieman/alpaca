<template>
  <div
    class="clock"
    :class="`clock-${size}`"
  >
    <div class="outer-clock-face">
      <div class="marking marking-one" />
      <div class="marking marking-two" />
      <div class="marking marking-three" />
      <div class="marking marking-four" />
      <div class="inner-clock-face">
        <div class="hand hour-hand" />
        <div class="hand min-hand" />
        <div class="hand second-hand" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CoreClock',

  props: {
    size: {
      required: false,
      type: String,
      default: 'medium',
    }
  },

  data: () => ({
    secondHand: null,
    minsHand: null,
    hourHand: null
  }),

  mounted() {
    this.secondHand = document.querySelector('.second-hand');
    this.minsHand = document.querySelector('.min-hand');
    this.hourHand = document.querySelector('.hour-hand');
    setInterval(this.setDate, 1000);
    this.setDate();
  },

  methods: {
    setDate() {
      const now = new Date();

      const seconds = now.getSeconds();
      const secondsDegrees = ((seconds / 60) * 360) + 90;
      this.secondHand.style.transform = `rotate(${secondsDegrees}deg)`;

      const mins = now.getMinutes();
      const minsDegrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
      this.minsHand.style.transform = `rotate(${minsDegrees}deg)`;

      const hour = now.getHours();
      const hourDegrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
      this.hourHand.style.transform = `rotate(${hourDegrees}deg)`;
    }
  }
}
</script>
<style scoped>
.clock {
  margin: 0;
  font-size: 10px;
  display: flex;
  flex: 1;
  align-items: center;
  text-align: center;
  font-size: 10px;
  border: 1px solid #545271;
  border-radius: 50%;
  margin: 1px auto;
  position: relative;
  padding: 1px;
  background: #545271;
}
.clock-medium {
  width: 56px;
  height: 56px;
  max-height: 56px;
  max-width: 56px;
}
.clock-small {
  width: 40px;
  height: 40px;
  max-height: 40px;
  max-width: 40px;
}

.outer-clock-face {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  background: #fefefc;
  overflow: hidden;
}

.outer-clock-face::after {
  -webkit-transform: rotate(90deg);
  -moz-transform: rotate(90deg);
  transform: rotate(90deg)
}

.outer-clock-face::before,
.outer-clock-face::after,
.outer-clock-face .marking{
  content: '';
  position: absolute;
  width: 1px;
  height: 100%;
  background: #b8b8c5;
  z-index: 0;
  left: 49%;
}

.outer-clock-face .marking {
  background: #bdbdcb;
  width: 1px;
}

.outer-clock-face .marking.marking-one {
  -webkit-transform: rotate(30deg);
  -moz-transform: rotate(30deg);
  transform: rotate(30deg)
}

.outer-clock-face .marking.marking-two {
  -webkit-transform: rotate(60deg);
  -moz-transform: rotate(60deg);
  transform: rotate(60deg)
}

.outer-clock-face .marking.marking-three {
  -webkit-transform: rotate(120deg);
  -moz-transform: rotate(120deg);
  transform: rotate(120deg)
}

.outer-clock-face .marking.marking-four {
  -webkit-transform: rotate(150deg);
  -moz-transform: rotate(150deg);
  transform: rotate(150deg)
}

.inner-clock-face {
  position: absolute;
  top: 10%;
  left: 10%;
  width: 80%;
  height: 80%;
  background: #fefefc;
  -webkit-border-radius: 100%;
  -moz-border-radius: 100%;
  border-radius: 100%;
  z-index: 1;
}

.inner-clock-face::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 1px;
  height: 1px;
  border-radius: 1px;
  background: #4d4b63;
  z-index: 11;
}

.hand {
  width: 50%;
  right: 50%;
  height: 1px;
  background: #61afff;
  position: absolute;
  top: 50%;
  border-radius: 6px;
  transform-origin: 100%;
  transform: rotate(90deg);
  transition-timing-function: cubic-bezier(0.1, 2.7, 0.58, 1);
}

.hand.hour-hand {
  width: 20%;
  z-index: 3;
}

.hand.min-hand {
  height: 3px;
  z-index: 10;
  width: 35%;
}

.hand.second-hand {
  background: #ff5e5e;
  width: 40%;
}
</style>
