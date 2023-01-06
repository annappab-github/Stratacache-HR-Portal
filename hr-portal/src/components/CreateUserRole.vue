<template>
  <div class="create-user">
    <div class="loader" v-if="loading"></div>
    <div class="card card-primary m-4">
      <!-- <div class="card-header">
        <h3 class="card-title">Add new Roles</h3>
        <div class="card-tools">
          <a class="btn btn-danger"
            ><i class="fas fa-shield-alt"></i> See all Roles</a
          >
        </div>
      </div> -->
      <div class="card-body">
        <div class="form-group">
          <label class="role-title" for="name">Role Name</label>
          <input type="text" name="name"
            class="form-control"
            v-model="roles.name"
            required
          />
        </div>
        <div id="demo" >
        <h5>Assign Permission</h5>
            <div class="d-flex justify-content-around">
                <div class="User">
                  <h5>User</h5>
                  <div v-for="(permission,idx) in getPermissions" :key="idx">
                    <div v-if="permission.name.includes('User')">
                      <input type="checkbox" :value="permission.id" id="permission.id" v-model="selectedPermission"> 
                      {{formatName(permission.name)}}
                    </div>
                  </div>
                </div>
                <div class="Role">
                  <h5>Role</h5>
                  <div v-for="(permission,idx) in getPermissions" :key="idx">
                    <div v-if="permission.name.includes('Role')">
                      <input type="checkbox" :value="permission.id" id="permission.id" v-model="selectedPermission"> 
                      {{formatName(permission.name)}}
                    </div>
                  </div>
                </div>
                <div class="Role">
                  <h5>Employee</h5>
                  <div v-for="(permission,idx) in getPermissions" :key="idx">
                    <div v-if="permission.name.includes('Viewer')">
                      <input type="checkbox" :value="permission.id" id="permission.id" v-model="selectedPermission"> 
                      {{formatName(permission.name)}}
                    </div>
                  </div>
                </div>
                <div class="Audit">
                  <h5>Audit/Reports</h5>
                  <div v-for="(permission,idx) in getPermissions" :key="idx">
                    <div v-if="permission.name.includes('Audit')">
                      <input type="checkbox" :value="permission.id" id="permission.id" v-model="selectedPermission"> 
                      {{formatName(permission.name)}}
                    </div>
                  </div>
                </div>
                <div class="File">
                  <h5>File Access</h5>
                  <div v-for="(permission,idx) in getPermissions" :key="idx">
                    <div v-if="permission.name.includes('File-Access-Permission')">
                      <input type="checkbox" :value="permission.id" id="permission.id" v-model="selectedPermission"> 
                      {{formatName(permission.name)}}
                    </div>
                  </div>
                </div>
              </div>
        </div>
      </div>

      <div>
        <button class="btn btn-primary ml-4 mb-4" @click="createRole">Create Role</button>
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
      getPermissions: '',
      roles: {
        name: '',
        permissions: ''
      },
      selectedPermission: [],
      loading: false,
    };
  },
  computed: {
    routeNames: () => { return routeNames; }
  },
  mounted() {
    this.loading = true;
    axios.get(this.$hostName + '/getAllPermissions')
      .then((response) => {
        this.getPermissions = response.data;
      })
      .finally(() => {
        this.loading =  false;
      });
  },
  methods: {
    formatName(name){
      return name.split('-').join(' ');
    },
    createRole(){
      if(this.roles.name==''){
        Swal.fire({
          text: 'Enter Role Name',
          confirmButtonText: 'Ok',
        });
      }
      else if(this.selectedPermission==''){
        Swal.fire({
          text: 'Select atleast one permission',
          confirmButtonText: 'Ok',
        });
      }
      else{
        this.loading = true;
        this.roles.permissions = this.selectedPermission;
        axios.post(this.$hostName + '/postRole',{
          name: this.roles.name,
          permissions: this.roles.permissions
        })
        .then(response => {
          if(response.status==202){
            this.loading = false;
          Swal.fire({
          text: 'Role already Exists',
          confirmButtonText: 'Ok',
        });
          }
          else{
            this.loading =  false;
            this.$router.push({name:routeNames.manageRole});
          }
        });
      }
      
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
.create-user{
  font-family: 'Montserrat';
  top: 2%;
  right: 1%;
  position: absolute;
  left: 60px;
  width: calc(100% - 60px);
}
.role-title{
  font-size: 30px;
  font-weight: bold;
}
</style>