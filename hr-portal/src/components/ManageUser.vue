<template>
  <div class="manage-user">
    <div class="title">
      <h1 class="mt-4">Manage User</h1>
    </div>
    <div class="user-manager" v-if="refinedUsers">
      <div class="top-header">
        <div class="search">
          <div class="icon">
            <span class="mdi mdi-magnify" style="color: #000"></span>
          </div>
          <input
            class="search-bar"
            placeholder="Search Users"
            v-model="search"
          />
        </div>
        <div class="filters-container">
          <!-- <Tabs /> -->
        </div>
        <div class="create-user">
          <!-- <span
            v-if="this.permissions.includes('Create-User')"
            @click="createNewUser"
            class="mdi mdi-plus-circle-outline add"
          ></span> -->
          <button class="btn btn-dark" v-if="this.permissions.includes('Create-User')" @click="createNewUser">Create User</button>
        </div>
      </div>
    </div>
    <div class="loader" v-if="loading"></div>
    <div class="row bg-light user-header">
      <div class="col-lg-6 margin-tb"></div>
    </div>
    <div class="manage-user-table">
      <table class="table desktop-table">
        <thead>
          <tr class="table-header">
            <th
              class="user-table-headers"
              :class="[{ sorting: sort == 'employee_id' }, direction]"
              @click="updateSort('employee_id')"
            >
              Employee ID
            </th>
            <th
              class="user-table-headers"
              :class="[{ sorting: sort == 'name' }, direction]"
              @click="updateSort('name')"
            >
              Name
            </th>
            <th
              class="user-table-headers"
              :class="[{ sorting: sort == 'email' }, direction]"
              @click="updateSort('email')"
            >
              Email
            </th>
            <th
              class="user-table-headers"
              :class="[{ sorting: sort == 'role' }, direction]"
              @click="updateSort('role')"
            >
              Role
            </th>
            <th
              class="user-table-headers"
              :class="[{ sorting: sort == 'force_change_password' }, direction]"
              @click="updateSort('force_change_password')"
            >
              Password Flag
            </th>
            <th
              v-if="
                this.permissions.includes('Edit-User') ||
                this.permissions.includes('Delete-User')
              "
            >
              Action
            </th>
          </tr>
        </thead>
        <tbody
          v-if="
            this.permissions.includes('Edit-User') &&
            this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td>{{ user.employee_id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td>
            <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-pencil edit"
                    @click="editUser(user.sl_id)"
                  ></span>
                </div>

                <!-- <div class="icon">
                  <span
                    class="mdi mdi-delete delete"
                    @click="openModel(item)"
                  ></span>
                </div> -->
              </div>
            </td>
          </tr>
        </tbody>
        <tbody
          v-if="
            this.permissions.includes('Edit-User') &&
            !this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td>
            <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-pencil edit"
                    @click="editUser(user.sl_id)"
                  ></span>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
        <tbody
          v-if="
            !this.permissions.includes('Edit-User') &&
            this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td>
            <!-- <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-delete delete"
                    @click="openModel(item)"
                  ></span>
                </div>
              </div>
            </td> -->
          </tr>
        </tbody>
        <tbody
          v-if="
            !this.permissions.includes('Edit-User') &&
            !this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td>
          </tr>
        </tbody>
      </table>

      <table class="table mobile-table">
        <tbody
          v-if="
            this.permissions.includes('Edit-User') &&
            this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td style="font-size: 12px; width:250px">
              <p style="margin:0px;">{{ user.employee_id }}</p>
              <p style="margin:0px;">{{ user.name }}</p>
              <p style="margin:0px;">{{ user.email }}</p>
              <p style="margin:0px;">{{ user.role }}</p>
              <p style="margin:0px;">Password Flag: {{ user.force_change_password == 1 ? 'True' : 'False' }}</p>
            </td>
            <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-pencil edit"
                    @click="editUser(user.sl_id)"
                  ></span>
                </div>

                <!-- <div class="icon">
                  <span
                    class="mdi mdi-delete delete"
                    @click="openModel(item)"
                  ></span>
                </div> -->
              </div>
            </td>
          </tr>
        </tbody>
        <tbody
          v-if="
            this.permissions.includes('Edit-User') &&
            !this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td style="font-size: 12px; width:250px">
              <p style="margin:0px;">{{ user.employee_id }}</p>
              <p style="margin:0px;">{{ user.name }}</p>
              <p style="margin:0px;">{{ user.email }}</p>
              <p style="margin:0px;">{{ user.role }}</p>
              <p style="margin:0px;">Password Flag: {{ user.force_change_password == 1 ? 'True' : 'False' }}</p>
            </td>
            <!-- <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td> -->
            <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-pencil edit"
                    @click="editUser(user.sl_id)"
                  ></span>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
        <tbody
          v-if="
            !this.permissions.includes('Edit-User') &&
            this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td style="font-size: 12px; width:250px">
              <p style="margin:0px;">{{ user.employee_id }}</p>
              <p style="margin:0px;">{{ user.name }}</p>
              <p style="margin:0px;">{{ user.email }}</p>
              <p style="margin:0px;">{{ user.role }}</p>
              <p style="margin:0px;">Password Flag: {{ user.force_change_password == 1 ? 'True' : 'False' }}</p>
            </td>
            <!-- <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td> -->
            <!-- <td>
              <div class="row d-flex justify-content-center">
                <div class="icon">
                  <span
                    class="mdi mdi-delete delete"
                    @click="openModel(item)"
                  ></span>
                </div>
              </div>
            </td> -->
          </tr>
        </tbody>
        <tbody
          v-if="
            !this.permissions.includes('Edit-User') &&
            !this.permissions.includes('Delete-User')
          "
        >
          <tr v-for="(user, item) in refinedUsers" :key="item" class="table-data">
            <td style="font-size: 12px; width:250px">
              <p style="margin:0px;">{{ user.employee_id }}</p>
              <p style="margin:0px;">{{ user.name }}</p>
              <p style="margin:0px;">{{ user.email }}</p>
              <p style="margin:0px;">{{ user.role }}</p>
              <p style="margin:0px;">Password Flag: {{ user.force_change_password == 1 ? 'True' : 'False' }}</p>
            </td>
            <!-- <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.force_change_password == 1 ? 'True' : 'False' }}</td> -->
          </tr>
        </tbody>
      </table>
    </div>
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
                      @click="deleteUser(modalData)"
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
</template>

