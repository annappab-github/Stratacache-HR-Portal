<template>
<div class=""  v-show="hideLoginPage" style="overflow:hidden !important">
  <div class="portal-bg">
    <div class="portal-text"></div>
  </div>
  <div class="login-user my-5" style="overflow:hidden !important">
    <div class="loginLoader" v-if="loading"></div>
    <div class="mx-0 px-0 " style="overflow:hidden !important">
    <div class="container h-100">
      <div class="row d-flex card-holder">
        <div class="">
          <div class="card" style="">
           <div class="ml-4 mt-2">
             </div>
            <div class="row card-text">
              <div class="col-sm-12">
                <div class="card-body px-5 py-1" style="margin-top: 4%;">
                <div class="logo-container text-center mb-2">
                  <img class="logo scala" :src="logo" />
                </div>
                <div class="mb-5"></div>  

              <form @submit.prevent="submit" v-if="!showForgotPassword">
                <div class="mb-4 row">
                  <label for="form3Example1cg" class="col-sm-3 col-form-label font-weight-bold">Email</label>
                  <div class="col-sm-9">
                    <input type="email" id="form3Example1cg" class="form-control" placeholder="Email" v-model="form.email" required/>
                   <div class="" style="color: red;" v-if="emailError">Please provide email</div>
                  </div>
                </div>
                <div class="mb-5 row ">
                  <label for="form3Example3cg" class="col-sm-3 col-form-label font-weight-bold">Password</label>
                  <div class="col-sm-9">
                    <input type="password" id="form3Example3cg" class="form-control" placeholder="Password" v-model="form.password" required/>
                   <div class="" style="color: red;" v-if="passwordError">Please provide password</div>
                  </div>
                </div>

               <div class="errors">
                  <p v-if="errors.length" style="color: red;">
                    <ul >
                      <li v-for="(error,idx) in this.errors" :key="idx">{{error}}</li>
                    </ul>
                  </p>
                </div>
                
                <div class="d-flex justify-content-center pt-3">
                  <button @click="loginUser"  type="submit" class="btn btn-lg text-white login-btn font-weight-bold">Login</button>
                </div>
                <!-- <br> -->
                <div class="forgot-password">
                   <label class="forgot-password-text" @click="forgotPassword(0)">Forgot Password?</label>
                </div>
                  <div class="blower text-center" @click="openWhistleBlower(true)">
                      <img class="whistleImg" :src="mailingImg" />
                      <div class="whistle-text" >whistle Blower</div>
                  </div>
              </form>

              <form @submit.prevent="submit" v-else>
               <div class="resetLoader" v-if="loading1"></div>
                <!-- <div class="resetLoaderText" v-if="loading1"> 
                  <label class="loaderText"> Kindly please wait</label>
                </div>-->
                <div class="mb-4 row" v-if="myModel == false">
                  <label for="form3Example1cg" class="col-sm-3 col-form-label font-weight-bold">Email</label>
                  <div class="col-sm-9">
                    <input type="email" id="form3Example1cg" class="form-control" placeholder="Email" v-model="resetForm.email" required/>
                   <div class="" style="color: red;" v-if="emailError">Please provide email</div>
                  </div>
                </div>
                 <!-- Pop up modal -->
                <div class="d-flex justify-content-center pt-3" v-if="myModel == false">
                  <button @click.prevent="requestResetPassword" type="submit" class="btn btn-lg text-white login-btn font-weight-bold">Reset</button>
                </div>
                 <div v-if="myModel">
                  <div class="confirmation pb-4" style="text-align: center">
                    <label>Please check your email and click on the provided link to reset your password.</label>
                  </div>
                 </div>
                <div class="forgot-password">
                  <label class="forgot-password-text" @click="forgotPassword(1)">Click here to login</label>
                </div>
              </form>

                <div class="errors1">
                  <p v-if="errors1.length" style="color: red;">
                    <ul >
                      <li v-for="(error,idx) in this.errors1" :key="idx">{{error}}</li>
                    </ul>
                  </p>
                </div>
            </div>
              </div>
            </div>
            
            
           
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
    <WhistleBlower v-if="whistleBlower"  @openWhistleBlower="openWhistleBlower"/>
</template>
<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { encryptData } from '../js/utils/encrypt';
import loginimage from '../assets/Stratacachelogo.png';
import scalalogo from '../assets/scalalogo.png';
import WhistleBlower from './WhistleBlower.vue';
import mailingImg from '../assets/mailing.gif';
import { setCookie } from '../js/composables';
import Swal from 'sweetalert2/dist/sweetalert2.js';

