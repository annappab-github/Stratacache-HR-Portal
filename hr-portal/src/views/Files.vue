
<template>
       <div>
           <ManageFile/>
       </div>
</template>

<script>
import ManageFile from '../components/ManageFile.vue';

export default {
    components: {
        ManageFile
    }
};
</script>
<style scoped>
.background{
background-image: url('../assets/Background.png');
background-position: center;
background-repeat: no-repeat;
background-size: cover;
height: 100%;
width: 100%;
}

</style>