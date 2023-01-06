
<template>
  <div class="topBg" ></div>
  <div class="loader" v-if="loading"></div>
  <div class="row  ">
      <div class="co-md-2"></div>
        <div class="d-lg-flex justify-content-around left-side">
         <div class="left-column">
          <div class="leave-container">
                <nav class="tab-bg">
                  <div class="nav nav-tabs " id="nav-tab" role="tablist">
                    <button class="leave-button1 nav-link active font-weight-bold " id="nav-leave-list-tab" data-bs-toggle="tab" data-bs-target="#nav-leave-list" type="button" role="tab" aria-controls="nav-leave-list" aria-selected="true" @click="tabSwitch('leave-list')">Leaves for Approval</button>
                    <button class="leave-button2 nav-link font-weight-bold " id="nav-assignedto-tab" data-bs-toggle="tab" data-bs-target="#nav-assignedto" type="button" role="tab" aria-controls="nav-assignedto" aria-selected="false" @click="tabSwitch('assignedto')">Apply on behalf of</button>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                   <!--Leave List-->
              <div class="tab-pane fade show active " id="nav-leave-list" role="tabpanel" aria-labelledby="nav-leave-list-tab">
                <div class="col-lg-12 leaveModal leave-list mobile-table">
                    <table class="table ">
                      <div class="no-announcement" v-if="this.leaveLength <= 0">
                        <img class="noAnnouncementImg" :src="noHolidayImg" alt="icons" >
                        <div class="mt-2" style="font-size: 20px"> There are no records!</div>
                      </div>
                      
                      <tr style="height: 54px;" v-for="(leaves,index) in pendingData" :key="index">
                        <td style="font-size: 12px;">
                          <p style="margin:0px;" class="mobileview-text">{{leaves.name}}</p>
                          <p style="margin:0px;">{{leaves.leave_type}}</p>
                          <p style="margin:0px;">{{leaves.start_date}} - {{leaves.end_date}}</p>
                          <p style="margin:0px;">{{leaves.num_of_days}} day(s)</p>
                          <p style="margin:0px;">{{leaves.duration_type}}</p>
                          <p style="margin:0px;text-transform: capitalize;">{{leaves.status}}</p>
                        </td>
                        <td>
                          <div class="" v-if="leaves.status == 'pending'">
                            <a style="text-decoration: underline;cursor: pointer" @click="approveLeave(leaves)"><span title="Approve" style="font-size: 30px !important;color:orange;" class="mdi  " :class="'mdi-checkbox-outline mr-3'"></span></a>
                            <a style="text-decoration: underline;cursor: pointer" @click="rejectLeave(leaves)"><span title="Reject" style="font-size: 30px !important;color:red;" class="mdi  " :class="'mdi-close-box-outline'"></span></a>
                          </div>
                        </td>
                      </tr>
                      
                      <tr  style="height: 54px;" v-for="(leaves,index) in approvedData" :key="index">
                        <td style="font-size: 12px;">
                          <p style="margin:0px;" class="mobileview-text">{{leaves.name}}</p>
                          <p style="margin:0px;">{{leaves.leave_type}}</p>
                          <p style="margin:0px;">{{leaves.start_date}} - {{leaves.end_date}}</p>
                          <p style="margin:0px;">{{leaves.num_of_days}} day(s)</p>
                          <p style="margin:0px;">{{leaves.duration_type}}</p>
                          <p style="margin:0px;text-transform: capitalize;">{{leaves.status}}</p>
                        </td>
                        <td>
                          <button
                                v-if="leaves.status == 'pending'"
                                class="btn btn  approve px-2"
                                style="display: inline-flex"
                                @click="approveLeave(leaves)"
                                >
                                Approve
                               </button>
                
                            <button
                              v-if="leaves.status == 'pending'"
                              class="btn btn  rejected px-2 m-2"
                              style="display: inline-flex"
                              @click="rejectLeave(leaves)"
                            >
                              Reject
                            </button>

                            <span v-else-if="leaves.status == 'Approved'"
                              class="px-2 m-2 text-center mdi mdi-check-circle "
                              style="font-size: 30px;color: green;">
                            </span>

                            <span v-else-if="leaves.status == 'Rejected'"
                              class="px-2 m-2 text-center mdi mdi-check-circle"
                              style="font-size: 30px;color: green;">
                            </span>

                            <span v-else-if="leaves.status == 'Cancelled'"
                              class="px-2 m-2 text-center mdi mdi-cancel"
                              style="font-size: 30px;color: red;">
                            </span>
                        </td> 
                      </tr>
                  </table>
                </div>


             <!-- tab view start-->

               <div class="col-lg-12 leaveModal leave-list tab-table">
                    <table class="table ">
                     <div class="no-announcement" v-if="this.leaveLength <= 0">
                        <img class="noAnnouncementImg" :src="noHolidayImg" alt="icons" >
                        <div class="mt-2" style="font-size: 28px" > There are no records!</div>
                      </div>
                       
                      <tr style="height: 54px;" v-for="(leaves,index) in pendingData" :key="index">
                        <td style="font-size: 22px;">
                          <p style=" margin:0px;" class="tabview-text">{{leaves.name}}</p>
                          <p style="margin:0px;">{{leaves.leave_type}}</p>
                          <p style="margin:0px; ">{{leaves.start_date}} - {{leaves.end_date}}</p>
                          <p style="margin:0px; ">{{leaves.num_of_days}} day(s)</p>
                          <p style="margin:0px; ">{{leaves.duration_type}}</p>
                          <p style="margin:0px; text-transform: capitalize;">{{leaves.status}}</p>
                        </td>
                        <td>
                          <div class="" v-if="leaves.status == 'pending'">
                            <a style="text-decoration: underline;cursor: pointer" @click="approveLeave(leaves)"><span title="Approve" style="font-size: 45px !important;color:orange;" class="mdi  " :class="'mdi-checkbox-outline mr-3'"></span></a>
                            <a style="text-decoration: underline;cursor: pointer" @click="rejectLeave(leaves)"><span title="Reject" style="font-size: 45px !important;color:red;" class="mdi  " :class="'mdi-close-box-outline'"></span></a>
                          </div>
                        </td>
                      </tr>
                      
                      <tr  style="height: 54px;" v-for="(leaves,index) in approvedData" :key="index">
                        <td style="font-size: 22px;">
                          <p style=" margin:0px;" class="tabview-text">{{leaves.name}}</p>
                          <p style="margin:0px;">{{leaves.leave_type}}</p>
                          <p style="margin:0px;">{{leaves.start_date}} - {{leaves.end_date}}</p>
                          <p style="margin:0px;">{{leaves.num_of_days}} day(s)</p>
                          <p style="margin:0px;">{{leaves.duration_type}}</p>
                          <p style="margin:0px;text-transform: capitalize;">{{leaves.status}}</p>
                        </td>
                        <td>
                          <button
                                v-if="leaves.status == 'pending'"
                                class="btn btn  approve px-2"
                                style="display: inline-flex"
                                @click="approveLeave(leaves)"
                                >
                                Approve
                               </button>
                
                            <button
                              v-if="leaves.status == 'pending'"
                              class="btn btn  rejected px-2 m-2"
                              style="display: inline-flex"
                              @click="rejectLeave(leaves)"
                            >
                              Reject
                            </button>

                            <span v-else-if="leaves.status == 'Approved'"
                              class="px-2 m-2 text-center mdi mdi-check-circle "
                              style="font-size: 40px;color: green;">
                            </span>

                            <span v-else-if="leaves.status == 'Rejected'"
                              class="px-2 m-2 text-center mdi mdi-check-circle"
                              style="font-size: 40px;color: green;">
                            </span>

                            <span v-else-if="leaves.status == 'Cancelled'"
                              class="px-2 m-2 text-center mdi mdi-cancel"
                              style="font-size: 40px;color: red;">
                            </span>
                        </td> 
                      </tr>
                  </table>
                </div>
