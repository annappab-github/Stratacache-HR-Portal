
<template>
  <div class="loader" v-if="loading"></div>
    <div v-if="showLeaveNotification">
      <div class="viewer-overlay">
        <div class="leaves_popup">
          <h2 class="mt-4">Hi {{userDetail('name')}}</h2>
            <span style="color: red;font-size: 30px;">You have Pending leaves to Approve</span><br>
            <a style="text-decoration:underline;color:blue;font-size: 25px;" @click="goToRoute()">Click here</a>
        </div>
      </div>
     </div>     


  <div class="background">
    <!-- {{celebrationData}} -->
  <!-- <div class="celeb-gif-holder" v-for="(opt,index) in celebrationData" :key="index">
    <div v-if="opt[4]" class="celeb"></div>
  </div>  -->
  <!-- <div class="celeb" v-if="celebrationGIF"> -->
  <div class="bday-celeb-holder" v-if="celebrationGIF==1">
    <div class="card-closer" @click="closeCard()">
      <span class="mdi mdi-close-circle cancel"></span>
    </div>
    <div class="bday-card" v-if="(celebrationType.includes('HAPPY BIRTHDAY') && !celebrationType.includes('HAPPY ANNIVERSARY')) || (!celebrationType.includes('HAPPY BIRTHDAY') && celebrationType.includes('HAPPY ANNIVERSARY'))">
      <div class="outside">
        <div class="front" v-if="celebrationType.includes('HAPPY BIRTHDAY')">
          <p>Happy Birthday</p>
          <p>{{this.loggedInUser[0]['name']}}</p>
          <div class="cake"></div>
        </div>
        <div class="front" v-else>
          <p>Happy Work Anniversary</p>
          <p>{{this.loggedInUser[0]['name']}}</p>
          <div class="anniversary"></div>
        </div>
        <div class="back"></div>
      </div>
      <div class="inside" v-if="celebrationType.includes('HAPPY BIRTHDAY')">
        <p>Wishing you a very happy birthday filled with love and laughter</p>
        <h1>&#127873;</h1>
      </div>
      <div class="inside" v-else>
        <p>Wishing you a very happy anniversary filled with love and laughter</p>
        <h1>&#127882;</h1>
      </div>
    </div>
    <div class="bday-card" v-if="(celebrationType.includes('HAPPY BIRTHDAY') && celebrationType.includes('HAPPY ANNIVERSARY'))">
      <div class="outside">
        <div class="front">
          <p>Happy Birthday & Work Anniversary</p>
          <p>{{this.loggedInUser[0]['name']}}</p>
          <div class="cake"></div>
        </div>
        <div class="back"></div>
      </div>
      <div class="inside">
        <p>Wishing you a very happy birthday and work anniversary filled with love and laughter</p>
        <h1>&#127882;</h1>
      </div>
    </div>
  </div>
  <div class="topBg"></div>
    <div class="d-lg-flex justify-content-around left-side">
      
      <div class="left-column">
        <div class="leftBg">
          <div class="d-flex justify-content-between">
              <div class="user-detail">
                <label class="userName">{{userDetail('name')}}</label>
                <label class="user-designation">{{userDetail('designation')}}</label>
              </div>
              <div class="">
                <div v-if="this.email == 'admin@scala.com'" class="peopleShortNameProfile" :style="`background-color:#6e70ac`"> AD </div>
                <div v-else>
                  <div v-if="loggedInUser[0] != undefined && loggedInUser[0]['profile_pic']==null" class="peopleShortNameProfile" :style="`background-color:${loggedInUser[0]['imageBackgroundColour']}`">{{ loggedInUser[0]['employeeShortName'] }}</div>
                  <img v-else-if="loggedInUser[0] != undefined" class="userImg" :src="loggedInUser[0]['profile_pic']" >
                </div>
              </div>
          </div>
            
              <div class="manager-detail" :class="`${(userDetail('reportsTo') == 'null' ? 'manager-empty' : '')}`">
                <label class="managerName" v-if="userDetail('reportsTo') != 'null'">Reports to: <span class="managerName1">{{userDetail('reportsTo')}}</span> <span>({{userDetail('reportsTodesignation')}})</span></label>
              </div>
            <div class="leave-holder d-flex justify-content-center leaves" v-if="employement_type == 'Full Time Employee'">
              <div class="leave-container other-leaves" @click="otherLeaves()">
                <label class="leave-type m-0 other-leaves">Annual Leave</label> <br>
                <label v-if="leaveBalanceData[0]" class="balance m-0 other-leaves"><span class="mdi mdi-palm-tree leave-icon"></span>
                  <span>{{ leaveBalanceData[0].balance }}</span>
                </label> <br>
                <label class="daysAvailable m-0">DAYS AVAILABLE</label>
              </div>
              <div class="vertiLine"></div>
              <div class="leave-container other-leaves" @click="otherLeaves()">
                <label class="leave-type m-0 other-leaves">Sick Leave</label> <br>
                <label v-if="leaveBalanceData[0]" class="balance m-0 other-leaves"><span class="mdi mdi-briefcase-plus leave-icon"></span>
                  <span>{{ leaveBalanceData[1].balance}}</span>
                </label> <br>
                <label class="daysAvailable m-0">DAYS AVAILABLE</label>
              </div>
              <div class="vertiLine"></div>
              <div class="leave-container other-leaves" @click="otherLeaves()">
                <label class="leave-type m-0 other-leaves">Other Leaves</label> <br>
                <label v-if="leaveBalanceData[0]" class="balance m-0 other-leaves"><span class="mdi mdi-calendar-clock leave-icon"></span>
                  <!-- <span>{{ leaveBalanceData[0][7] }}</span> -->
                  </label> <br>
                <label class="daysAvailable m-0 other-leaves">OTHERS...</label>
              </div>
              
          </div>
        </div>
        <!--out of office info -->
          <div class="outOfOffice">
            <div class="whosOutText d-flex">
              <div class="celebrateImg">
                <img class="logoImg" :src="outofofficelogo" alt="icons" >
              </div>
              <div class="px-4 flex-fill">
                <label class="whosOut">WHO'S OUT</label>  
              </div>
            </div>
            <div class="onLeave">
              <div :class="`calenderIcons ${activecalTab==index ? 'activeDay' : ''}`" v-for="(cal,index) in calendar"    :key="index" @click="resetCalender(index, cal)"> 
                 <img class="todayIcon" :src="cal.icon" alt="icons" >
                 <div class="leave-text-holder">
                   <label class="leave-text">{{cal.name}}</label> 
                 </div>
              </div>
             <div>
             <hr style="margin: 1px">
              <div class="today" v-if="hideToday">
                <div class="personData">
                  <div class="today-horizontal">
                    <div v-if="outOfOffice[0]['today']==''">
                      <div>
                        <img :src="hardWorkImg" class="hardwork">
                        <label class="hardwork-name">Everyone seems to be working hard!</label>
                      </div>
                    </div>
                    <div class="flex-container" v-else>
                      <div class="outOfOffice-list" v-for="(emp, index) in outOfOffice[0]['today']" :key="index">
                        <div v-if="emp['profile_pic']==null" class="peopleShortNameOutOfOfficeToday" :style="`background-color:${emp['imageBackgroundColour']}`" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">{{ emp['employeeShortName'] }}</div>
                        <img v-else class="people" :src="emp['profile_pic']" alt="" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">
                        <!-- <div class="middle">
                          <div v-if="emp[0]=='noimage'" class="emp-text-noimage">{{emp[2]}}</div>
                          <div v-else class="emp-text">{{emp[2]}}</div>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                 

              <div class="thisWeek" v-if="this.whosOutFilter == 'Tomorrow' && this.hideToday == false">
                <div class="personData">
                  <div class="thisWeek-horizontal">
                    <div v-if="outOfOffice[0]['tomorrow']==''">
                      <div>
                        <img :src="hardWorkImg" class="hardwork">
                        <label class="hardwork-name">Everyone seems to be working hard!</label>
                      </div>
                    </div>
                    <div class="flex-container" v-else>
                      <div class="outOfOffice-list" v-for="(emp, index) in outOfOffice[0]['tomorrow']" :key="index">
                        <div v-if="emp['profile_pic']==null" class="peopleShortNameOutOfOfficeTomorrow" :style="`background-color:${emp['imageBackgroundColour']}`" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">{{ emp['employeeShortName'] }}</div>
                        <img v-else class="people" :src="emp['profile_pic']" alt="" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">
                        <!-- <div class="middle">
                          <div v-if="emp[0]=='noimage'" class="emp-text-noimage">{{emp[2]}}</div>
                          <div v-else class="emp-text">{{emp[2]}}</div>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="thisWeek" v-if="this.whosOutFilter == 'Rest of the month' && this.hideToday == false">
              <!-- <div class="px-3">
                  <label class="leave-text1">Rest of the month</label> 
                </div>-->
                <div class="personData">
                  <div class="thisWeek-horizontal">
                    <div v-if="outOfOffice[0]['thisMonth']==''">
                      <div>
                        <img :src="hardWorkImg" class="hardwork">
                        <div class="hardwork-name" style="display: inline;">Everyone seems to be working hard!</div>
                      </div>
                    </div>
                    <div class="flex-container" v-else>
                      <div class="outOfOffice-list" v-for="(emp, index) in outOfOffice[0]['thisMonth']" :key="index">
                        <div v-if="emp['profile_pic']==null" class="peopleShortNameOutOfOffice" :style="`background-color:${emp['imageBackgroundColour']}`" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">{{ emp['employeeShortName'] }}</div>
                        <!-- <div v-if="emp[0]=='noimage'" class="peopleShortNameOutOfOffice" :style="`background-color:${emp[4]}`" >{{ emp[3] }}</div> -->
                        <img v-else class="people" :src="emp['profile_pic']" alt="" data-toggle="tooltip" data-placement="bottom" :title="emp['name']">
                        <div v-if="emp==''"></div>
                        <!-- <div class="middle">
                          <div v-if="emp[0]=='noimage'" class="emp-text-noimage">{{emp[2]}}</div>
                          <div v-if="emp[0]!='noimage'" class="emp-text">
                            {{empTextFormat(emp[2],'name')}}<br>
                            {{empTextFormat(emp[2],'date')}}
                          </div>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             </div>
            </div>
          </div>

          <!-- birthday anniversary detail      -->
          <div class="celebration d-flex">
            <div class="celebrateImg">
              <img class="logoImg" :src="celebrationlogo" alt="icons" >
            </div>
            <div class="celeb-container flex-fill px-4">
              <label class="celebrateText">LET'S CELEBRATE</label>
            </div>
          </div>
          <div class="celebrate" >
              <div :class="`celebIcons ${activecelebTab==index ? 'activeDay' : ''}`" v-for="(cele,index) in celebrate"    :key="index" @click="resetCelebration(index)"> 
                    <img class="todayIcon" :src="cele.icon" alt="icons" >
                    <div class="celeb-text-holder">
                      <label class="celeb-text">{{cele.name}}</label> 
                    </div>
              </div>
              <hr style="margin: 1px;">
                 <div class="no-celebration" v-if="filteredCelebrationData().length == '0'">
                    <img class="nocelebrationImg" :src="nocelebrationImg" alt="icons" >&nbsp;&nbsp;
                    <div class="" style="display: inline">No Upcoming Celebration!</div>
                 </div>
                  <div class="events" v-else>
                    <div class="">
                      <div  v-for="(opt,index) in filteredCelebrationData()" :key="index" style="">
                        <div class="">
                          <div :class="`bday-container d-flex ${opt[4] ? 'activeCelebration' : ''}`">
                            <div class="px-3">
                              <div v-if="opt[0]==null" class="peopleShortNameCelebrate" :style="`background-color:${opt[6]}`">{{ opt[5] }}</div>
                              <img v-else class="celeb-pic" :src="opt[0]" width="50" height="50">
                            </div>
                            <div class="celeb-detail">
                              <label class="date m-0">{{reFormatDate(opt[2])}}</label> <br>
                              <label class="employeeName m-0 ">{{opt[1]}}</label> 
                            </div>
                            <div class="celeb-event d-flex justify-content-center" >
                              <label class="event  mt-3" v-if="opt[3] == 'HAPPY ANNIVERSARY'">{{opt[3]}}</label>
                              <label class="event  mt-3" v-if="opt[3] == 'HAPPY BIRTHDAY'">{{opt[3]}}</label>
                            </div>
                          </div>  
                        </div>  
                      </div>
                    </div>   
                  </div>  
          </div>
      </div>
      <div class="right-column mx-4 ">
        <div class="announcement-holder">
          <div class="announcement d-flex">
            <div class="announcementImg">
                <img class="logoImg" :src="writeimg" alt="icons">
            </div>
            <div class="announcementText ml-4 flex-fill">ANNOUNCEMENTS</div>
          </div>
            <!-- <div class="" v-for="(opt,index) in celebrationData" :key="index">
              <div v-if="opt[4]" class="row bdayGreeting">
                  <div class="col-sm-1 ml-4 mt-3 mb-3">
                      <div v-if="opt[0]=='noimage'" class="peopleShortNameCelebrate">{{ opt[5] }}</div>
                      <img v-else class="bdaycard"   :src="getImgUrl(opt[0])" width="50" height="50">
                  </div>
                  <div class="col-sm-8">
                    <div class="bdayMsg ml-4">
                      Wishing you the best on your birthday and everything good in the year ahead.
                    </div>
                  </div>
                </div>
            </div> -->
            <div class="announcement-scroller">
              <div class="no-announcement" v-if="announcementData.length == '0'">
                    <img class="noAnnouncementImg" :src="noAnnouncementImg" alt="icons" >
                    <div class="" style="display: inline">No Announcements!</div>
              </div>
              <div class="announcement-list" v-else>
                <div id="accordion">
                    <div class="card" v-for="(announce,index) in announcementData" :key="index">
                        <div class="card-header" id="headingOne" >
                            <h5 class="mb-0">
                                <button class="btn btn-default announcement-btn py-3 px-4" id="announcementBtn" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapse" @click="announcementClick(index)">
                                  {{announce[0]}}
                                </button>
                            </h5>
                        </div>
                        <div :id="'collapse'+index" class="collapse" :class="`${index==0 ? 'show' : ''}`" aria-labelledby="headingOne" data-parent="#accordion" >
                            <div class="card-body">
                                <p v-html="announce[1]"></p>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          
        </div>
        
        <div class="holidayList">
          <div class="timeoff d-flex">
            <div class="timeoffImg">
                <img class="logoImg" :src="holidaylogo" alt="icons">
            </div>
            <div class="timeoffText ml-4 ">
              <span class="upcomingText">UPCOMING HOLIDAYS</span> 
            </div>
          </div>
            <div class="upcomingTime-off">
              <div :class="`countryFlags ${activeHolidayTab==index ? 'activeCountry' : ''}`" v-for="(flag,index) in flags" :key="index" @click="resetActiveCountry(index,flag)">
                <img class="flag-img" :src="flag.flag[0]" />
                <div class="flag-text-holder">
                  <div class="flag-text">{{flag.flag[2]}}</div>
                </div>
              </div>
              <hr style="margin: 1px">
              <div class="holiday-scroller">
                <div class="no-upcoming-holiday" v-if="filteredDataTemp().length == 0">
                  <div class="no-upcoming-holiday-data">
                    <img :src="noHolidayImg" class="no-upcoming-holiday-image">
                    <span class="no-holiday-text">Unfortunately there are no upcoming holidays!</span> 
                  </div>
                </div>
                <div class="holidayDataContainer" >
                  <table class="table" style="margin: 0" v-for="(hld, j) in filteredDataTemp()" :key="j">
                    <tr :class="`${(hld.type == 'optional' ? 'optionalHoliday' : 'publicHoliday')} timeOffRow`" >
                      <td class="d-flex" >
                        <div class="calenderIcon">
                          <span class="mdi mdi-calendar calendar px-2"></span>
                        </div>
                        <div class="holidayDetail">
                          <div v-if="hld.type == 'optional'" :class="`${(hld.type == 'optional' ? 'optional-text' : 'public-text')} pt-1`">{{hld.date}} {{(hld.type == "optional" ? "* optional" : "")}}</div>
                          <div v-else-if="hld.type == 'public (CBR)'" :class="`${(hld.type == 'public (CBR)' ? 'optional-text' : 'public-text')} pt-1`">{{hld.date}} {{(hld.type == "public (CBR)" ? "* only for Canberra" : '')}}</div>
                          <div v-else :class="`${(hld.type == 'public (Special Non-Working Day)' ? 'optional-text' : 'public-text')} pt-1`">{{hld.date}} {{(hld.type == "public (Special Non-Working Day)" ? "* special non-working day" : '')}}</div>
                          <div class="holiday-name-holder">
                            <div v-for="(publicHoliday,j) in hld.regions" :key="j" class="holiday-name">
                              {{publicHoliday.holiday}}  ({{publicHoliday.region}}) <span v-if="hld.regions.length > 1 && hld.regions.length != j+1">,&nbsp;</span>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  </div>
              </div>
            </div>
          </div>

          
        </div>
    </div>
  </div>
