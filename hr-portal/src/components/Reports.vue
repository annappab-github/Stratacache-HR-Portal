<template>
  <div class="report-container">
    <div class="title">
      <h1 class="mt-4">Reports</h1>
    </div>
    
    <div class="report-loader" v-if="loading"></div>

    <div class="container mt-5">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="report">Report Name</label>
            <select @change="loadInputBasedOnReport($event)" class="form-control" id="report">
                <option disabled value="">Select Report</option>
                <option  value="1">Employee Report</option>
                <option  value="2">Leave Tracker Report(AL and SL only)</option>
                <option  value="3">Leave Balance Report(All)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="container report-form">
    <div class="row">

        <div v-if="reportType == '1'" class="col-md-6">
          <div class="form-group">
            <label for="start_Date">Start Date<span class="required">*</span></label>
            <input class="form-control" id="start_Date" type="date" placeholder="Enter Start Date" v-model="reportsData.startDate" @change="handleDate(reportsData.startDate)">
          </div>
        </div>

        <div v-if="reportType == '1'" class="col-md-6">
          <div class="form-group">
            <label for="end_Date">End Date<span class="required">*</span></label>
            <input class="form-control" id="end_Date" min="" type="date" placeholder="Enter End Date" v-model="reportsData.endDate">
          </div>
        </div>

        <div v-if="reportType == '1'" class="col-md-6">
            <div class="form-group">
                <label for="status">Employee Status<span class="required">*</span></label>
                <select2-multiple-control id="status" v-model="reportsData.status" :options="statusOptions" @change="myChangeEvent($event,type[0])" />
            </div>
        </div>

        <div v-if="reportType == '1'" class="col-md-6">
            <div class="form-group">
                <label for="Department">Department<span class="required">*</span></label>
                <select2-multiple-control v-model="reportsData.department" :options="departmentOptions" @change="myChangeEvent($event,type[1])"/>
            </div>
        </div>

        <div v-if="reportType == '1'" class="col-md-6">
          <div class="form-group">
            <label for="country">Location<span class="required">*</span></label>
            <select2-multiple-control class="mySelect" v-model="reportsData.country" :options="const_country" @change="myChangeEvent($event,type[2])"/>
          </div>
        </div>
        

        <!-- <div class="col-md-6">
          <div class="form-group">
            <label for="country">Employee Type<span class="required">*</span></label>
            <select class="form-control" id="type" v-model="reportsData.employeeType"  @change="myChangeEvent(reportsData.employeeType,type[3])">
                    <option v-for="(option,index) in employeeType" :key="index" :value="option">{{ option }}</option>
                </select>
          </div>
        </div> -->

        <div v-if="reportType == '1'" class="col-md-6">
          <div class="form-group">
            <label for="country">Employee Type<span class="required">*</span></label>
            <select2-multiple-control class="mySelect" v-model="reportsData.employeeType" :options="typeOptions" @change="myChangeEvent($event,type[3])"/>
          </div>
        </div>
      </div>

      <div v-if="reportType == '1'" class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input class="radio-btn mr-2" id="PDF" value="pdf" type="radio" v-model="fileFormat">
              <label for="PDF">PDF</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
              <input class="radio-btn mr-2" id="CSV" value="csv" type="radio" v-model="fileFormat">
              <label for="CSV">CSV</label>
            </div>
        </div>

          <div class="col-md-4">
              <div class="form-group">
                <input class="radio-btn mr-2" id="Excel" value="xlsx" type="radio" v-model="fileFormat">
                <label for="Excel">Excel</label>
              </div>
            </div>
          </div>
      </div>
        
        <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" :disabled="isDisabled" @click="downloadReports">Download Reports</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
import Select2MultipleControl from 'v-select2-multiple-component';
// import $ from 'jquery';


