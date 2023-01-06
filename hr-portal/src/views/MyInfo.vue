
<template>
  <EmployeeInfo v-if="loadComponent" :propsEmpId="encryptedEmployeeID" :propsEmail="encryptedEmailID" :editProfilePic="true" />
</template>

<script>
import EmployeeInfo from '../components/EmployeeInfo.vue';
export default {
    components: {
      EmployeeInfo
    },
    data(){
        return{          
          encryptedEmployeeID: '',
          encryptedEmailID: '',
          loadComponent: false
        };
    },
    mounted(){
      this.encryptedEmployeeID = localStorage.getItem('EmployeeID');
      this.encryptedEmailID = localStorage.getItem('Email');
      this.loadComponent = true;
    }
};
</script>
<style scoped>
</style>