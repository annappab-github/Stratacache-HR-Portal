
<template>
<div class="employee-info">
  <div class="loader" v-if="loading"></div>
  <div class="topBg" >
    <div class="row top">
      <div class="col-1"></div>
      <div class="col-sm-3 ">
        <div class="profileImg-holder">
          <div v-if="this.email == 'admin@scala.com'" class="peopleShortNameMyInfo" :style="`background-color:#6e70ac`"> AD </div>
          <div v-else>
            <input type="file" id="file" ref="file" @change="profilePicUpload($event)" accept="image/x-png,image/jpeg" style="display: none">
            
            <div v-if="loggedInUser[0] != undefined && loggedInUser[0]['profile_pic']==null" class="peopleShortNameMyInfo" :style="`background-color:${loggedInUser[0]['imageBackgroundColour']}`">{{ loggedInUser[0]['employeeShortName'] }}</div>
            <div v-else-if="loggedInUser[0] != undefined" class="profileImg" :style="`background-Image: url(${loggedInUser[0]['profile_pic']})`"></div>
          </div>
            <!-- <div class="" v-if="loggedInUser[17]=='noimage' && editProfilePic" @click="$refs.file.click()">
              <span class="mdi mdi-camera  cameraImgNoImg" style="font-size: 20px; color: #fff;"></span>
            </div> -->
            <!-- <div class="" v-else-if="editProfilePic" @click="$refs.file.click()">
              <span class="mdi mdi-camera  cameraImg" style="font-size: 20px; color: #fff;"></span>
            </div> -->
            <div class="" v-if="loggedInUser[0] != undefined && loggedInUser[0]['profile_pic'] != null && editProfilePic" @click="gotoeditpage()">
              <span class="mdi mdi-pencil cameraImg  " style="font-size: 20px; color: #fff;"></span>
            </div>
            <div class="" v-else-if="loggedInUser[0] != undefined && editProfilePic" @click="gotoeditpage()">
              <span class="mdi mdi-pencil cameraImgNoImg" style="font-size: 20px; color: #fff;"></span>
            </div>
        </div>
      </div>
      <div class="col-sm-7">
         
        <div class="myData">
            <div class="ml-3 pb-1 myName">{{userDetail('name')}}</div>
            <div class="ml-3 pb-1 myPosition">{{userDetail('position')}}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="row bottom">
      <div class="col-lg-1"></div>
          <div class="col-lg-3 profileBg-container">
              <div class="profileBg">
                  <div class="reportsTo">
                      <div class="list-container">
                          <span class="mdi mdi-email ml-3"></span>
                          <div class=" ml-2" style="display:inline">{{userDetail('email')}}</div>
                      </div>
                  </div>
                  <hr>
                  <div class="hiringInfo">
                      <div class="ml-3 pb-1 font-weight-bold" style="font-size: 18px;">Hire Date</div>
                      <div class="list-container">
                          <span class="mdi mdi-calendar-week ml-3"></span>
                          <div class="ml-2 pb-1" >{{userDetail('hireDate')}}</div>
                      </div>
                      <div class="list-container">
                          <span class="mdi mdi-calendar-clock ml-3"></span>
                          <div class="ml-2 ">{{userDetail('month')}}</div>
                      </div>
                  </div>
                  <hr>
                  <div class="employeeData" >
                      <!-- <div class="ml-3 pb-1"> #<span class="ml-2">{{employeeId}}</span></div> -->
                      <div class="list-container pb-1">
                          <span class="mdi mdi-pound ml-3"></span>
                          <div class="ml-2 pb-1" style="display: inline">{{employeeId}}</div><br>
                      </div>
                      <div class="list-container">
                          <span class="mdi mdi-account-group ml-3"></span>
                          <div class="ml-2 pb-1" style="display: inline">{{userDetail('department')}}</div><br>
                      </div>
                      <div class="list-container">
                          <span class="mdi mdi-map-marker ml-3"></span>
                          <div class="ml-2 pb-1"  style="display: inline">{{userDetail('place')}}</div>
                      </div>
                  </div>
                  <hr style="display: none;" v-if="managerData.length == '0'">
                  <hr v-else>
                    <div class="no-celebration" v-if="managerData.length == '0'">
                    </div>
                  <div class="repManager"  v-else>
                      <div class="ml-3 font-weight-bold" style="font-size: 18px;">Manager</div>
                      <div class="list-container ml-3">
                          <div v-if="managerData[0] != undefined && managerData[0]['profile_pic']==null" class="managerNoImg" :style="`background-color:${managerData[0]['imageBackgroundColour']}`">{{ managerData[0]['employeeShortName'] }}</div>
                          <div v-else class="managerImg" :style="`background-Image: url(${managerData[0]['profile_pic']})`"></div>
                          <div class="ml-2 mt-1 pb-1"><strong>{{userDetail('managerName')}}</strong></div>
                      </div>
                      <div class="ml-4 pb-1">{{userDetail('managerPosition')}}</div>
                  </div>
                  <hr  v-if="directReports != ''">
                  <div class="dirManager"  v-if="directReports != ''">
                      <div class="ml-3 pb-1 font-weight-bold" style="font-size: 18px;">Direct Reports</div>
                      <div class="direct-report-container">
                        <div v-for="(emp, index) in directReports" :key="index" class="list-container ml-3">
                          <!-- <span class="mdi mdi-account-circle ml-3"></span> -->
                            <p v-if="emp['profile_pic']==null" class="directReportNoImg" :style="`background-color:${emp['imageBackgroundColour']}`">{{ emp['employeeShortName'] }}</p>
                            <!--<div v-if="emp[17]=='noimage'" class="directReportImg" :style="`background-color:${emp[19]}`">{{ emp[18] }}</div> -->
                            <div v-else class="directReportImg" :style="`background-Image: url(${emp['profile_pic']})`"></div>
                            <p class="dirManagerName ml-2" @click="reportingEmployeeDetails(emp['empid'], emp['email'])">{{emp['name']}}</p>
                        </div>
                      </div>
                  </div>
              </div>
      </div>
      <div class="col-lg-7 tab-cols">
      
          <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link nav-upper active font-weight-bold " id="nav-personal-tab" data-bs-toggle="tab" data-bs-target="#nav-personal" type="button" role="tab" aria-controls="nav-personal" aria-selected="true" @click="tabSwitch('personal')">Personal</button>
                <button class="nav-link nav-upper font-weight-bold " id="nav-timeOff-tab" data-bs-toggle="tab" data-bs-target="#nav-timeOff" type="button" role="tab" aria-controls="nav-timeOff" aria-selected="false" @click="tabSwitch('timeOff')">Time Off</button>
                <button class="nav-link nav-upper font-weight-bold " id="nav-requesttimeOff-tab" data-bs-toggle="tab" data-bs-target="#nav-requesttimeOff" type="button" role="tab" aria-controls="nav-requesttimeOff" aria-selected="false" v-if="!isDirectReport && employement_type == 'Full Time Employee'" @click="tabSwitch('requesttimeOff')">Request Time Off</button>
              </div>
               <div class="row  leaveView" v-if="showLeaveBalance && employement_type == 'Full Time Employee'">
                  <div class="optionalLeave text-center" v-for="(leaveBal,index) in leaveBalanceFilter(leaveBalanceData)" :key="index" :style="`${leaveBal.bal == '' ? 'display: none' : ''}`">
                    <div  v-if="leaveBal.bal != ''" style="position: relative;"> 
                      <div class="d-flex justify-content-center">
                        <div class="leaveImg">
                            <img :src="leaveBal.img" class="annualLeaveImg">
                        </div>
                      </div>  
                      <div class="leaveCount">
                        <label v-if="leaveBalanceData[0]" class="day2">
                          <span class="text-white">{{ leaveBal.bal }}</span>
                        </label>
                      </div>
                      <div class="expiring-leaves" v-if="checkExpiringLeaves(leaveBal)">
                        <img :src="require('@/assets/warning.png')" class="leve-expiry-alert" :title="leavesExpiryAlertMessage(leaveBal)" @click="expiryNotificationModel(leaveBal)">
                      </div>
                      <div class="annual1 ">{{ leaveBal.name }}</div>
                    </div>
                </div>
              <!-- <div class="alertMsg mt-3"><span style="color: red; font-weight: bold">NOTE: </span> CLICK ON THIS <span class="mdi mdi-alert-circle" style="color: red; font-size: 20px"></span> ICON ABOVE TO SEE LEAVE EXPIRY INFORMATION</div>-->
              </div>  
          </nav>
          <div class="tab-content" id="nav-tabContent">
           <!--Reguest Time off -->
                <div class="tab-pane fade " id="nav-requesttimeOff" role="tabpanel" aria-labelledby="nav-requesttimeOff-tab">
                    
                    <div class="announcement-holder">
                      <div class="announcement d-flex">
                        <div class="leaveText ml-3 flex-fill">APPLY FOR LEAVE </div>
                      </div>
                      <div class="row mx-1 d-flex">
                        <div class="col-6 left-side big-screen">
                          <div class="type-of-leave">Type of leave<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              
                          <select class="selected-leave"  v-model="appliedLeaveStatus.leaveCat" :style="!isLeaveUpdateDisabled ? '': 'grey'" :disabled="isLeaveUpdateDisabled" @change="getLeaveIdByType(appliedLeaveStatus.leaveCat,leaveBalanceData)">
                            <option value="" disabled selected>Select your option</option>
                            <option v-for="(leavetype,index) in filteredLeaveType"  :key="index" :value="leavetype">
                              <span>{{leavetype}} </span>
                              <span v-if="this.filteredLeaveTypeBal[index] != '' && this.filteredLeaveTypeBal[index] != null"> - {{this.filteredLeaveTypeBal[index]}}</span>
                            </option>
                          </select>
                          
                            <!--optional leave -->
                              <div v-if="appliedLeaveStatus.leaveCat == 'Optional Leave'" class="type-of-leave">Optional Leave List<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <select class="optional-leave" v-if="appliedLeaveStatus.leaveCat == 'Optional Leave'"  v-model="selectedOptionalLeaveDate" >
                                    <option value="" disabled selected>Select your option</option>
                                    <option  v-for="(leavetype,index) in optionalLeaveList" :key="index">
                                      <span>{{leavetype.holidayname}} - ({{dateFormatter(leavetype.holidaydate)}})</span>  
                                    </option>
                              </select>
                                <!--optional leave ends-->

                          <div v-if="appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day'">
                              <div class="from-date">From<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <input
                                type="date"
                                class="form-control from1"
                                v-model="appliedLeaveStatus.from"
                                id="from"
                                @change="getStartDate()"
                                autocomplete="off"
                                required
                                />
                          </div>
                          <div v-if="(appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration != 'Full Day')">
                              <div class="from-date" >Select Date<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <input
                                type="date"
                                class="form-control from select-date"
                                id="from"
                                autocomplete="off"
                                min=""
                                v-model="this.appliedLeaveStatus.from"
                                required
                                @change="halfDay()"
                                />
                          </div>
                          <div class=" leave-reason">Comments</div>
                          <textarea rows="3" maxlength="100" class="form-control" cols="0" required v-model="appliedLeaveStatus.reason"> </textarea>
                          <button class="leave-apply" v-if="editleave" @click="submitAppliedLeaveData()">Apply</button>
                          <button class="leave-apply" v-if="applyNewLeave" @click="updateLeaveData()">Update</button>
                          <button class="leave-cancel" v-if="editleave" @click="clearLeaveData()">Clear</button> 
                          <button class="leave-new" v-if="applyNewLeave" @click="submitNewAppliedLeaveData()">New</button>  
                             
                        </div>
                        <div class="col-6 right-side big-screen">  
                          <div class="type-of-leave">Duration<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                          <div class="mt-2" @change="setSelectDateMin()">
                            <input type="radio" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" v-model="appliedLeaveStatus.duration" name="option" id="id1" class="" value="First Half"><label for="id1" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" class="duration-label">First Half</label>
                            <input type="radio" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" v-model="appliedLeaveStatus.duration" name="option" id="id2"  class="" value="Second Half"><label for="id2" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" class="duration-label"> Second Half</label>
                            <!-- <br v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'"> -->
                            <input type="radio" checked v-model="appliedLeaveStatus.duration" name="option" id="id3"  class="" value="Full Day"><label for="id3" class="duration-label"> Full Day</label>
                        </div>
                          <div v-if="appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day'">
                              <div class="to-date">To<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <input
                                type="date"
                                class="form-control to"
                                min= ""
                                v-model="appliedLeaveStatus.to"
                                id="to"
                                autocomplete="off"
                                />
                          </div>
                          <div class="choose-file">Please choose file</div>
                          <button @click="$refs.file.click()" class="choose-file-text">choose file</button>
                          <div class="attach-files-text">{{this.medicalAttachment.name}}</div>
                          <div class="note-text">Note: For more than one file do zip and upload</div>
                          <input type="file" id="file" ref="file" class="mt-2 pt-2" @change="uploadMedicalAttachment($event)" accept=".zip,.rar,.7zip,application/pdf,application/msword,image/x-png,image/jpeg" style="display: none">
                             
                        </div>

                        <div class="col-12 mobile-screen">
                          <div class="type-of-leave">Type of leave<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              
                              <select class="selected-leave"  v-model="appliedLeaveStatus.leaveCat" :style="!isLeaveUpdateDisabled ? '': 'grey'" :disabled="isLeaveUpdateDisabled" @change="getLeaveIdByType(appliedLeaveStatus.leaveCat)">
                                <option value="" disabled selected>Select your option</option>
                                <option v-for="(leavetype,index) in filteredLeaveType"  :key="index" :value="leavetype">
                                  <span>{{leavetype}} </span>
                                  <span v-if="this.filteredLeaveTypeBal[index] != '' && this.filteredLeaveTypeBal[index] != null"> - {{this.filteredLeaveTypeBal[index]}}</span>
                                </option>
                              </select>
                                <!--optional leave -->
                              <div v-if="appliedLeaveStatus.leaveCat == 'Optional Leave'" class="type-of-leave">Optional Leave List<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <select class="optional-leave" v-if="appliedLeaveStatus.leaveCat == 'Optional Leave'"  v-model="selectedOptionalLeaveDate" >
                                    <option value="" disabled selected>Select your option</option>
                                    <option  v-for="(leavetype,index) in optionalLeaveList" :key="index">
                                      <span>{{leavetype.holidayname}} - ({{dateFormatter(leavetype.holidaydate)}})</span>  
                                    </option>
                              </select>
                                <!--optional leave ends-->

                              <div class="type-of-leave">Duration<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                              <div class="mt-2" @change="setSelectDateMin()">
                                  <input type="radio" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" v-model="appliedLeaveStatus.duration" name="option-mobile" id="id1" class="" value="First Half"><label for="id1" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" class="duration-label">First Half</label>
                                  <input type="radio" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" v-model="appliedLeaveStatus.duration" name="option-mobile" id="id2"  class="" value="Second Half"><label for="id2" v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'" class="duration-label"> Second Half</label>
                                  <br v-if="this.appliedLeaveStatus.leaveCat != 'Optional Leave'">
                                  <input type="radio" checked v-model="appliedLeaveStatus.duration" name="option-mobile" id="id3"  class="" value="Full Day"><label for="id3" class="duration-label"> Full Day</label>
                              </div>                              


                          
                              <div v-if="appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day'">
                                  <div class="from-date" >From<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                                  <input
                                    type="date"
                                    class="form-control from1"
                                    v-model="appliedLeaveStatus.from"
                                    id="from"
                                    @change="getStartDate()"
                                    autocomplete="off"
                                    required
                                    />
                              </div>
                              <div v-if="(appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration != 'Full Day')">
                                    <div class="from-date" >Select Date<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                                    <input
                                      type="date"
                                      class="form-control from select-date"
                                      id="from"
                                      autocomplete="off"
                                      min=""
                                      v-model="this.appliedLeaveStatus.from"
                                      required
                                      @change="halfDay()"
                                      />
                              </div>

                              <div v-if="appliedLeaveStatus.leaveCat != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day'">
                                  <div  class="to-date">To<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                                  <input
                                    type="date"
                                    class="form-control to"
                                    min= ""
                                    v-model="appliedLeaveStatus.to"
                                    id="to"
                                    autocomplete="off"
                                  />
                              </div>

                              <div class=" leave-reason">Comments</div>
                              <textarea rows="3" maxlength="100" class="form-control" cols="0" required v-model="appliedLeaveStatus.reason"> </textarea>

                              <div class="choose-file">Please choose file</div>
                              <button @click="$refs.file.click()" class="choose-file-text">choose file</button>
                              <div class="attach-files-text">{{this.medicalAttachment.name}}</div>
                              <div class="note-text">Note: For more than one file do zip and upload</div>
                              <input type="file" id="file" ref="file" class="mt-2 pt-2" @change="uploadMedicalAttachment($event)" accept=".zip,.rar,.7zip,application/pdf,application/msword,image/x-png,image/jpeg" style="display: none;">

                              <button class="leave-apply" v-if="editleave" @click="submitAppliedLeaveData()">Apply</button>
                              <button class="leave-apply" v-if="applyNewLeave" @click="updateLeaveData()">Update</button>
                              <button class="leave-cancel" v-if="editleave" @click="clearLeaveData()">Clear</button> 
                              <button class="leave-new" v-if="applyNewLeave" @click="submitNewAppliedLeaveData()">New</button> 



                        </div>  
                      </div>
                    </div>                
                </div>

                
                
            <!-- Personal -->
            <div class="tab-pane fade show active" id="nav-personal" role="tabpanel" aria-labelledby="nav-personal-tab">
                <div class="mb-2">
                    <span class="mdi mdi-account-circle" style="font-size: 25px;color: #3F3F40"></span>
                    <div class="personal ml-2 font-weight-bold">Basic information</div>
                </div>
                <div class="form-group col-lg-4 font-weight-normal">
                    <div for="employeeid" style="color: #000">Employee #</div>
                        <input
                        type="text"
                        class="form-control"
                        :value="this.employeeId"
                        id="employeeId"
                        aria-describedby="emailHelp" autocomplete="off"
                        readOnly/>
                </div>
                <div class="d-flex">
                    <div class="form-group col-lg-4">
                          <div for="name" style="color: #000">Name</div>
                            <input
                            type="text"
                            class="form-control"
                            :value="userDetail('name')"
                            id="employeeNam"
                            aria-describedby="emailHelp"  autocomplete="off"
                          readOnly />
                    </div>
                    <!-- <div class="form-group col-lg-4 font-weight-normal">
                      <div for="name" class="ml-4"> Last Name</div>
                          <input
                          type="text"
                          class="ml-4 form-control"
                          id="employeeName"
                          aria-describedby="emailHelp" autocomplete="off"
                        readOnly />
                    </div>-->
              </div>
              <div class="form-group col-lg-4 font-weight-normal">
                  <div for="dob" style="color: #000">Birth Date</div>
                      <input
                      type="text"
                      class="form-control"
                      :value="userDetail('bday')"
                      id="employeeDob"
                      aria-describedby="emailHelp"  autocomplete="off"
                    readOnly />
              </div>
              <div class="d-flex">
                  <div class="form-group col-lg-4 font-weight-normal">
                      <div for="Gender" style="color: #000">Employee Grade</div>
                          <input
                          type="text"
                          class="form-control"
                          :value="userDetail('empGrade')"
                          id="employeeGender"
                          aria-describedby="emailHelp"  autocomplete="off"
                        readOnly />
                  </div>
              </div>
              <hr>
              <div class="">
                  <span class="mdi mdi-home" style="font-size: 25px;"></span>
                  <div class="card-text ml-2 font-weight-normal" style="color: #000">Address</div>
                  <div class="form-group col-lg-5">
                      <textarea rows="4" class="form-control" cols="40" :value="userDetail('address')" readOnly>
                      </textarea>
                  </div>
              </div>
              <hr>
              <div class="">
                  <span class="mdi mdi-cellphone" style="font-size: 25px;"></span>
                  <div class="card-text ml-2 font-weight-normal" style="color: #000">Emergency Contact</div>
                  <div class="form-group col-lg-4">
                      <input
                      type="text"
                      class="form-control"
                      :value="userDetail('contact')"
                      id="employeeId"
                      aria-describedby="emailHelp"  autocomplete="off"
                    readOnly />
                  </div>
              </div>
            </div>
            <!--personal end-->

            <!-- Time off -->
            <div class="tab-pane fade" id="nav-timeOff" role="tabpanel" aria-labelledby="nav-timeOff-tab">
                
             
              <div class="col-lg-12 tab-cols UserLeavesTab">
                <nav>
                    <div class="nav nav-tabs celebration" id="nav-tab" role="tablist">
                      <button class="nav-link active font-weight-bold leaveListTabs" id="nav-upcoming-tab" data-bs-toggle="tab" data-bs-target="#nav-upcoming" type="button" role="tab" aria-controls="nav-upcoming" aria-selected="true" @click="tabSwitch('upcoming')">Holidays</button>
                      <button v-if="employement_type == 'Full Time Employee'" class="nav-link font-weight-bold leaveListTabs" id="nav-leave-upcoming-tab" data-bs-toggle="tab" data-bs-target="#nav-leave-upcoming" type="button" role="tab" aria-controls="nav-leave-upcoming" aria-selected="false" @click="tabSwitch('leave-upcoming')">Upcoming</button>
                      <button v-if="employement_type == 'Full Time Employee'" class="nav-link font-weight-bold leaveListTabs" id="nav-leave-history-tab" data-bs-toggle="tab" data-bs-target="#nav-leave-history" type="button" role="tab" aria-controls="nav-leave-history" aria-selected="false" @click="tabSwitch('leave-history')">History</button>

                        <div class="pt-1 filterImg" id="filter" v-if="employement_type == 'Full Time Employee' && filterFlag">
                          <img class="filters" :src="filterlogo" @click="show = !show"/>
                        </div>

                    </div>
                </nav>
              
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-upcoming" role="tabpanel" aria-labelledby="nav-upcoming-tab">
                    <div class="col-lg-12 upcomingModal">
                      <div class="upcomingTime-off" >
                          <div class="no-announcement" v-if="checkUpcoming()">
                                <img class="noAnnouncementImg" :src="noHolidayImg" alt="icons" >
                                <div class="" > There are no upcoming holidays!</div>
                          </div>
                          <table class="table">
                            <tr v-for="(holidays,index) in upcomingHolidays" :key="index" class="no-bottom-border" >
                                <div class="" v-for="(filterMonths, idx) in holidays" :key="idx">
                                  <td style="padding: 2px; width: 40px;">
                                  <span class="mdi mdi-calendar pl-2 " style="font-size: 28px;color: black"></span>
                                  </td>
                                  <td style="padding: 0px" class="pl-3">  
                                    <div class="holidayDate">{{filterMonths.day}} &nbsp; <span v-if="filterMonths.type == 'optional'">*</span><span class="holidayType">{{filterMonths.type}}</span></div>
                                    {{filterMonths.reason}}
                                  </td>
                                  <hr style="margin: 0px;">
                                </div>
                            </tr>
                          </table>
                      </div>
                    </div>
                  </div>
                  <div class="" v-if="hideFilter">
                  <Transition name="filter">
                        <div v-if="show" class="filterContainer">
                          <div class="filterCondition">
                            <table>
                              <tr>
                                <td><img class="filterClose" :src="close" @click="show = !show"/></td>
                                <td style="margin-left:40%;">Filters</td>
                              </tr>
                              <tr>
                                <th>YEAR</th>
                              </tr>
                              <tr>
                              <td v-for="(year,index) in years" :key="index"><button :class="`${activeYear==index ? 'activeFilter' : ''}`" @click="resetActiveYear(index,year)">{{year.name}}</button></td>
                              </tr>
                              <tr>
                                <th>MONTH</th>
                              </tr>
                              <tr>
                                <td v-for="(month,index) in months" :key="index"><button :class="`${activeMonth==index ? 'activeFilter' : ''} ${currentmon+1 > index  && index != 0 && this.leaveFilter.category == 'leave-upcoming' && activeYear=='0' ? 'disableMonth' : ''} ${ this.leaveFilter.category == 'leave-history' && activeYear=='0' && currentmon+1 < index? 'disableMonth' : ''} `" @click="resetActiveMonth(index,month)">{{month.name}}</button></td>
                              </tr>
                              <tr>
                                <th>LEAVE TYPE</th>
                              </tr>
                              <tr>
                                <td v-for="(type,index) in leaveFilterType" :key="index" :style="`${type == null ? 'display: none' : ''}`">
                                  <button v-if="type != 'Annual' && type != null" :class="`${activeType==index ? 'activeFilter' : ''}`"  @click="resetActiveType(index,type)">{{type}}</button>
                                  <button v-if="type == 'Annual'" :class="`${activeType==index ? 'activeFilter' : ''}`" @click="resetActiveType(index,'Earned')">Earned</button>
                                </td>
                              </tr>
                              <tr style="justify-content:space-evenly;">
                                <td><button class="applyFilter" @click="applyFilter(this.filter)">Apply Filter</button></td>
                                <td><button class="clearFilter" @click="clearFilter">Clear Filter</button></td>
                              </tr>
                            </table>
                          </div>
                        </div>
                      </Transition>
                    </div>    

                  <!--upcoming Leave-->
                <div class="tab-pane fade" id="nav-leave-upcoming" role="tabpanel" aria-labelledby="nav-leave-upcoming-tab">
                  <div class="col-lg-12 leaveModal">
                      
                      <div class="upcomingTime-off">
                        <div class="no-announcement" v-if="checkTimeOff(leaveFilteredArr)">
                            <img class="noAnnouncementImg" :src="timeoff" alt="icons" >
                            <div class="mt-3" v-if="this.filter.category == 'Upcoming'">No Upcoming Time Off!</div>
                            <div class="mt-3" v-else>No Records Found!</div>
                        </div>
                        <div class="resetLoader" v-if="loading1"></div>
                        <table class="table upcoming-desktop" v-if="leaveFilteredArr != ''">
                          <thead>
                            <tr class="text-center">
                              <th scope="col"></th>
                              <th scope="col" >Type</th>
                              <th scope="col">From</th>
                              <th scope="col">To</th>
                              <th scope="col">Days</th>
                              <th scope="col">Status</th>
                              <th scope="col">Comments</th>
                              <th scope="col">Action</th>

                            </tr>
                          </thead>
                          <tr v-for="(leaves,index) in leaveFilteredArr" :key="index" style="height: 54px;" >
                              <td style="padding: 2px; width: 4rem;">
                                <div class="mdi mdi-calendar pl-2 text-center" style="font-size: 28px;color: black"></div>
                              </td>
                              <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.leave_type}}</div>
                              </td>
                              <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.start_date}}</div>
                              </td>
                               <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.end_date}}</div>
                              </td>
                              <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.num_of_days}}</div>
                              </td>
                              <td style="padding: 0px;text-transform: capitalize;">  
                                <div class="date text-center">{{leaves.status}}</div>
                              </td>
                              <td style="padding: 0px;">  
                                <div v-if="leaves.comments != null && leaves.comments != ''" class="date text-center"><a style="text-decoration: underline;cursor: pointer" @click="showComments(leaves.comments)"><span style="font-size: 25px !important;color:red;" class="mdi" :class="'mdi-comment-account'"></span></a></div>
                              </td>
                              <td style="padding: 0px; " v-if="leaves.status == 'Rejected' || leaves.status == 'Cancelled'"></td>
                              <td style="padding: 0px; " v-if="leaves.status == 'pending'">  
                                <div v-if="!isDirectReport" class="date text-center">
                                  <div title="Edit Leave" style ="font-size: 25px; color: red; display: inline; cursor: pointer" class="mdi mdi-table-edit ml-2 mt-1" @click="editLeave(leaves)"></div>
                                  <div @click="openModel(leaves)" title="Delete Leave" style ="font-size: 25px; color: red; display: inline; cursor: pointer" class="mdi mdi-delete ml-3 mt-1"></div>
                                </div>
                              </td>
                              <td style="padding: 0px;" v-if="leaves.status == 'Approved'">
                                <div v-if="!isDirectReport && !handleDateDifference(leaves.end_date)" title="Cancel Leave" class="date text-center"><a style="text-decoration: underline;cursor: pointer" @click="openCancelLeaveModel(leaves)"><span style="font-size: 25px !important;color:red;" class="mdi" :class="'mdi-table-cancel'"></span></a></div>
                              </td>
                            </tr>
                        </table>

                        <table class="table upcoming-mobile" v-if="leaveFilteredArr != ''">
                          <tr v-for="(leaves,index) in leaveFilteredArr" :key="index" style="height: 54px;" >

                              <td style="font-size: 12px; width:175px">
                                <p style="margin:0px;"><strong>{{leaves.leave_type}}</strong></p>
                                <p style="margin:0px;">{{leaves.start_date}} - {{leaves.end_date}}</p>
                                <!-- <p style="margin:0px;">{{leaves.end_date}}</p> -->
                                <p style="margin:0px;">{{leaves.num_of_days}} day(s)</p>
                                <p style="margin:0px;">{{leaves.duration_type}}</p>
                                <p style="margin:0px; text-transform: capitalize;">{{leaves.status}}</p>

                              </td>

                              <td style="padding: 0px; width: 50px;">  
                                <div v-if="leaves.comments != null && leaves.comments != ''" class="date text-center"><a style="text-decoration: underline;cursor: pointer" @click="showComments(leaves.comments)"><span style="font-size: 25px !important;color:red;" class="mdi" :class="'mdi-comment-account'"></span></a></div>
                              </td>
                              <td style="padding: 0px; " v-if="leaves.status == 'Rejected' || leaves.status == 'Cancelled'"></td>
                              <td style="padding: 0px; " v-if="leaves.status == 'pending'">  
                                <div v-if="!isDirectReport" class="date text-center">
                                  <div title="Edit Leave" style ="font-size: 25px; color: red; display: inline; cursor: pointer" class="mdi mdi-table-edit ml-2 mt-1" @click="editLeave(leaves)"></div>
                                  <div @click="openModel(leaves)" title="Delete Leave" style ="font-size: 25px; color: red; display: inline; cursor: pointer" class="mdi mdi-delete ml-3 mt-1"></div>
                                </div>
                              </td>
                              <td style="padding: 0px;" v-if="leaves.status == 'Approved'">
                                <div v-if="!isDirectReport && !handleDateDifference(leaves.end_date)" title="Cancel Leave" class="date text-center"><a style="text-decoration: underline;cursor: pointer" @click="openCancelLeaveModel(leaves)"><span style="font-size: 25px !important;color:red;" class="mdi" :class="'mdi-table-cancel'"></span></a></div>
                              </td>
                            </tr>
                        </table>
                      </div>
                  </div>
                </div> <!--upcoming leave ends-->

                 <!--Leave history-->
                <div class="tab-pane fade" id="nav-leave-history" role="tabpanel" aria-labelledby="nav-leave-history-tab">
                  <div class="col-lg-12 leaveModal">
                      <div class="upcomingTime-off">
                        <div class="no-announcement" v-if="checkHistory(historyleaveFilteredArr)">
                            <img class="noAnnouncementImg" :src="timeoff" alt="icons" >
                            <div class="mt-3" v-if="this.filter.category == 'leave-history'">No History Time Off!</div>
                            <div class="mt-3" v-else>No Records Found!</div>
                          </div>
                        <div class="resetLoader" v-if="loading1"></div>
                           <table class="table" v-if="historyleaveFilteredArr != ''">
                            <thead>
                            <tr class="text-center">
                              <th scope="col"></th>
                              <th scope="col" >Leave Category</th>
                              <th scope="col">From</th>
                              <th scope="col">To</th>
                              <th scope="col">Duration</th>
                              <th scope="col">Status</th>
                            </tr>
                          </thead>
                            <tr v-for="(leaves,index) in historyleaveFilteredArr" :key="index" style="height: 54px;">
                              <td style="padding: 2px; width: 4rem;">
                                <span class="mdi mdi-calendar pl-2 text-center" style="font-size: 28px;color: black"></span>
                              </td>
                              <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.leave_type}}</div>
                              </td>
                               <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.start_date}}</div>
                              </td>
                               <td style="padding: 0px">  
                                <div class="date text-center">{{leaves.end_date}}</div>
                              </td>
                               <td style="padding: 0px">  
                                 <div class="date text-center">{{leaves.duration_type}}</div>
                              </td>
                               <td style="padding: 0px;text-transform: capitalize;">  
                                <div class="date text-center mr-2">{{leaves.status}}</div>
                              </td>
                          </tr>
                        </table>
                      </div>
                  </div>
                </div> <!--leave history ends-->

              </div>
              
          </div>
          
        </div>
         
      </div>
    </div>
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
                      @click="deleteLeaveData(modalData)"
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