export default {
  components: {
    Select2MultipleControl
  },
  data() {
    return {
      loading: false,
      isDisabled:false,
      fileFormat: 'pdf',
      reportsData: {
        startDate: '',
        endDate: '',
        status: [],
        department: [],
        country_and_city: [],
        employeeType: []
      },
      employeeStatus:{},
      employeeType:{},
      departments:{},
      country: {},
      departmentOptions: [],
      statusOptions: [],
      typeOptions: [],
      countryOptions: [],
      cityOptions: [],
      type: ['status','department','country','emptype'],
      // employeeType: ['All','Full Time Employee','Full Time Consultant','Part Time Consultant'],
      const_country: ['All','Bangalore - India','Delhi - India','Mumbai - India',
      'Canberra - Australia','Sydney - Australia','Singapore','China','Hong Kong','Japan','Malaysia','Philippines'],
      empType: [],
      reportType: '1'
    };
},
async mounted(){
  this.loading = true;
  let department = [];
  let status = [];
  let type = [];

  let currentDate = new Date();
  this.reportsData.startDate = currentDate.getFullYear()+'-'+ `${currentDate.getMonth()+1}` +'-'+currentDate.getDate();
  this.reportsData.endDate = this.reportsData.startDate;

  document.getElementById('end_Date').min = this.reportsData.startDate;


  const all = ['All'];  
  await axios.get(this.$hostName + '/getAllDepertments').then((response) => {
    this.departments = response.data;
    for (let i = 0; i < this.departments.length; i++) {
      department.push(this.departments[i].deparment);
    }
    this.departmentOptions = [...all,...department];
    // this.reportsData.department.push(this.departmentOptions);
  }).catch((error) => {
    console.log(error);
  });


  await axios.get(this.$hostName + '/getAllStatus').then((response) => {
    this.employeeStatus = response.data;
    for(let i=0;i<this.employeeStatus.length;i++){
      status.push(this.employeeStatus[i].status);
    }
    this.statusOptions = [...all,...status];
    // this.reportsData.status.push(this.statusOptions);
  }).catch((error) => {
    console.log(error);
  }).finally(()=>{
    this.loading = false;
  });

 

  await axios.get(this.$hostName + '/getAllEmploymentTypes').then((response) => {
    this.employeeType = response.data;
    for(let i=0;i<this.employeeType.length;i++){
      type.push(this.employeeType[i].type);
    }
    this.typeOptions = [...all,...type];
    // this.reportsData.employeeType.push(this.typeOptions);
  }).catch((error) => {
    console.log(error);
  }).finally(()=>{
    this.loading = false;
  });

  // axios.get(this.$hostName + '/cityAndCountries').then((response) => {
  //   this.country = response.data;
  //   console.log(response);
  //   // for (let i = 0; i < this.country.length; i++) {
  //   //   countries.push(this.country[i].country);
  //   //   cities.push(this.country[i].city)
  //   // }
  //   // this.countryOptions = [...all,...countries];
  //   // this.cityOptions = countries;
  // }).catch((error) => {
  //   console.log(error);
  // }).finally(()=>{
  //   this.loading = false;
  // })
},
methods: {
  handleDate(val){
    document.getElementById('end_Date').min = val;
  },
  myChangeEvent(val,type) {
    if(type == 'status' && val[0] == 'All'){
      this.reportsData.status = this.statusOptions;
    }
    else if(type == 'status'){
      this.reportsData.status = val;
    }else if(type == 'department' && val[0] == 'All'){
      this.reportsData.department = this.departmentOptions;
    }else if(type == 'department'){
      this.reportsData.department = val;
    }else if(type == 'emptype' && val == 'All'){
      this.reportsData.employeeType = ['All','Full Time Employee','Full Time Consultant','Part Time Consultant'];
    }else if(type == 'emptype'){
      this.reportsData.employeeType = val;
    }
    else if(type == 'country' && val[0] == 'All'){
      this.reportsData.country_and_city = this.const_country;
    }else {
      this.reportsData.country_and_city = val;
    }
  },
  loadInputBasedOnReport(e){
    this.reportType = e.target.value;
  },
  downloadReports(){
    if(this.reportType == '1') {
      const fromDate = moment(this.reportsData.startDate).format('YYYY-MM-DD');
      const toDate = moment(this.reportsData.endDate).format('YYYY-MM-DD');
      const fileType = this.fileFormat;

      var formData = {
        fDate: fromDate,
        tDate: toDate,
        status: this.reportsData.status,
        country_city: this.reportsData.country_and_city,
        department: this.reportsData.department,
        empType: this.reportsData.employeeType,
        rName: 'Employee List',
        rType: 'employee',
        fType: fileType,
      };
      if(formData.fDate == 'Invalid date' || formData.tDate == 'Invalid date' || formData.status.length == 0 || formData.country_city.length == 0 || formData.department.length == 0 || formData.empType == ''){
        Swal.fire({
              text: 'Kindly select the required fields.',
              confirmButtonText: 'Ok',
            });
      }else{
        axios({
          url: this.$hostName + '/downloadReport',
          method: 'POST',
          responseType: 'blob',
          data: formData,
        }).then((response) =>{
          const binaryResponse = new Blob([response.data]);
          if (binaryResponse.size > 10) {
              var fileURL = window.URL.createObjectURL(binaryResponse);
              var fileLink = document.createElement('a');
              fileLink.href = fileURL;
              var fileName =
                'Employee' +
                moment(new Date()).format('YYYY-MM-DD HH:mm:ss') +
                '.' +
                fileType;
              fileLink.setAttribute('download', fileName);
              document.body.appendChild(fileLink);
              fileLink.click();
              fileLink.remove();
            } else {
              Swal.fire({
                text: 'No data for the selected filter criteria',
                confirmButtonText: 'Ok',
              });
            }
        });
      }
    } else {
      let reportType = '';
      let reportName = '';
      if(this.reportType == '2') {
        reportType = 'leaveBalanceALSL';
        reportName = 'Leave_Tracker_ALSL_';
      }else {
        reportType = 'leaveBalanceAll';
        reportName = 'Leave_Balance_All_';
      }
      var formDataForLeaveReport = {
        rType: reportType,
        fType: 'xlsx',
      };
      this.loading = true;
      axios({
        url: this.$hostName + '/downloadReport',
        method: 'POST',
        responseType: 'blob',
        data: formDataForLeaveReport,
      })
        .then((response) => {
          const binaryResponse = new Blob([response.data]);
          if (binaryResponse.size > 10) {
            var fileURL = window.URL.createObjectURL(binaryResponse);
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            var fileName =
              reportName +
              moment(new Date()).format('YYYY-MM-DD HH:mm:ss') +
              '.xlsx';
            fileLink.setAttribute('download', fileName);
            document.body.appendChild(fileLink);
            fileLink.click();
            fileLink.remove();
          } else {
            Swal.fire({
              text: 'No data for the selected filter criteria',
              confirmButtonText: 'Ok',
            });
          }
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
}
};
</script>

<style scoped>
.report-container{
font-family: 'Montserrat';
position: absolute;
left: 60px;
width: calc(100% - 60px);
}
.title {
margin-left: 2%;
}
.report-form{
margin-top: 2%;
}
.required {
    color: red;
}

.report-loader{
position: fixed;
top: 0px;
left: 60px;
width: calc(100% - 60px);
height: 100%;
background-color: #eceaea;
background-image: url("../assets/loading.gif");
background-size: 150px;
background-repeat: no-repeat;
background-position: calc(calc(50%) - 30px);
z-index: 999;
}

</style>