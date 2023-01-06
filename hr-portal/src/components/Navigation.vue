<template>
  <div class="navigation" :class="{ 'nav-opened': navOpen }">
    <div class="navigation-shell d-flex flex-column justify-content-between">
      <div>
        <div class="navigation-button menu-button"  @click="toggleNav()">
          <div class="copy" id="title">
            <span class="">The Portal</span>
          </div>
          <div class="icon menu" style="color: #fff">
            <!-- <span v-if="navOpen" class="mdi mdi-backburger"></span>
            <span v-else class="mdi mdi-menu"></span> -->
            <!-- <img src="../assets/team.png" height="30" width="45" alt=""> -->
            <img  :src="newIcon" class="new-icon" alt="">
          </div>
        </div>
        <div>
          <div class="nav-holder" v-if="this.permissions.includes('View-User')">
            <NavigationButton
              title="Manage User"
              @mouseover="getPos('user')"
              @mouseout='removePopUp()'
              :routeName="routeNames.manageUser"
              :icon="'mdi-account-multiple-plus'"
              @click.enter="goToRoute(routeNames.manageUser)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('View-Role')">
            <NavigationButton
              title="Manage Role"
              class="icons"
              @mouseover="getPos('role')"
              @mouseout='removePopUp()'
              :routeName="routeNames.manageRole"
              :icon="'mdi-account-cog'"
              @click="goToRoute(routeNames.manageRole)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('Viewer')">
            <NavigationButton
              class="icons"
              title="Home"
              @mouseover="getPos('home')"
              @mouseout='removePopUp()'
              :icon="'mdi-card-account-details-outline'"
              :routeName="routeNames.home"
              @click="goToRoute(routeNames.home)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('Viewer')">
            <NavigationButton
              class="icons"
              title="My Info"
              @mouseover="getPos('info')"
              @mouseout='removePopUp()'
              :routeName="routeNames.myInfo"
              :icon="'mdi-information-outline'"
              @click="goToRoute(routeNames.myInfo)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('Viewer')">
            <NavigationButton
              title="People"
              class="icons"
              @mouseover="getPos('people')"
              @mouseout='removePopUp()'
              :routeName="routeNames.People"
              :icon="'mdi-account-group'"
              @click="goToRoute(routeNames.People)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('View-Audit')">
            <NavigationButton
              title="Audits"
              class="icons"
              @mouseover="getPos('audits')"
              @mouseout='removePopUp()'
              :routeName="routeNames.report"
              :icon="'mdi-file-chart'"
              @click="goToRoute(routeNames.report)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('View-Audit')">
            <NavigationButton
              title="Reports"
              class="icons"
              @mouseover="getPos('report')"
              @mouseout='removePopUp()'
              :routeName="routeNames.employeeReport"
              :icon="'mdi-chart-box'"
              @click="goToRoute(routeNames.employeeReport)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('Viewer')">
            <NavigationButton
              title="Files"
              class="icons"
              @mouseover="getPos('file')"
              @mouseout='removePopUp()'
              :routeName="routeNames.Files"
              :icon="'mdi-file-account'"
              @click="goToRoute(routeNames.Files)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('File-Access-Permission')">
            <NavigationButton
              title="Files Upload"
              class="icons"
              @mouseover="getPos('fileupload')"
              @mouseout='removePopUp()'
              :routeName="routeNames.fileupload"
              :icon="'mdi-file-send'"
              @click="goToRoute(routeNames.fileupload)"
            />
          </div>
          <div class="nav-holder" v-if="this.permissions.includes('View-Audit')">
            <NavigationButton
              title="Leaves Adjust"
              class="icons"
              @mouseover="getPos('Leaves')"
              @mouseout='removePopUp()'
              :routeName="routeNames.leaves"
              :icon="'mdi-table-cog'"
              @click="goToRoute(routeNames.leaves)"
            />
          </div>
          <!--<div class="nav-holder" v-if="this.permissions.includes('Viewer')">
            <NavigationButton
              title="Assignedto"
              class="icons"
              @mouseover="getPos('assignedto')"
              @mouseout='removePopUp()'
              :routeName="routeNames.assignedto"
              :icon="'mdi-account-group'"
              @click="goToRoute(routeNames.assignedto)"
            />
          </div>-->
          <div class="nav-holder" v-if="this.reportee_count > 0">
            <NavigationButton v-if="!showNotificationBell"
              title="team"
              class="icons"
              @mouseover="getPos('team')"
              @mouseout='removePopUp()'
              :routeName="routeNames.team"
              :icon="'mdi-bell'"
              @click="goToRoute(routeNames.team)"
            />
            </div>
            <div class="nav-holder" v-if="this.reportee_count > 0">
            <NavigationButton v-if="showNotificationBell"
              title="team"
              class="icons"
              @mouseover="getPos('team')"
              @mouseout='removePopUp()'
              :routeName="routeNames.team"
              :icon="'mdi-bell-badge'"
              :leaves="this.leaveCounter"
              @click="goToRoute(routeNames.team)"
            />
          </div>
        </div>
      </div>
      <div>
        <div class="nav-holder">
          <NavigationButton
            class="icons"
            @mouseover="getPos('userProfile')"
            @mouseout='removePopUp()'
            title="User Profile"
            :routeName="routeNames.userProfile"
            :icon="'mdi-account-circle'"
            :image="this.emp_img"
            :bgColor="this.emp_bg"
            :initials="this.emp_initials"
            @click="goToRoute(routeNames.userProfile)"
          />
        </div>
        <div class="nav-holder">
          <NavigationButton
            class="icons"
            @mouseover="getPos('help')"
            @mouseout='removePopUp()'
            title="Help"
            :routeName="routeNames.help"
            :icon="'mdi-help-circle-outline'"
            @click="goToRoute(routeNames.help)"
          />
        </div>
         <div class="nav-holder">
           <NavigationButton
            title="Log Out"
            :icon="'mdi-logout'"
            @click="logout"
          />          
         </div>
      </div>
       
    </div>
  </div>
  <div class="popup-modal" id="popup-modal" :style="`top: ${popupTop}; bottom: ${popupBottom}`">
    <div class="arrow-left"></div>
    <div class="popup-text">{{popupText}}</div>
  </div>
