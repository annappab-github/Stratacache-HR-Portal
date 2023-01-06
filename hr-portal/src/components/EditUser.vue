<template>
  <div class="edit-user">
    <div class="title">
      <h1 class="mt-4">Edit User</h1>
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
          placeholder="Enter Name"
          v-model="getUser.name"
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
          v-model="getUser.email"
        />
      </div>
      <div class="form-group">
        <label for="employee_id">Employee ID</label>
        <input
          type="text"
          class="form-control"
          id="employee_id"
          aria-describedby="emailHelp"
          placeholder="Enter Employee ID"
          v-model="getUser.employee_id"
        />
      </div>
      <div class="form-group">
        <label for="exampleFormControlSelect1">Roles</label>
        <select class="form-control" id="exampleFormControlSelect1" v-model="getUser.roles">
          <option v-for="(role, item) in getRoles" :value="role.name" :key="item">{{role.name}}</option>
        </select>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input
          type="password"
          class="form-control"
          id="exampleInputPassword1"
          placeholder="Password"
           v-model="getUser.password"
        />
      </div>
      <div class="custom-control custom-switch">
        <input
          type="checkbox"
          class="custom-control-input"
          id="customSwitches"
          v-model="getUser.force_change_password"
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
      <button type="submit" class="btn btn-primary" @click="editUser">Update User</button>
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
      token: '',
      loading: false,
      getRoles:'',
      getUser: {
        name: '',
        email: '',
        employee_id: '',
        roles: '',
        password: '',
        force_change_password: false
      },
      errors:[],
      nameCheck: '',
      emailCheck: '',
      employeeIdCheck: '',
    };
  },
  mounted() {
    this.loading = true;
    this.getUser.name = this.$route.params.name;
    this.getUser.email = this.$route.params.email;
    this.getUser.employee_id = this.$route.params.employee_id;
    this.getUser.roles = this.$route.params.role;
    this.getUser.force_change_password = this.$route.params.force_change_password;
    this.nameCheck = this.$route.params.name;
    this.emailCheck = this.$route.params.email;
    this.employeeIdCheck = this.$route.params.employee_id;
    if (this.getUser.force_change_password == 1) {
      this.getUser.force_change_password = true;
    } else {
      this.getUser.force_change_password = false;
    }
    axios.get(this.$hostName + '/getAllRoles').then(response => {
      this.getRoles = response.data.roles;
    })
    .finally(() => {
      this.loading =  false;
    });
        
  },
  computed: {
    routeNames: () => { return routeNames; }
    },
  methods: {
    editUser(e){
      this.errors = [];
      if(this.getUser.name=='' || this.getUser.email=='' || this.getUser.employee_id==''){
          if(this.getUser.name==''){
            this.errors.push('Name required.');
          }
          if(this.getUser.email==''){
            this.errors.push('Email required.');
          }
          if(this.getUser.employee_id==''){
            this.errors.push('Employee ID required.');
          }
          if (!this.errors.length) {
            return true;
          }
          e.preventDefault();
          return;
      }

          if(this.getUser.name!='' || this.getUser.email!='' || this.getUser.password!='' || this.getUser.employee_id!=''){
            if(this.getUser.password.length<6 && this.getUser.password!=''){
              this.errors.push('Minimum 6 characters required.');
              return;
            }
            if(this.getUser.email!=''){
              if (/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(this.getUser.email.trim())){
                // console.log('skip');
              } else{
                this.errors.push('Incorrect email format');
                return;
              }  
            }
          }
      this.loading = true;
      axios.put(this.$hostName + '/account/update/'+ this.$route.params.userid,{
          name: this.getUser.name,
          email: this.getUser.email,
          employee_id: this.getUser.employee_id,
          roles: this.getUser.roles,
          password: this.getUser.password,
          force_change_password: this.getUser.force_change_password,
          nameCheck: this.nameCheck,
          emailCheck: this.emailCheck,
          employeeIdCheck: this.employeeIdCheck,
        }).then(response =>{
          if(response.status==202){
            this.loading = false;
            Swal.fire({
            text: 'User already exists',
            confirmButtonText: 'Ok',
          });
          }
          else if(response.status==203){
            this.loading = false;
            Swal.fire({
            text: 'Email already exists',
            confirmButtonText: 'Ok',
          });
          }
          else if(response.status==204){
            this.loading = false;
            Swal.fire({
            text: 'Employee ID already exists',
            confirmButtonText: 'Ok',
          });
          }
          else{
            this.loading =  false;
            this.$router.push({name: routeNames.manageUser});
          }
        });
    },
    goToRoute(route) {
      if(this.navOpen) this.toggleNav();
      if(route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    }
  }
  
};
</script>

<style scoped>
.edit-user{
  font-family: 'Montserrat';
  position: absolute;
  left: 60px;
  width: calc(100% - 60px);
}
.create-form {
  width: 50%;
  margin-left: auto;
  margin-right: auto;
  margin-top: 5%;
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