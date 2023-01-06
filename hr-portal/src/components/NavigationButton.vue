<template>
  <div class="navigation-button" :class="{ active: isCurrentRoute }">
    <div class="copy">{{ title }}</div>
    <div class="icon">
      <span v-if="image==null"  class="mdi" :class="icon"></span>
      <div v-else-if="image=='noimage'" class="noImageInitials" :style="`background-color:${bgColor}`">{{ initials }}</div>
      <img v-else class="profile-image" :src="image">
      <div v-if="(leaves > 0)" class="leaveNotification" :style="`background-color:${bgColor}`">{{ leaves }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NavigationButton',
  props: {
    title: String,
    icon: String,
    routeName: String,
    image: String,
    bgColor: String,
    initials: String,
    leaves: String
  },
  computed: {
    isCurrentRoute() {
      return this.$route.name == this.routeName;
    },
  },
  methods: {
    getImgUrl(filename){
      try{ 
        return 'data:image/jpeg;base64,' + filename;
      }catch(_){
        return require('../assets/imagealt.png');
      }
    },
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.leaveNotification{
  background: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-top: -20px;
    margin-left: -14px;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;
    font-size: 10px;
    background-color: red;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 2px solid white;
    color: white;
    font-weight: bold;
    z-index: 10;
}
.noImageInitials{
  display: inline-block;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 16px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 3px solid #fff;
}
.profile-image{
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 3px solid #fff;
  
}
.navigation-button {
  width: 100%;
  height: 40px;
  position: relative;
  float: left;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 10px;

  &.active {
    background-color: #e78d8d;

    .line-container .line {
      height: 70%;
      border-right: 17px solid #1b4989;
      border-top: 17px solid transparent;
      border-bottom: 17px solid transparent;
      transition: height 150ms ease-in-out;
    }

    .icon {
      color: #fff;
      opacity: 1;
    }
    .icon:hover{
      opacity: 1;
    }
  }

  .line-container {
    width: 4px;
    height: 100%;
    right: 0;
    top: 0;
    position: absolute;

    display: flex;
    align-items: center;
    justify-content: center;

    .line {
      width: 4px;
      height: 0%;
      position: relative;
      // background-color: $brand-primary;
      
      transition: height 150ms ease-in-out;
    }
  }

  &:hover {
    .copy {
      opacity: 1;
    }

    .icon {
      opacity: 1;
      
    }
    .mdi{
      transform:scale(1.3);
    }
    .profile-image{
      transform:scale(1.3);
    }
  }

  .copy {
    width: auto;
    flex-grow: 1;
    text-align: right;
    font-size: 20px;
    color: #fff;
    // opacity: .4;
  }
  .profile-image{
    transition:.5s;
  }
  .mdi{
    transition:.5s;
  }
  .icon {
    width: 60px;
    height: 100%;
    font-size: 24px;
    color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: 25px;
    // opacity: 0.7;
    scale: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}
</style>