<!-- tab view ends-->

                  <div class="col-lg-12 leaveModal leave-list desktop-table">
                  <table class="table " >
                     <div class="no-announcement" v-if="this.leaveLength <= 0">
                        <img class="noAnnouncementImg" :src="noHolidayImg" alt="icons" >
                        <div class="mt-2" style="font-size: 22px" > There are no records!</div>
                      </div>
                    <thead>
                        <tr class="text-center">
                            <th scope="col"></th>
                            <th scope="col">Name</th>
                            <th scope="col" >Type</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Days</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Status</th>
                            <th scope="col">Attachment</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                      
                    <tr style="height: 54px;" v-for="(leaves,index) in pendingData" :key="index">
                        <td style="padding: 2px; width: 4rem;">
                          <span class="mdi mdi-calendar pl-2 text-center" style="font-size: 28px;color: black"></span>
                        </td>
                        <td style="padding: 0px">  
                            <div class="date text-center">{{leaves.name}}</div>
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
                        <td style="padding: 0px">  
                            <div class="date text-center">{{leaves.duration_type}}</div>
                        </td>
                        <td style="padding: 0px;text-transform: capitalize;">  
                          <div class="date text-center" >{{leaves.status}}</div>
                        </td>
                        <td style="padding: 0px;">  
                          <div class="date text-center" v-if="(leaves.medical_certificate != null)">
                            <a style="text-decoration: underline;cursor: pointer" @click="downloadAttachment(leaves.medical_certificate)"><span style="font-size: 25px !important" class="mdi" :class="'mdi-paperclip'"></span></a>
                          </div>
                        </td>
                        <td style="padding: 0px;  width:150px;">  
                          <div class="text-center" v-if="leaves.status == 'pending'">
                            <a style="text-decoration: underline;cursor: pointer ml-2" @click="approveLeave(leaves)"><span title="Approve" style="font-size: 30px !important;color:orange;" class="mdi  " :class="'mdi-checkbox-outline mr-3'"></span></a>

                            <a style="text-decoration: underline;cursor: pointer" @click="rejectLeave(leaves)"><span title="Reject" style="font-size: 30px !important;color:red;" class="mdi  " :class="'mdi-close-box-outline'"></span></a>
                          </div>
                        <button v-else-if="leaves.status == 'Approved'"
                          class="btn btn text-center approved px-2 m-2"
                          style="display: inline-flex">
                          Approved
                        </button>
                        <button v-else-if="leaves.status == 'Rejected'"
                          class="btn btn text-center rejected px-2 m-2"
                          style="display: inline-flex">
                          Rejected
                        </button>
                        </td>
                    </tr>
                    <tr  style="height: 54px;" v-for="(leaves,index) in approvedData" :key="index">
                        <td style="padding: 2px; width: 4rem;">
                          <span class="mdi mdi-calendar pl-2 text-center" style="font-size: 28px;color: black"></span>
                        </td>
                        <td style="padding: 0px">  
                            <div class="date text-center">{{leaves.name}}</div>
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
                        <td style="padding: 0px">  
                            <div class="date text-center">{{leaves.duration_type}}</div>
                        </td>
                        <td style="padding: 0px;text-transform: capitalize;">  
                          <div class="date text-center" >{{leaves.status}}</div>
                        </td>
                        <td style="padding: 0px;">  
                          <div class="date text-center" v-if="(leaves.medical_certificate != null)">
                            <a style="text-decoration: underline;cursor: pointer" @click="downloadAttachment(leaves.medical_certificate)"><span style="font-size: 25px !important" class="mdi" :class="'mdi-paperclip'"></span></a>
                          </div>
                      </td>
                        <td style="padding: 0px; text-align: center !important;">  
                          <button
                            v-if="leaves.status == 'pending'"
                            class="btn btn text-center approve px-2"
                            style="display: inline-flex"
                            @click="approveLeave(leaves)"
                            >
                            Approve
                          </button>
                        <button
                          v-if="leaves.status == 'pending'"
                          class="btn btn text-center rejected px-2 m-2"
                          style="display: inline-flex"
                          @click="rejectLeave(leaves)"
                        >
                          Reject
                        </button>
                        <span v-else-if="leaves.status == 'Approved'"
                          class="px-2 m-2 text-center mdi mdi-check-circle"
                          style="font-size: 30px;color: green;">
                        </span>
                        <span v-else-if="leaves.status == 'Rejected'"
                          class="px-2 m-2 text-center mdi mdi-check-circle"
                          style="font-size: 30px;color: green;">
                        </span>
                        <span v-else-if="leaves.status == 'Cancelled'"
                          class="px-2 m-2 text-center mdi mdi-cancel"
                          style="font-size: 30px;color: red;">
                        </span>
                        </td>
                    </tr>
                    </table>
              </div>
            </div>  
           <!--leave List ends-->

             <!--Leave assigned to -->
              <div class="tab-pane fade" id="nav-assignedto" role="tabpanel" aria-labelledby="nav-assignedto-tab">
                 <div class="leave-holder">
                    <div class="row ml-2 d-flex">

                    <!--desktop view -->
                      <div class="col-6 mt-4 big-screen" style="padding-left: 10px;">
                          <div class="manager-name" style="display: inline">On behalf of <span style="font-size: 25px; color: red">*</span> &nbsp; : &nbsp;</div>
                          <select class="selected-leave" v-model="reportees" @change="getReporteesRegion($event)">
                            <option value="" disabled selected>Select your option</option>
                            <option  v-for="(reportees,index) in directreportees"  :key="index">
                             <span>{{reportees.empid}} - {{reportees.name}}  </span>
                            </option>
                          </select>
                          
                          <div class="type-of-leave">Type of leave<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                          <select class="selected-leave" @change="getLeaveIdByType(appliedLeaveStatus.leaveCat)" v-model="appliedLeaveStatus.leaveCat">
                             <option value="" disabled selected>Select your option</option>
                             <option  v-for="(leavetype,index) in filterLeavesByRegion()"  :key="index">
                             <span>{{leavetype.name}}</span>
                             <span v-if="leavetype.balance != '' && leavetype.balance != null"> - {{leavetype.balance}}</span>
                            </option>
                          </select>
                          
                          
                            <div class=" leave-reason">Comments</div>
                            <textarea rows="3" maxlength="100" v-model="this.appliedLeaveStatus.reason" class="form-control reason" cols="0" > </textarea>

                            <div class="choose-file pt-1">Please choose file
                              <button @click="$refs.file.click()" class="" style="margin-left: 20px;">choose file</button>
                            </div>
                            <span class="attach-files-text">{{this.medicalAttachment.name}}</span>
                            <div class="mt-1" style="color:red">Note: For more than one file do zip and upload</div>
                            <input type="file" id="file" ref="file" class="mt-2 pt-2" @change="uploadMedicalAttachment($event)" accept=".zip,.rar,.7zip,application/pdf,application/msword,image/x-png,image/jpeg" style="display: none">

                      </div>


                      <div class="col-6 mt-4 big-screen">  
                        <div class="type-of-leave" style="; margin-top:0px" >Duration<span style="font-size: 25px; color: red; font-weight: bold" >*</span></div>
                          <div @change="setSelectDateMin()">
                            <label for="id1" v-if="leavename != 'Optional Leave'" class="ml-1"><input type="radio" v-model="appliedLeaveStatus.duration" name="option-desktop" id="id1" class="" value="First Half">First Half</label>
                            <label for="id2" v-if="leavename != 'Optional Leave'" class="ml-1"><input type="radio" v-model="appliedLeaveStatus.duration" name="option-desktop" id="id2"  class="" value="Second Half">Second Half</label>
                            <label for="id3" class="ml-1"><input type="radio" checked  v-model="appliedLeaveStatus.duration"  name="option-desktop" id="id3"  class="" value="Full Day">Full Day</label>
                          </div>
                          <div v-if="(this.leavename != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day')">
                            <div class="from-date" >From<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                            <input
                              type="date"
                              class="form-control from"
                              v-model="this.appliedLeaveStatus.from "
                              id="from"
                              autocomplete="off"
                              @change="getStartDate()"
                              required
                              />
                            <div class="to-date">To<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                            <input
                              type="date"
                              class="form-control to"
                              min= ""
                              v-model="this.appliedLeaveStatus.to"
                              id="to"
                              autocomplete="off"
                              />
                          </div>
                          <div v-if="(this.leavename != 'Optional Leave' && appliedLeaveStatus.duration != 'Full Day')">
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
                       <!--optional leave -->
                       <div>
                          <div v-if="leavename == 'Optional Leave' && leavebal >= 1" class="type-of-leave">Optional Leave List<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                            <select class="optional-selected-leave" v-if="leavename == 'Optional Leave' && leavebal >= 1"  v-model="selectedOptionalLeaveDate" >
                              <option value="" disabled selected>Select your option</option>
                              <option  v-for="(leavetype,index) in optionalLeaveList" :key="index">
                              <span>{{leavetype.holidayname}} - ({{dateFormatter(leavetype.holidaydate)}})</span>  
                              </option>
                            </select>
                       </div>     
                          <!--optional leave ends-->
                            <button class="leave-apply"  @click="submit()">Apply</button>
                            <button class="leave-cancel" @click="clearLeaveData()">Clear</button> 
                      </div>
                      <!--desktop view ends-->


                      <!--mobile view start-->

                      <div class="col-12 mobile-screen">
                          <div class="manager-name" style="display: inline">On behalf of <span style="font-size: 25px; color: red">*</span> &nbsp; : &nbsp;</div>
                          <select class="selected-leave" v-model="reportees" @change="getReporteesRegion($event)">
                            <option value="" disabled selected>Select your option</option>
                            <option  v-for="(reportees,index) in directreportees"  :key="index">
                             <span>{{reportees.empid}} - {{reportees.name}}  </span>
                            </option>
                          </select>

                          <div class="type-of-leave">Type of leave<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                          <select class="selected-leave" @change="getLeaveIdByType(appliedLeaveStatus.leaveCat)" v-model="appliedLeaveStatus.leaveCat">
                             <option value="" disabled selected>Select your option</option>
                             <option  v-for="(leavetype,index) in filterLeavesByRegion()"  :key="index">
                             <span>{{leavetype.name}}</span>
                             <span v-if="leavetype.balance != '' && leavetype.balance != null"> - {{leavetype.balance}}</span>
                            </option>
                          </select>



                          <div v-if="(this.leavename != 'Optional Leave' && appliedLeaveStatus.duration == 'Full Day')">
                            <div class="from-date" >From<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                            <input
                              type="date"
                              class="form-control from"
                              v-model="this.appliedLeaveStatus.from "
                              id="from"
                              autocomplete="off"
                               @change="getStartDate()"
                              required
                              />
                            <div class="to-date">To<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                            <input
                              type="date"
                              class="form-control to"
                              min= ""
                              v-model="this.appliedLeaveStatus.to"
                              id="to"
                              autocomplete="off"
                              />
                          </div>

                          <div v-if="(this.leavename != 'Optional Leave' && appliedLeaveStatus.duration != 'Full Day')">
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
                          

                           <!--optional leave -->
                          <div>
                              <div v-if="leavename == 'Optional Leave' && leavebal >= 1" class="type-of-leave">Optional Leave List<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                                <select class="optional-selected-leave" v-if="leavename == 'Optional Leave' && leavebal >= 1"  v-model="selectedOptionalLeaveDate" >
                                  <option value="" disabled selected>Select your option</option>
                                  <option  v-for="(leavetype,index) in optionalLeaveList" :key="index">
                                  <span>{{leavetype.holidayname}} - ({{dateFormatter(leavetype.holidaydate)}})</span>  
                                  </option>
                                </select>
                          </div>     

                          <div class="type-of-leave">Duration<span style="font-size: 25px; color: red; font-weight: bold">*</span></div>
                          <div @change="setSelectDateMin()">
                            <label for="id1" v-if="leavename != 'Optional Leave'" class="ml-1"><input type="radio" v-model="appliedLeaveStatus.duration" name="option" id="id1" class="" value="First Half">First Half</label>
                            <label for="id2" v-if="leavename != 'Optional Leave'" class="ml-1"><input type="radio" v-model="appliedLeaveStatus.duration" name="option" id="id2"  class="" value="Second Half">Second Half</label>
                            <br class="line-break" style="display: block">
                            <label for="id3" class="ml-1"><input type="radio" checked v-model="appliedLeaveStatus.duration"  name="option" id="id3"  class="" value="Full Day">Full Day</label>
                          </div>


                          <div class=" leave-reason">Comments</div>
                          <textarea rows="3" maxlength="100" v-model="this.appliedLeaveStatus.reason" class="form-control reason" cols="0" > </textarea>  
                      

                          <div class="choose-file pt-1">Please choose file</div>
                          <button @click="$refs.file.click()" class="choose-file-text" >choose file</button>  
                          <span class="attach-file-text">{{this.medicalAttachment.name}}</span>
                          <div class=" note-text" style="color:red">Note: For more than one file do zip and upload</div>
                          <input type="file" id="file" ref="file" class="mt-2 pt-2" @change="uploadMedicalAttachment($event)" accept=".zip,.rar,.7zip,application/pdf,application/msword,image/x-png,image/jpeg" style="display: none">


                          <div class="">
                            <button class="leave-apply"  @click="submit()">Apply</button>
                            <button class="leave-cancel" @click="clearLeaveData()">Clear</button> 
                           </div>

                      </div>
                      <!--mobile view ends-->

                    </div>
                  </div>
                </div>               

            </div> 
           <!--Leave assigned to ends -->
          </div> 
           </div>
        </div>
    </div>
    <div class="co-md-2"></div>

