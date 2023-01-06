<template>
  <div class="register-user my-4"> 
    <div class="loader" v-if="loading"></div>
    <div class="">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="">
          <div class="card" style="border-radius: 15px; width:80rem;height:35rem;border-radius: 15px;">
            <div class="row">
              <div class="col-6">
                <img class="register-card" :src="logo" alt="">
              </div>
              <div class="col-6">
              <div class="card-body px-5 py-2" style="margin-top: 8%;">
              <h2 class="font-weight-bold text-center mt-4 mb-5">{{ t('register.title', {}, {locale: lang})}}</h2>

              <form>
                <div class="mb-4 row">
                  <label for="form3Example1cg" class="col-sm-4 col-form-label">{{ t('register.name', {}, {locale: lang})}}</label>
                  <span class="w-full text-red-500" v-if="errors.name">{{errors.name[0]}}</span>
                  <div class="col-sm-8">
                    <input type="text" id="form3Example1cg" class="form-control" v-model="form.name"/>
                  </div>
                </div>
                <div class="mb-4 row">
                  <label for="form3Example2cg" class="col-sm-4 col-form-label">{{ t('register.email', {}, {locale: lang})}}</label>
                  <div class="col-sm-8">
                    <input type="email" id="form3Example2cg" class="form-control" v-model="form.email"/>
                  </div>
                </div>
                <div class="mb-4 row">
                  <label for="form3Example4cg" class="col-sm-4 col-form-label">Employee ID:</label>
                  <div class="col-sm-8">
                    <input type="text" id="form3Example4cg" class="form-control" v-model="form.employeeId"/>
                  </div>
                </div>
                <div class="mb-4 row">
                  <label for="form3Example3cg" class="col-sm-4 col-form-label">{{ t('register.password', {}, {locale: lang})}}</label>
                  <div class="col-sm-8">
                    <input type="password" id="form3Example3cg" class="form-control" v-model="form.password" name="password"/>
                  </div>
                </div>
                <div class="mb-4 row">
                  <label for="form3Example4cg" class="col-sm-4 col-form-label">{{ t('register.confirmPassword', {}, {locale: lang})}}</label>
                  <div class="col-sm-8">
                    <input type="password" id="form3Example4cg" class="form-control" v-model="form.password_confirmation" name="password_confirmation"/>
                  </div>
                </div>

              <div class="errors mb-2">
                  <p v-if="errors.length" style="color: red;">
                    <ul >
                      <li v-for="(error,idx) in this.errors" :key="idx">{{error}}</li>
                    </ul>
                  </p>
                </div>
                <span v-if="msg.email">{{msg.email}}</span>
                <div class="d-flex justify-content-center">
                  <button @click.prevent="saveForm" type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-white register-btn">{{ t('register.btnRegister', {}, {locale: lang})}}</button>
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
  </div>
</template>
<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { useI18n } from 'vue-i18n';
import registerimage from '../assets/registerbg1.jpg';

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        employeeId: '',
        password: '',
        password_confirmation: '',
      },
      errors: [],
      loading: false,
      msg: [],
      logo: registerimage
    };
  },
  setup() {
    const { t, locale } = useI18n();
    return { t, locale, fallbackWarn: false };
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
  },
  mounted() {
  },
  methods: {
    saveForm(e) {
      this.errors = [];
      if (
        this.form.name == '' ||
        this.form.email == '' ||
        this.form.employeeId == '' ||
        this.form.password == '' ||
        this.form.password_confirmation == ''
      ) {
        if (this.form.name == '') {
          this.errors.push('Name required.');
        }
        if (this.form.email == '') {
          this.errors.push('Email required.');
        }
        if (this.form.employeeId == '') {
          this.errors.push('Employee ID required.');
        }
        if (this.form.password == '') {
          this.errors.push('Password required.');
        }
        if (this.form.password_confirmation == '') {
          this.errors.push('Password confirmation required.');
        }
        if (!this.errors.length) {
          return true;
        }
        e.preventDefault();
        return;
      }

      if (
        this.form.name != '' ||
        this.form.email != '' ||
        this.form.password != '' ||
        this.form.password_confirmation != ''
      ) {
        if (this.form.password.length < 6) {
          this.errors.push('Minimum 6 characters required.');
          return;
        }
        if (this.form.password != this.form.password_confirmation) {
          this.errors.push('Password and Confirm Password does not match');
          return;
        }
        if (this.form.email != '') {
          if (
            /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(this.form.email.trim())
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
        .post(this.$hostName + '/register', this.form)
        .then((response) => {
          if (response.status == 202) {
            this.loading = false;
            this.$alert('User already exists');
          } else if (response.status == 203) {
            this.loading = false;
            this.$alert('Email already exists');
          } else {
            if (this.errors.name) {
              this.loading = false;
              this.errors = [this.errors.name[0]];
              return;
            } else {
              this.loading = false;
              this.$router.push({ name: routeNames.login });
              this.$alert('Registration Successful');
            }
          }
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        });
    },
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
  },
};
</script>
<style scoped>
.container {
  position: absolute;
  left: 0;
  right: 60px;
  top: 0;
  bottom: 0;
  width: 1140px; /* Assign a value */
  height: 420px; /* Assign a value */
  margin: auto;
}
.log:hover {
  cursor: pointer;
}
.btn-success:hover {
  color: #fff;
  background-color: #5e72e4;
  border-color: #5e72e4;
  -webkit-transform: translateY(-1px);
}

.register-btn {
  background-color: #5e72e4;
}
.register-card {
  width: 42rem;
  height: 35rem;
  opacity: 0.8;
  border-top-left-radius: 15px;
  border-bottom-left-radius: 15px;
}
</style>