<!-- cancel leave popup -->
     <div v-if="cancelLeaveModel">
      <transition name="model">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" @click="cancelLeaveModel = false">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="confirmation pb-4" style="text-align: center">
                    <h4>Are you sure you want to cancel this?</h4>
                  </div>
                  <div align="center">
                    <input
                      type="button"
                      class="btn btn-primary"
                      style="margin-right: 10px; width: 100px"
                      value="OK"
                      @click="cancelApprovedLeave(modalData)"
                    />
                    <input
                      type="button"
                      class="btn btn-danger"
                      style="margin-left: 10px; width: 100px"
                      value="Cancel"
                      @click="cancelLeaveModel = false"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <!--successfully leave applied message -->
    <div class="alert alert-success successMsg" role="alert" v-if="formSubmitMsg">
              Successfully applied
    </div>

    <div class="alert alert-success successMsg" role="alert" v-if="leaveUpdateMsg">
              Updated Successfully
    </div>
          
</div>
<Teleport to="body">
  <transition name="leave-expiry">
    <div v-if="leaveNotificationMessage != ''" class="leave-notification-model">
      <p>{{ leaveNotificationMessage }}</p>
    </div>
  </transition>
</Teleport>
</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import { decryptData, encryptData } from '../js/utils/encrypt';
import vacation from '../assets/holiday.png';
import filters from '../assets/filter.png';
import close from '../assets/close.png';
import annualImg from '../assets/park.png';
import medicalImg from '../assets/medicalappointment.png';
import optionalImg from '../assets/optional.png';
import marriageImg from '../assets/marriage.png';
import hospitalImg from '../assets/hospital.png';
import compansatoryImg from '../assets/compansatory.png';
import childcareImg from '../assets/childcare.png';
import compassionImg from '../assets/compassion.png';
import maternityImg from '../assets/maternity.png';
import paternityImg from '../assets/paternity.png';
import timeoff from '../assets/timeoff.png';
import noHoliday from '../assets/noHoliday.png';
import { routeNames } from '../js/routeNames';
import Swal from 'sweetalert2/dist/sweetalert2.js';

