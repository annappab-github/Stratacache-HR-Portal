<template>
  <div id="app">
    <div class="content" >
      <!-- <div class="views" :class="{ 'nav-push': navPush }"> -->
      <div class="views new-pos" id="views">
        <router-view @showNavigationBar="checkAthentication" @leaveNotificationCount="leaveNotificationCount"/>
      </div>
      <transition name="slide-fade">
        <Navigation v-if="isAuthenticated==true" @navOpen= "navOpen" @checkAthentication="checkAthentication"/>
      </transition>
    </div>
  </div>
</template>

<script>
import Navigation from './components/Navigation.vue';
import { routeNames } from './js/routeNames';
import { getCookie } from './js/composables';
import { computed } from 'vue';
require('./styles/main.scss');

export default {
  components: {
    Navigation,
  },
  computed: {
    routeNames: () => { return routeNames; }
  },
  provide() {     
    return {      
        leaveCount: computed(() => this.notificationCount)    
         };   
  },

  data() {
    return{
      isAuthenticated : false,
      lang: 'en',
      navPush: false,
      notificationCount: 0
    };  
  },
  mounted () {
    this.checkAthentication();
  },
  methods: {
    navOpen(event){
      this.navPush = event;
    },
    checkAthentication() {
      this.isAuthenticated = (getCookie('token') != '' ? true : false);

    },
    leaveNotificationCount(leaveCount){
      this.notificationCount = leaveCount;
    }
  }
};
</script>

<style lang='scss' scoped>
@font-face {
  font-family: "Montserrat";
  src: local("Montserrat"),
   url(./fonts/Montserrat/Montserrat-VariableFont_wght.ttf) format("truetype");
}

@media only screen and (max-width: 575.98px) {
#views {
      overflow: auto !important;
      width: 100% !important;
      height: 100% !important;
      top: 0 !important;
      // left:21px !important;
      position: initial !important;
      // margin-left: 15% !important;
      transition: all 300ms ease-in-out !important;
    }

}
.new-pos{
  left: 0 !important;
  width: 100% !important;
}

#app {
  max-width: $max-width;
  max-height: $max-height;
  width: $max-width;
  height: $max-height;
  top: 0;
  left: 0;
  position: absolute;
  // background-color: #fff;
  background-color: #fff;

  font-family: $text-font-stack;
  font-weight: 500;
  color: $general-black-light;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;

  // overflow: hidden;

  .content {
    width: 100%;
    height: 100%;
    position: relative;
    float: left;

    display: flex;

    .views {
      overflow: auto;
      scrollbar-width: none;
      width: calc(100% - 60px);
      height: 100%;
      // position: absolute;
      top: 0;
      left: 60px;
      padding: 0;
      transition: all 300ms ease-in-out;

      &.nav-push{
        position: absolute;
        width: calc(100% - 200px);
        left: 200px !important;
      }
    }
    
  }
  
  /* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
  transition: all .5s ease;
}
.slide-fade-leave-active {
  transition: all .9s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(-10px);
  opacity: 0;
}
}
@import './styles/bootstrap/bootstrap.css';
@import './styles/general/style.css';
</style>