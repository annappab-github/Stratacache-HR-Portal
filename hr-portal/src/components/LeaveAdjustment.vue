
<template>
    <div class="report-container">
        <div class="title">
            <h1 class="mt-4">Manage Leaves</h1>
        </div>

        <div class="report-loader" v-if="loading"></div>

        <div class="container">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link" :class="selectedTab == 'adjustment' ? 'active' : ''" id="adjustment-tab" type="button" role="tab" @click="changetab('adjustment')" >Leave adjustment</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" :class="selectedTab == 'entitlement' ? 'active' : ''" id="entitlement-tab" type="button" role="tab"  @click="changetab('entitlement')">Entitlement rules</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" :class="selectedTab == 'entitlement_adjustment' ? 'active' : ''" id="entitlement-adjustment-tab" type="button" role="tab"  @click="changetab('entitlement_adjustment')">Entitlement adjustment</button>
            </li>
          </ul>
        </div>
        <div class="container">
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" v-if="selectedTab == 'adjustment'" id="adjustment" role="tabpanel">
              <div class="row mt-4">
                <h4 class="col-md-12 mb-4">Leave Adjustment Form</h4>
                <div class="col-sm-6">
                  <div class="form-group">
                      <label for="report">Employee's Name</label>
                      <select class="form-control" id="report" v-model="selectedEmp" @change="getSelectedEmpLeaveBal(selectedEmp)">
                          <option disabled value="" selected>Select Employee</option>
                          <option v-for="(emp, index) in users" :key="index">{{emp.name}} - {{emp.employee_id}}</option>
                      </select>
                  </div>
                </div>
              </div>

        
              <div class="row report-form mt-4">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id1">Annual leave</label>
                    <input v-model="formData[0].balance" class="form-control" id="id1" type="number" placeholder="Enter Annual leave">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id2">Sick leave</label>
                    <input v-model="formData[1].balance" class="form-control" id="id2" type="number" placeholder="Enter Sick leave">
                  </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id3">Optional Leave</label>
                        <input v-model="formData[2].balance" class="form-control" id="id3" type="number" placeholder="Enter Optional Leave" :readonly="regionName == 'IN' ? false : true">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id4">Compensatory Leave</label>
                        <input v-model="formData[3].balance" class="form-control" id="id4" type="number" placeholder="Enter Compensatory Leave">
                    </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id5">Marriage Leave</label>
                    <input v-model="formData[4].balance" class="form-control" id="id5" type="number" placeholder="Enter Marriage Leave">
                  </div>
                </div>
                

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id6">Bereavement Leave</label>
                    <input v-model="formData[5].balance" class="form-control" id="id6" type="number" placeholder="Enter Bereavement Leave">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id7">Maternity Leave</label>
                    <input v-model="formData[6].balance" class="form-control" id="id7" type="number" placeholder="Enter Maternity Leave" :readonly="gender== 'female' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id8">Paternity Leave</label>
                    <input v-model="formData[7].balance" class="form-control" id="id8" type="number" placeholder="Enter Paternity Leave" :readonly="gender== 'male' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id9">Childcare Leave</label>
                    <input v-model="formData[8].balance" class="form-control" id="id9" type="number" placeholder="Enter Childcare Leave" :readonly="regionName == 'SG' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id11">Carers Leave</label>
                    <input v-model="formData[9].balance" class="form-control" id="id11" type="number" placeholder="Enter Carers Leave" :readonly="regionName == 'AU' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id12">National Service Leave</label>
                    <input v-model="formData[10].balance" class="form-control" id="id12" type="number" placeholder="Enter National Service Leave" :readonly="regionName == 'SG' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id13">Hospital Leave</label>
                    <input v-model="formData[11].balance" class="form-control" id="id13" type="number" placeholder="Enter Hospital Leave" :readonly="regionName == 'SG' ? false : true">
                  </div>
                </div>

                <!--<div class="col-md-4">
                  <div class="form-group">
                    <label for="id14">Unpaid Leave</label>
                    <input v-model="formData[12].balance" class="form-control" id="id14" type="text" placeholder="Enter Unpaid Leave">
                  </div>
                </div>-->

              </div>
              <div class="row mt-4">
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-primary" @click="updateBalance(formData)">Update</button>
                </div>
              </div>
              </div>
            </div>


            <div class="tab-pane fade show active" v-if="selectedTab == 'entitlement'" id="entitlement" role="tabpanel">
              <div class="row mt-4">
                <h4 class="col-md-12 mb-3">Leave Entitlement Rules</h4>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="region_id">Select Region <span class="required">*</span></label>
                    <select class="form-control" id="region_id" v-model="selectedregion">
                      <option value="" selected disabled>Select region</option>
                      <option v-for="(region, index) in entitlementRules[1].allRegions" :key="index" :value="region.country">{{ region.country }}</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="leave-type-id">Select Leave type <span class="required">*</span></label>
                    <select class="form-control" id="leave-type-id" v-model="selectedLeavetype">
                      <option value="" selected disabled>Select leave type</option>
                      <option v-for="(leavetype, index) in entitlementRules[2].leaveName" :key="index" :value="leavetype.id">{{ leavetype.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6">
                  <div class="form-group">
                    <label for="iaccrual-id">Leave Accrual <span class="required">*</span></label>
                    <select id="accrual-id" class="form-control" v-model="accrual">
                      <option value="yearly">Yearly</option>
                      <option value="monthly">Monthly</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6">
                  <div class="form-group">
                    <label>Carry forward <span class="required">*</span></label><br/>
                    <div class="form-check form-check-inline mr-5">
                      <input class="form-check-input" type="radio" value="yes" id="yes" v-model="leaveCarryForword">
                      <label class="form-check-label" for="yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" value="no" id="no" v-model="leaveCarryForword">
                      <label class="form-check-label" for="no">No</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6">
                  <div class="form-group">
                    <label for="max-limit">Max Limit <span class="required">*</span></label>
                    <input class="form-control" id="max-limit" type="text" v-model="maxLimit" placeholder="Enter Max limit">
                    <span>If no max limit enter 'NA'</span>
                  </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-primary" @click="addOrUpdateLeaveRules">{{ buttonval }}</button>
                </div>
              </div>
            </div>


            
            <div class="tab-pane fade show active" v-if="selectedTab == 'entitlement_adjustment'" id="entitlement-adjustment" role="tabpanel">
              <div class="row mt-4">
                <h4 class="col-md-12 mb-3">Leave Entitlement Adjustment</h4>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="report">Select Employee's</label>
                      <select class="form-control" id="report" v-model="selectedEmp_entitlement_adjust" @change="getSelectedEmpEntitlementBal(selectedEmp_entitlement_adjust)">
                        <option disabled value="" selected>Select Employee</option>
                        <option v-for="(emp, index) in users" :value="emp.employee_id" :key="index">{{emp.name}} - {{emp.employee_id}}</option>
                      </select>
                  </div>
                </div>
              </div>

              <div class="row report-form mt-4">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id1">Annual leave</label>
                    <input v-model="entitlement_adjust_formData.annual_leave" class="form-control" id="id01" type="number" placeholder="Enter Annual leave">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id2">Sick leave</label>
                    <input v-model="entitlement_adjust_formData.sick_leave" class="form-control" id="id02" type="number" placeholder="Enter Sick leave">
                  </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id3">Optional Leave</label>
                        <input v-model="entitlement_adjust_formData.optional_leave" class="form-control" id="id03" type="number" placeholder="Enter Optional Leave" :readonly="regionNameEntAdjust == 'IN' ? false : true">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id4">Compensatory Leave</label>
                        <input v-model="entitlement_adjust_formData.compensatory_leave" class="form-control" id="id04" type="number" placeholder="Enter Compensatory Leave">
                    </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id5">Marriage Leave</label>
                    <input v-model="entitlement_adjust_formData.marriage_leave" class="form-control" id="id05" type="number" placeholder="Enter Marriage Leave">
                  </div>
                </div>                

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id6">Bereavement Leave</label>
                    <input v-model="entitlement_adjust_formData.bereavement_leave" class="form-control" id="id06" type="number" placeholder="Enter Bereavement Leave">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id7">Maternity Leave</label>
                    <input v-model="entitlement_adjust_formData.maternity_leave" class="form-control" id="id07" type="number" placeholder="Enter Maternity Leave" :readonly="entAdjustGender == 'female' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id8">Paternity Leave</label>
                    <input v-model="entitlement_adjust_formData.paternity_leave" class="form-control" id="id08" type="number" placeholder="Enter Paternity Leave" :readonly="entAdjustGender== 'male' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id9">Childcare Leave</label>
                    <input v-model="entitlement_adjust_formData.childcare_leave" class="form-control" id="id09" type="number" placeholder="Enter Childcare Leave" :readonly="regionNameEntAdjust == 'SG' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id11">Carers Leave</label>
                    <input v-model="entitlement_adjust_formData.carers_leave" class="form-control" id="id011" type="number" placeholder="Enter Carers Leave" :readonly="regionNameEntAdjust == 'AU' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id12">National Service Leave</label>
                    <input v-model="entitlement_adjust_formData.national_service_leave" class="form-control" id="id12" type="number" placeholder="Enter National Service Leave" :readonly="regionNameEntAdjust == 'SG' ? false : true">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id13">Hospital Leave</label>
                    <input v-model="entitlement_adjust_formData.hospitalization_leave" class="form-control" id="id13" type="number" placeholder="Enter Hospital Leave" :readonly="regionNameEntAdjust == 'SG' ? false : true">
                  </div>
                </div>

              </div>
              <div class="col-md-12 text-center my-4">
                  <button type="button" class="btn btn-primary" @click="addUpdateEntitlementValue">{{ entitlement_adjustment_buttonval }}</button>
              </div>
            </div>           
           
          </div>
        </div>
        
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2/dist/sweetalert2.js';

export default {
    components: {
    },
    watch: {
      selectedregion(newval) {
        let leavetype = this.selectedLeavetype;
        this.checkSelectedRule(newval, leavetype);
      },
      selectedLeavetype(newval) {
        let region = this.selectedregion;
        this.checkSelectedRule(region, newval);
      }
    },
    data(){
      return{
        users: [],
        selectedEmp: '',
        employeeId: '',
        // leaveBalanceData: [],
        annualBal: 0,
        gender: '',
        entAdjustGender: '',
        regionName: '',
        regionNameEntAdjust: '',
        selectedTab: 'adjustment',
       formData: [
            {
                'leave_name_id': 1,
                'empid': '',
                'name': 'Annual Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 2,
                'empid': '',
                'name': 'Sick Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 3,
                'empid': '',
                'name': 'Optional Leave Balance ',
                'balance': ''
            },
            {
                'leave_name_id': 4,
                'empid': '',
                'name': 'Compensatory Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 5,
                'empid': '',
                'name': 'Marriage Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 6,
                'empid': '',
                'name': 'Bereavement Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 7,
                'empid': '',
                'name': 'Maternity Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 8,
                'empid': '',
                'name': 'Paternity Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 9,
                'empid': '',
                'name': 'Childcare Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 10,
                'empid': '',
                'name': 'Carers Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 11,
                'empid': '',
                'name': 'National Service Leave Balance',
                'balance': ''
            },
            {
                'leave_name_id': 12,
                'empid': '',
                'name': 'Hospital Leave Balance',
                'balance': ''
            }
        ],
        entitlementRules: [
          { rules: [] },
          { allRegions: [] },
          { leaveName: [] }
        ],
        selectedregion: '',
        selectedLeavetype: '',
        accrual: 'yearly',
        leaveCarryForword: 'yes',
        maxLimit: '',
        buttonval: 'Create',
        selectedRuleId: 0,
        entitlement_adjustment_buttonval: 'Create',
        entAdjestFormFields: {'annual_leave': null,'sick_leave': null,'optional_leave': null,'compensatory_leave': null,'marriage_leave': null,'bereavement_leave': null,'maternity_leave': null,'paternity_leave': null,'childcare_leave': null,'national_service_leave': null,'hospitalization_leave': null,'carers_leave': null},
        entitlement_adjust_formData: new Object(),
        selectedEmp_entitlement_adjust: ''
      }; 
    },
    mounted(){
      this.entitlement_adjust_formData = JSON.parse(JSON.stringify(this.entAdjestFormFields));
      axios
      .get(this.$hostName + '/getAllUsers')
      .then((response) => {
        this.users = response.data.users;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
      axios
      .get(this.$hostName + '/getallentitlementrules')
      .then((response) => {
        this.entitlementRules = response.data;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
    
    },
    methods:{
      async getSelectedEmpEntitlementBal(emp) {
        if (this.selectedEmp_entitlement_adjust != '') {
          this.regionNameEntAdjust = emp.slice(0,2);
          await axios.get(`${this.$hostName}/getEmpEntitlementData/${emp}`)
          .then((response) => {
            if(response.data.length > 0) {
              delete(response.data[0].created_at);
              delete(response.data[0].updated_at);
              this.entitlement_adjust_formData = response.data[0];
              this.entitlement_adjustment_buttonval = 'Update';
            } else {
              this.entitlement_adjust_formData = JSON.parse(JSON.stringify(this.entAdjestFormFields));
              this.entitlement_adjustment_buttonval = 'Create';
            }
          }).catch((error) => {
            console.log(error);
          });
  
          for(const index in this.users) {
            if(this.users[index].empid == emp) {
              this.entAdjustGender = this.users[index].gender;
            }
          }          
        }
      },
      async addUpdateEntitlementValue() {
        // createentitlementdata
        // updateentitlementdata
        if(this.selectedEmp_entitlement_adjust != '') {
          const apiUrl = (this.entitlement_adjustment_buttonval == 'Create' ? 'createentitlementdata' : 'updateentitlementdata');
          this.entitlement_adjust_formData.empid = this.selectedEmp_entitlement_adjust;
          let formdata = [this.entitlement_adjust_formData];
          await axios.post(`${this.$hostName}/${apiUrl}`, formdata)
          .then(() => {
            Swal.fire({
                  text: `Leave entitlement ${this.entitlement_adjustment_buttonval.toLowerCase()}d successfully.`, 
                  confirmButtonText: 'Ok',
                }).then(() => {
                  this.entitlement_adjustment_buttonval = 'Create';
                  this.selectedEmp_entitlement_adjust = '';
                  this.entitlement_adjust_formData = JSON.parse(JSON.stringify(this.entAdjestFormFields));
                });
          }).catch((error) => {
            console.log(error);
          });
        } else {
          Swal.fire({
            text: 'Please select Employee!', 
            confirmButtonText: 'Ok',
          });
        }
      },
      async addOrUpdateLeaveRules() {
        let accrual = this.accrual.trim();
        let carry_forword = this.leaveCarryForword.trim();
        let leave_type = this.selectedLeavetype;
        let region = this.selectedregion.trim();
        let max_limit = this.maxLimit.trim();
        if(max_limit != '' && accrual != '' && carry_forword != '' && leave_type != '' && region != '') {
          let formData = {region, accrual, leave_type, carry_forword, max_limit };
          let apiurl = '';
          let msgtext = '';
          if(this.buttonval == 'Create') {
            apiurl = 'createentitlementrule';
            msgtext = 'created';
          }
          else {
            apiurl ='updateentitlementrule';
            formData.id = this.selectedRuleId;
            msgtext = 'updated';
          }
          await axios.post(`${this.$hostName}/${apiurl}`, formData)
            .then((response) => {
              this.entitlementRules = response.data.updatedData;
              Swal.fire({
                text: `Leave rule ${msgtext} successfully.`, 
                confirmButtonText: 'Ok',
              }).then(() => {
                this.selectedLeavetype = '';
                this.selectedregion = '';
                this.maxLimit = '';
                this.accrual = 'yearly';
                this.leaveCarryForword = 'yes';
              });
            }).catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.loading = false; 
            });
        } else {
          Swal.fire({
            text: 'Please fill all required fields.',
            confirmButtonText: 'Ok'
          });
        }

      },
      checkSelectedRule(region, leavetype) {
        if (region != '' && leavetype != '') {
          let rules = this.entitlementRules[0].rules;
          for(let rule of rules) {
            if (rule.region == region && rule.leave_type == leavetype) {
              this.buttonval = 'Update';
              this.accrual = rule.accrual;
              this.leaveCarryForword = rule.carry_forword;
              this.maxLimit = rule.max_limit;
              this.selectedRuleId = rule.id;
              break;
            } else {
              this.buttonval = 'Create';
              this.accrual = 'yearly';
              this.leaveCarryForword = 'yes';
              this.maxLimit = '';
              this.selectedRuleId = 0;
            }
          }
        }
      },
      changetab(tabname) {
        this.selectedTab = tabname;
      },
    async  getSelectedEmpLeaveBal(data){
      // this.loading = true;
        this.employeeId = data.split('-')[1].trim(' ');
        this.regionName = this.employeeId.slice(0,2);
      await axios.post(this.$hostName + '/getEmployeeLeaveBalance', {empId: this.employeeId})
            .then((response) => {
              this.formData = response.data[0].leaveBalanceData;
              }).catch((error) => {
                console.log(error);
              })
              .finally(() => {
                this.loading = false; 
              });
        for(const index in this.users) {
          if(this.users[index].empid == this.employeeId) {
            this.gender = this.users[index].gender;
          }
        }
      },
      updateBalance(data){
        this.loading = true;
        axios
        .put(this.$hostName + '/updateLeaveBal', {
            data,
        })
        .then((response) => {
          if (response.status == 200) {
            Swal.fire({
              text: 'Leave details updated successfully.', 
              confirmButtonText: 'Ok',
            });
          } else if (response.status == 203) {
            Swal.fire({
              text: response.data, 
              confirmButtonText: 'Ok',
            });
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
.disabled {
  pointer-events:none;
  color:#AAA;
  background:#F5F5F5;
}
.enabled{
  color:#fff;
  background:#fff;
}
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
span.required {
  color: red;
}
</style>