</template>

<script>
import verticalline from '../assets/verticalline.png';
import outofoffice from '../assets/outofoffice.png';
import celebrationimg from '../assets/celebration.png';
import anniversaryimg from '../assets/anniversary.png';
import write from '../assets/advertising.png';
import vacation from '../assets/holiday.png';
import bdaycard from '../assets/SG002.png';
import hardwork from '../assets/work.png';
import all from '../assets/all.png';
import australia from '../assets/australia.png';
import china from '../assets/china.png';
import hongkong from '../assets/hong-kong.png';
import india from '../assets/india.png';
import japan from '../assets/japan.png';
import malaysia from '../assets/malaysia.png';
import philippines from '../assets/philippines.png';
import singapore from '../assets/singapore.png';
import today from '../assets/today.png';
import tomorrow from '../assets/tomorrow.png';
import month from '../assets/month.png';
import noHoliday from '../assets/noHoliday.png';
import allCelebImg from '../assets/party.png';
import bdayImg from '../assets/happybirthday.png';
import annivImg from '../assets/anniv.png';
import nocelebrationImg from '../assets/nocelebration.png';
import noAnnouncement from '../assets/noAnnouncement.png';
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import { decryptData, encryptData } from '../js/utils/encrypt';

export default {
    components: {
    },
    data() {
        return {
        verticallinelogo: verticalline,
        outofofficelogo: outofoffice,
        celebrationlogo: celebrationimg,
        holidaylogo: vacation,
        anniversaryBg: anniversaryimg,
        bdaycard: bdaycard,
        writeimg: write,
        hardWorkImg: hardwork,
        noHolidayImg: noHoliday,
        allCelebImg: allCelebImg,
        nocelebrationImg: nocelebrationImg,
        noAnnouncementImg: noAnnouncement,
        calendar: [
          {icon: today, name: 'Today'},
          {icon: tomorrow, name: 'Tomorrow'},
          {icon: month, name: 'Rest of the month'},
        ],
        celebrate: [
            {icon: allCelebImg, name: 'All'},
            {icon: bdayImg, name: 'Birthday'},
            {icon: annivImg, name: 'Anniversary'},
        ],
        filterCelebration: 'All',
       activecalTab: 0,
       activecelebTab: 0,
        numPages: 0,
        currentPage: 1,
        filter: 'All',
        whosOutFilter: '',
        celebration: '',
        hideToday: true,
        hideAnniversary: true,
        hideBday: true,
        outOfOffice: [
            {imgs: 'IN032', name: 'Manish Kumar'},
            {imgs: 'SG002', name: 'Ayesha Kumar'},
            {imgs: 'IN025', name: 'Mayank Gautam'},
            {imgs: 'IN060', name: 'Tahir Imran'},
            {imgs: 'SG035', name: 'Rachna Masani'},
        ],
        flags: [
          {flag: [all,'All','All']},
          {flag: [australia,'AU','Australia']},
          {flag: [china,'CN','China']},
          {flag: [hongkong,'HK','Hong Kong']},
          {flag: [india,'IN','India']},
          {flag: [japan,'JP','Japan']},
          {flag: [malaysia,'MY','Malaysia']},
          {flag: [philippines,'PH','Philippines']},
          {flag: [singapore,'SG','Singapore']}
        ],
        activeHolidayTab: 0,
        loading: false,
        email: '',
        loggedInUser: [],
        employeeId: '',
        celebrationData: [],
        announcementData: [],
        holidayData: [],
        leaveBalanceData: [],
        employees: [],
        upcomingHolidays: false,
        managerData: [],
        celebrationGIF: 0,
        celebrationType: [],
        showLeaveNotification: false,
        employement_type: ''
     };
    },
    computed: {
      routeNames: () => { return routeNames; }
    },
    mounted(){
      this.$emit('showNavigationBar');
      const secretKey = process.env.KEY || 'myscecretkey';
      var encryptedEmailID = localStorage.getItem('Email');
      this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');

      var encryptedEmployeeID = localStorage.getItem('EmployeeID');
      this.employeeId = decryptData(encryptedEmployeeID, secretKey);
      
      if(this.email != 'admin@scala.com'){
        this.loading = true;
        axios.post(this.$hostName + '/getEmployeeReportingManager', {empid: this.employeeId}).then((response) =>{
        let leaveList = response.data;
        let tempLeavelist = leaveList.filter( element => {
          if(element.status == 'pending')
              return true;
        });
        if(tempLeavelist.length > 0 ){
          this.showLeaveNotification = true;          
          this.$emit('leaveNotificationCount', tempLeavelist.length);
        } else {
          this.showLeaveNotification = false;          
          this.$emit('leaveNotificationCount', 0);
        }
        
      });
        axios.post(this.$hostName + '/home',{empId: this.employeeId},)
        .then((response) => {
          response.data[0].employeeData.forEach((element) => {
            this.loggedInUser.push(element);
            this.employement_type = this.loggedInUser[0]['employement_type'];
          });
          if(this.email == 'admin@scala.com'){
            this.employeeId = 'ADM01';
          } else {
            this.employeeId = this.loggedInUser[0]['empid'];
          }
          this.celebrationData = response.data[1].celebrationData;
          this.announcementData = response.data[2].announcementData;
          this.holidayData = response.data[3].holidayData;
          this.leaveBalanceData = response.data[4].leaveBalanceData;
          this.outOfOffice = response.data[5].whoIsOutData;
          this.managerData = response.data[6].managerData;
          this.celebrationCheck();
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
      }
        
    },
   methods: {
    closeCard(){
      this.celebrationGIF = '';
    },
    goToRoute(){
      this.$router.push('/team');
    },
    celebrationCheck(){
      // let name = this.loggedInUser[0]['name'];
      let empId = this.loggedInUser[0]['empid'];
      let data = this.celebrationData;
      var today = new Date();
      let mm = today.getMonth() + 1; // Months start at 0!
      let dd = today.getDate();
      if(mm<10){
        mm='0'+mm;
      }
      if(dd<10){
        dd='0'+dd;
      }
      var todaysDate = `${dd}-${mm}`;
      const secretKey = process.env.KEY || 'myscecretkey';
      let mkLocalData = localStorage.getItem('celebrationGIF');
      if (!mkLocalData) {
          mkLocalData = 'null';
      }
      var celebGIF = decryptData(mkLocalData, secretKey);
      if(celebGIF == null){
        this.celebrationGIF = 0;
      } else {
        this.celebrationGIF = celebGIF;
      }
      var counter = 0;
      for (let idx in data){
        if(data[idx][2].substring(0,5)== todaysDate){
          if(empId==data[idx][7]){
            counter = counter + 1;
            if(counter%2){
              this.celebrationGIF = this.celebrationGIF + 1;
            }
            const encryptedcelebrationGIF = encryptData(this.celebrationGIF, secretKey);
            localStorage.setItem('celebrationGIF', encryptedcelebrationGIF);
            if(data[idx][4])
              this.celebrationType.push(data[idx][3]);
          }
        }
      }
    },
    otherLeaves(){
      this.$router.push({ name: routeNames.myInfo, params: { others: true } });
    },
     filteredDataTemp() {
      let tempData = [], data = this.holidayData;
      for(let key in data) {
        for(let type in data[key]){
          let format = {date: '', type: '', regions: []};
          format.type =type;
          let dataDateWise = data[key][format.type];
          if(this.filter == 'All') {
            for(let holiday in dataDateWise) {
                format.date = key;
                let tempformat = {region: dataDateWise[holiday].region, holiday: dataDateWise[holiday].holiday};
                format.regions.push(tempformat);
            }
            if(format.regions.length > 0)
              tempData.push(format);
          } else {   
            for(let holiday in dataDateWise) {
              let region = dataDateWise[holiday].region;
              if(region.includes(',')) { 
                let regArr = region.split(',');
                for(let arr in regArr){
                  if (regArr[arr] == this.filter){

                    format.date = key;
                    let tempformat = {region: regArr[arr], holiday: dataDateWise[holiday].holiday};
                    format.regions.push(tempformat);
                  }
                }
              } else {
                if(region == this.filter) {

                  (dataDateWise[holiday]);
                  format.date = key;
                  let tempformat = {region: dataDateWise[holiday].region, holiday: dataDateWise[holiday].holiday};
                  format.regions.push(tempformat);
                }
              }
            }
            if(format.regions.length > 0)
              tempData.push(format);
          }
        }
      }
      return tempData;
     },

     filteredCelebrationData(){
        let  celebAllData = this.celebrationData;
        let filteredArr = [];
        let d = new Date();
        let year = d.getFullYear();
        if(this.filterCelebration == 'All'){
          // return this.celebrationData;
          for(let data in celebAllData){
            if(celebAllData[data][3] == 'HAPPY BIRTHDAY'){
              filteredArr.push(celebAllData[data]);
            } 
            else if( celebAllData[data][2].split('-')[2] != year && celebAllData[data][3] == 'HAPPY ANNIVERSARY') {
              filteredArr.push(celebAllData[data]);
            }
          }
          return filteredArr;
        } else {
          for(let data in celebAllData){
            if(celebAllData[data][3] == 'HAPPY BIRTHDAY' && this.filterCelebration == 'HAPPY BIRTHDAY'){
              filteredArr.push(celebAllData[data]);
            } else if(celebAllData[data][3] == 'HAPPY ANNIVERSARY' && this.filterCelebration == 'HAPPY ANNIVERSARY' && celebAllData[data][2].split('-')[2] != year) {
              filteredArr.push(celebAllData[data]);
            }
          }
           return filteredArr;
        }
     },


     empTextFormat(data,type){
       var pos = data.indexOf('(');
       var str1 = data.substring(0,pos-1);
       var str2 = data.substring(pos);
       if(type == 'name'){
         return str1;
       } else {
         return str2;
       }
     },
       changePage(type){
      if (type == 'next') {
        if(this.currentPage<this.numPages)
          this.currentPage = this.currentPage+1;
      } else if(type == 'previous') {
        if(this.currentPage>1)
          this.currentPage = this.currentPage-2;
      } 
    },
      userDetail(type){
        if(this.email == 'admin@scala.com'){
          if(type == 'name'){
            return 'Admin';
          } else if (type == 'designation'){
            return 'Administrator';
          } else if (type == 'reportsTo'){
            return 'Somebody';
          }else if (type == 'reportsTodesignation'){
            return '';
          }
        } else if(this.loggedInUser[0] != undefined){
          if(type == 'name'){
            return this.loggedInUser[0]['name'];
          } else if (type == 'designation'){
            return this.loggedInUser[0]['position'];
          } else if (type == 'reportsTo'){
            if(this.managerData == ''){
              return 'null';
            } else {
            return `${this.managerData[0]['name']}`;
            }
          }else if (type == 'reportsTodesignation'){
            return this.managerData[0]['position'];
          }
        }
        
      },
      getImgUrl(filename){
        try{ 
          return 'data:image/jpeg;base64,' + filename;
        }catch(_){
          return require('../assets/imagealt.png');
        }
      },
      reFormatDate(val){
        const dateArr = val.split('-');
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
          'July', 'August', 'September', 'October', 'November', 'December'
        ];
        var celeb = dateArr[2]+'-'+dateArr[1]+'-'+dateArr[0];
        var celebDate = new Date(celeb);
        var date = celebDate.getDate();
        var month = monthNames[celebDate.getMonth()];
        return date+' '+month;
      },
    announcementClick(index){
      var collapse1 = document.getElementById('collapse'+index);
      var collapse = document.getElementsByClassName('collapse');
      for (let i = 0; i < collapse.length; i++) {
        if(collapse[i].classList.contains('show') && i!=index){
          collapse[i].classList.remove('show');
        } 
      }
      if(collapse1.classList.contains('show')){
        collapse1.classList.remove('show');
      } else {

      collapse1.classList.add('show');
      }
    },
    resetActiveCountry(index,flag) {
      this.activeHolidayTab = index;
      this.filter = flag.flag[1];
    },
    resetCalender(index, cal){
       this.whosOutFilter = cal.name;
       if(this.whosOutFilter == 'Tomorrow' || this.whosOutFilter == 'Rest of the month')
         this.hideToday = false;
        else {
          this.hideToday = true;
        }
       this.activecalTab = index;
    },
    resetCelebration(index){
      if(index == '0'){
       this.filterCelebration = 'All';
      } else if(index == '1'){
        this.filterCelebration = 'HAPPY BIRTHDAY';
      } 
      else if(index == '2'){
        this.filterCelebration = 'HAPPY ANNIVERSARY';
      }
      this.activecelebTab = index;
    },
  }
};
</script>
<style scoped>
.leaves_popup {
  height: 220px;
  position: absolute;
  z-index: 20;
  background-color: #fff;
  backdrop-filter: blur(5px);
  width: 50%;
  transform: translate(50%,50%);
  box-shadow: 1px 0px 40px #787171;
  text-align: center;
}
.viewer-overlay  {
   width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  -webkit-backdrop-filter: blur(5px);
  position:absolute;
  z-index:21;
 
}

.nocelebrationImg{
  width: 70px;
}
.no-celebration{
  text-align: center;
  position: relative;
  line-height: 228px;
  height: 228px;
}
.no-announcement{
  text-align: center;
  position: relative;
  top: 50%;
  transform: translate(0,-50%);
  
}
.noAnnouncementImg{
  width: 100px;
}
.flag-img{
  border-radius: 50%;
  -webkit-border-radius: 50%;
  height: 37px;
  width: 37px;
  /* box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; */
}
.todayIcon{
 /* box-shadow: rgba(0, 0, 0, 0.2) 0px 1px 1px;*/
}
.holidayDataContainer{
  text-align: left;
}
.no-upcoming-holiday{
  width: 100%;
  height: 100%;
  text-align: center;
  
}
.no-upcoming-holiday-data{
  position: relative;
  top: 50%;
  transform: translate(0,-50%);
  
}
.no-upcoming-holiday-image{
  height: 100px
}
.nav-button{
  font-size: 10px;
}
.leave-text1{
  border-bottom: 2px solid #EE3234;
  margin: 0;
  font-weight: bold;
}

.calenderIcons{
  padding: 2px;
  display: inline-flex;
  height: 45px;
  width: 45px;
  position: relative;
  margin: 5px 10px 5px 10px;
  cursor: pointer;
}
.calenderIcons:hover .leave-text-holder{
  opacity: 1;
}
.activeDay {
    border-bottom: 2px #122e55 solid;
    font-weight: bold;
}
.todayIcon{
  width: 40px;
  height: 40px;
}
.celebIcons{
  padding: 2px;
  display: inline-flex;
  height: 45px;
  width: 45px;
  position: relative;
  margin: 5px 10px 5px 10px;
  cursor: pointer;
}
.celebIcons:hover .celeb-text-holder{
  opacity: 1;
}
.location {
  /* width: 10%; */
  position: relative;
  /* float: right; */

}
a {
  text-decoration: none;
  display: inline-block;
  padding: 8px 16px;
}

a:hover {
  background-color: #ddd;
  color: black;
}

.previous {
  background-color: #f1f1f1;
  color: black;
}
.next {
  background-color: #04AA6D;
  color: white;
}
.round {
  border-radius: 50%;
}
.hardwork{
  padding: 1px;
  margin: 7px 10px 7px 10px;
  height: 40px;
  widows: 40px;
  border-radius: 50%;
  background-color: #ffb2b2;
}
.holiday-name-holder{
  width: 100%;
}
.holiday-name{
  display: inline-block;
}
.optionalHoliday{
  background-color: #ECECEC;
}
.timeOffRow{
  height: 54px;
}
.timeOffRow td{
  padding: 0;
  border-bottom: #dee2e6 solid 1px;
  border-top: none;
}
.calenderIcon{
  padding: 2px;
  font-size: 28px;
  color: #000000;
}
.calendar{
  position: relative;
  top: 5%;
}
.holidayDetail{
  display: inline-block;
  
  width: calc(100% - 50px);
  padding: 0;
}
.optional-text{
  font-weight: bold;
}
.public-text{
  font-weight: bold;
}
.bdayMsg{
    font-size: 30px;
    
    letter-spacing: 0px;
    animation: blinker 1s ease-in-out infinite alternate;
}
.bdaycard{
  width: 60px;
  height: 60px;
  z-index: 999;
  border-radius: 50%;
}
.bdayGreeting{
  width: 100%;
  background-image: url('../assets/anniversary.png');
  background-color: #fff;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  position: relative;
  border-radius: 5px;
  color: orangered;
}

@keyframes blinker { to { opacity: 0; } }

.upcomingTime-off{
  height: 286px;
  text-align: center;
}
.holiday-scroller{
  overflow: auto;
  height: 228px;
}
.timeOff{
    background-color: #EE3234;
    height: 60px;
    line-height: 60px;
    color:  #fff;
    font-size: 22px;
    font-weight: 700px;
    
    letter-spacing: 0px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}
.left-side{
    font-family: 'Montserrat';
    position: absolute;
    /* overflow: auto; */
    left: 60px;
    padding: calc(25vh - 60px) 20px 30px 20px;
    margin-bottom: 50px;
    top: 0;
    height: 100%;
    width: calc(100% - 60px);
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
.left-side::-webkit-scrollbar {
    display: none;
}
.card-header{
  padding: 0;
  background-color: #d05959;
  color: white !important;
}
.btn:focus {
  outline: none;
  box-shadow: none;
}
.btn:hover{
  background-color:  #e78d8d;
  color: white;
}
.announcement-btn{
  width: 100%;
  text-align: left;
  color: #fff;
}
.card-body{
  height: 376px;
  position: relative;
  overflow: auto;
  background-color: white;
  white-space: pre-wrap;
  scrollbar-width: thin;
}

.left-column{
  width: 560px;
  margin: 0px 15px 0px 15px;
  min-width: 420px;
  
}
.right-column{
  width: 50%;
  margin: 0px 15px 0px 15px;
  height: calc(1080px - 10vh);
}
.announcement-holder{
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  height: 557px;
  position: relative;
}
.announcement-scroller{
  overflow-y: scroll;
  scrollbar-width: none;
  height: 497px;
  background-color: white;
}
.announcement-scroller::-webkit-scrollbar {
  display: none;
}
.holidayList{
  top: 30px;
  position: relative;
  height: 346px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
}
.celeb-pic{
  border-radius: 50%;
  margin-top: 3px;
  /* vertical-align: middle; */
  /* position: relative; */
  /* top: 50%; */
  /* transform: translate(0,-50%); */
}
.peopleShortNameProfile{
  display: inline-block;
  width: 100px;
  height: 100px;
  margin-right: 30px;
  margin-top: 30px;
  margin-bottom: 14px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 40px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 4px solid #fff;
}
.peopleShortNameOutOfOfficeToday{
  width: 100%;
  height: 100%;
  border-radius:50%;
  font-size: 20px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  /* top: 26%;
  transform: translate(0,-50%); */
}
.peopleShortNameOutOfOfficeTomorrow{
  width: 100%;
  height: 100%;
  border-radius:50%;
  font-size: 20px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  /* top:26%;
  transform: translate(0,-50%); */
}

.peopleShortNameOutOfOffice{
  width: 100%;
  height: 100%;
  border-radius:50%;
  font-size: 20px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  /* top: 26%;
  transform: translate(0,-50%); */
}
.peopleShortNameCelebrate{
  width: 50px;
  height: 50px;
  border-radius:50%;
  font-size: 25px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 3px;
  /* position: relative;
  top: 50%;
  transform: translate(0,-50%); */
}
.bday-container{
  background-color: #aaccff;
  height: 58px;
  width: 100%;
  background: transparent;
  border-bottom: #dee2e6 solid 1px;
}
.activeCelebration {
  background-image: url('../assets/celeb.gif');
}
.anniversary-container{
  background-color: #e1edff;
  background-size: cover;
  height: 58px;
  width: 100%;
  background: transparent;
  border-bottom: #dee2e6 solid 1px;
}
.user-detail{
  width: 100%;
  padding-left: 20px;
}
.manager-detail{
  width: 100%;
  padding-left: 20px;
  padding-top: 10px;
}
.manager-empty{
  height: 54px;
}
.background{
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  position: absolute;
  overflow: auto;
  height: 100%;
  width: 100%;
}
.background::-webkit-scrollbar {
    display: none;
}
.logoImg {
  width: 100%;
  height: 100%;
  padding: 20%;
}
@media (min-width: 768px) and (max-width: 991.98px) { 
  .bday-card{
    height: 500px !important;
    width: 700px !important;
    left: 60% !important;
  }
  .bday-card:hover {
    transform: rotate(-3deg)scale(1.0)translate(-43%,-10%) !important;
  }
  .bday-card:hover .outside {
    transform: rotateY(-90deg) !important;
  }
  .celeb-text{
  display: none;
  }
  .leave-text{
    display: none;
  }
  .flag-text{
     display: none;
  }
  .right-column{
    width: 100%;
    margin: 30px 15px 0px 0px !important;
  }
  .announcement-holder{
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
    height: 557px;
    overflow-y: scroll;
    scrollbar-width: none;
  }
  .left-column{
    position: relative;
    left: 50%;
    transform: translate(-50%);
    width: auto !important;
    min-width: auto !important;
    margin: 0 6px 0 0px !important;
  }
  .holidayList{
    width: 100% !important;
    position: relative;
    margin-left: 0px !important;
    height: 329px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }

  .holiday-scroller{
  overflow: auto !important;
  height: 208px !important;
}

}
@media only screen and (max-width: 575.98px) {
.flex-container{
  grid-template-columns: repeat(auto-fit, minmax(45px, 47px)) !important;
}
.thisWeek-horizontal{
  padding: 0 10px 0 10px !important;
}
.today-horizontal{
  padding: 0 10px 0 10px !important;
}
.flag-img{
  height: 26px !important;
  width: 26px !important;
}
.bday-celeb-holder{
  left: 60px;
}
.countryFlags{
  padding: 0px !important;
  height: 32px !important;
  width: 30px !important;
  margin: 5px 2px 5px 2px !important;
}
.celeb-text{
    display: none;
}
.nocelebrationImg{
  width: 70px;
  height: 70px;
  margin: 0px 5px 0px 5px;
}
.leave-text{
  display: none;
}
.flag-text{
  display: none;
}
div.personData{
    overflow: auto !important;
    position: relative;
    width: 100%;
    height: 100px !important;
    overflow-x: hidden !important;
    /* padding: 0 15px 0 15px; */
}
  .holiday-scroller{
  overflow: auto !important;
  height: 208px !important;
}
  .hardwork{
  padding: 1px;
  margin: 10px 10px 7px 0px !important;
  height: 30px ;
  widows: 30px;
  border-radius: 50%;
  background-color: #ffb2b2;
}
.hardwork-name{
  font-size: 14px;
}
  .no-upcoming-holiday{
  width: 100%;
  height: 80%;
  text-align: center;
  
}
.no-holiday-text{
  width: 100px !important;
  position: absolute;
  text-align: center;
}
.no-upcoming-holiday-data{
  position: relative;
  top: 50%;
  left: 30%;
  transform: translate(-50%,-50%);
}
.no-upcoming-holiday-image{
  height: 100px
}
  .leave-holder{
    width: 100%;
    position: absolute;
    top: 190px !important;
  }
  .daysAvailable{
    font-size: 10px !important;
  }
  .leave-type{
    font-size: 10px !important;
  }
  .balance{
    font-size: 25px !important;
  }
  .timeoffText{
    font-size: 18px !important;
  }
  .upcomingText{
    top: 50% !important;
    transform: translate(-50%);
    font-size: 20px;
    position: absolute !important;
  }
  .left-column{
    position: relative;
    left: 50%;
    transform: translate(-50%);
    width: auto !important;
    min-width: auto !important;
    margin: 80px 6px 0 6px !important;
  }
  .holidayList{
    width: 310px !important;
    position: relative;
    margin-left: 0px !important;
    height: 350px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }
  .upcomingTime-off{
    height: 290px !important;
  }

  .left-side{
    padding: 0 !important;
    left: 60px !important;
    overflow-x: hidden;
    width: 80% !important;
  }
  .background{
    overflow-y: scroll;
    overflow-x: hidden;
  }
  .leftBg{
    width: 310px !important;
  }
  .userImg{
    width: 80px !important;
    height: 80px !important;
    margin-top: 20px !important;
    margin-right: 20px !important;
  }
  .peopleShortNameProfile{
    width: 80px !important;
    height: 80px !important;
    margin-top: 20px !important;
    margin-right: 20px !important;
    font-size: 40px;
  }
  .user-designation{
    position: relative;
    left: 15px !important;
    top: 92px !important;
    font-size: 12px !important;
    width: 200px !important;
  }
  .user-detail{
    width: 70% !important;
    padding: 30px 15px 15px 15px !important;
  }
  .manager-detail{
    padding: 20px 15px 0px 15px !important;
  }
  .userName{
    font-size: 16px !important;
    width: 60% !important;
  }
  .right-column{
    height: 100%;
    width: 310px !important;
    margin-left: 12px !important;
    margin-top: 15px !important;
    margin-bottom: 60px !important;
  }
  .outOfOffice{
    width: 310px !important;
  }
  .celebration{
    width: 310px !important;
  }
  .celebrate{
    width: 310px !important;
  }
  .event{
    font-size: 13px !important;
  }
  .employeeName{
    font-size: 11px !important;
  }
  .date{
    font-size: 11px !important;
  }
  .bday-card{
    height: 400px !important;
    width: 500px !important;
    left: 60% !important;
  }
  .cake{
    width: 160px !important;
    height: 160px !important;
  }
  .outside p{
    font-size: 20px !important;
  }
  .anniversary{
    width: 160px !important;
    height: 160px !important;
  }
  .bday-card:hover {
    transform: rotate(-3deg)scale(1.0)translate(-43%,-10%) !important;
  }
  .bday-card:hover .outside {
    transform: rotateY(-90deg) !important;
  }
  .card-closer{
    right: 20% !important;
    height: 30px !important;
    width: 30px !important;
  }


  .leaves_popup {
    /* height: 100%; */
    position: absolute;
    /* left: -50px; */
    z-index: 20;
    background-color: #fff;
    backdrop-filter: blur(5px);
    width: 70%;
    transform: translate(30%,50%);
    box-shadow: 1px 0px 40px #787171;
    text-align: center;
  }





}
/*mobile view css end*/







.leftBg{
  background-color: #d05959;
  background-position: center, center;
  background-repeat: no-repeat, repeat;
  position: relative;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  height: 300px;
  top: 0px;
  opacity: 0.2px;
  background-size: cover, cover;
  width: 100%;
}
/*.leftBg1{
  background-color: #d05959;
  background-position: center, center;
  background-repeat: no-repeat, repeat;
  position: relative;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  height: 250px;
  top: 0px;
  opacity: 0.2px;
  background-size: cover, cover;
  width: 100%;
}*/
.whosOutText{
  
  letter-spacing: 0px;
  font-size: 22px;
  background-color: #EE3234;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  position: relative;
  top: 35px;
  height: 60px;
  color: #fff;
}
.whosOut{
  height: 60px;
  line-height: 60px;
}
.onLeave{
    position: relative;
    height: 161px;
    top: 35px;
    text-align: center;
   /* overflow-y: scroll;*/
   background-color: white;
    box-shadow: rgb(0 0 0 / 35%) 0px 5px 15px;
}
/*.leave-text{
 background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  width: 100%;
  font-size: 8px;
  position: absolute;
  top: -10px;
  left: calc(100% - 25px);
  transform: translate(-100%);
  z-index: 999;
}*/
.leave-text-holder {
  position: absolute;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.leave-text-holder .leave-text {
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  bottom: 150%;
  left: 50%;
  margin-left: -40px;
  opacity: 0;
}

.leave-text-holder .leave-text::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: black transparent transparent transparent;
}

.calenderIcons:hover .leave-text {
  opacity: 1;
}
/*.leave-text-holder{
  position: absolute;
  opacity: 0;
  text-align: center;
  width: 90px;
}*/

/*--celebrate hover */

.celeb-text-holder {
  position: absolute;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.celeb-text-holder .celeb-text {
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  bottom: 150%;
  left: 50%;
  margin-left: -40px;
  opacity: 0;
}

.celeb-text-holder .celeb-text::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: black transparent transparent transparent;
}

.celebIcons:hover .celeb-text {
  opacity: 1;
}

/*.celeb-text{
   background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  width: 100%;
  font-size: 8px;
  position: absolute;
  top: -10px;
  left: calc(100% - 25px);
  transform: translate(-100%);
  z-index: 999;
}
.celeb-text-holder{
  position: absolute;
  opacity: 0;
  text-align: center;
  width: 90px;
}*/
.userName{
  color: white;
  font-size: 25px;
  position: absolute;
  top: 30px;
  width: 72%;
  
  letter-spacing: 0px;
  font-weight: 600;
}
.user-designation{
  color: white;
  font-size: 15px;
  position: absolute;
  top: 110px;
  width: 70%;
  
  letter-spacing: 0px;
}
.managerName{
  color: white;
  font-size: 16px;
  width: 100%;
  letter-spacing: 0px;
}
.managerName1{
  color: white;
  font-size: 16px;
  width: 100%;
  
  letter-spacing: 0px;
}
.manager-designation{
  color: #000;
  font-size: 12px;
  position: absolute;
  left: 148px;
  top: 172px;
  width: 120%;
  
  letter-spacing: 0px;
}
.userImg {
  display: inline-block;
  width: 100px;
  height: 100px;
  min-height: 100px;
  min-width: 100px;
  left: 180px;
  top: 4px;
  margin-top: 30px;
  margin-right: 30px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  border: 4px solid #fff;
}

.verticalline{
 position: absolute;
 left: 64%;
 top: -45px;
}
.leave-holder{
  width: 100%;
}
.leave-container{
  width: 100%;
}
.leaves{
  color: #000;
  text-align: center;
}
.leaves .leave-type{
  color: white;
  font-size: 16px;
  width: 100%;
  
  letter-spacing: 0px;
}
.leaves .balance {
  color: white;
  font-size: 32px;
  font-weight: 700px;
  position: relative;
  top: -5px;
  width: 100%;
  
  letter-spacing: 0px;
}
.leaves .daysAvailable {
  color:white;
  font-size: 13px;
  font-weight: bold;
  position: relative;
  top: -15px;
  width: 100%;
  letter-spacing: 0px;
}

.vertiLine{
  border-left: 2px solid white;
  height: 70px;
  position:relative;
  top: 7px;
  /* overflow: hidden; */
}

.cardText{
     position: absolute;
     top: 132px;
     width: 100%;
}
.col-6{
    padding-left: 0px;
    padding-right: 0px;
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
.timeoff{
  background-color: #EE3234;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  position: sticky;
  top: 0px;
  color: #fff;
  width: 100%;
  z-index: 1;
}
.announcement-list{
  position: relative;
}
.celebrateText{
  color: #fff;
  height: 60px;
  line-height: 60px;
  font-size: 22px;
  
  letter-spacing: 0px;
}
.announcementText{
  color: #fff;
  height: 60px;
  line-height: 60px;
  font-size: 22px;
  
  letter-spacing: 0px;
}
.timeoffText{
  color: #fff;
  height: 60px;
  font-size: 22px;
  
  letter-spacing: 0px;
}
.upcomingText{
  position: relative;
  top: 20%;
  transform: translate(0,-50%);
}
.celeb-icon{
  position: relative;
  top: 50%;
  transform: translate(0,-50%);
}
.celebrateImg{
 width: 60px;
 height: 60px;
 text-align: center;
}
.announcementImg{
 width: 60px;
 height: 60px;
 line-height: 60px;
 text-align: center;
}
.timeoffImg{
 width: 60px;
 height: 60px;
 line-height: 60px;
 text-align: center;
}
.celebration{
background-color: #EE3234;
background-position: center;
background-repeat: no-repeat;
background-size: cover;
position: relative;
top: 65px;
height: 60px;
color: #fff;
}
.celebrate{
  margin-top: 66px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  text-align: center;
  background-color: white;
}
.events{
    height: 228px;
    overflow-y: auto;
    position: relative;
}
/* width */
::-webkit-scrollbar {
  width: 3px;
}
::-moz-scrollbar {
  width: 3px;
}
/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 10px;
}
::-moz-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 10px;
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #000; 
  border-radius: 10px;
}
.employeeName{
  text-align: center;
  width: 100%;
  line-height: 14px;
  font-size: 16px;
  
  letter-spacing: 0px;
}
.date{
  text-align: center;
  width: 100%;
  height: 20px;
  font-weight: bold;
  font-size: 16px;
  letter-spacing: 0px;
}
.celeb-detail{
  width: 100%;
}
.celeb-event{
  width: 80%;
}
.event {
  position: relative;
  text-align: center;
  font-size: 16px;
  letter-spacing: 0px;
}

div.personData{
    overflow: auto;
    position: relative;
    width: 100%;
    height: 102px;
    overflow-x: hidden;
    /* overflow-y: hidden; */
    /* padding: 0 15px 0 15px; */
}

.today-horizontal{
  width: 100%;
  padding: 0 25px 0 25px;
}
.thisWeek-horizontal{
  width: 100%;
  padding: 0 25px 0 25px;
}
.flex-container{
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(50px, 50px));
}
.outOfOffice-list{
  padding: 3px 5px 3px 5px;
  height: 46px;
  width: 50px;
  margin: 2px 3px 2px 3px;
}
.people{
  width: 100%;
  height: 100%;
  border-radius:50%;
  font-size: 20px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}
