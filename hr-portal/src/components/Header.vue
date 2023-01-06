<template>
  <div class="bgimg"></div>
</template>

<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { decryptData } from '../js/utils/encrypt';
import { useI18n } from 'vue-i18n';
import headerlogo from '../assets/Stratacachelogo.png';
import { getCookie, deleteCookie } from '../js/composables';

export default {
  name: 'Header',
  components: {
  },
  setup() {
    const { t, locale } = useI18n();
    return { t, locale, fallbackWarn: false };
  },
  data() {
    return {
      showDropDown: false,
      username: '',
      permissions: [],
      logo: headerlogo
    };
  },
  mounted() {
    let permissionData = localStorage.getItem('Permissions');
    
    if (!permissionData) {
      permissionData = 'null';
    }
    const secretKey = process.env.KEY || 'myscecretkey';
    this.permissions = decryptData(permissionData, secretKey);
    if ( getCookie('token')) {
      this.username = localStorage.getItem('username').replace(/['"]+/g, '');
    }
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
  },
  methods: {
    logout() {
      localStorage.removeItem('Login');
      if (routeNames.login == this.$router.currentRoute.name) {
        return;
      }
      axios
        .post(this.$hostName + '/logout', null)
        .then(() => {
          this.$router.push({ name: routeNames.login });
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        })
        .finally(() => {
          // localStorage.removeItem('token');
          deleteCookie('token');
          localStorage.removeItem('Permissions');
          localStorage.removeItem('username');
          localStorage.removeItem('Role');
          localStorage.removeItem('EmployeeID');
          localStorage.removeItem('Email');
          localStorage.removeItem('celebrationGIF');
          location.reload();
        });
    },
    profile() {
      if (this.$router.currentRoute._value.path.includes('/userprofile')) {
        return;
      } else {
        this.$router.push({ name: routeNames.userProfile });
      }
    },
    report() {
      if (this.$router.currentRoute._value.path.includes('/report')) {
        return;
      } else {
        this.$router.push({ name: routeNames.report });
      }
    },
    refreshApp() {
      window.ScalaDone();
    },
    goToRoute(route) {
      if (this.navOpen) this.toggleNav();
      if (route == this.$router.currentRoute.name) return;
      this.$router.push({ name: route });
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">

.bgimg{
 /* background-image: linear-gradient(rgba(0,128,0,0.2), rgba(0,128,0,0.4));*/
  background-color: #e7e7e7;
  width: 100%;
  height: 1080px;
  position: absolute;
}
</style>
