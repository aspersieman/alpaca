<template>
  <v-app>
    <div id="ui-canvas" style="margin: 0;">
      <div id="container-id-6-" data-element-type="container">
        <div id="section-id-7-" data-element-type="section">
          <div id="container-id-8-" data-element-type="container">
            <div id="container-id-0-" data-element-type="container">
               <div id="container-id-3-" data-element-type="container">
                  <p id="text-id-9-" data-element-type="text">Get productive with Lamoid</p>
                  <p id="text-id-10-" data-element-type="text">To-Do list and productivity tool to help you get things done</p>
               </div>
               <a id="button-id-2-" data-element-type="button" href="/layouts"><span>Get Started</span></a>
            </div>
            <a id="image-id-0-" data-element-type="image">
              <v-img
                id="element-id-1-"
                src="/img/logo/tasks-frame_chrome_mac_light.png"
              />
            </a>
          </div>
        </div>
        <div id="section-id-34-" data-element-type="section">
           <div id="container-id-22-" data-element-type="container">
              <div id="container-id-9-" data-element-type="container">
                 <p id="text-id-49-" data-element-type="text">Getting things done starts with a list&nbsp;</p>
                 <p id="text-id-13-" data-element-type="text">Lamoid is a simple task list, time and productivity tracker that gets out of your way and lets you get things done</p>
              </div>
           </div>
        </div>
        <div id="container-id-21-" data-element-type="container">
           <div id="section-id-19-" data-element-type="section">
              <div id="container-id-20-" data-element-type="container">
                 <div id="container-id-1-" data-element-type="container">
                    <p id="text-id-2-" data-element-type="text">Add tasks to plan your day</p>
                    <p id="text-id-22-" data-element-type="text">Quickly add new tasks to keep track of what needs doing</p>
                 </div>
                 <a id="image-id-1-" data-element-type="image">
                   <v-img
                     id="element-id-4-"
                     src="/img/logo/tasks-no-notes-frame_chrome_mac_light.png"
                   />
                 </a>
              </div>
           </div>
           <div id="section-id-78-" data-element-type="section">
              <div id="container-id-75-" data-element-type="container">
                 <div id="container-id-76-" data-element-type="container">
                    <p id="text-id-1-" data-element-type="text">Track your time</p>
                    <p id="text-id-24-" data-element-type="text">Log time for each activity</p>
                 </div>
                 <a id="image-id-2-" data-element-type="image">
                   <v-img
                     id="element-id-5-"
                     src="/img/logo/start-timer.gif"
                   />
                 </a>
              </div>
           </div>
           <div id="section-id-20-" data-element-type="section">
              <div id="container-id-16-" data-element-type="container">
                 <div id="container-id-17-" data-element-type="container">
                    <p id="text-id-3-" data-element-type="text">Dashboards help you stay on top</p>
                    <p id="text-id-26-" data-element-type="text">Get insights into your productivity, measure how much time you spend on which activities and get a breakdown by categories</p>
                 </div>
                 <a id="image-id-3-" data-element-type="image">
                   <v-img
                     id="element-id-7-"
                     src="/img/logo/dashboard-frame_chrome_mac_light.png"
                   />
                 </a>
              </div>
           </div>
        </div>
        <div id="section-id-10-" data-element-type="section">
           <div id="container-id-11-" data-element-type="container">
              <div id="container-id-68-" data-element-type="container">
                 <div id="container-id-35-" data-element-type="container">
                    <p id="text-id-29-" data-element-type="text">Lamoid</p>
                    <div id="container-id-30-" data-element-type="container"><a id="button-id-31-" data-element-type="button" href="/"><span id="element-id-32-">Home</span></a></div>
                 </div>
                 <div id="container-id-12-" data-element-type="container">
                    <p id="text-id-16-" data-element-type="text">Product</p>
                    <div id="container-id-15-" data-element-type="container"><a id="button-id-38-" data-element-type="button" href="/teams"><span id="element-id-37-">Teams</span></a></div>
                 </div>
                 <div id="container-id-53-" data-element-type="container">
                    <p id="text-id-47-" data-element-type="text">Contact</p>
                    <div id="container-id-48-" data-element-type="container"><a id="button-id-44-" data-element-type="button" href="mailto:aspersieman@gmail.com"><span id="element-id-43-">Email</span></a><a id="button-id-48-" data-element-type="button" href="https://twitter.com/aspersieman" target="_blank"><span id="element-id-47-">Twitter</span></a></div>
                 </div>
              </div>
              <p id="text-id-70-" data-element-type="text">© {{ year }} Lamoid, Inc. All rights reserved. {{ version }}</p>
           </div>
        </div>
      </div>
      <home-header />
    </div>
  </v-app>
</template>
<script>
import HomeHeader from '@/modules/front/home/Header.vue';

export default {
  components: { 
    HomeHeader
  },
  data: () => ({
    year: window.moment().format('YYYY'),
    version: window.APP_GLOBALS.APP_VERSION,
  }),
}
</script>
<style scoped lang="scss">
@import "../../../../sass/home";
</style>
