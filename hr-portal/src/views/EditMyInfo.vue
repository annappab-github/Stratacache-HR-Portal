<template>
  <EditMyInfo v-if="loadComponent" :propsEmpId="encryptedEmployeeID" :propsEmail="encryptedEmailID" />
</template>

<script>
import EditMyInfo from '../components/EditMyInfo.vue';
export default {
    components: {
      EditMyInfo
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