<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { decryptData } from '../js/utils/encrypt';
import Swal from 'sweetalert2/dist/sweetalert2.js';

export default {
  components: {},
  props: {
    direction: String,
    sort: String,
  },
  data() {
    return {
      users: '',
      userid: '',
      loading: false,
      permissions: [],
      modalData: '',
      myModel: false,
      search: '',
      filter: 'all',
      lang: '',
    };
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
    refinedUsers() {
      // let refined = this.filterUsers(this.users);
      let refined = this.sortUsers();
      refined = this.searchUsers();
      refined = this.direction == 'ascending' ? refined : refined.reverse();
      return refined;
    },
  },
  mounted() {
    this.lang = localStorage.getItem('language');
    this.loading = true;
    // this.permissions= JSON.parse(localStorage.getItem('permissions'));

    //Decrypting Permissions
    let mkLocalData = localStorage.getItem('Permissions');
    if (!mkLocalData) {
      mkLocalData = 'null';
    }
    const secretPermissionKey = process.env.KEY || 'myscecretkey';
    this.permissions = decryptData(mkLocalData, secretPermissionKey);

    axios
      .get(this.$hostName + '/getAllUsers')
      .then((response) => {
        for(let index in response.data.users){
          response.data.users[index].sl_id = index;
        }
        this.users = response.data.users;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
  },
  methods: {
    updateSort(value) {
      this.$emit('updateSort', value);
    },
    createNewUser() {
      // this.$router.push({ name: routeNames.newUser });
      this.$router.push({
        name: routeNames.editEmployee,
        params: {
          empid: ''
        },
      });
    },
    editUser(item) {
      this.$router.push({
        name: routeNames.editEmployee,
        params: {
          empid: this.users[item].employee_id
        },
      });
    },
    deleteUser(item) {
      this.loading = true;
      axios
        .delete(this.$hostName + '/delete/user/' + this.users[item].id)
        .then((response) => {
          if (response.status == 200) {
            this.$emit('User Deleted');
            this.myModel = false;
          }
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.getUser();
        });
    },
    getUser() {
      axios
        .get(this.$hostName + '/getAllUsers')
        .then((response) => {
          this.users = response.data.users;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
          Swal.fire({
            text: 'User removed succesfully',
            confirmButtonText: 'Ok',
          });
        });
    },
    openModel(idx) {
      this.myModel = true;
      this.modalData = idx;
    },
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
    sortUsers() {
      switch (this.sort) {
        case 'id':
        case 'name':
        case 'email':
        case 'employee_id':
        case 'role':
          return this.users.sort((userA, userB) => {
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
    searchUsers() {
      if (!this.search) return this.users;
      return this.users.filter((user) => {
        return (
          (user.name &&
            user.name
              .toString()
              .toLowerCase()
              .includes(this.search.toString().toLowerCase())) ||
          (user.id &&
            user.id
              .toString()
              .toLowerCase()
              .includes(this.search.toLowerCase()))
        );
      });
    },
  },
};
</script>

<style scoped lang="scss">

@media only screen and (max-width: 575.98px) {
  .search{
    left: 60px;
  }
  
  .create-user{
    position: relative;
    left: -70px;
    top: -5px;
  }

  .mobile-table{
    display: block !important;
    height: 100% !important;
  }

  .desktop-table{
    display: none;
  }

  .table-data {
  text-align: left !important;
}

}

.mobile-table{
  display: none;
}

thead{
  position: sticky;
  top: 0;
}
.manage-user-table{
  position: relative;
  height: 38rem;
  overflow: auto;
}
.manage-user{
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
::placeholder {
  color: #000;
  opacity: 1;
}
.user-table-headers:active {
  color: blue;
}

.add-user {
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
.user-header {
  // margin-top: 2%;
  margin-left: 5%;
  margin-right: 5%;
}
.table {
  width: 95.5%;
  margin-left: 2%;
  height: 200px;
  overflow: auto;
}
.delete {
  color: red;
  margin-left: 10px;
}
.delete:hover {
  cursor: pointer;
}
.edit {
  color: blue;
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

.user-manager {
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
        }
      }
    }

    .filters-container {
      width: auto;
      min-width: 100px;
      height: 100%;
      position: relative;

      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1%;
    }
  }
}
</style>