let leaveExpiryNotifyTimeout = '';
const currentYear = new Date().getFullYear();
export default {
    components: {
    },
    props: ['propsEmpId', 'propsEmail', 'editProfilePic'],
    data(){
        return{
            optionalLeaveList: [],
            selectedOptionalLeaveDate: '',
            formatedUserData: [],
            showLeaveBalance: false,
            monthNames: '',
            holidaylogo: vacation,
            filterlogo: filters,
            annualImg: annualImg,
            medicalImg: medicalImg,
            optionalImg: optionalImg,
            noHolidayImg: noHoliday,
            timeoff: timeoff,
            formSubmitMsg: false,
            leaveUpdateMsg: false,
            close: close,
            email: '',
            loading: false,
            loading1: false,
            myModel: false,
            cancelLeaveModel: false,
            balance: true,
            employees: [],
            loggedInUser: [],
            holidayData: [],
            leaveBalanceData: [],
            upcomingLeave: [],
            leaveHistory: [],
            startDate: '',
            employeeId: '',
            activeCategory: 0,
            categories: [
              {name: 'Upcoming'},{name: 'History'}
            ],
            activeYear: 0,
            disabledYear: true,
            disabledMonth: true,
            years: [
              {name: currentYear}
            ],
            activeMonth: 0,
            months: [
              {name: 'All'},{name: 'Jan'},{name: 'Feb'},
              {name: 'Mar'},{name: 'Apr'},{name: 'May'},
              {name: 'Jun'},{name: 'Jul'},{name: 'Aug'},
              {name: 'Sep'},{name: 'Oct'},{name: 'Nov'},
              {name: 'Dec'}
            ],
            activeType: 0,
            show: false,
            hideFilter: false,
            filter: {
              category: 'leave-upcoming',
              year: currentYear,
              month: 'All',
              type: 'All',
            },
            updatedProfilePic: '',
            leaveFilter: {
              category: 'leave-upcoming',
              year: currentYear,
              month: 'All',
              type: 'All',
            },
            applyFilterFlag: false,
            currentmon: '',
            leaveFilteredArr:[],
            historyleaveFilteredArr:[],
            leaveBal: [
                {name: 'Annual Leave', bal: '', img: annualImg},
                {name: 'Sick Leave', bal: '', img: medicalImg},
                {name: 'Optional Leave', bal: '', img: optionalImg},
                {name: 'Compensatory Leave', bal: '', img: compansatoryImg},
                {name: 'Marriage  Leave', bal: '', img: marriageImg},
                {name: 'Bereavement Leave', bal: '', img: compassionImg},
                {name: 'Maternity Leave', bal: '', img: maternityImg},
                {name: 'Paternity Leave', bal: '', img: paternityImg},
                {name: 'Childcare Leave', bal: '', img: childcareImg},
                {name: 'National Service Leave', bal: '', img: hospitalImg},
                {name: 'Hospital Leave', bal: '', img: hospitalImg}
            ],
            leaveFilterType: ['All'],
            directReports: [],
            managerData: [],
            checkYear: '',
            othersSelected: false,
            appliedLeaveStatus: {
              leaveCat: '',
              from: '',
              reason: '',
              duration: 'Full Day',
              to: '',
              status: 'pending',
              empId: this.employeeId,
              leaveid: '',
              autoApproved: false,
              updatedAt: ''
            },
            empLocation: '',
            empGender: '',
            appliedLeaveData: [
              {leaveType: 'Annual Leave', gender: 'All', region: ['All']},
              {leaveType: 'Sick Leave', gender: 'All', region: ['All']},
              {leaveType: 'Optional Leave', gender: 'All', region: ['INDIA']},
              {leaveType: 'Compensatory Leave', gender: 'All', region: ['All']},
              {leaveType: 'Marriage Leave', gender: 'All', region: ['All']},
              {leaveType: 'Bereavement Leave', gender: 'All', region: ['All']},
              {leaveType: 'Maternity Leave', gender: 'FEMALE',region: ['All']},
              {leaveType: 'Paternity Leave', gender: 'MALE', region: ['All']},
              {leaveType: 'Childcare Leave', gender: 'All', region: ['SINGAPORE']},
              {leaveType: 'Carers Leave', gender: 'All', region: ['AUSTRALIA']},
              {leaveType: 'National Service Leave', gender: 'All', region: ['SINGAPORE']},
              {leaveType: 'Hospital Leave', gender: 'All', region: ['SINGAPORE']},
              {leaveType: 'Unpaid Leave', gender: 'All', region: ['All']},
            ],
            filteredLeaveType: [],
            filteredLeaveTypeBal: [],
            optionalLeaveDate: '',
            editleave: true,
            applyNewLeave: false,
            editLeaveId: '',
            medicalAttachment: '',
            isMedicalCertificateAttached: false,
            isApplyLeaveBtnDisabled:false,
            isLeaveUpdateDisabled: false,
            todaysDate: '',
            isDirectReport: false,
            isLeaveCancelledBasedOnCurrentDate: false,
            employement_type: '',
            filterFlag: false,
            employeeExpiryLeaves: [],
            leaveNotificationMessage: '',
            leaveBalAll: {}
        };
    },
    mounted(){

      this.filteredLeaveType = [];
      this.filteredLeaveTypeBal = [];
      let currentDate = new Date();
      if(currentDate.getDate() < 10){
        if(currentDate.getMonth()+1 < 10){
          this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
        } else {
           this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
        }
      } else {
        if(currentDate.getMonth()+1 < 10){
          this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
        } else {
          this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
        }
      }
      this.appliedLeaveStatus.to = this.appliedLeaveStatus.from;
      if(this.appliedLeaveStatus.leaveCat != 'Optional Leave' || this.appliedLeaveStatus.duration == 'Full Day'){
          setTimeout(()=>{
            $('.to').attr({'min': this.appliedLeaveStatus.from})
            $('.from1').attr({'min': this.appliedLeaveStatus.from})
          },100);
        } 
      this.othersSelected = this.$route.params.others;
      const secretKey = process.env.KEY || 'myscecretkey';
      var encryptedEmployeeID = this.propsEmpId;
      var encryptedEmailID = this.propsEmail;
      this.employeeId = decryptData(encryptedEmployeeID, secretKey).replace(/['"]+/g, '');
      this.appliedLeaveStatus.empId = this.employeeId;
      try {
          this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');
      } catch (error) {
          console.log('error');
      }
      if(this.email!='admin@scala.com'){
        this.mountedFunction();
        this.filterLeaveTypeBasedOnRegion();
        // this.filterLeaveTypeBasedOnRegion(this.appliedLeaveData);
      }
      if(this.$route.name == 'directreport'){
        this.isDirectReport = true;
      }
    },

    methods: {
       setSelectDateMin(){
          if(this.appliedLeaveStatus.duration != 'Full Day'){
              $('.select-date').attr({'min': this.appliedLeaveStatus.from})
            } else {
              $('.to').attr({'min': this.appliedLeaveStatus.from})
              $('.from1').attr({'min': this.appliedLeaveStatus.from})
            }
        },
       getStartDate(){
         $('.to').attr({'min': this.appliedLeaveStatus.from})
          let fromdt =  new Date(this.appliedLeaveStatus.from);
          let todt =  new Date(this.appliedLeaveStatus.to);
          if(fromdt > todt) {
            this.appliedLeaveStatus.to = this.appliedLeaveStatus.from;
          }
        },
      async expiryNotificationModel(leave) {
        this.leaveNotificationMessage = '';
        clearTimeout(leaveExpiryNotifyTimeout);
        leaveExpiryNotifyTimeout = setTimeout(() => {
          this.leaveNotificationMessage = this.leavesExpiryAlertMessage(leave);
          leaveExpiryNotifyTimeout = setTimeout(() => {
            this.leaveNotificationMessage = '';
          }, 3 * 1000);
        });
      },   
      leavesExpiryAlertMessage(leave) {
        let alertMessage = '';
        for(let index in this.employeeExpiryLeaves) {
          if (index != 'empid') {
            let expiringLeaves = this.employeeExpiryLeaves[index].no_of_leaves_expiring;
            if(this.employeeExpiryLeaves[index].leave_name.includes(leave.name) && parseFloat(expiringLeaves) > 0) {
              let levName = (parseInt(expiringLeaves) < 2 ? 'Leave' : 'Leaves');
              const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
              let dateMonthYear = this.employeeExpiryLeaves[index].expiry_date.split('-');
              let formatLeaves = parseInt(expiringLeaves) == expiringLeaves ? expiringLeaves : parseFloat(expiringLeaves).toFixed(2);
              alertMessage = `${formatLeaves} ${levName} will be expire by ${dateMonthYear[0]} ${monthNames[parseInt(dateMonthYear[1])-1]} ${dateMonthYear[2]}`;
              break;
            }
          }
        }
        return alertMessage;
      },
      checkExpiringLeaves(leave) {
        let expiringflag = false;
        for(let index in this.employeeExpiryLeaves) {
          if (index != 'empid') {
            if(this.employeeExpiryLeaves[index].leave_name.includes(leave.name) && parseFloat(this.employeeExpiryLeaves[index].no_of_leaves_expiring) > 0) {
              expiringflag = true;
              break;
            }
          }
        }
        return expiringflag;
      },
      halfDay(){
        this.appliedLeaveStatus.to = this.appliedLeaveStatus.from; 
      },
      dateFormatter(date){
        let finalDate = new Date(date);
        let month = finalDate.getMonth() + 1; 
        let currentdate = finalDate.getDate();
        if(month<10){
          month = '0'+month;
        }
        if(currentdate < 10){
          currentdate = '0' + currentdate;
        }
        return currentdate + '-' + month + '-' + finalDate.getFullYear();
      },
      cancelApprovedLeave(data){
        this.cancelLeaveModel = false;
        this.loading = true;
        let cancleApprovedLeaveDetails = {
          empid: this.employeeId,
          id: data.id,
          leaveId: data.leave_id,
          totalDays: data.num_of_days,
          status: 'Cancelled'
        };
          axios.put(this.$hostName + '/approveAppliedLeave/'+ cancleApprovedLeaveDetails.id, {leaveDetails: cancleApprovedLeaveDetails}).then((response) => {
            if (response.status == 200) {
            Swal.fire({
              text: 'Leave request cancelled successfully',
              confirmButtonText: 'Ok',
            });  
            this.mountedFunction();
          }
          }).catch((error) => {
            console.log(error);
            Swal.fire({
              text: 'Something went wrong. Please try again later.', 
              confirmButtonText: 'Ok',
            });
            return;
          }).finally(() => {
            this.loading = false;
          });
      },
      filterLeaveTypeBasedOnRegion(){
        var currentMonth = new Date();
        this.currentmon = currentMonth.getMonth();
        this.loading = true;
        axios.post(this.$hostName + '/myInfo', {fetchType: 'upcoming', year: new Date().getFullYear(), empId: this.employeeId})
        .then((response) => {
          this.loggedInUser = [];
          response.data[0].employeeData.forEach((element) => {
            this.loggedInUser.push(element);
          });
        });
      },
      async submitNewAppliedLeaveData(){
          this.editleave = true;
          this.applyNewLeave = false;
          this.appliedLeaveStatus.leaveCat = '';
          this.appliedLeaveStatus.reason = '';
          this.isLeaveUpdateDisabled = false;
          this.appliedLeaveStatus.autoApproved = false;
          this.appliedLeaveStatus.duration = 'Full Day';
          this.appliedLeaveStatus.status = 'pending';
          let currentDate = new Date();
        if(currentDate.getDate() < 10){
          if(currentDate.getMonth()+1 < 10){
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
          } else {
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
          }
        } else {
          if(currentDate.getMonth()+1 < 10){
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
          } else {
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
          }
        }
          this.appliedLeaveStatus.to = this.appliedLeaveStatus.from;
          if(this.appliedLeaveStatus.leaveCat != 'Optional Leave' || this.appliedLeaveStatus.duration == 'Full Day'){
          setTimeout(()=>{
             $('.to').attr({'min': this.appliedLeaveStatus.from})
            $('.from1').attr({'min': this.appliedLeaveStatus.from})
          },100);
        }
          this.medicalAttachment = '';

      },
     async submitAppliedLeaveData(){
      this.loading = true;
      this.balance = true;
      this.isLeaveUpdateDisabled = false;
      let id = '';
      this.appliedLeaveStatus.autoApproved = false;
      this.appliedLeaveStatus.status = 'pending';
      if(this.appliedLeaveStatus.leaveCat == ''){
        Swal.fire({
              text: 'Please select leave type',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
      }  
      
      else if(this.appliedLeaveStatus.leaveCat == 'Optional Leave' &&  this.selectedOptionalLeaveDate == ''){
            Swal.fire({
                  text: 'Please select Optional leave',
                  confirmButtonText: 'Ok',
                });
                this.loading = false;
                return;
        } 
      
      const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
      const firstDate = new Date(this.appliedLeaveStatus.from);
      const secondDate = new Date(this.appliedLeaveStatus.to);
      let firstDay = firstDate.getDay();
      const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
      
      if(this.appliedLeaveStatus.leaveCat == 'Compensatory Leave') {
        if(this.appliedLeaveStatus.reason == ''){
          Swal.fire({
              text: 'Please enter comments',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
        }
      } else if(this.appliedLeaveStatus.leaveCat == 'Sick Leave'){
        if(this.appliedLeaveStatus.reason == ''){
          Swal.fire({
              text: 'Please enter comments',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
      } else if((this.empLocation == 'SINGAPORE' || (this.empLocation == 'INDIA' && (diffDays >= 2 || firstDay == 5 || firstDay == 1))) && this.medicalAttachment == '') {
          Swal.fire({
            text: 'Please choose file', 
            confirmButtonText: 'Ok',
          });
          this.loading = false;
          return; 
        }
        this.appliedLeaveStatus.autoApproved = true;
        this.appliedLeaveStatus.status = 'Approved';
      } else if(this.appliedLeaveStatus.leaveCat == 'Optional Leave'){
         let optionalLeaveDate = this.selectedOptionalLeaveDate.split('- (')[1].replace(')', '');
          let date = optionalLeaveDate.split('-')[0];
          let month = optionalLeaveDate.split('-')[1];
          let year = optionalLeaveDate.split('-')[2];
          let finalDate = year + '-' + month + '-' + date;
          this.appliedLeaveStatus.to = finalDate;
          this.appliedLeaveStatus.from = finalDate;
      }
        await axios.post(this.$hostName + '/leaveData', this.appliedLeaveStatus)
            .then((response) => {
              if(response.status == 200){
                Swal.fire({
                  text: 'Leave request submitted successfully',
                  confirmButtonText: 'Ok',
                });
              } else if(response.status == 203) {
                Swal.fire({
                  text: response.data,
                  confirmButtonText: 'Ok',
                });
                this.balance = false;
                return false;
              } 
              id = response.data.id;
            }).catch((error) => {
              console.log(error);
              Swal.fire({
                text: 'Something went wrong. Please try again later.', 
                confirmButtonText: 'Ok',
              });
              this.balance = false;
              return;
            })
            .finally(() => {
              this.loading = false;
            });
            if(!this.balance) {
              this.loading = false;
              return false;
            }
            if(this.isMedicalCertificateAttached == true){
                let formData = new FormData();
                formData.append('empId', this.employeeId);
                formData.append('file', this.medicalAttachment);
                formData.append('id', id);
              await axios.post(this.$hostName + '/uploadMedicalCertificate', formData, {headers : {'Content-Type': 'multipart/form-data'}})
                .then((response) => {
                    console.log(response);
                }).catch((error) => {
                  console.log(error);
                }).finally(() =>{
                  this.loading = false;
                });
          }
         
          this.clearLeaveData();
          this.mountedFunction();
          this.tabSwitch('leave-upcoming');
         
      },
      getLeaveIdByType(type){
        let leaveType = type + ' ' +'Balance';
        axios.post(this.$hostName + '/getleaveid', {leaveType: leaveType}).then((res) =>{
          this.appliedLeaveStatus.leaveid = res.data.empLeaveData.id;
        });
      },
       editLeave(leave){
        this.editleave = false;
        this.applyNewLeave = true;
        this.isLeaveUpdateDisabled = true;
        this.appliedLeaveStatus.id = leave.id;
        if(leave.leave_type == 'Optional Leave'){
          let newDate;
          let finalValue;
          for(let i=0; i<this.optionalLeaveList.length; i++){
              let dateList = this.optionalLeaveList[i].holidaydate;
              if(leave.start_date == dateList){
                this.selectedOptionalLeaveDate = this.optionalLeaveList[i].holidaydate;
                newDate = this.dateFormatter(dateList);
                finalValue = this.optionalLeaveList[i].holidayname + ' - (' + newDate + ')';
              }
          }
          this.selectedOptionalLeaveDate = finalValue;
        }

        this.appliedLeaveStatus.leaveCat = leave.leave_type;
        this.appliedLeaveStatus.duration = leave.duration_type;
        this.appliedLeaveStatus.from = leave.start_date;
        this.appliedLeaveStatus.to = leave.end_date;
        this.appliedLeaveStatus.reason = leave.reason;
        this.appliedLeaveStatus.updatedAt = leave.updated_at;
        this.leaveFilteredArr.forEach((element) => {
          if(this.appliedLeaveStatus.id == element.id ){
               this.appliedLeaveStatus.id = element.id;
                this.appliedLeaveStatus.leaveCat = element.leave_type;
                this.appliedLeaveStatus.duration = element.duration_type;
                this.appliedLeaveStatus.from = element.start_date;
                this.appliedLeaveStatus.to = element.end_date;
                this.appliedLeaveStatus.leaveid = element.leave_id;
                this.appliedLeaveStatus.reason = element.reason;
                this.appliedLeaveStatus.updatedAt = element.updated_at;
            }
        });
        this.tabSwitch('requesttimeOff');
      },
     async updateLeaveData(){
      this.loading = true;
      this.balance = true;
      
      this.isLeaveUpdateDisabled = false;
      if(this.appliedLeaveStatus.leaveCat == ''){
        Swal.fire({
              text: 'Please select leave type',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
      }   
      
      else if(this.appliedLeaveStatus.leaveCat == 'Optional Leave' &&  this.selectedOptionalLeaveDate == ''){
            Swal.fire({
                  text: 'Please select Optional leave',
                  confirmButtonText: 'Ok',
                });
                this.loading = false;
                return;
        } 
      
      const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
      const firstDate = new Date(this.appliedLeaveStatus.from);
      const secondDate = new Date(this.appliedLeaveStatus.to);
      let firstDay = firstDate.getDay();
      const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
      
      if(this.appliedLeaveStatus.leaveCat == 'Compensatory Leave') {
        if(this.appliedLeaveStatus.reason == ''){
          Swal.fire({
              text: 'Please enter comments',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
        }
      } else if(this.appliedLeaveStatus.leaveCat == 'Sick Leave'){
        if(this.appliedLeaveStatus.reason == ''){
          Swal.fire({
              text: 'Please enter comments',
              confirmButtonText: 'Ok',
            });
            this.loading = false;
            return; 
      } else if((this.empLocation == 'SINGAPORE' || (this.empLocation == 'INDIA' && (diffDays >= 2 || firstDay == 5 || firstDay == 1))) && this.medicalAttachment == '') {
          Swal.fire({
            text: 'Please choose file', 
            confirmButtonText: 'Ok',
          });
          this.loading = false;
          return; 
        }
        this.appliedLeaveStatus.autoApproved = true;
        this.appliedLeaveStatus.status = 'Approved';
      } else if(this.appliedLeaveStatus.leaveCat == 'Optional Leave'){
         let optionalLeaveDate = this.selectedOptionalLeaveDate.split('- (')[1].replace(')', '');
          let date = optionalLeaveDate.split('-')[0];
          let month = optionalLeaveDate.split('-')[1];
          let year = optionalLeaveDate.split('-')[2];
          let finalDate = year + '-' + month + '-' + date;
          this.appliedLeaveStatus.to = finalDate;
          this.appliedLeaveStatus.from = finalDate;
      }

       await axios.put(this.$hostName + `/updateleave/${this.appliedLeaveStatus.id}`, this.appliedLeaveStatus)
          .then((response) => {
            if(response.status == 200){
              Swal.fire({
                text: 'Leave request updated successfully',
                confirmButtonText: 'Ok',
              });
            } else if(response.status == 203) {
              Swal.fire({
                text: response.data,
                confirmButtonText: 'Ok',
              });
              this.balance = false;
              return false;
            } 
           
          }).catch((error) => {
            console.log(error);
            Swal.fire({
              text: 'Something went wrong. Please try again later.', 
              confirmButtonText: 'Ok',
            });
            return;
          })
          .finally(() => {
            this.loading = false;
          });
          if(!this.balance) {
            this.loading = false;
            return false;
          }
          this.editleave = true;
          this.applyNewLeave = false;
          this.clearLeaveData();
          this.mountedFunction();
          this.tabSwitch('leave-upcoming');
     },
      deleteLeaveData(leaves){
        let appliedLeaveId = leaves.id;
        axios.delete(this.$hostName + '/deleteAppliedLeaveData/' + appliedLeaveId, {updatedAt: leaves.updated_at }).then((response) =>{
           if (response.status == 200) {
            this.$emit('User Deleted');
            this.myModel = false;
            Swal.fire({
                text: 'Leave request deleted successfully',
                confirmButtonText: 'Ok',
            });
            this.getUpcomingData();
          } else if(response.status == 203) {
              Swal.fire({
                text: response.data,
                confirmButtonText: 'Ok',
              });
              this.balance = false;
              return false;
          }
        }).catch((error) =>{
          console.log('', error);
          Swal.fire({
            text: 'Something went wrong. Please try again later.', 
            confirmButtonText: 'Ok',
          });
          return;
        }).finally(() => {
            this.myModel = false;
        });

      },
      clearLeaveData(){
        this.appliedLeaveStatus.leaveCat = '';
        this.appliedLeaveStatus.reason = '';
        this.appliedLeaveStatus.updatedAt = '';
        this.appliedLeaveStatus.duration = 'Full Day';
        this.selectedOptionalLeaveDate = '';
        this.medicalAttachment = '';
        this.isLeaveUpdateDisabled = false;
        let currentDate = new Date();
        if(currentDate.getDate() < 10){
          if(currentDate.getMonth()+1 < 10){
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
          } else {
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-0'+ currentDate.getDate();
          }
        } else {
          if(currentDate.getMonth()+1 < 10){
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-0'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
          } else {
            this.appliedLeaveStatus.from = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-'+ currentDate.getDate();
          }
        }
        this.appliedLeaveStatus.to = this.appliedLeaveStatus.from;
       if(this.appliedLeaveStatus.leaveCat != 'Optional Leave' || this.appliedLeaveStatus.duration == 'Full Day'){
          setTimeout(()=>{
            $('.to').attr({'min': this.appliedLeaveStatus.from})
            $('.from1').attr({'min': this.appliedLeaveStatus.from})
          },100);
        }

      },
      openModel(idx) {
        this.myModel = true;
        this.modalData = idx;
      },
      openCancelLeaveModel(idx){
        this.cancelLeaveModel = true;
        this.modalData = idx;
      },
      getUpcomingData(){
        var currentMonth = new Date();
        this.currentmon = currentMonth.getMonth();
        this.loading = true;
        axios.post(this.$hostName + '/myInfo', {fetchType: 'upcoming', year: new Date().getFullYear(), empId: this.employeeId})
        .then((response) => {
          this.loggedInUser = [];
          response.data[0].employeeData.forEach((element) => {
            this.loggedInUser.push(element);
          });
          this.formatedUserData = this.loggedInUser;
          this.empLocation = this.formatedUserData[0].location.toUpperCase();
          this.empGender = this.formatedUserData[0].gender.toUpperCase();
         
          this.startDate = this.loggedInUser[0]['start_Date'];
          this.leaveBalanceData = response.data[1].leaveBalanceData;
          this.leaveFilteredArr = response.data[2].upcomingLeaveData;
          this.upcomingHolidays = response.data[3].upcomingHolidayData;
          this.managerData = response.data[4].managerData;
          this.directReports = response.data[5].directReportsData;
          if(this.othersSelected){
            this.tabSwitch('timeOff');
          }
        })
        .finally(() => {
             this.loading = false;
        });
      },
      gotoeditpage() {
        this.$router.push({
        name: routeNames.editMyInfo,
        params: {
          empid: this.employeeId
        },
      });
      },
      checkUpcoming(){
        let data = this.upcomingHolidays;
        if(data != undefined){
          if(Object.keys(data).length > 0){
              return false;
          } else {
             return true;
          }
        }
      },
      checkTimeOff(data){
          if(data == '' || this.leaveFilteredArr == 'file does not exist'){
            return true;
          } else {
            return false;
          }
      },
      checkHistory(data){
         if(data == '' || this.historyleaveFilteredArr == 'file does not exist'){
            return true;
          } else {
            return false;
          }
      },
      async mountedFunction() {
        var date = new Date();
        this.currentmon = date.getMonth();
        this.loading = true;

        await axios.post(this.$hostName + '/getEmployeeLeaveBalance', {empId: this.employeeId})
        .then((response) => {
          this.leaveBalAll = this.getBalanceByLeaveType(response.data[0].leaveBalanceData);
        });

        await axios.post(this.$hostName + '/myInfo', {fetchType: 'upcoming', year: new Date().getFullYear(), empId: this.employeeId})
        .then((response) => {
          this.loggedInUser = [];
          response.data[0].employeeData.forEach((element) => {
            this.loggedInUser.push(element);
          });
          this.filteredLeaveTypeBal = [];
          this.filteredLeaveType = [];
          this.formatedUserData = this.loggedInUser;
          this.empLocation = this.formatedUserData[0].location.toUpperCase();
          this.empGender = this.formatedUserData[0].gender.toUpperCase();
          for(let i=0; i<this.appliedLeaveData.length; i++){
            for(let j=0; j<this.appliedLeaveData[i].region.length; j++){
              if((this.empLocation == this.appliedLeaveData[i].region[j] || this.appliedLeaveData[i].region[j] == 'All') && (this.empGender == this.appliedLeaveData[i].gender || this.appliedLeaveData[i].gender == 'All')){
               this.filteredLeaveType.push(this.appliedLeaveData[i].leaveType);
               this.filteredLeaveTypeBal.push(this.leaveBalAll[this.appliedLeaveData[i].leaveType+' Balance']);
              }
            }
          }
         
          this.startDate = this.loggedInUser[0]['start_Date'];
          this.employement_type = this.loggedInUser[0]['employement_type'].trim();
          this.leaveBalanceData = response.data[1].leaveBalanceData;
          this.leaveFilteredArr = response.data[2].upcomingLeaveData;
          this.upcomingHolidays = response.data[3].upcomingHolidayData;
          this.managerData = response.data[4].managerData;
          this.directReports = response.data[5].directReportsData;
          if(this.othersSelected){
            this.tabSwitch('timeOff');
          }
        });
        if( this.empLocation == 'INDIA'){
          await axios.get(this.$hostName + '/getOptionalLeaves' ).then((response)=>{
            this.optionalLeaveList = response.data[0].optionalLeaveData;
          });
        }
        await axios.get(this.$hostName + '/employeeleaveexpirydata/' + this.employeeId ).then((response)=>{
          this.employeeExpiryLeaves = response.data;
        }).catch((error) => {
          console.log(error);
        });
        await axios.post(this.$hostName + '/myInfo',{fetchType: 'history', year: new Date().getFullYear(), empId: this.employeeId},)
            .then((response) => {
              this.loggedInUser = [];
              response.data[0].employeeData.forEach((element) => {
                 this.loggedInUser.push(element);
               });
              // this.leaveHistory = response.data[2].upcomingLeaveData;
              //  this.historyleaveFiltered(this.leaveHistory);
               this.historyleaveFilteredArr = response.data[2].upcomingLeaveData;

        }).catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
      },
      handleDateDifference(startDate){
        let date = new Date;
        let formCurrentDate = date.getFullYear()+'-'+ `${date.getMonth()+1}` +'-'+ date.getDate();
            const date1 = new Date(formCurrentDate);
            const date2 = new Date(startDate);
            date2.setHours(0,0,0,0);
            const diffTime = Math.abs(date1 - date2);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if(diffDays == 0){
            return true;
            }
            else {
              return false;
            }
      },
      getBalanceByLeaveType(data) {
        let leaveArr = {};

        for(let leave in data){
          leaveArr[data[leave].name] = data[leave].balance;
        }

        return leaveArr;
      },
      leaveBalanceFilter(data){
        let leaveArr = {};

        for(let leave in data){
            leaveArr[data[leave].name] = data[leave].balance;
        }
        
        for(let i=0; i<this.filteredLeaveType.length; i++){
            this.leaveFilterType[i+1] = this.filteredLeaveType[i];
        }

        for(let i=0; i<this.leaveBal.length; i++){
          let balanceFromDb = leaveArr[this.leaveBal[i].name+' Balance'];
          this.leaveBal[i].bal =  balanceFromDb != undefined ? balanceFromDb : '';
        }
        
        return this.leaveBal;
      },
      reportingEmployeeDetails(emp_id, emp_email) {
        if (emp_id != '' && emp_email != '') {
            const secretKey = process.env.KEY || 'myscecretkey';
            const encryptedEmpId = encryptData(emp_id, secretKey);
            let currentUrl = window.location.href;
            let directReportUrl = '';
            if (this.$route.name == routeNames.myInfo) {
                directReportUrl = currentUrl.replace(routeNames.myInfo, routeNames.directreport);
            } else {
              directReportUrl = currentUrl.substring(0, currentUrl.indexOf(routeNames.directreport) + routeNames.directreport.length);
            }
            window.open(`${directReportUrl}/${encodeURIComponent(encryptedEmpId)}`);

        }
      },
     
      tabSwitch(type){
        this.leaveFilter.category = type;
        this.filter.category = type;
        var tab1 = document.getElementById('nav-personal');
        var btnTab1 = document.getElementById('nav-personal-tab');
        var tab2 = document.getElementById('nav-timeOff');
        var btnTab2 = document.getElementById('nav-timeOff-tab');
        var tab3 = document.getElementById('nav-upcoming');
        var btnTab3 = document.getElementById('nav-upcoming-tab');
        var tab4 = document.getElementById('nav-leave-upcoming');
        var btnTab4 = document.getElementById('nav-leave-upcoming-tab');
        var tab5 = document.getElementById('nav-requesttimeOff');
        var btnTab5 = document.getElementById('nav-requesttimeOff-tab');
        var tab6 = document.getElementById('nav-leave-history');
        var btnTab6 = document.getElementById('nav-leave-history-tab');
        if(type == 'personal'){
          this.showLeaveBalance = false;
          tab2.classList.remove('show');
          tab2.classList.remove('active');
          btnTab2.classList.remove('active');
          tab1.classList.add('show');
          tab1.classList.add('active');
          btnTab1.classList.add('active');
          tab5.classList.remove('show');
          tab5.classList.remove('active');
          btnTab5.classList.remove('active');
        } 
        else if(type == 'timeOff'){
          // $("#filter").hide();
          this.showLeaveBalance = true;
          setTimeout(()=>{
            try {
              tab1.classList.remove('show');
              tab1.classList.remove('active');
              btnTab1.classList.remove('active');
            } catch(error){
                console.log(error);
            }
            try{
              tab2.classList.add('show');
              tab2.classList.add('active');
              btnTab2.classList.add('active');
            } catch(error){
              console.log(error);
            }
            try{
              tab3.classList.add('show');
              tab3.classList.add('active');
              btnTab3.classList.add('active');
            } catch(error){
              console.log(error);
            }
            try{
              tab4.classList.remove('show');
              tab4.classList.remove('active');
              btnTab4.classList.remove('active');
            } catch(error){
              console.log(error);
            }
            try{
              tab6.classList.remove('show');
              tab6.classList.remove('active');
              btnTab6.classList.remove('active');
            } catch(error){
              console.log(error);
            }
            try{
              tab5.classList.remove('show');
              tab5.classList.remove('active');
              btnTab5.classList.remove('active');
            } catch(error){
              console.log(error);
            }
          });
        } 
        else if(type == 'requesttimeOff') {
          this.showLeaveBalance = true;
          tab5.classList.add('show');
          tab5.classList.add('active');
          btnTab5.classList.add('active');
          tab1.classList.remove('show');
          tab1.classList.remove('active');
          btnTab1.classList.remove('active');
          tab2.classList.remove('show');
          tab2.classList.remove('active');
          btnTab2.classList.remove('active');
           
        }
        else if(type == 'upcoming'){
          //  $('#filter').hide();
           this.filterFlag = false;
          this.hideFilter = false;
          tab4.classList.remove('show');
          tab4.classList.remove('active');
          btnTab4.classList.remove('active');
          tab3.classList.add('show');
          tab3.classList.add('active');
          btnTab3.classList.add('active');
          tab6.classList.remove('show');
          tab6.classList.remove('active');
          btnTab6.classList.remove('active');
           tab2.classList.add('show');
          tab2.classList.add('active');
          btnTab2.classList.add('active');
          tab1.classList.remove('show');
          tab1.classList.remove('active');
          btnTab1.classList.remove('active');
          tab5.classList.remove('show');
          tab5.classList.remove('active');
          btnTab5.classList.remove('active');
        } 
        else if(type == 'leave-upcoming'){
          // $('#filter').show();
          this.filterFlag = false;
          this.hideFilter = true;
          // this.show = false;
          this.years = [{name: currentYear},{name: currentYear+1}];
          this.activeYear = 0;
          // this.activeMonth = 0;
          this.activeType = 0;
          this.clearFilter();
          tab3.classList.remove('show');
          tab3.classList.remove('active');
          btnTab3.classList.remove('active');
          tab4.classList.add('show');
          tab4.classList.add('active');
          btnTab4.classList.add('active');
          tab6.classList.remove('show');
          tab6.classList.remove('active');
          btnTab6.classList.remove('active');
          tab2.classList.add('show');
          tab2.classList.add('active');
          btnTab2.classList.add('active');
          tab1.classList.remove('show');
          tab1.classList.remove('active');
          btnTab1.classList.remove('active');
          tab5.classList.remove('show');
          tab5.classList.remove('active');
          btnTab5.classList.remove('active');
        } 
        else if(type == 'leave-history') {
          // $('#filter').show();
          this.filterFlag = true;
          this.hideFilter = true;
          // this.show = false;
          this.years = [{name: currentYear},{name: currentYear-1}];
          this.activeYear = 0;
          this.activeMonth = 0;
          this.activeType = 0;
          this.clearFilter();
          tab3.classList.remove('show');
          tab3.classList.remove('active');
          btnTab3.classList.remove('active');
          tab6.classList.add('show');
          tab6.classList.add('active');
          btnTab6.classList.add('active');
          tab4.classList.remove('show');
          tab4.classList.remove('active');
          btnTab4.classList.remove('active');
           tab2.classList.add('show');
          tab2.classList.add('active');
          btnTab2.classList.add('active');
          tab1.classList.remove('show');
          tab1.classList.remove('active');
          btnTab1.classList.remove('active');
          tab5.classList.remove('show');
          tab5.classList.remove('active');
          btnTab5.classList.remove('active');
        }
      },
      getImgUrl(filename){
          try{ 
            return 'data:image/jpeg;base64,' + filename;
          }catch(_){
          return require('../assets/imagealt.png');
          }
      },
     
      userDetail(type){
        if(this.email == 'admin@scala.com'){
          if(type == 'name'){
            return 'Admin';
          } else if (type == 'designation'){
            return 'Administrator';
          } else if (type == 'managerName'){
            return 'Somebody';
          } else if (type == 'email'){
            return 'admin@scala.com';
          }  else if (type == 'position'){
            return 'no position';
          } else if (type == 'hireDate'){
            return 'no hire date';
          } else if (type == 'month'){
            return 'no month';
          } else if (type == 'department'){
            return 'no department';
          } else if (type == 'place'){
            return 'no place';
          } else if (type == 'managerPosition'){
            return 'no manager position';
          } else if (type == 'bday'){
            return 'no birthday';
          } else if (type == 'contact'){
            return 'no contact';
          } else if (type == 'empGrade'){
            return 'no empGrade';
          }
        } else if(this.loggedInUser[0] != undefined){
          if(type == 'name'){
            return this.loggedInUser[0]['name'];
          } else if (type == 'designation'){
            return this.loggedInUser[0]['position'];
          } else if (type == 'managerName'){
            return `${this.managerData[0]['name']}`;
          } else if (type == 'managerPosition'){
             return this.managerData[0]['position'];
          } else if (type == 'empGrade'){
            return this.loggedInUser[0]['employee_grade'];
          } else if (type == 'bday'){
            return this.loggedInUser[0]['birth_dates'];
          } else if (type == 'contact'){
            return this.loggedInUser[0]['mobile_number'];
          } else if (type == 'email'){
            return this.loggedInUser[0]['email'];
          } else if (type == 'place'){
            return this.loggedInUser[0]['location'];
          } else if (type == 'position'){
            return this.loggedInUser[0]['position'];
          } else if (type == 'department'){
            return this.loggedInUser[0]['department'];
          } else if (type == 'hireDate'){
            return this.loggedInUser[0]['start_Date'];
          } else if (type == 'address'){
            return this.loggedInUser[0]['address'];
          }else if (type == 'month'){
            const dateArr = this.startDate.split('-');
            var formattedStartDate = dateArr[2]+'-'+dateArr[1]+'-'+dateArr[0];
            const hireDate = new Date(formattedStartDate);
            var today = new Date();
            // var time =(today.getTime() - hireDate.getTime()) / 1000;
            // var year  = Math.abs(Math.round((time/(60 * 60 * 24))/365.25));
            var year = today.getYear() - hireDate.getYear();
            var month = (today.getMonth()+1) - (hireDate.getMonth()+1);
            if(month<0){
              year = year-1;
              month = 12+month;
            }
            if(year>1){var str1 = 'years';} else { str1 = 'year';}
            if(month>1){var str2 = 'months';} else { str2 = 'month';}
            if(year==0 && month>0){
              return (month-(year*12))+' '+ str2;
            } else if(year==0 && month == 0){
              return '<1 month';
            } else if(year>0 && month == 0){
              return year +' '+ str1;
            }
            else {
              return year +' '+ str1 +'\xa0\xa0'+month+' '+ str2 ;
            }
          }
        }
        
      },
      resetActiveYear (index,year) {
        this.activeYear = index;
        this.filter.year = year.name;
      },
      resetActiveMonth (index,month) {
        this.activeMonth = index;
        this.filter.month = month.name;
      },
      resetActiveType (index,type) {
        this.activeType = index;
        this.filter.type = type;
      },
      clearFilter () {
        this.activeCategory = 0;
        this.activeYear = 0;
        this.activeMonth = 0;
        this.activeType = 0;
        this.filter.year = currentYear;
        this.filter.month = 'All';
        this.filter.type = 'All';

        let defaultData = {
            'category': 'leave-history',
            'year': this.filter.year,
            'month': this.filter.month,
            'type': this.filter.type
        };

        this.applyFilter(defaultData);
      },
      async applyFilter (filtered) {
        let data = filtered;
        data.empId = this.employeeId;
        this.leaveFilter = data;
        this.show = false;
        // if(this.leaveFilter.category == 'leave-upcoming')
        //   this.leaveFiltered(this.upcomingLeaves);
        // else 
        //   this.historyleaveFiltered(this.leaveHistory); 
        await axios.post(this.$hostName + '/leaves/filter',data,)
            .then((response) => {
               this.historyleaveFilteredArr = response.data;

        }).catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
      },
      uploadMedicalAttachment(event){
        let url = event.target.files[0];
          if(url.size > 1024 * 1024) {
            event.preventDefault();
            alert('File too big (> 1MB)');
            return;
        } else {
          this.isMedicalCertificateAttached = true;
          this.medicalAttachment = url;
        }
      },
      profilePicUpload (event) {
        this.updatedProfilePic = event.target.files[0];
        if (this.updatedProfilePic) {
          if(this.updatedProfilePic.size > 1024 * 1024) {
            event.preventDefault();
            alert('File too big (> 1MB)');
            return;
          } else {
            this.loading = true;
            let formData = new FormData();
            formData.append('empId', this.employeeId);
            formData.append('file', this.updatedProfilePic);
            formData.append('id', this.loggedInUser[0]);
            axios
              .post(this.$hostName + '/myInfo/updateProfilePhoto', formData, {headers : {'Content-Type': 'multipart/form-data'}})
              .then((response) => {
                console.log(response);
            })
            .catch((error) => {
              console.log(error);
              this.errors = error.response.data.errors;
            })
            .finally(() => {
              location.reload();
            });
          }
        }
        this.applyFilterFlag = true;
      },
      leaveFiltered(res){
          this.yearFilter(res).then(yearRes=> {
            this.monthFilter(yearRes).then(monthRes=> {
              this.typeFilter(monthRes).then(typeRes => {
                this.leaveFilteredArr = typeRes;
              });
            });
          });
      },

      cateogryFilter(){
        return new Promise((resolve) => {
          if(this.leaveFilter.category == 'Upcoming' ) {
            this.disabledYear = false;
            this.disabledMonth = false;
            this.loading1 = true;
            var currentMonth = new Date();
            this.currentmon = currentMonth.getMonth();
            this.loading1 = true;
            axios.post(this.$hostName + '/myInfo',{fetchType: 'upcoming', year: new Date().getFullYear(), empId: this.employeeId},)
            .then((response) => {
              this.loggedInUser = [];
              response.data[0].employeeData.forEach((element) => {
                this.loggedInUser.push(element);
              });
              this.upcomingLeave = response.data[2].upcomingLeaveData;
              let newData = this.upcomingLeave;
              let filteredArr = [];
              for(let key in newData){
                for(let j in newData[key]) {
                  let month = key;
                  let day = newData[key][j].day;
                  let type = newData[key][j].type;
                  let reason = newData[key][j].reason;
                  let comments = newData[key][j].comments;
                  filteredArr.push({month, day, type, reason,comments});
                }
              }
              resolve(filteredArr);
            })
            .catch((error) => {
              console.log(error);
            });
          }
          else {
            this.disabledYear = true;
            var selectedYear = this.leaveFilter.year;
            this.loading1 = true;
            axios.post(this.$hostName + '/myInfo',{fetchType: 'history', year: selectedYear, empId: this.employeeId},)
            .then((response) => {
              this.upcomingLeave = response.data[2].upcomingLeaveData;
              if(this.upcomingLeave == 'file does not exist'){
                resolve('file does not exist');
              } 
              else{
                response.data[0].employeeData.forEach((element) => {
                  this.loggedInUser.push(element);
                });
                this.startDate = this.loggedInUser[0]['start_Date'];
                  let data = response.data[2].upcomingLeaveData;      
                  let filteredArr = [];
                    for(let key in data){
                          for(let j in data[key]) {
                              let month = key;
                              let day = data[key][j].day;
                              let type = data[key][j].type;
                              let reason = data[key][j].reason;
                              let comments = data[key][j].comments;
                              filteredArr.push({month, day, type, reason,comments});
                            }
                        }
                        this.typeFilter(filteredArr);
                        resolve(filteredArr);
              }
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              // this.loading1 = false;
            });
          }
        });
      },
      yearFilter(data){
        return new Promise((resolve) => {
          var year = currentYear;
          var selectedYear = this.leaveFilter.year;
          this.checkYear = selectedYear;
          
          if(this.leaveFilter.year == year) {
            let filteredArr = [];
              for(let key in data){
                for(let j in data[key]) {
                  let month = key;
                  let day = data[key][j].day;
                  let type = data[key][j].type;
                  let reason = data[key][j].reason;
                  let status = data[key][j].status;
                  let noofdays = data[key][j].noofdays;
                  let duration = data[key][j].duration; 
                  let start_date = data[key][j].start_date;
                  let end_date = data[key][j].end_date;
                  let comments = data[key][j].comments;
                  let id = data[key][j].id;
                  let leave_id = data[key][j].leave_id;
                  filteredArr.push({month, day, type, reason, status, noofdays, duration, start_date, end_date,comments, id, leave_id});
                }
              }
            resolve(filteredArr);
            this.loading1 = false;
          }
          else if(this.leaveFilter.year < currentYear){
            this.loading1 = true;
            axios.post(this.$hostName + '/myInfo',{fetchType: 'history', year: selectedYear, empId: this.employeeId},)
            .then((response) => {
              this.upcomingLeave = response.data[2].upcomingLeaveData;
              if(this.upcomingLeave == 'file does not exist'){
                resolve('file does not exist');
              } else{
                response.data[0].employeeData.forEach((element) => {
                  this.loggedInUser.push(element);
                });
                this.startDate = this.loggedInUser[0]['start_Date'];
                  let data = response.data[2].upcomingLeaveData;      
                  let filteredArr = [];
                    for(let key in data){
                          for(let j in data[key]) {
                              let month = key;
                              let day = data[key][j].day;
                              let type = data[key][j].type;
                              let reason = data[key][j].reason;
                              let status = data[key][j].status;
                              let noofdays = data[key][j].noofdays;
                              let duration = data[key][j].duration;
                              let start_date = data[key][j].start_date;
                              let end_date = data[key][j].end_date;
                              let comments = data[key][j].comments;
                              let id = data[key][j].id;
                              let leave_id = data[key][j].leave_id;
                              filteredArr.push({month, day, type, reason, status, noofdays, duration, start_date, end_date,comments, id, leave_id});
                            }
                        }
                        this.typeFilter(filteredArr);
                        resolve(filteredArr);
                        this.upcomingLeave = filteredArr;
              }
              
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              //  this.loading1 = false;
            });
          }
        else if(this.leaveFilter.year > currentYear){
            this.loading1 = true;
            axios.post(this.$hostName + '/myInfo',{fetchType: 'history', year: selectedYear, empId: this.employeeId},)
            .then((response) => {
              this.upcomingLeave = response.data[2].upcomingLeaveData;
              if(this.upcomingLeave == 'file does not exist'){
                resolve('file does not exist');
              } else{
                response.data[0].employeeData.forEach((element) => {
                  this.loggedInUser.push(element);
                });
                this.startDate = this.loggedInUser[2];
                  let data = response.data[2].upcomingLeaveData;      
                  let filteredArr = [];
                    for(let key in data){
                          for(let j in data[key]) {
                              let month = key;
                              let day = data[key][j].day;
                              let type = data[key][j].type;
                              let reason = data[key][j].reason;
                              let comments = data[key][j].comments;
                              filteredArr.push({month, day, type, reason,comments});
                            }
                        }
                        this.typeFilter(filteredArr);
                        resolve(filteredArr);
                        this.upcomingLeave = filteredArr;

              }
              
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              //  this.loading1 = false;
            });
          }
        });
      },
       monthFilter(monthData){
         return new Promise((resolve) => {
         let filteredArr = [];
         if(monthData == 'file does not exist'){
          resolve('file does not exist');
         } else{
            if(this.leaveFilter.month == 'All'){
              resolve(monthData);
            } else if(this.leaveFilter.month != 'All'){
              for(let i in monthData){
                    if(monthData[i].month.slice(0, 3) == this.leaveFilter.month){
                        let month = monthData[i].month;
                        let day = monthData[i].day;
                        let type = monthData[i].type;
                        let reason = monthData[i].reason;
                        let comments = monthData[i].comments;
                        filteredArr.push({month, day, type, reason,comments});
                    }
              }
              this.upcomingLeave = filteredArr;
              resolve(filteredArr);
            }
          }

        });
         
      },
      typeFilter(data){
         this.loading1 = false;
        return new Promise((resolve) => {
          let filteredArr = [];
          if(data == 'file does not exist'){
            resolve('file does not exist');
          }
          else {
            if(this.leaveFilter.type == 'All'){
              resolve(data);
            } else if(this.leaveFilter.type != 'All') {
                  for(let key in data){
                        if(data[key].type == this.leaveFilter.type) {
                          let month = data[key].month;
                          let day = data[key].day;
                          let type = data[key].type;
                          let reason = data[key].reason;
                          let status = data[key].status;
                          let noofdays = data[key].noofdays;
                          let duration = data[key].duration;
                          let start_date = data[key].start_date;
                          let end_date = data[key].end_date;
                          let comments = data[key].comments;
                          let id = data[key].id;
                          let leave_id = data[key].leave_id;
                          filteredArr.push({month, day, type, reason, status, noofdays, duration, start_date, end_date,comments, id,leave_id });
                        } 
                  }
                this.upcomingLeave = filteredArr;
            resolve(filteredArr); 
            }
          }
        });
      },

       historyleaveFiltered(res){
          this.yearFilter(res).then(yearRes=> {
            this.monthFilter(yearRes).then(monthRes=> {
              this.typeFilter(monthRes).then(typeRes => {
                this.historyleaveFilteredArr = typeRes;
              });
            });
          });
      },

      showComments(data) {
        Swal.fire({
          text: data, 
          confirmButtonText: 'Ok',
        });
      }
    }
};
</script>
<style scoped>
.successMsg{
   padding-left: 130px;
   top: 250px;
   left: 50%;
   transform: translate(-50%,0%);
   position: relative;
   width: 600px;
   font-size: 25px;
}
.right-side{
  padding-left: 7!important;
}
.left-side{
  padding-right: 7!important;
}
.duration-label{
    margin-left: 2px;
    margin-right: 15px;
    position: relative;
    transform: translate(0%, -10%);
    font-size: 15px;
}
.type-of-leave{
  color: #000;
  font-size: 20px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 20px;
}
.leave-duration{
   color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 20px;
}
.selected-leave{
  /* margin-top: 10px; */
} 
.optional-leave{
  margin-top: 10px;
  width: 65% !important;
}
select{
  height: 30px;
}
.name{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}
.choose-file{
  /* top: 235px; */
  /* position: absolute; */
  margin-top: 20px;
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-weight: bold;
  font-family: 'Montserrat';
}
.choose-file-text{
  /* top: 266px; */
  /* position: absolute; */
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
}
.note-text{
  /* top: 320px; */
  /* position: absolute; */
  margin-top: 10px;
  color: red;
  /* width: 300px; */
}
.attach-files-text{
  /* top: 290px;
  left: 20px;
  position: absolute; */
  color: #000;
}


.from-date{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 20px;
}

.to-date{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 7px;
/*  position: absolute;*/
}
.to{
  margin-top: 0px;
  /* position: absolute;*/
}


.leave-reason{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 20px;
}
.leave-apply{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  /* top: 380px; */
  background-color: red;
  width: 100px;
  height: 36px;
  border: none;
  /* left: 15px; */
  /* position: absolute; */
  margin-top: 20px;
  margin-right: 20px;

}
.leave-cancel{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  /* top: 380px; */
  background-color: grey;
  width: 100px;
  height: 36px;
  border: none;
  margin-top: 20px;

  /* left: 155px; */
  /* position: absolute; */
}
.leave-new{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  /* top: 380px; */
  /* left: 155px; */
  /* position: absolute; */
  background-color: green;
  width: 100px;
  height: 36px;
  border: none;
  margin-top: 20px;
}
.leave-duration{
  color: #000;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}

input[type='radio'] {
        -webkit-appearance: none;
        border-radius: 50%;
        outline: none;
        box-shadow: 0 0 0 1px red;
        height: 20px;
        width: 20px;
        /* margin-top: 8px; */
        /* margin-left: 20px; */
        /* margin-right: 10px;  */
       /* margin-left: 0px;*/
    }


    input[type='radio']:before {
        content: '';
        display: block;
        width: 60%;
        height: 60%;
        margin: 20% auto;
        border-radius: 50%;
    }

    input[type='radio']:checked:before {
        background: #D05959;
    }

    .line-break{
      display: none;
    }

.edit{
  color: red;
  font-size: 25px;
}
.delete{
  color: red;
  font-size: 25px;
}
.col-md-6{
  padding: 0px 30px 0px 30px;
}
.pending-status{
   background-color: yellow;
  
  /* apply background only for text*/
  -webkit-background-clip: content-box;
  background-clip: content-box;
  text-align: center;
  font-weight: bold;
  
} 

.approved-status {
  background-color: green;
  
  /* apply background only for text*/
  -webkit-background-clip: content-box;
  background-clip: content-box;
  text-align: center;
  color: #fff;
  font-weight: bold;
}
.approved-status1 {
  background-color: green;
  padding: 3px 10px 3px 10px;
  text-align: center;
  color: #fff;
  font-weight: bold;

}
.rejected-status{
  background-color: red;
  padding: 3px 10px 3px 10px;
  text-align: center;
  color: #fff;
  font-weight: bold;
}
.employee-info{
  position: relative;
  height: 100%;
  width: 100%;
  overflow-y: scroll;
}
.noAnnouncementImg{
  width: 100px;
}
.no-announcement{
  text-align: center;
  position: relative;
  top: 50%;
  transform: translate(0,-50%);
}
.leaveCount{
  height: 5px;
}
.resetLoader{
  width: 100%;
  height: 100%;
  background-color: #eceaea;
  background-image: url(/img/loading.50d8d981.gif);
  background-size: 120px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
  top: 0px; 
  position: absolute;
}
.disableYear {
  background-color: #DDDDDD !important;
  pointer-events: none;
  cursor: default;
  text-decoration: none;
}
.disableMonth {
  background-color: #DDDDDD !important;
  pointer-events: none;
  cursor: default;
  text-decoration: none;
}
.optionalLeave{
  padding: 0px;
  width: 100px;
  text-align: center;
  margin: 10px 7px 0px 7px;
}
 .cameraImg{
  border-radius: 50%;
  position: relative;
  padding: 2px 5px 2px 5px;
  top: -36px;
  left: calc(50% + 5.5vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 999;
}
.cameraImgNoImg{
  border-radius: 50%;
  position: relative;
  padding: 2px 5px 2px 5px;
  top: -36px;
  left: calc(50% + 5.5vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 99;
}

.annualLeaveCount{
  padding: 8px;
}

.holidayType{
  font-size: 14px;
  font-weight: 500;
}
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0rem; 
    border-top-right-radius: 0rem; 
     height: 54px;
}
.UserLeavesTab{
  padding: 0px;
  margin-bottom: 65px;
  
}
.upcomingModal{
  padding: 0px;
}

.leaveModal{
  padding: 0px;
}

.filters{
  height: 40px;
  width: 40px;
  cursor: pointer;
}
.filterClose {
  padding: 2px;
  display: inline-flex;
  height: 15px;
  width: 15px;
  margin: 5px;
  cursor: pointer;
}
.filterCondition {
    /* background-image: url("../../assets/Happy.gif"); */
    background-color: white;
    height: 100%;
    width: 67%;
    border: 1px solid #b2beb5;
    margin: 0;
    float: right;
}

.filterCondition table {
    width: 100%;
    height: 100%;
    border-collapse: collapse;
}

.filterCondition tr {
    /* border-bottom: 2px solid #b2beb5; */
    margin-right: auto;
    width: 100%;
    display: inline-flex;
    flex-direction: row;
    flex-wrap: wrap;
    /* justify-content: space-evenly; */
}

.filterCondition tr:first-child {
    border-bottom: 2px solid #b2beb5;
}

.filterCondition tr:last-child {
    border-top: 2px solid #b2beb5;
    margin: 15px 5px 5px 0px;
    padding: 10px;
}

.filterCondition tr th {
    /* border-bottom: 2px solid #b2beb5; */
    margin: 10px 0px 10px 10px;
    font-size: 12px;
}

.applyFilter {
    border: 2px solid #EE3234 !important;
    background-color: #EE3234 !important;
    color: white;
}

.clearFilter {
    border: 2px solid #dddddd !important;
    background-color: #dddddd !important;
    /* color: white; */
}

.activeFilter {
    background-color:  #b2beb5 !important;
}

.filterCondition button {
    border: 2px solid #b2beb5;
    padding: 5px;
    margin: 3px;
    border-radius: 5px;
    font-size: 12px;
    min-width: 80px;
    background-color: white;
}

.filterContainer {
    position: absolute;
    width: 100%;
    height: 89.5%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 100;
}
#month{
    background-color: #EE3234;
    color:  #fff;
    font-size: 16px;
    font-weight: 700px;
}
.logoImg{
  width: 50px;
  height: 50px;
}
.filterImg{
    left: calc(94%);
    top: 2px;
    position: absolute;
    float: left;
}
.celebration {
    background-color: #EE3234;
    color:  #fff;
    font-size: 16px;
    letter-spacing: 0px;
    margin-top: 20px;
}

.upcomingTime-off{
  height: 29rem;
  width: 100%;
  overflow: auto;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}
.date{
  color:  #000;
  margin-top: 10px;
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0px;
} 
.holidayDate{
  color:  #000;
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0px;
} 
.events{
   color:  #000;
  font-size: 14px;
  font-weight: 700px;
  letter-spacing: 0px;
}
.card{
   box-shadow: rgba(0, 0, 0, 0.35) 0px 3px 10px;
   background-color: #D6E0FF !important;
}

.table{
  overflow: auto;
}
.annual1{
  font-weight: bold;
  color: #000;
  font-size: 12px;
  letter-spacing: 0px;
  text-align: center;
  position: relative;
}
.annualLeaveImg{
  width: 70px;
  height: 70px;
  /*position: absolute;
  left: 50%;
  transform: translate(-50%);*/
}
.day2 {
  font-size: 12px;
  background-color: red;
  border-radius: 50px 50px 50px 50px;
  letter-spacing: 0px;
  right: -2rem;
  top: -5rem;
  position: relative;
  padding: 2px 8px 2px 8px;
  z-index: 99;
}

.bottom{
  margin: 0;
  height: 80%;
  position: absolute;
  width: calc(100% - 60px);
  left: 60px;
  font-family: 'Montserrat';
}
.profileBg{
    background-color:  #d05959;
    color: white;
    min-width: 0%;
    width: 100%;
    height: 100%;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 0px 10px;
}
textarea {
  resize: none;
}
.profile-pic{
  padding: 0;
}
.profileImg-holder{
  display: inline-block;
  width: 45%;
  height: 50%;
  top: 30px;
  position: absolute;
  left: calc(30%);
  z-index: 1;
}
.top{
  height: 100%;
  width: calc(100% - 60px);
  left: 60px;
  position: absolute;
}
.profileImg{
  width: 20vh;
  height: 20vh;
  top: 6px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 60px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  left: calc(50% + 1vh);
  transform: translate(-50%);
  position: relative;
  border: 4px solid #fff;
}
.peopleShortNameMyInfo {
  display: inline-block;
  width: 20vh;
  height: 20vh;
  top: 6px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 60px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  left: calc(50% + 1vh);
  transform: translate(-50%);
  position: relative;
  border: 4px solid #fff;
}
.myData{
  display: inline-block;
    position: relative;
    color: #fff;
    /* left: 30%; */
    top: 50%;
    transform: translate(0,-50%);
}
.myName{
  font-size: 32px;
  font-family: 'Montserrat';
  letter-spacing: 0px;
}

.card-text {
    display:inline;
}
.dirManagerName {
    display:inline;
    cursor: pointer;
}
.dirManagerName:hover{
    color: #000000;
    text-decoration: underline;
}
.personal{
    display:inline;
    color: #3F3F40;
}
.gender{
     display:inline;
}
.reportsTo{
  padding-top: 40px !important;
}
.leaveView{
  margin-left: 0px;
  margin-bottom: 8px;
}

.mobile-screen{
  display: none;
}

.upcoming-mobile{
  display: none;
}

@media only screen and (max-width: 575.98px) {

.topBg{
  height: 230px !important;
}

.modal-content{
  width: 100% !important;
}

.upcoming-desktop{
  display: none !important;
}

.upcoming-mobile{
  display: block !important;
}

  .duration-label{
    /* margin-left: 3px; */
    /* margin-right: 10px; */
    position: relative;
    transform: translate(0%, -10%);
    font-size: 15px;
}

.to-date{
  margin-top: 20px !important;
}

.big-screen{
  display: none !important;
}

.mobile-screen{
  display: initial !important;
}

.line-break{
  display: block !important;
}


.attach-files-text{
    /* position: absolute;
    top: 645px;
    left: 15px; */
}




  .type-of-leave{
  color: #000;
  font-size: 14px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}
.leave-duration{
   color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}
.selected-leave{
  margin-top: 10px;
  width: 90%;
  
} 
select{
  height: 30px;
}
.name{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}



.to{
  /* width: 90%; */
  margin-bottom: 30px;

}

.leave-reason{
  color: #000;
  font-size: 18px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}
.leave-apply{
   color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 25px;
  margin-right: 10px;
  background-color: red;
  width: 100px;
  height: 32px;
  border: none;
}
.leave-cancel{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 25px;
  margin-left: 0px;
  background-color: grey;
  width: 100px;
  height: 32px;
  border: none;
}

.leave-new{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  background-color: green;
  width: 100px;
  height: 32px;
  border: none;
  margin-top: 25px;
}

.leave-duration{
  color: #000;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px;
}

input[type='radio'] {
        -webkit-appearance: none;
        border-radius: 50%;
        outline: none;
        box-shadow: 0 0 0 1px red;
        height: 20px;
        width: 20px;
        /* margin-top: 8px; */
        /* margin-left: 10px; */
        margin-right: 10px; 
       margin-left: 0px !important;
    }

    input[type='radio']:before {
        content: '';
        display: block;
        width: 40%;
        height: 40%;
        margin: 30% auto;
        border-radius: 50%;
    }

    input[type='radio']:checked:before {
        background: #D05959;
    }
  .profileBg-container{
    padding: 0px !important;
    border-left: 10px solid white;
  }
  .bottom{
    overflow-y: scroll;
  }
  .managerNoImg{
  width: 30px !important;
  height: 30px !important;
  padding: 2px 0 !important;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 14px !important;
  color: white;
  justify-content: center;
  align-items: center;
  text-align: center;
  border: 2px solid #fff;
   letter-spacing: 0px;
} 

  .optionalLeave{
margin: 25px 0px 0px 0px!important;
  }
  .leaveView{
  margin-left: 0 !important;
  margin-bottom: 0 !important;
}
.filters{
    left: calc(94%);
    top: 78px;
    position: absolute;
    float: left;
}
  .filterCondition {
    background-color: white;
    height: 100%;
    width: 100%;
    border: 1px solid #b2beb5;
    margin: 0;
    float: right;
}

  .filterCondition button {
    border: 2px solid #b2beb5;
    padding: 1px;
    margin: 0px;
    border-radius: 5px;
    font-size: 10px;
}


  .filterImg{
  left: calc(86%);
  top: -32px;
  position: absolute;
  float: left;
}

 .cameraImg{
  border-radius: 50%;
  position: relative;
  padding: 4px 8px 4px 8px;
  top: -25px;
  left: calc(50% + 2.5vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 999;
}

.cameraImgNoImg{
  border-radius: 50%;
  position: relative;
  padding: 2px 5px 2px 5px;
  left: calc(50% + 2.5vh);
  top: -25px;
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 99;
}
.leaveListTabs{
  padding: 5px !important;
}
  .profileImg-holder{
    /* display: inline-block; */
    max-height: 60px !important;
    width: 60px !important;
    left: 39px;
    top: -12em;
    position: absolute;
    z-index: 1;
  }
  .profileImg{
  width: 10vh !important;
  height: 10vh !important;
  top: 0px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 60px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  left: calc(50% + 1vh);
  transform: translate(-50%);
  position: relative;
  border: 2px solid #fff;
}

  .peopleShortNameMyInfo{
    top: -1px;
    position: relative;
    z-index: 1;
    font-size: 27px !important;
    width: 10vh !important;
    height: 10vh !important;
    left: calc(50% + 1vh);
    border: 2px solid #fff;
  }
  .top{
    height: 100%;
    width: calc(100% - 60px) !important;
    left: 60px !important;
  }
.profileBg{
    background-color:  #d05959;
    min-width: 100%;
    height: 100%;
}
.myData{
    position: absolute;
    left: 77px;
    /* transform: translate(-50%); */
    top: -20px;
    color: #fff;
}
.reportsTo{
  padding-top: 2px !important;
  padding-bottom: 0px !important;
}
.myName{
  font-size: 20px;
  font-family: 'Montserrat';
  letter-spacing: 0px;
  margin: 0px 0px 0px 50px !important;
  width: 60% !important;
}
.myPosition{
  font-size: 12px;
  width: 200px;
  font-family: 'Montserrat';
  letter-spacing: 0px;
   margin: 0px 0px 0px 50px !important;
}

.tab-cols{
  padding-top: 20px;
}
hr{
    margin-top: 3px;
  margin-bottom: 3px;
}

.nav-upper{
  padding: 0px 5px;
  font-size: 12px;
}

.leave-notification-model {
    width: 200px;
    font-size: 14px;
    padding: 10px;
    margin-left: -100px;
  }

  .note-text{
    margin-top: 20px;
  }

}
@media (min-width: 768px) and (max-width: 991.98px) { 
  .filterContainer {
    position: absolute;
    width: 100%;
    height: 83%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 100;
}
  .managerNoImg{
  width: 30px !important;
  height: 30px !important;
  padding: 4px 0 !important;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 12px !important;
  color: white;
  justify-content: center;
  align-items: center;
  text-align: center;
  border: 2px solid #fff;
   letter-spacing: 0px;
} 
  .reportsTo{
  padding-top: 10px !important;
}
  .filterCondition {
    background-color: white;
    height: 100%;
    width: 70% !important;
    border: 1px solid #b2beb5;
    float: right;
}
  
.filterCondition button {
    border: 2px solid #b2beb5;
    padding: 3px;
    margin: 3px;
    border-radius: 5px;
    font-size: 12px;
    min-width: 80px;
    background-color: white;
}

.filterImg{
  top: 45px;
}

.applyFilter {
    border: 2px solid #1B4989 !important;
    background-color: #1B4989 !important;
    color: white;
}

.clearFilter {
    border: 2px solid #dddddd !important;
    background-color: #dddddd !important;
    /* color: white; */
}

.activeFilter {
    background-color:  #b2beb5 !important;
}
  .cameraImg{
  border-radius: 50%;
  position: relative;
  padding: 4px 8px 4px 8px;
  top: 140px;
  left: calc(50% + 4vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 999;
  font-size: 30px !important;

}
.cameraImgNoImg{
  border-radius: 50%;
  position: relative;
  padding: 4px 8px 4px 8px;
  top: 140px;
  left: calc(50% + 4.5vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
  z-index: 1000;
  font-size: 30px !important;
}
  .peopleShortNameMyInfo {
  display: inline-block;
  width: 140px;
  height: 140px;
  top: 40px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 60px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  left: calc(50% + 2.3vh);
  transform: translate(-50%);
  position: absolute;
}
  .profileImg-holder{
  max-height: 22%;
  width: 45%;
  left: calc(2%);
  top: 6%;
  position: absolute;
  z-index: 1;
}
  .tab-cols{
  padding-top: 25px;
}
  .card{
   box-shadow: rgba(0, 0, 0, 0.35) 0px 3px 10px;
   background-color: #D6E0FF !important;
   margin: 0px 10px 20px 10px !important;
}
.profileBg-container{
  padding: 0px !important;
  margin-left: 15px;
}
 .profileImg{
    width: 160px !important;
    height: 160px !important;
    top: 20px !important;
    position: absolute;
    text-align: center;
    border-radius: 50%;
    border-radius: 2px solid #fff;
  }
  .profileBg{
    background-color:  #d05959;
    min-width: 0%;
    height: 104%;
}
}
.direct-report-container {
  max-height: 400px;
  overflow-y: auto;
}
.list-container {
    display: flex;
}
.managerImg{
    width: 3.5vh;
    height: 3.5vh;
    border-radius: 50%;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;
    font-size: 18px;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    border: 2px solid #fff;
}     
.managerNoImg{
  width: 27px;
  height: 27px;
  padding: 2.2px 0;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  font-size: 12px;
  color: white;
  justify-content: center;
  align-items: center;
  text-align: center;
  border: 2px solid #fff;
   letter-spacing: 0px;
}      
.directReportImg {
    width: 27px;
    height: 27px;
    border-radius: 50%;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;
    font-size: 14px;
    color: white;
    justify-content: center;
    align-items: center;
    text-align: center;
    border: 2px solid #fff;
}
.directReportNoImg {
  padding: 3px 0;
  border-radius: 50px;
  background-repeat: no-repeat;
  vertical-align: top;
  background-position: center center;
  background-size: cover;
  font-size: 12px;
  color: white;
  justify-content: center;
  align-items: center;
  text-align: center;
  border: 2px solid #fff;
   letter-spacing: 0px;
   width: 28px;
    height: 28px;
}
.announcement-holder{
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  /* height: ; */
  position: relative;
  margin-top: 20px;
  padding-bottom: 20px;
  margin-bottom: 50px;
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
.leaveText{
  color: #fff;
  height: 50px;
  line-height: 50px;
  font-size: 22px;
  letter-spacing: 0px;
}
.status-holder{
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  height: 200px;;
  position: relative;
  margin-top: 50px;
}
.status{
  background-color: #EE3234;
  position: relative;
  top: 0px;
  color: #fff;
  width: 100%;
  z-index: 1;
}
.leve-expiry-alert {
  display: inline;
  position: absolute;
  top: 52px;
  left: 66px;
  width: 14px;
  height: 14px;
  background: #FFF;
  border-radius: 50%;
  cursor: pointer;
}
.leave-notification-model {
  position: fixed;
  z-index: 1001;
  bottom: 50px;
  left: 50%;
  font-size: 16px;
  min-width: 200px;
  max-width: 400px;
  margin-left: -125px;
  background: rgba(104, 103, 103, 1);
  color: #fff;
  border-radius: 10px;
  text-align: center;
  padding: 16px;
}
.leave-notification-model p {
  margin: 0;
}

/* @media only screen and (max-width: 575.98px) {
  
} */
.leave-expiry-enter-active,
.leave-expiry-leave-active {
  transition: all 0.5s ease-in-out;
}

.leave-expiry-enter-from,
.leave-expiry-leave-to {
  transform: translateY(30px);
  opacity: 0;
}



</style>