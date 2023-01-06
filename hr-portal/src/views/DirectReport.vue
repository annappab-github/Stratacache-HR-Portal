<template>
  <EmployeeInfo v-if="loadComponent" :propsEmpId="encryptedEmployeeID" :propsEmail="encryptedEmailID" />
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
    props: ['id'],
    mounted(){
      this.encryptedEmployeeID = this.id;
      this.loadComponent = true;
    }
};
</script>
<style scoped>
</style>