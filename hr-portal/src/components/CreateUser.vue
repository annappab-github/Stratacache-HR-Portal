<template>
  <div class="create-user">
    <div class="title">
      <h1 class="mt-4">Create User</h1>
    </div>
    <div class="create-form">
      <div class="user-loader" v-if="loading"></div>
      <div class="form-group">
        <label for="name">Name</label>
        <input
          type="text"
          class="form-control"
          id="name"
          aria-describedby="emailHelp"
          v-model="user.name"
        />
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input
          type="email"
          class="form-control"
          id="email"
          aria-describedby="emailHelp"
          v-model="user.email"
        />
      </div>
      <div class="form-group">
        <label for="email">Employee ID</label>
        <input
          type="text"
          class="form-control"
          id="employeeId"
          aria-describedby="emailHelp"
          v-model="user.employee_id"
        />
      </div>
      <div class="form-group">
        <label for="exampleFormControlSelect1">Role</label>
        <select class="form-control" id="exampleFormControlSelect1" v-model="user.role">
          <option v-for="(role, item) in getRoles" :value="role.id" :key="item" >{{role.name}}</option>
        </select>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input
          type="password"
          class="form-control"
          id="exampleInputPassword1"
          v-model="user.password"
        />
      </div>
      <div class="custom-control custom-switch">
        <input
          type="checkbox"
          class="custom-control-input"
          id="customSwitches"
          v-model="user.force_change_password"
        />
        <label
          class="custom-control-label mt-4 field-title"
          for="customSwitches"
          >Password Flag</label
        >
      </div>
      <div class="errors mb-2">
        <p v-if="errors.length" style="color: red;">
          <ul >
            <li v-for="(error,idx) in this.errors" :key="idx">{{error}}</li>
          </ul>
        </p>
      </div>
      <div class="row d-flex justify-content-center">
        <button type="submit" class="btn btn-primary create-btn" @click="createUser">Submit</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import Swal from 'sweetalert2/dist/sweetalert2.js';

export default {
  data() {
    return {
      getRoles: '',
      loading: false,
      user: {
        // id: '',
        name: '',
        email: '',
        employee_id: '',
        role: 3,
        force_change_password: false,
        password: '',
        // permission: 1
      },
      errors: [],
    };
  },
  mounted() {
    this.loading = true;
    axios
      .get(this.$hostName + '/getAllRoles')
      .then((response) => {
        this.getRoles = response.data.roles;
      })
      .finally(() => {
        this.loading = false;
      });
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
  },
  methods: {
    createUser(e) {
      this.errors = [];
      if (
        this.user.name == '' ||
        this.user.email == '' ||
        this.user.employee_id == '' ||
        this.user.password == ''
      ) {
        if (this.user.name == '') {
          this.errors.push('Name required.');
        }
        if (this.user.email == '') {
          this.errors.push('Email required.');
        }
        if (this.user.employee_id == '') {
          this.errors.push('Employee ID required.');
        }
        if (this.user.password == '') {
          this.errors.push('Password required.');
        }
        if (this.user.password.length < 6) {
          this.errors.push('Minimum 6 characters required.');
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
        this.user.employee_id != '' ||
        this.user.password != ''
      ) {
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
        .post(this.$hostName + '/account/create', {
          // id: this.user.id,
          name: this.user.name,
          email: this.user.email,
          employee_id: this.user.employee_id,
          role: this.user.role,
          force_change_password: this.user.force_change_password,
          password: this.user.password,
          // permission: this.user.permission
        })
        .then((response) => {
          if (response.status == 202) {
            this.loading = false;
            Swal.fire({
              text: 'User already exists',
              confirmButtonText: 'Ok',
            });
          } else if (response.status == 203) {
            this.loading = false;
            Swal.fire({
              text: 'Email already exists',
              confirmButtonText: 'Ok',
            });
          } else {
            this.loading = false;
            this.$router.push({ name: routeNames.manageUser });
          }
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
.create-user{
  font-family: 'Montserrat';
  position: absolute;
  left: 60px;
  width: calc(100% - 60px);
}
.create-form {
  width: 40%;
  margin-left: auto;
  margin-right: auto;
  margin-top: 5%;
  color: #000;
}

.create-btn {
  background-color: #5e72e4;
}
.btn-primary:hover {
  color: #fff;
  background-color: #5e72e4;
  border-color: #5e72e4;
  -webkit-transform: translateY(-1px);
}
.title {
  margin-left: 2%;
}
.custom-control-input:checked ~ .custom-control-label::before {
  color: #fff;
  border-color: #000;
  background-color: #000;
}
.user-loader{
  position: fixed;
  top: 0px;
  left: 60px;
  width: calc(100% - 60px);
  height: 100%;
  background-color: #eceaea;
  background-image: url("../assets/loading.gif");
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
</style>