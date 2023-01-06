<template>
    <div class="user-profile">
      <div class="loader" v-if="loading"></div>
        <h1 class="mt-4 title" >Profile Settings</h1>
        <div class="card details">
          <div class="card-body">
            <div>
              <div class="d-flex flex-column bd-highlight mb-3">
                <div class="d-flex justify-content-end mx-5">
                  <div class="d-flex align-items-center">
                    <input type="checkbox" id="password-change" class="mx-2" v-model="checkbox" @click="check">
                    <label for="password-change" class="mt-2">Change Password</label>
                  </div>
                </div>
            
                <div class="d-sm-flex justify-content-between profile-margin">
                  <div class="left mr-5" style="width: 100%;">
                    <div class="form-group">
                      <label for="name">Name</label>
                      <input
                        type="text"
                        class="form-control"
                        id="name"
                        aria-describedby="emailHelp"
                        placeholder="Enter Name"
                        v-model="this.user.name"
                        readonly
                      />
                    </div>
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        aria-describedby="emailHelp"
                        placeholder="Enter Email"
                        v-model="this.user.email"
                        readonly
                      />
                    </div>
                  </div>
              <div class="right" style="width: 100%;">
                <div v-cloak>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Password</label>
                      <input
                        :class='{valid: passwordValidation.valid}'  :type="passwordVisible ? 'text' : 'password'"
                        class="form-control"
                        id="exampleInputPassword1"
                        placeholder="Password"
                        v-model="user.password"
                        :disabled="checkbox == false"
                      />
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword2">Confirm Password</label>
                      <input
                        type="password"
                        class="form-control"
                        id="exampleInputPassword2"
                        placeholder="Confirm Password"
                        v-model.lazy="user.confirmPassword"
                        :disabled="checkbox == false"
                      />
                    </div>
                  </div>
                </div>
                </div>
                 <div class="errors mx-5 ">
                      <p v-if="errors.length" style="color: red;">
                        <ul >
                          <li v-for="(error,idx) in this.errors" :key="idx">{{error}}</li>
                        </ul>
                      </p>
                    </div>
                <transition name="hint" appear>
                    <div v-if='passwordValidation.error.length > 0 && !submitted' class='hints' >
                      <div class="row">
                        <p class="ml-3 p-1 error" v-for='(error,index) in passwordValidation.error' :key="index">{{error}}</p>
                      </div>
                    </div>
                  </transition>

            <button type="submit" class="btn submitBtn mx-5 mt-4 mx-auto"  @click.prevent="editUser" :disabled="checkbox==false">Save Changes</button>
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
                      <h4>Your Password has been reset. <h4></h4> Redirecting to Login Page!</h4>
                    </div>
                    <div align="center">
                      <!-- <input
                        type="button"
                        class="btn btn-primary"
                        style="margin-right: 10px;"
                        value="Click here to log In"
                        @click="logout()"
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
import { decryptData } from '../js/utils/encrypt';
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { deleteCookie, removeAllLocalStorage } from '../js/composables';
export default {
  data() {
    return {
      email: '',
      permissions: [],
      role: '',
      user: {
        id: '',
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
      },
      loading: false,
      errors: [],
      nameCheck: '',
      emailCheck: '',
      checkbox: false,
      employeeIdCheck: '',
      employees: [],
      loggedInUser: [],
      myModel: false,
          rules: [
				{ message:'> One lowercase required.', regex:/[a-z]+/ },
				{ message:'> 8 characters minimum.', regex:/.{8,}/ },
				{ message:'> One uppercase required.',  regex:/[A-Z]+/ },
				{ message:'> One number required.', regex:/[0-9]+/ },
        { message:'> One special character.', regex: /(?=.*?[#?!@$%^&*-])+/ },
			],
			password:'',
			passwordVisible:false,
			submitted:false,
    };
  },
  mounted() {
    this.loading = true;
    var loggedInUserArr = [];
    const secretKey = process.env.KEY || 'myscecretkey';
    this.email = decryptData(localStorage.getItem('Email'),secretKey).replace(/['"]+/g, '');
    let permissions = localStorage.getItem('Permissions');
    let role = localStorage.getItem('Role');
    this.permissions = decryptData(permissions, secretKey);
    this.role = decryptData(role, secretKey);

    if(this.email != 'admin@scala.com'){
      axios.get(this.$hostName + '/employees').then((response) => {
        response.data.forEach((element) => {
            this.employees.push(element);
            if(element[10]==this.email){
                loggedInUserArr.push(element);
            }
            
            
        });
        for (let k = 0; k < loggedInUserArr[0].length; k++) {
          this.loggedInUser.push(loggedInUserArr[0][k]);
        }
        this.employeeIdCheck = this.loggedInUser[1];
      })
      .catch((error) => {
        console.log(error);
      });
    }
    axios
      .get(this.$hostName + '/getUser')
      .then((response) => {
        this.user = response.data;
        this.nameCheck = response.data.name;
        this.emailCheck = response.data.email;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
      notSamePasswords() {
			if (this.passwordsFilled) {
				return this.user.password !== this.user.confirmPassword;
			} else {
				return false;
			}
		},
		passwordsFilled() {
			return this.user.password !== '' && this.user.confirmPassword !== '';
		},
		passwordValidation () {
			let error = [];
			for (let condition of this.rules) {
				if (!condition.regex.test(this.user.password) || this.user.password == undefined) {
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
    check() {
      this.checkbox = !this.checkbox;
    },
    editUser(e) {
      this.errors = [];
      if (
        this.user.name == '' ||
        this.user.email == '' ||
        this.user.password == undefined ||
        this.user.confirmPassword == undefined  
      ) {
        if (this.user.name == '') {
          this.errors.push('Name required.');
        }
        if (this.user.email == '') {
          this.errors.push('Email required.');
        }
        if (this.user.password == '' || this.user.password == undefined) {
          this.errors.push('Password required.');
        }
        if (
          this.user.confirmPassword == '' ||
          this.user.confirmPassword == undefined
        ) {
          this.errors.push('Password confirmation required.');
        }
        if (!this.errors.length) {
          return true;
        }
        e.preventDefault();
        return;
      }

      if (
        this.user.name != '' ||
        this.user.email != '' ||
        this.user.password != '' ||
        this.user.confirmPassword != ''
      ) {
        // if (this.user.password.length < 6) {
        //   this.errors.push('Minimum 6 characters required.');
        // }
        if(this.user.password != this.user.confirmPassword){
          //  if(this.user.password.length < 8 || this.user.password.search(/[0-9]/) < 0 || this.user.password.search(/.*[a-z]/) < 0 || this.user.password.search(/.*[A-Z]/) < 0 || this.user.password.search(/.*[!#$%&?@"]/) < 0){
          //  this.errors.push('Password rules does not satisfy') 
          //  }
           this.errors.push('Passwords do not match.'); 
          e.preventDefault();
          return;
        } else {
           if(this.user.password.length < 8 || this.user.password.search(/[0-9]/) < 0 || this.user.password.search(/.*[a-z]/) < 0 || this.user.password.search(/.*[A-Z]/) < 0 || this.user.password.search(/.*[!#$%&?@"]/) < 0){
           this.errors.push('Password rules does not satisfy'); 
            e.preventDefault();
          return;
           }
          
        }
         
        if (this.user.password != this.user.confirmPassword) {
          this.errors.push('Password and Confirm Password does not match');
          return;
        }
        if (this.user.email != '') {
          if (
            /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(this.user.email.trim())
          ) {
            // console.log('skip');
          } else {
            this.errors.push('Incorrect email format');
            return;
          }
        }
      }

      this.loading = true;
      axios
        .put(this.$hostName + '/changepassword/' + this.user.id, {
          newpassword: this.user.password,
          nameCheck: this.nameCheck
        })
        .then((response) => {
          if (response.status == 202) {
            this.loading = false;
            alert('New password cannot be the same as old password');
          } else {
            this.logout();
          }
        });
    },
    logout() {
      axios
        .post(this.$hostName+'/logout', null)
        .then(() => {
          this.myModel = true;
          // this.$router.push({ name: routeNames.login });
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        })
        .finally(() => {
          deleteCookie('token');
          removeAllLocalStorage();
          let intervalCache = '';
          intervalCache = setInterval(() => {
            clearInterval(intervalCache);
            this.$router.push({path: '/'});
          },2000);
        });
    },
  }
};
</script>
<style scoped lang="scss">
.submitBtn{
  background-color: #EE3234;
  color: #fff;
}
.submitBtn:hover{
  background-color: #a70f11;
  cursor: pointer;
  color: white;
}
.user-profile{
  font-family: 'Montserrat';
}
.title{
  position: relative;
  left: 60px;
}
[v-cloak] {
  opacity: 0.8;
}
.profile-margin{
  margin-left: 20px;
  margin-right: 20px;
}
.hints {
  padding: 0em;
  width: 57rem;
  font-weight: 700px;
  background: whitesmoke;
  margin: 0em 0;
  left: 0px;
  position: relative;
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
p{
 margin-bottom: 0rem !important;
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
.details {
  max-width: 950px;
  min-width: 300px;
  position: relative;
  left: 50%;
  transform: translate(-50%);
  margin-top: 20px;
  box-shadow: 0px 1px 7px 3px #ded9d9;
}
@media (min-width: 768px) and (max-width: 991.98px) { 
.details {
  width: 80% !important;
  left: 50% !important;
  transform: translate(-46%) !important;
}
 .hints {
  padding-left: 1em;
  width: 30rem !important;
  height: 5rem;
  font-weight: 700px;
  flex-grow: 0 !important;
  background: whitesmoke;
  margin: 1em 2em;
  position: relative;
  font-size: 0.8em;
  color: darken(#d4dedf, 50%);
  p {
    padding-left: 1em;
    // margin: 50px !important;
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

@media only screen and (max-width: 575.98px) {
  .modal-content{
    width: 100% !important;
  }
.details {
  min-width: 300px;
  width: 80%;
  position: relative;
  right: -60px !important;
  margin-left: 30px;
  margin-top: 20px;
  box-shadow: 0px 1px 7px 3px #ded9d9;
}
 .hints {
  padding-left: 1em;
  width: 16rem !important;
  height: 9rem;
  font-weight: 700px;
  flex-grow: 0 !important;
  background: whitesmoke;
  margin: 0em 0;
  position: relative;
  font-size: 0.8em;
  color: darken(#d4dedf, 50%);
  p {
    padding-left: 1em;
    // margin: 50px !important;
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