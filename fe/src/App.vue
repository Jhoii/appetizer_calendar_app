<template>
  <div id="app">
    <CalendarEvent />
  </div>
</template>

<script>
import CalendarEvent from "./components/CalendarEvent.vue";

export default {
  name: "App",
  components: {
    CalendarEvent
  }
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