</template>

<script>
import $ from 'jquery';
import { decryptData } from '../js/utils/encrypt';
import axios from 'axios';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import noHoliday from '../assets/noHoliday.png';


export default {
components: {

  },
  
  data(){
      return{      
       loading: false,    
       noHolidayImg: noHoliday,
         employeeId: '',
         whoAppliedLeaveData: '',
         selectedEmpRegion: '',
          appliedLeaveData: [
              {leaveType: 'Annual Leave', region: ['All']},
              {leaveType: 'Sick Leave', region: ['All']},
              {leaveType: 'Optional Leave', region: ['IN']},
              {leaveType: 'Unpaid Leave', region: ['All']},
            ],
            reportees: '',
            appliedLeaveStatus: {
              leaveCat: '',
              from: '',
              reason: '',
              duration: 'Full Day',
              to: '',
              status: 'Approved',
              empId: this.reporteesId,
              leaveid: '',
              autoApproved: true
            },
            reporteesId :'',
            reporteeIndex: '',
            directreportees: [],
            reporteesLeaveBal : [],
            todaysDate: '',
            formSubmitMsg: false,
            pendingData: [],
            approvedData: [],
            balance: true,
            isApplyLeaveBtnDisabled:false,
            isRadioBtnDisabled: false,
            isMedicalCertificateAttached: false,
            medicalAttachment: '',
            optionalLeaveList: [],
            selectedOptionalLeaveDate: '',
            leavename: '',
            leavebal: '',
            allLeaveList: [],
            leaveLength: ''
      };
  },
  mounted(){
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
     if(this.leavename != 'Optional Leave' && this.appliedLeaveStatus.duration == 'Full Day'){
        $('.to').attr({'min': this.appliedLeaveStatus.from})
        $('.from').attr({'min': this.appliedLeaveStatus.from})
      }
    const secretKey = process.env.KEY || 'myscecretkey';
    var encryptedEmployeeID = localStorage.getItem('EmployeeID');
    this.employeeId = decryptData(encryptedEmployeeID, secretKey).replace(/['"]+/g, '');
    this.mountedFunction();
},
methods: {
  setSelectDateMin(){
    if(this.appliedLeaveStatus.duration != 'Full Day'){
        $('.select-date').attr({'min': this.appliedLeaveStatus.from})
      } else {
         $('.to').attr({'min': this.appliedLeaveStatus.from})
        $('.from').attr({'min': this.appliedLeaveStatus.from})
      }
  },
  getStartDate(){
    $('.to').attr({'min': this.appliedLeaveStatus.from});
    let fromdt =  new Date(this.appliedLeaveStatus.from);
    let todt =  new Date(this.appliedLeaveStatus.to);
    if(fromdt > todt) {
      this.appliedLeaveStatus.to = this.appliedLeaveStatus.from
    }
    
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
 async submit(){
    let id = '';
    this.balance = true;
    this.appliedLeaveStatus.empId = this.reportees.split('-')[0];
    var leavename = this.appliedLeaveStatus.leaveCat.split('-')[0];
      if(this.appliedLeaveStatus.leaveCat == ''){
          Swal.fire({
                text: 'Please select leave type',
                confirmButtonText: 'Ok',
              });
              this.loading = false;
              return;
      } else if(leavename.trim() == 'Sick Leave' && this.appliedLeaveStatus.reason == ''){
          Swal.fire({
                text: 'Please enter comments',
                confirmButtonText: 'Ok',
              });
              this.loading = false;
              return;
      } else if(leavename.trim() == 'Optional Leave' &&  this.selectedOptionalLeaveDate == ''){
          Swal.fire({
                text: 'Please select Optional leave',
                confirmButtonText: 'Ok',
              });
              this.loading = false;
              return;
      }
      else if(this.medicalAttachment == ''){
          Swal.fire({
                text: 'Please choose file',
                confirmButtonText: 'Ok',
              });
              this.loading = false;
              return;
      }
      this.balance = true;
      this.appliedLeaveStatus.leaveCat = leavename.trim(' '); 
      if(this.appliedLeaveStatus.leaveCat == 'Optional Leave'){
        let optionalLeaveDate = this.selectedOptionalLeaveDate.split('- (')[1].replace(')', '');
        let date = optionalLeaveDate.split('-')[0];
        let month = optionalLeaveDate.split('-')[1];
        let year = optionalLeaveDate.split('-')[2];
        let finalDate = year + '-' + month + '-' + date;
        this.appliedLeaveStatus.to = finalDate;
        this.appliedLeaveStatus.from = finalDate;
      }
      this.loading = true;
      await axios.post(this.$hostName + '/leaveData', this.appliedLeaveStatus)
          .then((response) => {
            if(response.status == 200){
              Swal.fire({
                text: 'Leave request submitted successfully.',
                confirmButtonText: 'Ok',
              });
            }
            else if(response.status == 203) {
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
              formData.append('empId', this.appliedLeaveStatus.empId);
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
              this.clearLeaveData()
              this.mountedFunction();
              this.tabSwitch('leave-list');
  },
    clearLeaveData(){
      this.appliedLeaveStatus.leaveCat = '';
      this.appliedLeaveStatus.reason = '';
      this.appliedLeaveStatus.duration = 'Full Day';
      this.editleave = false;
      this.selectedOptionalLeaveDate = '';
       this.medicalAttachment = '';
       this.reportees = '';
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
     setTimeout(() =>{
        if(this.leavename != 'Optional Leave' && this.appliedLeaveStatus.duration == 'Full Day'){
           $('.to').attr({'min': this.appliedLeaveStatus.from})
            $('.from').attr({'min': this.appliedLeaveStatus.from})
        }
      }, 100)
    },
 async mountedFunction(){
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
        setTimeout(() =>{
          if(this.leavename != 'Optional Leave' && this.appliedLeaveStatus.duration == 'Full Day'){
             $('.to').attr({'min': this.appliedLeaveStatus.from})
            $('.from').attr({'min': this.appliedLeaveStatus.from})
          }
        },100)
        this.medicalAttachment = '';
        this.appliedLeaveStatus.leaveCat = '';
        this.appliedLeaveStatus.reason = '';
        this.medicalAttachment = '';
        this.selectedOptionalLeaveDate = '';
        this.appliedLeaveStatus.duration = 'Full Day';
        this.leavename = '';

    this.loading = true;
    this.pendingData = [];
     this.approvedData = [];
      axios.get(this.$hostName + '/getOptionalLeaves' ).then((response)=>{
          this.optionalLeaveList = response.data[0].optionalLeaveData;
        });
   await axios.post(this.$hostName + '/getEmployeeReportingManager', {empid: this.employeeId}).then((response) =>{
     this.allLeaveList = response.data;
     this.leaveLength = Object.keys(this.allLeaveList).length
      for(let i=0; i<response.data.length; i++){
        if(response.data[i].status == 'pending'){
          this.pendingData.push(response.data[i]);
        } else if(response.data[i].status == 'Approved' || response.data[i].status == 'Rejected' || response.data[i].status == 'Cancelled'){
          this.approvedData.push(response.data[i]);
        }
      }
      let tempLeavelist = response.data.filter( element => {
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
    }).catch((error) => {
      console.log(error);
    })
    .finally(() => {
    });
  await axios.post(this.$hostName + '/getManagerDirectReportees', {empid: this.employeeId})
          .then((response) => {
            this.directreportees = response.data[1].directReportsData;
            this.reporteesLeaveBal = response.data[2];
            }).catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.loading = false; 
            });
              this.reportees = '';
             
  },
  async approveLeave(data) {
    let executionFlag = false;  
    let comments = ''; 
    await Swal.fire({
      html: '<b><span stye="font-size:20px;text-left;">comments:</span></b>',
      input: 'textarea',
      confirmButtonText: 'Approve',
      confirmButtonColor: 'orange',
      showCloseButton: true,
      focusConfirm: false,
    }).then(function(input){
      if(input.value == '' || input.value) {
        comments = input.value;
        executionFlag = true;
      } else {
        return false;
      }
    });
  if (executionFlag) {
    let leaveDetails = {
      empid: data.empid,
      leaveId: data.leave_id,
      totalDays: data.num_of_days,
      leaveType: data.leave_type,
      status: 'Approved',
      comments: comments,
      updatedAt: data.updated_at
    };
    this.loading = true;
    axios
      .put(this.$hostName + `/approveAppliedLeave/${data.id}`, {
        leaveDetails: leaveDetails,
      })
      .then((response) => {
        if (response.status == 200) {
          this.mountedFunction();
          Swal.fire('Approved', '', 'success');
        } else if (response.status == 203){
          Swal.fire({
                text: response.data, 
                confirmButtonText: 'Ok',
              });
              return; 
        } else {
          Swal.fire({
                text: 'Something went wrong. Please try again later.', 
                confirmButtonText: 'Ok',
              });
              return; 
        }
      })
      .catch((error) => {
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
    }
  },
  async rejectLeave(data) {
    const { value: textarea } = await Swal.fire({
      html: '<b><span stye="font-size:20px;">comments:</span></b>',
      input: 'textarea',
      confirmButtonText: 'Reject',
      cancelButtonText: 'Cancel',
      confirmButtonColor: 'red',
      showCloseButton: true,
      focusConfirm: false,
      onOpen: function () {
        Swal.disableConfirmButton();
        Swal.getInput().addEventListener('keyup', function(e) {
        if(e.target.value === '') {
          Swal.disableConfirmButton();
        } else {
          Swal.enableConfirmButton();
        }
      });
    }  
    });
    if (textarea) {
      this.loading = true;
      let leaveDetails = {
        empid: data.empid,
        leaveId: data.leave_id,
        totalDays: data.num_of_days,
        leaveType: data.leave_type,
        status: 'Rejected',
        comments: textarea,
        updatedAt: data.updated_at
      };
      axios
        .put(this.$hostName + `/approveAppliedLeave/${data.id}`, {
          leaveDetails: leaveDetails,
        })
        .then((response) => {
          if (response.status == 200 || response.status == 'ok' ) {
            this.mountedFunction();
            Swal.fire('Rejected', '', 'success');
          } else if (response.status == 203){
          Swal.fire({
                text: response.data, 
                confirmButtonText: 'Ok',
              });
              return; 
        } else {
            Swal.fire({
                  text: 'Something went wrong. Please try again later.', 
                  confirmButtonText: 'Ok',
                });
                return; 
          }
        })
        .catch((error) => {
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
    }
  },
  getReporteesRegion(event){
    this.selectedEmpRegion = event.target.value.substr(0,2);
    this.reporteeIndex = event.target.selectedIndex - 1;
  },
  getLeaveIdByType(type){
    this.leavename = type.split('-')[0].trim();
    this.leavebal = type.split('-')[1];
    axios.post(this.$hostName + '/getleaveid', {
      leaveType: this.leavename+ ' ' + 'Balance'
    }).then((res) =>{
    this.appliedLeaveStatus.leaveid = res.data.empLeaveData.id;
    });
  },
  filterLeavesByRegion(){
    let filteredLeaveType = [];
    let temp = [];
      for(let i=0; i<this.appliedLeaveData.length; i++){
        for(let j=0; j<this.appliedLeaveData[i].region.length; j++){
          if(this.reporteesLeaveBal[this.reporteeIndex] != undefined){
            if(this.selectedEmpRegion == this.appliedLeaveData[i].region[j] || this.appliedLeaveData[i].region[j] == 'All'){
              temp.push(this.getBalanceByLeaveType(this.reporteesLeaveBal[this.reporteeIndex].getReporteesLeaveBal, this.appliedLeaveData[i].leaveType));
              filteredLeaveType.push(this.appliedLeaveData[i].leaveType);
            }
          }
        }
      }
      let finalFilteredData = [];
      for(let k=0; k<filteredLeaveType.length; k++){
          let json = {
            name: '',
            balance: ''
          };
        json.name = filteredLeaveType[k];
        json.balance = temp[k];
        finalFilteredData.push(json);
      }
      return finalFilteredData;
  },
  getBalanceByLeaveType(data, name) {
    let leaveArr = {};

    for(let leave in data){
      leaveArr[data[leave].name] = data[leave].balance;
    }

    return leaveArr[name+' Balance'];
  },
    tabSwitch(type){
      var tab1 = document.getElementById('nav-leave-list');
      var btnTab1 = document.getElementById('nav-leave-list-tab');
      var tab2 = document.getElementById('nav-assignedto');
      var btnTab2 = document.getElementById('nav-assignedto-tab');
      if(type == 'leave-list'){
        tab2.classList.remove('show');
        tab2.classList.remove('active');
        btnTab2.classList.remove('active');
        tab1.classList.add('show');
        tab1.classList.add('active');
        btnTab1.classList.add('active');
      } 
      else if(type == 'assignedto'){
        tab1.classList.remove('show');
        tab1.classList.remove('active');
        btnTab1.classList.remove('active');
        tab2.classList.add('show');
        tab2.classList.add('active');
        btnTab2.classList.add('active');
      } 
    },
    downloadAttachment(blobData){
    let type = blobData.split('/')[1];
    let fileTyepe = type.split(';')[0];
    var a = document.createElement('a');
    a.href = blobData;
    a.download = 'attachment.' + fileTyepe; 
    a.click();
  }
    
  },
  

};
</script>
<style scoped>
.leave-apply{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  top: 360px;
  left: 30px;
  background-color: red;
  width: 120px;
  height: 36px;
  border: none;
  position: absolute;
}
.leave-cancel{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  top: 360px;
  left: 180px;
  position: absolute;
  background-color: grey;
  width: 120px;
  height: 36px;
  border: none;

}
/*mobile view start */
@media only screen and (max-width: 575.98px) {
.choose-file{
  margin-top: 20px !important;
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0px;
} 
.no-announcement{
  text-align: center;
  position: absolute;
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%,-50%) !important;
}
  .approve{
    margin-right:10px !important;
  }

.desktop-table{
  display: none !important;
  width: 100%;
}

.mobile-table{
  display: block !important;
}

.tab-table{
   display: none !important;
  width: 100%;
}

.leave-holder{
box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
height: 520px !important;
position: relative;
z-index: 20;
overflow: auto;
}

.left-side{
font-family: 'Montserrat';
position: fixed !important;
margin-bottom: 50px;
left: 0% !important;
padding: calc(25vh - 60px) 20px 30px 20px;
height: 100%;
width: calc(100%  ) !important;
}

.left-column{
font-family: 'Montserrat';
position: fixed;
left: 0px !important;
width: calc(100% - 80px) !important;
}


.leave-button1{
padding-left: 2 px;
padding-right: 4px;
font-size: 12px;
}

.leave-button2{
  padding-left: 4px;
  padding-right: 2px;
  font-size: 12px;
}

.big-screen{
display: none !important;
}

.mobile-screen{
display: initial !important;
}

.tab-screen{
display: none !important;

}

.textArea{
width: 90%;
}

.from{
width:90% !important;
}

.to{
width:90% !important;
}

.leave-cancel{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  margin-top: 10px !important;
  margin-left: 10px !important;
  background-color: grey;
  width: 100px !important;
  height: 36px;
  border: none;
  position: static !important;
}

.leave-apply{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  top: 360px;
  left: 30px;
  background-color: red;
  width: 100px !important;
  height: 36px;
  border: none;
  position: static !important;
}


input[type='radio'] {
    -webkit-appearance: none;
    border-radius: 50%;
    outline: none;
    box-shadow: 0 0 0 1px red;
    height: 20px;
    width: 20px;
    margin-top: 5px;
    margin-right: 5px !important;
    margin-left: 5px !important;
}

.leave-container[data-v-72a2780d] {
  position: relative;
  top: -43px;
  z-index: 20;
}

.leave-list{
box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
overflow : auto;
position: relative;
height: 500px !important;

}

.choose-file-text{
margin-left: 0px !important;
}

.attach-file-text{
position: relative;
top: 30px;
left: -100px;
}

.note-text{
margin-top: 30px;
}
.mobileview-text{
  font-size: 18px;
}
}
/*mobile view end*/


/* Tab view start */

@media (min-width: 768px) and (max-width: 991.98px) {
.no-announcement{
    text-align: center;
    position: absolute;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%,-50%) !important;
}
.approved{
font-size: 40px !important;
}
.rejected{

font-size: 40px !important;

}
.leave-apply{
  color: #fff;
  font-size: 22px;
  letter-spacing: 0px;
  font-family: 'Montserrat';
  top: 370px !important;
  left: 30px;
  background-color: red;
  width: 100px !important;
  height: 36px;
  border: none;
  position: absolute !important;
}

.leave-holder{
  height: 510px !important;
}
.leave-container{
  position: relative;
  top: -50px;
  z-index: 20;
  left: 17px !important;
  width: calc(100% - 30px) !important;
}

.left-column{
  font-family: 'Montserrat';
  position: absolute;
  left: 37px !important;
  margin: 8px 15px 0px 15px;
}

.left-side{
  font-family: 'Montserrat';
  position: absolute;
  margin-bottom: 50px;
  left: 10px !important;
  padding: calc(25vh - 60px) 20px 30px 20px;
  margin-bottom: 50px;
  top: 60px;
  height: 100%;
  width: calc(100% - 10px) !important;
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.from{
width:90% !important;
}

.to{
width: 90% !important;

}

.textArea{
width: 90% !important
}

.leave-cancel{
  margin-top: 10px;
  margin-left: 8px !important;
  width: 100px !important;
  height: 36px;
}

input[type='radio'] {
    -webkit-appearance: none;
    border-radius: 50%;
    outline: none;
    box-shadow: 0 0 0 1px red;
    height: 20px;
    width: 20px;
    margin-top: 5px;
    margin-right: 10px !important;
    margin-left: 20px !important;
}

.choose-file{
  margin-top: 35px !important;
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0px;
} 

.desktop-table{
  display: none !important;
  width: 100%;
}

.mobile-table{
  display: none !important;
  width: 100%;
}

.tab-table{
   display: block !important;
}
.tabview-text{
  font-size: 25px !important;
}


}
/*Tab view ends*/

.mobile-table{
  display: none;
  width: 100%;

}


.tab-table{
  display: none ;
  width: 100%;
}

.mobile-screen{
  display: none;
}

.approve {
background-color: orange;
-webkit-background-clip: border-box;
background-clip: border-box;
text-align: center;
font-weight: bold;
color: #fff;
}
.type-of-leave{
color: #000;
font-size: 20px;
letter-spacing: 0px;
font-family: 'Montserrat';
margin-top: 28px;
}
.manager-name{
color: #000;
font-size: 28px;
letter-spacing: 0px;
font-family: 'Montserrat';
font-weight: bold;
}
.approved{
background-color: green;
-webkit-background-clip: border-box;
background-clip: border-box;
text-align: center;
font-weight: bold;
color: #fff;
}
.rejected{
background-color: red;
-webkit-background-clip: border-box;
background-clip: border-box;
text-align: center;
font-weight: bold;
color: #fff;
}
.leaveModal{
padding: 0px;
}
.upcomingModal{
padding: 0px;
}
.date{
margin-top: 10px;
font-size: 16px;
font-weight: bold;
letter-spacing: 0px;
} 
.leave-duration{
color: #000;
font-size: 18px;
letter-spacing: 0px;
font-family: 'Montserrat';
margin-top: 40px;
}
.selected-leave{
margin-top: 10px;
width: 200px;
} 
.optional-selected-leave{
margin-top: 10px;
/* width: 300px; */

width: 85%;

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
.from-date{
color: #000;
font-size: 18px;
letter-spacing: 0px;
font-family: 'Montserrat';
margin-top: 18px;
}
.from{
width: 80%;
}
.to{
width: 80%;
}

.textArea{
width: 90% !important;
}

.reason{
width: 80%;
}
.to-date{
color: #000;
font-size: 18px;
letter-spacing: 0px;
font-family: 'Montserrat';
margin-top: 30px;
}
.leave-reason{
color: #000;
font-size: 18px;
letter-spacing: 0px;
font-family: 'Montserrat';
margin-top: 30px;
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
    margin-top: 5px;
    margin-right: 10px;
    margin-left: 15px;
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
.left-side{
font-family: 'Montserrat';
position: absolute;
margin-bottom: 50px;
padding: calc(25vh - 60px) 20px 30px 20px;
margin-bottom: 50px;
top: 60px;
height: 100%;
width: calc(100% - 100px);
-ms-overflow-style: none;  /* IE and Edge */
scrollbar-width: none; /* Firefox */
}
.left-column{
font-family: 'Montserrat';
position: absolute;
left: 60px;
width: calc(100% - 60px);
margin: 8px 15px 0px 15px;
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

.tab-bg{
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
.leave-holder{
z-index: 20;
box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
height: 500px;
position: relative;
}
.leave-container{
position: relative;
top: -50px;
z-index: 20;
left: 60px;
width: calc(100% - 20px);
}

.leave-list{
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  overflow : auto;
  position: relative;
  height: 520px;

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

.choose-file-text{
margin-left: 20px;
}

.noAnnouncementImg{
  width: 100px;
}
.no-announcement{
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
}
.choose-file{
  margin-top: 45px;
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0px;
} 



</style>