export default {
  components: {
    WhistleBlower
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      resetForm: {
        email: '',
      },
      mailDetail: {
        from: 'notifications@theportal.scala-apac.org',
        to: '',
        subject: 'Link to generate new password',
        type: 'Reset Password Notification',
        message: '',
      },
      whistleBlower : false,
      errors: [],
      errors1: [],
      emailError: false,
      passwordError: false,
      loading: false,
      permissions: [],
      role: '',
      loading1: false,
      logo: loginimage,
      scalaimg: scalalogo,
      employeedID: '',
      force_change_password: false,
      showForgotPassword: false,
      myModel: false,
      hideLoginPage: true,
      mailingImg: mailingImg
    };
  },
  mounted() {
    this.$emit('showNavigationBar');
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
	
  },
  methods: {
    openWhistleBlower(val){
      if(val) {
        this.whistleBlower = true;
        this.hideLoginPage = false;
      } else {
        this.whistleBlower = false;
        this.hideLoginPage = true;
      }
    },
    requestResetPassword(){
      //Forgot password changes
      this.errors1 = [];
      if (this.resetForm.email == '') {
        this.errors1.push('Email required.');
        if (!this.errors1.length) {
          return true;
        }
        return;
      }
      const secretKey = process.env.KEY || 'myscecretkey';
      const encryptedData = encryptData(Date.now(), secretKey);
      const encryptedEmail = encryptData(this.resetForm.email, secretKey);
      const url = location.href+'reset-password/'+encodeURIComponent(encryptedData)+'/'+encodeURIComponent(encryptedEmail);
      this.mailDetail.message = url;
    
      let formData = new FormData();
      this.mailDetail.to = this.resetForm.email;
      formData.append('to', this.mailDetail.to);
      formData.append('subject', this.mailDetail.subject);
      formData.append('message', this.mailDetail.message);
      formData.append('from', this.mailDetail.from);
      formData.append('type', this.mailDetail.type);
      this.loading1 = true;
      axios
        .post(this.$hostName + '/forgotPassword', formData, {headers : {'Content-Type': 'multipart/form-data'}})
        .then((response) => {
          if(response.status == 200){
            console.log(response+'Email sent successfully!!');
            this.myModel = true;
          } else if(response.status == 203) {
            Swal.fire({
              text: response.data,
              confirmButtonText: 'Ok',
            });
            return;
          } 
        setTimeout(() => {
            this.clearFormData();
             this.myModel = true;
        }, 2000);
        })
      .catch((error) => {
        console.log(error);
        this.errors1 = error.response.data.errors;
        Swal.fire({
          text: 'Something went wrong. Please try again later.', 
          confirmButtonText: 'Ok',
        });
        setTimeout(() => {
            this.clearFormData();
        }, 2000);
      })
      .finally(() => {
        this.loading1 = false;
      });
      
      axios.post(this.$$hostName + '/reset-password', {email: this.resetForm.email}).then(result => {
          this.response = result.data;
      }, error => {
          console.error(error);
      });

    },
    clearFormData(){
      this.resetForm.email = '';
    },
    loginUser(e) {
      this.errors = [];
      if (this.form.email == '' || this.form.password == '') {
        if (this.form.email == '') {
          this.errors.push('Email required.');
        }
        if (this.form.password == '') {
          this.errors.push('Password required.');
        }
        if (!this.errors.length) {
          return true;
        }
        e.preventDefault();
        return;
      }
      this.loading = true;
      axios
        .post(this.$hostName + '/login', this.form)
        .then((response) => {
          if(response.status == 201) {            
            const secretKey = process.env.KEY || 'myscecretkey';
            let token = response.data.data.token;
            const encryptedTokenData = encryptData(token, secretKey);
  
            if(response.data.data.user.force_change_password == 0) {
              setCookie('pwdResetToken', encryptedTokenData, 1);
              this.$router.push({ name: routeNames.passwordChange });
            } else {            
              setCookie('token', encryptedTokenData, 1);
              let data = Object.keys(response.data.data.permissions).map((key) => {
                return response.data.data.permissions[key].name;
              });
    
              this.permissions = data;
              this.role = response.data.data.roles[0];
              var email = JSON.stringify(response.data.data.user.email);
    
              const encryptedEmailID = encryptData(email, secretKey);
              const encryptedPermissionData = encryptData(this.permissions,secretKey);
              const encryptedRoleData = encryptData(this.role, secretKey);
              localStorage.setItem('Email', encryptedEmailID);
              localStorage.setItem('Permissions', encryptedPermissionData);
              localStorage.setItem('Role', encryptedRoleData);
    
              var empID = response.data.data.user.employee_id;
              const emcryptedEmpID = encryptData(empID, secretKey);
              localStorage.setItem('EmployeeID', emcryptedEmpID);
              this.$router.push({ name: routeNames.home });
            }
          } else if(response.status == 202) {
            this.errors = [response.data];
          }
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        })
        .finally(() => {
          if (this.errors.email) {
            this.loading = false;
            this.errors = [this.errors.email[0]];
            return;
          } else {
            this.loading = false;
            // location.reload();
          }
        });

    },
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
    forgotPassword(num){
      if (num == 0) {
        this.showForgotPassword = true;
        this.myModel = false;
        this.errors=[];
        this.errors1=[];
      } else {
        this.showForgotPassword = false;
        this.errors=[];
        this.errors1=[];
      }
    }
  },
};
</script>
<style scoped>
.login-user {
 
}
.blower{
  position: absolute;
  left: 84%;
  top: 320px;
  transform: translate(-50%);
}
.whistleImg{
  width: 40px;
  height: 40px;
  cursor: pointer;
}

