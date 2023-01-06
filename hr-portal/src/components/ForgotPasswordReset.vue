<template>
  <div class="login-user my-5">
    <div class="resetLoader" v-if="loading"></div>
    <div class="">
    <div class="container h-100">
      <div class="d-flex justify-content-center align-items-center h-100">
        <div class="">
          <div class="card" style="">
           <div class="">
               <!-- <div class="logo-container">
                  <img class="logo scala" :src="scalaimg" />
                </div>-->
              
                <div class="logo-container">
                  <div class="resetText py-4">Reset Password</div>
                </div>


             </div>
            <div class="row">
              <div class="col-sm-12">
              <div class="card-body px-4 py-1" style="margin-top: 5%;">
                 <!-- <div class="logo-container text-center mb-1">
                  <img class="logo scala" :src="logo" />
                </div>
                <div class="fade_rule mb-5"></div>  -->

              <form @submit.prevent="submit">
                <div class="mb-4 row">
                  <label for="form3Example1cg" class="col-sm-5 col-form-label font-weight-bold">Email</label>
                  <div class="col-sm-7">
                    <div class="email-holder">{{form.email}}</div>
                    <!-- <input type="email" id="form3Example1cg" class="form-control" placeholder="Enter Email" v-model="form.email" disabled/> -->
                   <div class="" style="color: red;" v-if="emailError">Please provide valid email</div>
                  </div>
                </div>
                <div  v-cloak>
                <div class="mb-4 row ">
                  <label for="form3Example3cg" class="col-sm-5 col-form-label font-weight-bold">New Password</label>
                  <div class="col-sm-7">
                    <input  id="form3Example3cg" class="form-control"   :class='{valid: passwordValidation.valid}' :type="passwordVisible ? 'text' : 'password'" placeholder="New Password" v-model="form.newPassword" required/>
                  <div class="" style="color: red;" v-if="passwordError">Please provide valid password</div>
                  </div>
                </div>
              
                <div class="mb-2 row">
                  <label for="form3Example1cg" class="col-sm-5 col-form-label font-weight-bold">Confirm Password</label>
                  <div class="col-sm-7">
                    <input type="Password"  id="form3Example4cg" class="form-control" placeholder="Confirm Password" v-model.lazy="form.confirmPassword" required/>
                   <div class="" style="color: red;" v-if="passwordError">Please provide valid password</div>
                  </div>
                </div>

                 <div class="errors">
                  <p v-if="errors.length" style="color: red;">
                    <ul >
                      <li v-for="(error,idx) in this.errors" :key="idx">{{error}}</li>
                    </ul>
                  </p>
                </div>

                  <transition name="hint" appear>
                    <div v-if='passwordValidation.error.length > 0 && !submitted' class='hints mt-3' >
                      <div class="pl-2 row">
                        <label class="ml-2" v-for='(error,index) in passwordValidation.error' :key="index">{{error}}</label>
                      </div>
                    </div>
                  </transition>
                </div>
             
                
                <div class="d-flex justify-content-center pt-4">
                  <button @click.prevent="resetPassword" type="submit" class="btn btn-lg text-white login-btn font-weight-bold">Save</button>
                </div>
                <br>
              </form>
            </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Pop up modal -->
  <div v-if="myModel">
      <transition name="model">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <!-- <button type="button" class="close" @click="myModel = false">
                    <span aria-hidden="true">&times;</span>
                  </button> -->
                </div>
                <div class="modal-body">
                  <div class="confirmation pb-4" style="text-align: center">
                    <h4>Your Password has been reset. <h4></h4> Please login again to continue!</h4>
                  </div>
                  <div align="center">
                    <input
                      type="button"
                      class="btn btn-primary"
                      style="margin-right: 10px;"
                      value="Click here to log In"
                      @click="goToRoute(routeNames.login)"
                    />
                    <!-- <input
                      type="button"
                      class="btn btn-danger"
                      style="margin-left: 10px; width: 100px"
                      value="Cancel"
                      @click="myModel = false"
                    /> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import loginimage from '../assets/Stratacachelogo.png';
import scalalogo from '../assets/scalalogo.png';
import { decryptData } from '../js/utils/encrypt';

