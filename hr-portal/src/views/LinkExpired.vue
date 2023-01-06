<template>
<div class="login-user my-5">
    <div class="resetLoader" v-if="loading"></div>
        <div class="container h-100">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="">
                <div class="card">
                    <div class="">
                        <div class="logo-container mt-5">
                          <img class="closeBtn" :src="linkexpired" />
                        </div>
                    </div>
                        <div class="row">
                         <div class="col-lg-12 text-center"> 
                           <div class="font-weight-bold linkName">Link Expired</div>
                           <div class="resetText">To reset your password, return to the login page and select "Forgot Password" to send a new email.</div>
                         </div>
                         <div class="log-in">
                            <input
                              type="button"
                              class="btn btn-primary"
                              style="margin-right: 10px;"
                              value="Click here to log In"
                              @click="goToRoute(routeNames.login)"
                            />
                          </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
import linkexpired from '../assets/linkexpired.png';
import { routeNames } from '../js/routeNames';
export default {
  data() {
    return {
      linkexpired: linkexpired,
    };
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
  },
  methods: {
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
  }
};
</script>
<style scoped>
.log-in{
  width: 100%;
  padding: 10px 15px 0 15px;
  text-align: center;
}
.linkName {
    font-size: 30px;
    margin: 20px 0px 20px 0px;
}
.resetText{
  font-size: 20px;
  padding: 0px 50px 0px 50px;
}
.logo-container{
  width: 100%;
  height: 100%;
  text-align: center;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}
.closeBtn{
    width: 10vh;
    height: 10vh;
}

.container {
  position: absolute;
  left: 0;
  right: 60px;
  top: 0;
  bottom: 0;
  width: 1140px; /* Assign a value */
  height: 400px; /* Assign a value */
  margin: auto;
}
.login-card {
  width: 40rem;
  height: 35rem;
  opacity: 0.8;
  border-top-left-radius: 0px;
  border-bottom-left-radius: 0px;
  
}
.card{
   box-shadow: 0px 2px 22px grey;
   width: 32rem;
   height: 26rem;
   border-radius: 15px;
}
.fade_rule {
        height: 4px;
        border-radius: 5px;
        width: 10rem;
        text-align: center;
        left: 35%;
        position: relative;
        background-color: #E6E6E6;
        background-image: -webkit-gradient( linear, left bottom, right bottom, color-stop(0.01, grey),color-stop(0.8, white) );
}
.errors{
    position: absolute;
    top: 205px;
    left: 40px;
}
.resetLoader{
  position: fixed;
  top: 0px;
  left: 0px;
  width: calc(100% - 0px);
  height: 100%;
  background-color: #eceaea;
  background-image: url(/img/loading.50d8d981.gif);
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
@media only screen and (max-width: 575.98px) {
  .card{
    width: 23rem;
    height: 30rem;
    position: relative;
    left: -14.5% !important;
    padding: -20%;
    padding-left: -9%;
  }
 .container {
  position: absolute;
  right: 0px;
  top: 0;
  width: 500px !important;
  bottom: 0;
  z-index: 999;
}
}
</style>