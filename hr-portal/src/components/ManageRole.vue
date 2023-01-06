<template>
  <div class="manage-role">
    <div class="title">
      <h1 class="mt-4">Manage Role</h1>
    </div>
    <div class="role-manager" v-if="refinedRoles">
      <div class="top-header">
        <div class="search">
          <div class="icon">
            <span class="mdi mdi-magnify"></span>
          </div>
          <input
            class="search-bar"
            placeholder="Search Roles"
            v-model="search"
          />
        </div>
        <div class="create-role">
          <!-- <span
            v-if="this.permissions.includes('Create-User')"
            @click="createUserRole"
            class="mdi mdi-plus-circle-outline add"
          ></span> -->
          <button v-if="this.permissions.includes('Create-User')" class="btn btn-dark" @click="createUserRole()">Create Role</button>
        </div>
      </div>
    </div>
    <div class="loader" v-if="loading"></div>

    <table class="table">
      <thead>
        <tr class="table-header">
          <th
            class="user-table-headers"
            :class="[{ sorting: sort == 'name' }, direction]"
            @click="updateSort('name')"
          >
            Name
          </th>
          <th
            v-if="
              this.permissions.includes('Edit-Role') ||
              this.permissions.includes('Delete-Role')
            "
            width="280px"
          >
            Action
          </th>
        </tr>
      </thead>
      <tbody
        v-if="
          this.permissions.includes('Edit-Role') &&
          this.permissions.includes('Delete-Role')
        "
      >
        <tr class="table-data" v-for="(roles, idx) in refinedRoles" :key="idx">
          <td>{{ roles.name }}</td>
          <td>
            <div class="row d-flex justify-content-center">
              <div class="icon">
                <span
                  class="mdi mdi-pencil edit"
                  @click="editRoleDetails(roles.sl_id)"
                ></span>
              </div>
              <div class="icon">
                <span
                  class="mdi mdi-delete delete"
                  @click="openModel(roles.id)"
                ></span>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
      <tbody
        v-if="
          this.permissions.includes('Edit-Role') &&
          !this.permissions.includes('Delete-Role')
        "
      >
        <tr class="table-data" v-for="(roles, idx) in refinedRoles" :key="idx">
          <td>{{ roles.name }}</td>
          <td>
            <div class="row d-flex justify-content-center">
              <div class="icon">
                <span
                  class="mdi mdi-pencil edit"
                  @click="editRoleDetails(roles.sl_id)"
                ></span>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
      <tbody
        v-if="
          !this.permissions.includes('Edit-Role') &&
          this.permissions.includes('Delete-Role')
        "
      >
        <tr class="table-data" v-for="(roles, idx) in refinedRoles" :key="idx">
          <td>{{ roles.name }}</td>
          <td>
            <div class="row d-flex justify-content-center">
              <div class="icon">
                <span
                  class="mdi mdi-delete delete"
                  @click="openModel(roles.id)"
                ></span>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
      <tbody
        v-if="
          !this.permissions.includes('Edit-Role') &&
          !this.permissions.includes('Delete-Role')
        "
      >
        <tr class="table-data" v-for="(roles, idx) in refinedRoles" :key="idx">
          <td>{{ roles.name }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Pop Up Modal -->
    <div v-if="myModel">
      <transition name="model">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" @click="myModel = false">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="confirmation pb-4" style="text-align: center">
                    <h4>Are you sure you want to delete this?</h4>
                  </div>
                  <div align="center">
                    <input
                      type="button"
                      class="btn btn-primary"
                      style="margin-right: 10px; width: 100px"
                      value="OK"
                      @click="deleteRole(modalData)"
                    />
                    <input
                      type="button"
                      class="btn btn-danger"
                      style="margin-left: 10px; width: 100px"
                      value="Cancel"
                      @click="myModel = false"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>-->

<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { decryptData } from '../js/utils/encrypt';
import Swal from 'sweetalert2/dist/sweetalert2.js';

export default {
  data() {
    return {
      myModel: false,
      getRoles: '',
      loading: false,
      roleid: '',
      permissions: [],
      modalData: '',
      search: '',
      filter: 'all',
      users: '',
      lang: '',
    };
  },
  props: {
    direction: String,
    sort: String,
  },
  // components: {
  //   Tabs,
  // },
  computed: {
    routeNames: () => {
      return routeNames;
    },
    refinedRoles() {
      let refined = this.sortUsers();
      refined = this.searchRoles();
      refined = this.direction == 'ascending' ? refined : refined.reverse();
      return refined;
    },
  },
  mounted() {
    this.lang = localStorage.getItem('language');
    // this.permissions= JSON.parse(localStorage.getItem('permissions'));

    //Decrypting Permissions
    let mkLocalData = localStorage.getItem('Permissions');
    if (!mkLocalData) {
      mkLocalData = 'null';
    }
    const secretPermissionKey = process.env.KEY || 'myscecretkey';
    this.permissions = decryptData(mkLocalData, secretPermissionKey);

    this.loading = true;
    axios
      .get(this.$hostName + '/getAllRoles')
      .then((response) => {
        for(let index in response.data.roles){
          response.data.roles[index].sl_id = index;
        }
        this.getRoles = response.data.roles;
      })
      .finally(() => {
        this.loading = false;
      });
  },
  methods: {
    updateSort(value) {
      this.$emit('updateSort', value);
    },
    createUserRole() {
      this.$router.push({ name: routeNames.newUserRole });
    },
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
    editRoleDetails(idx) {
      this.roleid = this.getRoles[idx].id;
      var roleid = this.getRoles[idx].id;
      var name = this.getRoles[idx].name;
      this.$router.push({
        name: 'edit-user-role',
        params: {
          roleid,
          name,
        },
      });
    },
    openModel(idx) {
      this.myModel = true;
      this.modalData = idx;
    },
    deleteRole(idx) {
      this.loading = true;
      axios.post(this.$hostName + '/deleteRole/' + idx).then((response) => {
        if (response.status == 202) {
          this.loading = false;
          this.myModel = false;
          Swal.fire({
            text: 'Role is in use, unassign the role to delete',
            confirmButtonText: 'Ok',
          });
        } else {
          this.getRole();
        }
      });
    },
    searchRoles() {
      if (!this.search) return this.getRoles;
      return this.getRoles.filter((roles) => {
        return (
          roles.name &&
          roles.name
            .toString()
            .toLowerCase()
            .includes(this.search.toString().toLowerCase())
        );
      });
    },
    sortUsers() {
      switch (this.sort) {
        case 'id':
        case 'name':
          return this.getRoles.sort((userA, userB) => {
            if (userA[this.sort] < userB[this.sort]) {
              return -1;
            }
            if (userA[this.sort] > userB[this.sort]) {
              return 1;
            }
            return 0;
          });

        default:
          return this.users;
      }
    },
    getRole() {
      axios
        .get(this.$hostName + '/getAllRoles')
        .then((response) => {
          this.getRoles = response.data.roles;
        })
        .finally(() => {
          this.loading = false;
          this.myModel = false;
          Swal.fire({
            text: 'Role Deleted Successfully',
            confirmButtonText: 'Ok',
          });
        });
    },
  },
};
</script>

<style scoped lang="scss">
@media only screen and (max-width: 575.98px) {
.create-role {
  font-size: xx-large;
  // color: #007bff;
  color: #000;
  margin-left: -94px;
}
}
.manage-role{
  font-family: 'Montserrat';
  position: absolute;
  left: 60px;
  width: calc(100% - 60px);
}
.table-header{
  border: none;
}
.user-table-headers {
  cursor: pointer;
}
.user-table-headers:active {
  color: blue;
}
.add-role {
  text-align: end;
  font-size: xx-large;
}
.add:hover {
  cursor: pointer;
}
#btn-align {
  margin-right: 5%;
  color: white;
}
.role-header {
  margin-top: 2%;
  margin-left: 5%;
  margin-right: 5%;
}
.table {
  position: absolute;
  width: 95.5%;
  left: 2%;
}
.delete {
  color: red;
  font-size: 25px;
  margin-left: 10px;
}
.delete:hover {
  cursor: pointer;
}
.edit {
  color: blue;
  font-size: 25px;
  margin-right: 10px;
}
.edit:hover {
  cursor: pointer;
}
.icon {
  font-size: 25px;
}
.table-data {
  text-align: center;
}
.create-role {
  font-size: xx-large;
  // color: #007bff;
  color: #000;
}
::placeholder {
  color: #000;
  opacity: 1;
}
.role-manager {
  width: 95.5%;
  height: 100%;
  position: relative;
  // background-color: #fff;

  display: flex;
  flex-direction: column;
  margin-left: 2%;
  margin-right: 5rem;

  .top-header {
    width: 100%;
    height: auto;
    min-height: 60px;
    padding: 0 10px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    // background-color: $brand-primary-light;

    .search {
      width: auto;
      flex-grow: 1;
      height: 100%;
      position: relative;
      display: flex;

      .icon {
        width: 40px;
        height: 100%;
        font-size: 24px;
        // color: $brand-primary;
        color: #000;
        // opacity: .5;
        position: relative;
        float: left;

        display: flex;
        align-items: center;
        justify-content: center;
      }

      input {
        width: 220px;
        height: 100%;
        position: relative;
        float: left;
        font-size: 14px;
        -webkit-appearance: none;
        outline: none;
        box-shadow: none;
        border: none;
        background-color: transparent;
        color: $general-black-light;
        flex-grow: 1;

        &::placeholder {
          opacity: 0.6;
          font-style: italic;
          color: #000;
        }
      }
    }
  }
}
</style>