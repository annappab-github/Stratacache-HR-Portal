<template>
<div id="edit-employee">
    <div class="edit-container">
        <div class="title">
            <h1>{{isNewEmployee ? page_title[0] : page_title[1]}}</h1>
        </div>
        <div class="container container-md container-sm container-lg">
            <div class="row row-sm row-md row-lg">
                <div class="profileImg-holder col-md-12 col-sm-12 col-lg-3" >
                        <div v-if="this.email == 'admin@scala.com'" class="peopleShortNameMyInfo" :style="`background-color:#6e70ac`"> AD </div>
                        <div v-else-if="!isNewEmployee">
                            <input type="file" id="file" ref="file" @change="profilePicUpload($event)" accept="image/x-png,image/jpeg" style="display: none">
                            <div v-if="employeeData.profile_pic == null" class="peopleShortNameMyInfo" :style="`background-color:${this.employeeData.imageBackgroundColour}`">{{ this.employeeData.employeeShortName }}</div>
                            <div v-else  class="profileImg" :style="`background-Image: url(${this.employeeData.profile_pic})`"></div>
                        </div>

                        <div v-if="isNewEmployee ? page_title[0] : ''">
                            <input type="file" id="file" ref="file" @change="previewUserImage($event)" accept="image/x-png,image/jpeg" style="display: none">
                            <div v-if="showUserProfile">
                                <div class="profileImg" :style="`background-Image: url('${this.defaultBackgroundImage}')`"></div>
                            </div>
                            <img v-if="!showUserProfile" class="profileImg" :src="this.url" id="preview">
                        </div>

                        

                        <div class="" @click="$refs.file.click()">
                            <span class="mdi mdi-pencil  cameraImgNoImg" style="font-size: 20px; color: #fff;"></span>
                        </div> 
                </div>

        <div class="create-form col-md-12 col-sm-12 col-lg-9">
        <div class="user-loader" v-if="loading"></div>
        <form @submit.prevent="handleSubmit">
            <div class="container">
                <div class="row" >
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emp-id">Employee ID# <span class="required">*</span></label>
                            <input class="form-control" id="emp-id" type="text" placeholder="Enter Employee ID" v-model="employeeData.empid" >
                            <!-- <span class="invalid-feedback">Please Enter Employee ID</span> -->
                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="emp-name">Employee Name <span class="required">*</span></label>
                            <input class="form-control" id="emp-name" type="text" placeholder="Enter Employee Name" v-model="employeeData.name" maxlength="45" >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emp-email">Employee Email <span class="required">*</span></label>
                            <input class="form-control" id="emp-email" type="text" placeholder="Enter Employee Email" v-model="employeeData.email" >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role">Role <span class="required">*</span></label>
                            <select class="form-control" id="role" v-model="employeeData.role">
                                <option disabled value="">Select Role</option>
                                <option v-for="(option, index) in getRoles" :key="index" :value="option.name" :class="index%2==0 ? 'bg-alternate': ''">{{ option.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="password">Password <span class="required" v-if="isNewEmployee">*</span></label>
                            <input class="form-control" id="password" type="password" placeholder="Enter Password" v-model="employeeData.password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emp-location">Location <span class="required">*</span></label>
                            <select class="form-control" id="emp-location" v-model="employeeData.countrycity">
                                <option selected disabled value="">Select City and Country</option>
                                <option v-for="(option, index) in countryCitys" :key="index" :value="option.location + ',' + option.city" :class="index%2==0 ? 'bg-alternate': ''">{{ option.location == option.city ? option.location : option.city + ' - ' + option.location }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emp-position">Position <span class="required">*</span></label>
                            <input class="form-control" id="emp-position" type="text" placeholder="Enter Position" v-model="employeeData.position">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emp-department">Department <span class="required">*</span></label>
                            <select class="form-control" id="emp-department" v-model="employeeData.department">
                                <option selected disabled value="">Select department</option>
                                <option v-for="(option, index) in allDepartments" :key="index" :value="option.deparment" :class="index%2==0 ? 'bg-alternate': ''">{{ option.deparment }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_grade">Employee Grade <span class="required">*</span></label>
                            <select class="form-control" id="employee_grade" v-model="employeeData.employee_grade">
                                <option selected disabled value="">Select Employee Grade</option>
                                <option v-for="(option, index) in allGrades" :key="index" :value="option.grade" :class="index%2==0 ? 'bg-alternate': ''">{{ option.grade }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_type">Employee Type <span class="required">*</span></label>
                            <select class="form-control" id="employee_type" v-model="employeeData.employement_type">
                                <option selected disabled value="">Select Employee type</option>
                                <option v-for="(option, index) in employement_type" :key="index" :value="option.type" :class="index%2==0 ? 'bg-alternate': ''">{{ option.type }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_Date">Start Date <span class="required">*</span></label>
                            <input class="form-control" id="start_Date" type="date" placeholder="Enter Start Date" v-model="employeeData.start_Date">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="probation_period">Probation Period</label>
                            <input class="form-control" id="probation_period" type="text" placeholder="Enter Probation Period" v-model="employeeData.probation_period">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirmation_date">Confirmation Date</label>
                            <input class="form-control" id="confirmation_date" type="date" placeholder="Select Confirmation Date" v-model="employeeData.confirmation_date">
                        </div>
                    </div>                    

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="land_ext">Desk Phone</label>
                            <input class="form-control" id="land_ext" type="text" placeholder="Enter Desk Phone" v-model="employeeData.land_ext">
                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="mobile_number">Mobile Number</label>
                            <input class="form-control" id="mobile_number" type="text" placeholder="Enter Mobile Number" v-model="employeeData.mobile_number">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reporting_to">Reporting To# <span class="required">*</span></label>
                            <select class="form-control" id="reporting_to" v-model="employeeData.reporting_to">
                                <option selected disabled value="">Select Reporting Manager</option>
                                <option v-for="(option, index) in reporing_employees" :key="index" :value="option.empid" :class="index%2==0 ? 'bg-alternate': ''">{{ option.empid + ', ' + option.name + ', ' +  option.position}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birth_dates">DOB</label>
                            <input class="form-control" id="birth_dates" type="date" :max="todaydate" placeholder="Select DOB" v-model="employeeData.birth_dates">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                                <label for="birth_dates">Gender</label><br>
                            <div class="form-check form-check-inline mr-5">
                                <input class="form-check-input" type="radio" value="male" id="male" v-model="employeeData.gender" >
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="female" id="female" v-model="employeeData.gender">
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" v-model="employeeData.address" placeholder="Enter Address" ></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" v-model="employeeData.remarks" placeholder="Enter remarks" ></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="new_confirmation_date">New Confirmation Date</label>
                            <input class="form-control" id="new_confirmation_date" type="date" placeholder="Select new Confirmation Date" v-model="employeeData.new_confirmationdate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="terminationdate">Termination Date</label>
                            <input class="form-control" id="terminationdate" type="date" placeholder="Select Termination Date" v-model="employeeData.terminationdate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="resignation">Resignation Date</label>
                            <input class="form-control" id="resignation" type="date" placeholder="Select Resignation Date" v-model="employeeData.resignation">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_workingdate">Last Working Date</label>
                            <input class="form-control" id="last_workingdate" type="date" placeholder="Select Last Working Date" v-model="employeeData.last_workingdate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" v-model="employeeData.status">
                                <option selected disabled value="">Select Status</option>
                                <option v-for="(option, index) in const_status" :key="index" :value="option" :class="index%2==0 ? 'bg-alternate': ''">{{ option.toUpperCase() }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-5">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="force_change_password" v-model="employeeData.force_change_password" />
                            <label class="custom-control-label" for="force_change_password">Password Flag</label>
                        </div>
                    </div>
                </div>

                <div class="row" >
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" :disabled='isDisabled'>{{ isNewEmployee ? buttontext[0] : buttontext[1] }}</button>
                    </div>
                </div>

            </div>
        </form>
        </div>
            </div>
            
        </div>
        
        
    </div>
</div>
</template>

<script>
import axios from 'axios';
import { routeNames } from '../js/routeNames';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import $ from 'jquery';
import defaultUSer from '../assets/imagealt.png';

let required_fields = [
    {keyname: 'empid', inputid: 'emp-id', err_msg: 'Please fill out this field.'},
    {keyname: 'name', inputid: 'emp-name', err_msg: 'Please fill out this field.'},
    {keyname: 'email', inputid: 'emp-email', err_msg: 'Please fill out this field.', incurrect_format: 'Incorrect email format'},
    {keyname: 'role', inputid: 'role', err_msg: 'Please fill out this field.'},
    {keyname: 'department', inputid: 'emp-department', err_msg: 'Please fill out this field.'},
    {keyname: 'position', inputid: 'emp-position', err_msg: 'Please fill out this field.'},
    {keyname: 'employee_grade', inputid: 'employee_grade', err_msg: 'Please fill out this field.'},
    {keyname: 'start_Date', inputid: 'start_Date', err_msg: 'Please fill out this field.'},
    {keyname: 'password', inputid: 'password', err_msg: 'Please fill out this field.', length_err: 'Minimum 6 characters required.'},
    {keyname: 'reporting_to', inputid: 'reporting_to', err_msg: 'Please select this field.'},
    {keyname: 'countrycity', inputid: 'emp-location', err_msg: 'Please select this field.' },
    {keyname: 'employement_type', inputid: 'employee_type', err_msg: 'Please select this field.' },
];

export default {
    name: 'EditEmployee',
    components: {
    },
    data() {
        return {
            page_title: ['Add Employee','Edit Employee'],
            buttontext: ['Create Employee', 'Update Employee'],
            isNewEmployee: true,
            loading: false,
            employeeData: {},
            reporing_employees: [],
            isDisabled: false,
            getRoles:[],
            const_status: ['active', 'terminated', 'resigned'],
            required_field_count: 0,
            updatedProfilePic: '',
            defaultBackgroundImage: defaultUSer,
            showUserProfile: true,
            previewImageData: '',
            countryCitys: [],
            employement_type: [],
            allDepartments: [],
            allGrades: []
        };
    },
    watch: {
        employeeData: {
            handler(newval, oldval){
                if (oldval.empid != undefined && oldval.empid != '') {
                    if(this.required_field_count > 0)
                        this.checkRequiredFields();
                    this.isDisabled = false;
                }
            },
            deep: true
        },
    },
    computed: {
        todaydate() {
            let today = new Date();
            return today.toISOString().split('T')[0];
        }
    },
    mounted() {
        this.loading = true;
        if (this.$route.params.empid != '') {
            this.isNewEmployee = false;
            axios.get(this.$hostName + `/employeeinfo/${this.$route.params.empid}`).then(response => {
                this.employeeData = response.data[0];
                this.employeeData.countrycity = response.data[0].location+','+response.data[0].city;
                this.employeeData.emailCheck = response.data[0].email;
                this.employeeData.employeeIdCheck = response.data[0].empid;
                this.employeeData.password = '';
                this.employeeData.force_change_password = (response.data[0].force_change_password == 1 ? true : false);
                
            }).finally(() => {
                this.loading =  false;
            });
        } else {
            this.loading =  false;
            this.employeeData = {
                empid: '',
                name: '',
                email: '',
                role: '',
                department: '',
                password: '',
                position: '',
                employee_grade: '',
                start_Date: '',
                reporting_to: '',
                probation_period: '',
                address: '',
                birth_dates: '',
                confirmation_date: '',
                emid: '',
                urid: '',
                land_ext: '',
                countrycity: '',
                location: '',
                city: '',
                employement_type: '',
                mobile_number: '',
                new_confirmationdate: '',
                remarks: '',
                status: 'active',
                force_change_password: false,
                terminationdate: '',
                resignation: '',
                last_workingdate: '',
                gender: 'male'
            };
        }
        axios.get(this.$hostName + '/getAllRoles').then((response) => {
            this.getRoles = response.data.roles;
        });

        axios.get(this.$hostName + '/employeesidforreporting').then(response => {
            this.reporing_employees = response.data;
        });

        axios.get(this.$hostName + '/cityAndCountries').then(response => {
            this.countryCitys = response.data;
        });
        axios.get(this.$hostName + '/getAllDepertments').then(response => {
            // this.reporing_employees = response.data;
            this.allDepartments = response.data;
        });
        axios.get(this.$hostName + '/getAllGrades').then(response => {
            // this.reporing_employees = response.data;
            this.allGrades = response.data;
        });
        
        axios.get(this.$hostName + '/getAllEmploymentTypes').then(response => {
            // this.reporing_employees = response.data;
            this.employement_type = response.data;
        });
    },
    methods:{
        previewUserImage(event){
            this.showUserProfile = true;
            let url = event.target.files[0];
            this.previewImageData = url;
            if(url.size > 1024 * 1024) {
                event.preventDefault();
                alert('File too big (> 1MB)');
                return;
            } else {
                this.showUserProfile = false;
                this.url = URL.createObjectURL(event.target.files[0]);
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
            formData.append('id', this.employeeData.emid);
            axios
              .post(this.$hostName + '/myInfo/updateProfilePhoto', formData, {headers : {'Content-Type': 'multipart/form-data'}})
              .then((response) => {
                console.log(response);
            })
            .catch((error) => {
              this.errors = error.response.data.errors;
            })
            .finally(() => {
              location.reload();
            });
          }
        }
      },
        inputErrorMsg(id, errmsg) {
            this.required_field_count++;
            $(`#${id}`).next('span').remove();
            $(`#${id}`).css('border-color', 'red').after(`<span style="color:red; font-size: 14px;">${errmsg}</span>`);
        },
        removeInputError(id) {
            $(`#${id}`).css('border-color', '#ced4da').next('span').remove();
        },
        checkRequiredFields() {
            this.required_field_count = 0;
            required_fields.forEach(element => {
                if(this.employeeData[element.keyname] == '' && element.keyname != 'password') {
                    this.inputErrorMsg(element.inputid, element.err_msg);
                } else if(element.keyname == 'password') {
                    if(this.employeeData[element.keyname] == '' && this.isNewEmployee) {
                        this.inputErrorMsg(element.inputid, element.err_msg);
                    } else if (this.employeeData[element.keyname] != '' && this.employeeData[element.keyname].length < 6) {
                        this.inputErrorMsg(element.inputid, element.length_err);
                    } else {
                        this.removeInputError(element.inputid);
                    }
                } else if(element.keyname == 'email' && this.employeeData[element.keyname].length > 0) {
                    if (/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(this.employeeData[element.keyname].trim())) {
                        this.removeInputError(element.inputid);
                    } else {
                        this.inputErrorMsg(element.inputid, element.incurrect_format);
                    }
                } else {
                    this.removeInputError(element.inputid);
                }
            });
            return this.required_field_count;
        },

        async handleSubmit(){
            let emid = '';
            this.loading =  true;
            let checkError = await this.checkRequiredFields();
            if(checkError == 0) {
                let employeeData = {...this.employeeData};
                employeeData.location = employeeData.countrycity.split(',')[0];
                employeeData.city = employeeData.countrycity.split(',')[1];
                if (this.isNewEmployee) {
                 await axios.post(this.$hostName + '/account/create', employeeData).then((response) => {
                    emid = response.data.id;
                        if (response.status == 202) {
                            this.loading = false;
                            Swal.fire({ text: 'Employee ID already used',
                                confirmButtonText: 'Ok' });
                        } else if (response.status == 203) {
                            this.loading = false;
                            Swal.fire({ text: 'Email already exists',
                                confirmButtonText: 'Ok' });
                        } else {
                            this.loading = false;
                            this.$router.push({ name: routeNames.manageUser });
                        }
                    });
                 let formData = new FormData();
                 formData.append('empId', employeeData.empid);
                 formData.append('file', this.previewImageData);
                 formData.append('id', emid);
                 await axios
                    .post(this.$hostName + '/myInfo/updateProfilePhoto', formData, {headers : {'Content-Type': 'multipart/form-data'}})
                        .then((response) => {
                        console.log(response);
                    })
                    .catch((error) => {
                    this.errors = error.response.data.errors;
                    })
                    .finally(() => {
                    // location.reload();
                    });
                } else {
                    axios.put(this.$hostName + '/account/update', employeeData).then(response =>{
                        if(response.status==202){
                            this.loading = false;
                            Swal.fire({
                                text: 'Employee ID already exists',
                                confirmButtonText: 'Ok',
                            });
                        } else if(response.status==203){
                            this.loading = false;
                            Swal.fire({
                                text: 'Email already exists',
                                confirmButtonText: 'Ok',
                            });
                        } else{
                            this.loading =  false;
                            this.$router.push({name: routeNames.manageUser});
                        }
                    });
                }
            } else {
                this.loading =  false;
            }

            // this.loading =  true;
            let ifcheck = false;
            if(ifcheck) {
                Swal.fire({
                    text: 'Something went wrong please try again after sometime',
                    confirmButtonText: 'Ok',
                });
            }
        }
    }
};
</script>

<style scoped>
.required {
    color: red;
}
.cameraImgNoImg{
  border-radius: 50%;
  position: relative;
  padding: 4px 5px 4px 8px;
  top: -43px;
  left: calc(50% + 5.5vh);
  transform: translate(-50%);
  background-color: #0c0d0d;
  cursor: pointer;
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
.profileImg-holder{
    display: inline-block;
    width: 15%;
    height: 15%;
    margin-left: auto;
    margin-right: auto;
    margin-top: 3%;
}

.input-error {
    color:red;
}
#edit-employee {
    font-family: 'Montserrat';
    margin: 30px 2px 30px 60px;
}
#edit-employee::-webkit-scrollbar {
    width: 10px;
}
.title {
    margin-left: 30px;
}
.edit-container {
    margin-left: auto;
    margin-right: auto;
}
.create-form {
  margin-top: 3%;
}
option.bg-alternate{
    background: rgb(242, 242, 242);
}
.custom-control-input:checked ~ .custom-control-label::before {
  color: #fff;
  border-color: #000;
  background-color: #000;
}
.user-loader{
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