</template>

<script>
import { routeNames } from '../js/routeNames';
import NavigationButton from './NavigationButton.vue';
import { decryptData } from '../js/utils/encrypt';
import Icon from '../assets/Portal_Logo.png';
import axios from 'axios';
import { deleteCookie, removeAllLocalStorage } from '../js/composables';
export default {
  name: 'Navigation',
  components: {
    NavigationButton,
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
  },
  inject: ['leaveCount'],
  watch: {
    leaveCounter(newValue){
      if(newValue>0){
        this.showNotificationBell = true;
      }
      else{
        this.showNotificationBell = false;
      }
    }
  },
  data() {
    return {
      navOpen: false,
      permissions: [],
      loggedInUser: [],
      employees: [],
      email: '',
      popupTop: '0px',
      popupText: '',
      popupBottom: '0px',
      newIcon: Icon,
      tooltipPos: [
        {popupTop: '76px', popupBottom: '0px'},
        {popupTop: '125px', popupBottom: '0px'},
        {popupTop: '175px', popupBottom: '0px'},
        {popupTop: '224px', popupBottom: '0px'},
        {popupTop: '276px', popupBottom: '0px'},
        {popupTop: '325px', popupBottom: '0px'},
        {popupTop: '376px', popupBottom: '0px'},
        {popupTop: '427px', popupBottom: '0px'},
        {popupTop: '478px', popupBottom: '0px'},
        {popupTop: '478px', popupBottom: '0px'},
        {popupTop: '525px', popupBottom: '0px'},
        {popupTop: 'auto', popupBottom: '125px'},
        {popupTop: 'auto', popupBottom: '76px'},
      ],
      iconsname: [
        {title: 'user', popuptext: 'Manage User',  permission: 'View-User'},
        {title: 'role', popuptext: 'Manage Role',  permission: 'View-Role'},
        {title: 'home', popuptext: 'Home',  permission: 'Viewer'},
        {title: 'info', popuptext: 'My Info',  permission: 'Viewer'},
        {title: 'people', popuptext: 'People',  permission: 'Viewer'},
        {title: 'audits', popuptext: 'Audits',  permission: 'View-Audit'},
        {title: 'report', popuptext: 'Reports',  permission: 'View-Audit'},
        {title: 'file', popuptext: 'Files',  permission: 'Viewer'},
        {title: 'team', popuptext: 'Team', permission: 'Viewer'},
        {title: 'fileupload', popuptext: 'Files Upload',  permission: 'File-Access-Permission'}, 
        {title: 'Leaves', popuptext: 'Leaves',  permission: 'View-Audit'}, 
        {title: 'userProfile', popuptext: 'User Profile',  permission: 'Viewer'},
        {title: 'help', popuptext: 'Help', permission: 'Viewer'},
      ],
      currentbtns: [],
      emp_img : null,
      emp_bg : '',
      emp_initials : '',
      reportee_count: '',
      empId: '',
      showNotificationBell: false,
      leaveCounter: this.leaveCount
    };
  },
  mounted() {
    //Decrypting Permissions
    let mkLocalData = localStorage.getItem('Permissions');
    if (!mkLocalData) {
      mkLocalData = 'null';
    }
    const secretPermissionKey = process.env.KEY || 'myscecretkey';
    this.permissions = decryptData(mkLocalData, secretPermissionKey);
    this.currentbtns = this.iconsname.filter(element => this.permissions.includes(element.permission));
    var encryptedEmailID = localStorage.getItem('Email');
    var encryptedEmpID = localStorage.getItem('EmployeeID');
    this.email = decryptData(encryptedEmailID, secretPermissionKey).replace(/['"]+/g, '');
    this.empId = decryptData(encryptedEmpID, secretPermissionKey).replace(/['"]+/g, '');

    if(this.email != 'admin@scala.com'){
      axios.get(this.$hostName + `/employeeinfobyemail/${this.email}`).then((response) => {
        response.data.forEach((element) => {
            this.loggedInUser.push(element);
        });
        this.reportee_count = this.loggedInUser[0]['reportee_count'];
        this.emp_img = this.loggedInUser[0]['profile_pic'];
        if(this.loggedInUser[0]['profile_pic'] == null)
          this.emp_img = 'noimage';
        this.emp_bg = this.loggedInUser[0]['imageBackgroundColour'];
        this.emp_initials = this.loggedInUser[0]['employeeShortName'];
      })
      .catch((error) => {
        console.log(error);
      }).finally(() => {
        this.loading = false;
      });
    }
    /*axios.post(this.$hostName + '/getEmployeeReportingManager', {empid: this.empId}).then((res) => {
      if(res.data.length > 0){
        this.leavesLength = res.data.length;
        console.log(this.leavesLength);
        this.showNotificationBell = true;
      }
    })*/
  },
  methods: {
    removePopUp(){
      var removeOpacity = document.getElementById('popup-modal');
      removeOpacity.classList.remove('show');
    },

    getPos(type){
      var addOpacity = document.getElementById('popup-modal');
      addOpacity.classList.add('show');
      if(type != 'userProfile' && type != 'help') {
        for (const key in this.currentbtns) {
          if (this.currentbtns[key].title == type) {
            this.set_popup_pos(this.tooltipPos[key].popupTop, this.tooltipPos[key].popupBottom, this.currentbtns[key].popuptext);
            break;
          }
        }
      } else {
        if(type == 'userProfile') {
          this.set_popup_pos(this.tooltipPos[this.tooltipPos.length-2].popupTop, this.tooltipPos[this.tooltipPos.length-2].popupBottom, this.iconsname[this.iconsname.length-2].popuptext);
        } else if (type == 'help') {
          this.set_popup_pos(this.tooltipPos[this.tooltipPos.length-1].popupTop, this.tooltipPos[this.tooltipPos.length-1].popupBottom, this.iconsname[this.iconsname.length-1].popuptext);
        }
      }
    },
    set_popup_pos(top, bottom, title) {
      this.popupTop = top;
      this.popupBottom = bottom;
      this.popupText = title;
    },
    goToRoute(route) {
      this.$emit('checkAthentication');
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
    toggleNav() {
      this.navOpen = !this.navOpen;
      this.$emit('navOpen',this.navOpen);
    },
    logout() {
      axios
        .post(this.$hostName + '/logout', null)
        .then(() => {
          deleteCookie('token');
          removeAllLocalStorage();
          this.$router.push({ path: '/' });
        })
        .catch((error) => {
          deleteCookie('token');
          removeAllLocalStorage();
          this.errors = error.response.data.errors;
        })
        .finally(() => {
        });
    },

  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.new-icon{
  height: 45px;
  width: 45px;
}
@media only screen and (max-width: 575.98px) {
  .popup-modal{
    display: none;
  }

}
@media (min-width: 768px) and (max-width: 991.98px) { 
  .popup-modal{
    display: none;
  }
}
.menus {
  margin-left: 36%;
  color: #7a7a7a;
  text-decoration: none;
}
.arrow-left {
  width: 0; 
  height: 0; 
  border-top: 7px solid transparent;
  border-bottom: 7px solid transparent; 
  position: absolute;
  top: 8px;
  left: -9px;
  border-right:10px solid #EE3234; 
}
.popup-modal{
  font-family: 'Montserrat';
  background: #EE3234;
  position: absolute;
  border-radius: 10px;
  width: 120px;
  left: 69px;
  height: 30px;
  line-height: 30px;
  text-align: center;
  opacity: 0;
  
}

.show{
  z-index: 1000;
  -webkit-animation: fadein .5s; /* Safari, Chrome and Opera > 12.1 */
       -moz-animation: fadein .5s; /* Firefox < 16 */
        -ms-animation: fadein .5s; /* Internet Explorer */
         -o-animation: fadein .5s; /* Opera < 12.1 */
            animation: fadein .5s;
  animation-fill-mode: forwards;
}
@keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Firefox < 16 */
@-moz-keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Safari, Chrome and Opera > 12.1 */
@-webkit-keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Internet Explorer */
@-ms-keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Opera < 12.1 */
@-o-keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}
.popup-text{
  color: white;
}
.nav-holder:hover .popup-modal{
  opacity: 1;
}
.menus:hover {
  color: #fff;
}
.menu-icons {
  margin-left: 8%;
  font-size: x-large;
}
.navigation {
  font-family: 'Montserrat';
  z-index: 1001;
  width: 220px;
  min-width: 60px;
  height: 100%;
  top: 0;
  left: -160px;
  position: fixed;
  background-color: #d05959;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;

  transition: all 300ms ease-in-out;
  overflow-y: auto;

  &.nav-opened {
    width: 360px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  }

  .navigation-shell {
    width: 200px;
    height: 98%;
    position: absolute;
    right: 0;
    top: 0;

    .navigation-button {
      width: 100%;
      height: 40px;
      // position: relative;
      float: left;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;

      &.menu-button {
        height: 60px;
      }

      &:hover {
        .copy {
          opacity: 1;
        }

        .icon {
          opacity: 1;
        }
      }

      .copy {
        // width: auto;
        flex-grow: 1;
        text-align: left;
        font-size: 14px;
        color: #fff;
        // opacity: .4;
      }

      .icon {
        width: 60px;
        font-size: 30px;
        height: 100%;
        color: #fff;

        display: flex;
        align-items: center;
        justify-content: center;
        // opacity: .4;
      }
    }
  }

  #title {
    color: #fff;
    font-size: 23px;
    // padding-left: 7px;
    // margin-top: 20%;
    // padding-bottom: 10%;
    text-align: center;
    // border-bottom: 1px solid;
  }
}
</style>