.whistle-text{
   color: black;
   width: 120px;
}
.whistle-text:hover{
  cursor: pointer;
  color: #EE3234;
  border-bottom: 1px solid #EE3234;
}
.root{
  position: absolute;
  top: -50px;
}
.card-text{
  font-family: 'Montserrat';
}
.resetLoader{
  width: 36rem;
  height: 26rem;
  background-color: #eceaea;
  background-image: url(/img/loading.50d8d981.gif);
  background-size: 120px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 0px);
  z-index: 999;
  top: -17px; 
  left: 14px;
  position: absolute;
  border-radius: 20px;
}
.resetLoaderText{
  width: 34rem;
  height: 26rem;
  background-color: #eceaea;
  background-size: 120px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 0px);
  z-index: 999;
  top: -40px; 
  left: 14px;
  position: absolute;
  text-align: center;
}
.confirmation{
  font-size: 22px;
}
.loaderText{
  font-size: 30px;
  top: 180px;
  position: relative;
  letter-spacing: 0px;
}
.forgot-password{
  position: absolute;
  left: 50%;
  transform: translate(-50%);
  bottom: -90px;
}
.forgot-password-text{
  color: black;

}
.forgot-password-text:hover{
  cursor: pointer;
  color: #EE3234;
  border-bottom: 1px solid #EE3234;
}

.errors{
    position: absolute;
    top: 205px;
    left: 35px;
}
.errors1{
    position: absolute;
    top: 145px;
    left: 35px;
}
.regis:hover {
  cursor: pointer;
}
.btn:focus {
  outline: none !important;
  box-shadow: none !important;
}
.login-btn {
  border-radius: 10px;
  width: 150px;
  position: absolute;
  background-color: #EE3234;
  /* bottom: -10px; */
}
.btn-success:hover {
  color: #fff;
  background-color: #f35b5d;
  border-color: #f35b5d;
  -webkit-transform: translateY(-1px);
}

.container {
  position: absolute;
  left: 60px;
  right: 60px;
  top: 0;
  bottom: 0;
  width: 1140px; /* Assign a value */
  height: 420px; /* Assign a value */
  margin: auto;
  overflow: hidden;
}
.login-card {
  width: 42rem;
  height: 35rem;
  opacity: 0.8;
  border-top-left-radius: 0px;
  border-bottom-left-radius: 0px;
}
.card{
   box-shadow: 0px 2px 22px grey;
   width:36rem;
   height:26rem;
   border-radius: 20px;
}
.loginLoader{
  position: fixed;
  top: 0px;
  left: 0px;
  width: calc(100% - 0px);
  height: 100%;
  background-color: #eceaea;
  background-image: url(/HR_Portal/img/loading.50d8d981.gif);
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.portal-bg{
  background-image: url("../assets/Portal_Bg.png");
  height: 100%;
  width: 100%;
  position: fixed;
  top: 0px;
  background-size: cover;
  overflow:hidden;
}
.portal-text{
  background-image: url("../assets/Portal_Bg_Text.png");
  width: 650px;
  height: 300px;
  position: absolute;
  left: 50%;
  transform: translate(-50%);
  top: 3%;
}
.card-holder{
  position: absolute;
  left: 50%;
  transform: translate(-50%);
  top: 40vh;
}
@media screen and (max-width: 1300px) {
  .portal-text{
    width: 450px;
    height: 200px;
    background-size: cover;
    top: 10px;
  }
  .card{
    width:32rem;
    height:23.5rem;
  }
  .blower{
    top: 300px;
  }
}
@media only screen and (max-width: 575.98px) {
  
.justify-content-center{
  padding-top: 0px !important;
}


.blower{
  position: absolute;
  left: 78%;
  top: 390px;
  transform: translate(-50%);
}
.whistleImg{
  width: 40px;
  height: 40px;
  cursor: pointer;
}

.whistle-text{
   color: black;
   width: 120px;
}
.whistle-text:hover{
  cursor: pointer;
  color: #EE3234;
  border-bottom: 1px solid #EE3234;
}
  .resetLoader{
    width: 20rem !important;
    height: 31rem;
  }
  .loginLoader{
    background-position: calc(calc(50%)) !important;
  }
  .errors{
    top: 282px !important;
    left: 20px !important;
  }
  .card{
    width: 20rem;
    height: 30rem;
    position: absolute;
  left: 50%;
  transform: translate(-50%);                          /* changed from -26.5% to 0 */
    top: -100px;                           /* from -70px to -100px */
  }
 .container {
    padding: 0;
    margin: none;
    width: 100% !important;
    bottom: 0;
    z-index: 998;
    position: absolute;
    left: 53%;
    transform: translate(-50%, 0);
    overflow-x: hidden !important;
  }
  .portal-text{
    width: 250px;
    height: 100px;
    background-size: cover;
    top: 30px;            /* from 130 to 30px */
  }
}
</style>