export default {
  data() {
    return {
      form: {
        email: '',
        newPassword: '',
        confirmPassword: '',
      },
      errors: [],
      passwordError: false,
      emailError: false,
      isAuthenticated: true,
      loading: false,
      token: '',
      permissions: [],
      role: '',
      logo: loginimage,
      scalaimg: scalalogo,
      
      email: '',
      user: {
        id: ''
      },
      myModel: false,
      rules: [
				{ message:'> One lowercase required.', regex:/[a-z]+/ },
        { message:'> One special character.', regex: /(?=.*?[#?!@$%^&*-])+/ },
				{ message:'> One uppercase required.',  regex:/[A-Z]+/ },
				{ message:'> 8 characters minimum.', regex:/.{8,}/ },
				{ message:'> One number required.', regex:/[0-9]+/ },
			],
			password:'',
			passwordVisible:false,
			submitted:false,
    };
  },
  mounted() {
    
    const secretKey = process.env.KEY || 'myscecretkey';

    //check for forgot password
    const token = decryptData(decodeURIComponent(this.$route.params.token), secretKey);
    const email = decryptData(decodeURIComponent(this.$route.params.email), secretKey);
    this.form.email = email;
    const currentTime = Date.now();
    const timeDifferenceInMin = Math.round((currentTime - token)/60000);
    let expiry_time = process.env.VUE_APP_LINK_EXPIRY_TIME;
    
    if(timeDifferenceInMin>expiry_time){
      this.goToRoute(routeNames.linkexpired);
    }

  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
   notSamePasswords() {
			if (this.passwordsFilled) {
				return this.form.newPassword !== this.form.confirmPassword;
			} else {
				return false;
			}
		},
		passwordsFilled() {
			return this.form.newPassword !== '' && this.form.confirmPassword !== '';
		},
		passwordValidation () {
			let error = [];
			for (let condition of this.rules) {
				if (!condition.regex.test(this.form.newPassword)) {
					error.push(condition.message);
				}
			}
			if (error.length == 0) {
				return { valid:true, error };
			} else {
				return { valid:false, error };
			}
		}
  },
  methods: {
    resetPasswords () {
			this.submitted = true;
			setTimeout(() => {
				this.submitted = false;
			}, 2000);
		},
		togglePasswordVisibility () {
			this.passwordVisible = !this.passwordVisible;
		}	,
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
    resetPassword(e){
      this.submitted = true;
			setTimeout(() => {
				this.submitted = false;
			}, 2000);
      this.errors = [];
      if (this.form.email == '' || this.form.newPassword == '' || this.form.confirmPassword == '') {
        if (!this.errors.length) {
          return true;
        }
        e.preventDefault();
        return;
      } 
    
      if( this.form.newPassword != '' && this.form.confirmPassword != ''){
        
        if(this.form.newPassword != this.form.confirmPassword){
           this.errors.push('Passwords do not match.'); 
          e.preventDefault();
          return;
        } else {
           if(this.form.newPassword.length < 8 || this.form.newPassword.search(/[0-9]/) < 0 || this.form.newPassword.search(/.*[a-z]/) < 0 || this.form.newPassword.search(/.*[A-Z]/) < 0 || this.form.newPassword.search(/.*[!#$%&?@"]/) < 0)
           {
           this.errors.push('Password rules does not satisfy'); 
            e.preventDefault();
          return;
           }
         
        }
      }
      
      this.loading = true;
      axios.put(this.$hostName + '/forgotPasswordReset',{
        email: this.form.email,
        new_password: this.form.newPassword,
        password_confirmation : this.form.confirmPassword,
      }).then(response =>{
        console.log('response',response);
        this.myModel = true;
      }).catch((error) => {
        console.log(error);
      });
    },
  },
};
</script>
<style scoped>
.login-user{
  font-family: 'Montserrat';
}
[v-cloak] {
  opacity: 0.8;
}
.hints {
  padding: 0em;
  font-weight: 700px;
  background: whitesmoke;
  margin: 0em 0;
  font-size: 0.8em;
  color: darken(#d4dedf, 50%);
  p {
    padding-left: 1em;
    &::before {
      content: ">" !important;
      font-size: 0.9em;
    }
  }
}
input, button {
  padding:6px;
}
.hint {
  &-enter {
    opacity: 0;
    transform: translate3d(-20px, 0, 0);
  }
  &-leave-to {
    opacity: 0;
    transform: translate3d(0, 20px, 0);
  }
  
}
.email-holder{
  border: solid 1px #CED4DA;
  height: 38px;
  padding-left: 10px;
  padding-top: 5px;
  background-color: #E9ECEF;
  border-radius: 5px;

}
.resetText{
  color: #fff;
  font-size: 25px;
  text-align: center;
}
.logo-container{
  background-color: #EE3234;
  width: 100%;
  height: 100%;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}

.regis:hover {
  cursor: pointer;
}
.btn:focus {
  outline: none !important;
  box-shadow: none !important;
}
.login-btn {
  border-radius: 30px;
  width: 150px;
  position: relative;
  background-color: #EE3234;

}
.btn-success:hover {
  color: #fff;
  background-color: #5e72e4;
  border-color: #5e72e4;
  -webkit-transform: translateY(-1px);
}

.container {
  position: absolute;
  left: 0;
  right: 60px;
  top: 0;
  bottom: 0;
  width: 1140px; /* Assign a value */
  height: 420px; /* Assign a value */
  margin: auto;
  padding: 0;
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
   width:39rem;
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
/*position: relative;*/
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
  .resetLoader{
    background-position: calc(calc(50%)) !important;
  }
  .modal-content{
    width: 100% !important;
  }
  .card{
    width: 23rem;
    height: 36rem;
    position: relative;
    left: -14.5%;
    padding: -20%;
    padding-left: -9%;
  }
 .container {
    position: absolute;
    right: 0px;
    top: 0;
    width: 500px !important;
    bottom: 0;
    z-index: 998;
  }
  .hints {
    padding-left: 0em;
    font-weight: 700px;
    background: whitesmoke;
    margin: 0em 0;
    font-size: 0.8em;
    color: darken(#d4dedf, 50%);
    p {
      padding-left: 1em;
      &::before {
        content: ">" !important;
        font-size: 0.9em;
      }
    }
  }
  input, button {
    padding:6px;
  }
  .hint {
    &-enter {
      opacity: 0;
      transform: translate3d(-20px, 0, 0);
    }
    &-leave-to {
      opacity: 0;
      transform: translate3d(0, 20px, 0);
    }
    
  }
}
</style>