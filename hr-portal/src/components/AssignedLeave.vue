
<template>
<div class="topBg" ></div>
    <div class="announcement-holder">
        <div class="announcement d-flex">
            <div class="announcementText ml-4 flex-fill">ASSIGNED</div>
        </div>
        <div class="row ml-5 d-flex">
            <div class="col-md-6">
                <div class="type-of-leave">Manger : <span class="ml-3">Manish</span></div>
                    <div class="from-date">From</div>
                        <input
                        type="date"
                        class="form-control mt-3 from"
                        id="from"
                        autocomplete="off"
                        />
                    <button class="leave-apply" @click="submitAppliedLeaveData()">Apply</button>
                    <button class="leave-cancel">cancel</button> 
            </div>
            <div class="col-md-6">  
              <div class="type-of-leave">Assigned To  : <span class="ml-3">Ashok Kumar</span></div>
              <div class="to-date">To</div>
                  <input
                  type="date"
                  class="form-control to mt-3"
                  id="to"
                  autocomplete="off"
                    />
            </div>
        </div> 
    </div>
</template>

<script>
export default {
    components: {
    },
    data(){
        return{          
         
        };
    },
    mounted(){
     
    }
};
</script>
<style scoped>
.type-of-leave{
  color: #000;
  font-size: 28px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 40px;
  font-weight: bold;
}
.leave-duration{
   color: #000;
  font-size: 36px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 60px;
  font-weight: bold;
}

.leave-apply{
  color: #fff;
  font-size: 24px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 80px;
  background-color: red;
  width: 130px;
  height: 40px;
  border: none;
  font-weight: bold;

}
.leave-cancel{
  color: #fff;
  font-size: 24px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 80px;
  margin-left: 50px;
  background-color: grey;
  width: 130px;
  height: 40px;
  border: none;
  font-weight: bold;
}
.name{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}
.from-date{
  color: #000;
  font-size: 28px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 40px;
  font-weight: bold;
}
.from{
    width: 450px;
    height: 40px;
    font-size: 20px;

}
.to{
    width: 450px;
    height: 40px;
    font-size: 20px;
}
.to-date{
  color: #000;
  font-size: 28px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 40px;
  font-weight: bold;
}
.topBg {
    background-image: url(/img/Portal_Banner.7c3ce9a6.png);
    height: 25%;
    width: 100%;
    position: relative;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

.announcement{
  background-color: #EE3234;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  position: relative;
  top: 0px;
  color: #fff;
  width: 100%;
  z-index: 1;
}
.announcementText{
  height: 62px;
  line-height: 62px;
  font-size: 38px;
  letter-spacing: 0px;
}
.announcement-holder{
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  height: 480px;
  position: relative;
  top: -50px;
  z-index: 20;
  width: 70%;
  left: calc(calc(19%) - 50px);
}
.announcement-scroller{
  overflow-y: scroll;
  scrollbar-width: none;
  height: 497px;
  background-color: white;
   z-index: 20;
}
.announcement-scroller::-webkit-scrollbar {
  display: none;
}
</style>