.row{
    margin-left: 0px;
    margin-right: 0px;
}
.middle {
  transition: .5s ease;
  position: absolute;
  opacity: 0;
  text-align: center;
  width: 80px;
}

/*country flags*/


.flag-text-holder {
  position: absolute;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.flag-text-holder .flag-text {
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  bottom: 150%;
  left: 50%;
  margin-left: -40px;
  opacity: 0;
}

.flag-text-holder .flag-text::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: black transparent transparent transparent;
}

.countryFlags:hover .flag-text {
  opacity: 1;
   z-index: 999;
}


/*.flag-text-holder{
  position: absolute;
  opacity: 0;
  text-align: center;
  width: 80px;
}
.countryFlags:hover .flag-text-holder{
  opacity: 1;
  
}
.flag-text {
  background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  top: -6px;
  left: calc(100% - 32px);
  transform: translate(-100%);
  width: 80%;
  font-size: 8px;
  position: absolute;
  z-index: 99999;
}*/
.outOfOffice-list:hover .image {
    opacity: 0.3;
}
.outOfOffice-list:hover .middle {
    opacity: 1;
}
.peopleShortNameOutOfOffice:hover{
  cursor: context-menu;
}
.peopleShortNameOutOfOfficeToday:hover{
  cursor: context-menu;
}
.peopleShortNameOutOfOfficeTomorrow:hover{
  cursor: context-menu;
}
.emp-text {
  background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  top: 32px;
  left: calc(50% - 20px);
  transform: translate(-50%);
  width: 120%;
  font-size: 8px;
  position: absolute;
  z-index: 99999;
}
.emp-text-noimage{
  background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  top: 33px;
  left: calc(50% - 20px);
  transform: translate(-50%);
  width: 120%;
  font-size: 8px;
  position: absolute;
  z-index: 99999;
}
.countryFlags{
  padding: 2px;
  display: inline-flex;
  height: 45px;
  width: 45px;
  position: relative;
  margin: 5px 5px 5px 5px;
  cursor: pointer;
  border: 2px solid transparent;
}
.activeCountry {
    border-bottom: 2px #122e55 solid;
    font-weight: bold;
}
.celeb {
  background-image: url("../assets/poppers.gif");
    height: 100%;
    width: 100%;
    /* display: none; */
    position: absolute;
    background-repeat: no-repeat;
    background-size: cover;
    z-index: 998;
}
.celeb-gif-holder{
  /* height: 1080px; */
  /* width: 100%; */
}
.other-leaves:hover{
  cursor: pointer;
}
.bday-celeb-holder{
  font-family: 'Montserrat';
  /* overflow: hidden; */
  z-index: 998;
  height: 100%;
  width: 100%;
  position: fixed;
  background-color: rgba(0, 0, 0, 0.5);
  top: 0;
  backdrop-filter: blur(5px);
}
.bday-card {
  width: 49%;
  height: 62%;
  position: absolute;
  margin: auto;
  left: 55%;
  transform: translate(-50%);
  right: 0;
  top: 0px;
  bottom: 0;
  -webkit-perspective: 1200px;
  perspective: 1200px;
  transition: 1s;
}
.bday-card:hover {
  transform: rotate(-5deg)scale(1.1)translate(-25%,-15%);
}
.bday-card:hover .outside {
  transform: rotateY(-130deg);
}
.outside,
.inside {
  height: 100%;
  width: 50%;
  position: absolute;
  left: 10.1%;
}
.inside {
  background: linear-gradient(to right, #e7e7e7, #ffffff 30%);
  line-height: 3;
  padding: 0 20px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;
  left: 10%;
}
.outside {
  -webkit-transform-style: preserve-3d;
  transform-style: preserve-3d;
  z-index: 1;
  transform-origin: left;
  transition: 2s;
  cursor: pointer;
}
.front,
.back {
  height: 100%;
  width: 100%;
  position: absolute;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  transform: rotateX(0deg);
}
.front {
  background-color: #ffffff;
}
.back {
  transform: rotateY(180deg);
  background: linear-gradient(to left, #e7e7e7, #ffffff 30%);
}
.cake {
  background-image: url("../assets/cake.png");
  width: 23vh;
  height: 23vh;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  background-repeat: no-repeat;
  background-size: cover;
  bottom: 30px;
}
.anniversary{
  background-image: url("../assets/celebration-anniversary.png");
  width: 23vh;
  height: 23vh;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  background-repeat: no-repeat;
  background-size: cover;
  bottom: 30px;
}
.outside p {
  font-size: 120%;
  text-transform: uppercase;
  margin-top: 30px;
  text-align: center;
  letter-spacing: 6px;
  color: #000046;
}
.inside p {
  font-size: 100%;
  text-align: center;
  color: #000046;
}
.inside h1 {
  font-size: 500%;
}
.card-closer{
  background-color: white;
  height: 40px;
  width: 40px;
  position: absolute;
  border-radius: 50%;
  right: 10%;
  top: 10%;
  z-index: 999;
}
.card-closer:hover{
  cursor: pointer;
}
.cancel{
  font-size: 50px;
  color: #d82d28;
  left: 50%;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
}
</style>