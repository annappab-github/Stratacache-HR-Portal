<template>
  <div class="dashboard">
    <div class="title">
      <h1 class="mt-4">People</h1>
    </div>
    <div class="search">
      <div class="icon">
        <span class="mdi mdi-magnify" style="color: #000"></span>
      </div>
      <input
            class="search-bar"
            placeholder="search by name, title, department, etc..."
            v-model="search"
      />
      <div class="org-chart" v-for="data in orgChartData" :key="data">
        <button class="btn btn-light org-button" @click="openDocument(data)">
        <span class="mdi mdi-sitemap org-chart-icon"></span>
        <span class="">Org Chart</span>
        </button>
      </div> 
    </div>
    <div class="loader" v-if="loading"></div>
    <div class="country-filter">
      <div :class="`countryFlags ${activeHolidayTab==index ? 'activeCountry' : ''}`" v-for="(flag,index) in flags" :key="index" @click="resetActiveCountry(index,flag)">
        <img class="flag-img" :src="flag.flag[0]" />
        <div class="flag-text-holder">
          <div class="flag-text">{{flag.flag[1]}}</div>
        </div>
      </div>
    </div>
    <div class="audit-table">
      <table class="table">
      <tr v-for="data in filteredList" :key="data">
        <td style="width:15%;padding:5px;" >
          <div v-if="data.profile_pic == null" class="peopleShortName" :style="`background-color:${data.imageBackgroundColour}`">{{ data.employeeShortName }}</div>
          <img v-else class="peopleImg" :src="getImgUrl(data.profile_pic)">
        </td>
        <td style="width:35%;padding:5px;" class="desktop-column">
          <span v-if="this.permissions.includes('View-Employee-Detail')" style="font-weight:bold;font-size: 15px;" class="emp-name" @click="reportingEmployeeDetails(data.empid, data.email)">{{ data.name }}</span>
          <span v-else style="font-weight:bold;font-size: 15px;" >{{ data.name }}</span><br/>
          <span>{{ data.position }} in {{ data.department }}</span><br/>
          <img v-if="countryReformat(data.location) == 'singapore'" class="flag" :src="singapore">
          <img v-if="countryReformat(data.location) == 'india'" class="flag" :src="india">
          <img v-if="countryReformat(data.location) == 'china'" class="flag" :src="china">
          <img v-if="countryReformat(data.location) == 'australia'" class="flag" :src="australia">
          <img v-if="countryReformat(data.location) == 'hongkong'" class="flag" :src="hongkong">
          <img v-if="countryReformat(data.location) == 'japan'" class="flag" :src="japan">
          <img v-if="countryReformat(data.location) == 'philippines'" class="flag" :src="philippines">
          <img v-if="countryReformat(data.location) == 'malaysia'" class="flag" :src="malaysia">
          <span class="ml-2">{{ data.location == data.city ? data.location : data.city + ' - ' + data.location }}</span>
          
          <!-- <span>Technical Solutions</span><br/> -->
        </td>
        <td style="width:25%;padding:5px;" class="desktop-column" >
          <a class="mdi mdi-email" :href="'mailto:'+data.email">&nbsp;{{ data.email }}</a><br/>
          <a class="mdi mdi-cellphone" :href="'tel:'+data.mobile_number">&nbsp;{{ data.mobile_number }}</a><br/>
          <span class="mdi mdi-deskphone" v-if="data.land_ext">&nbsp;{{ data.land_ext }}</span><br/>
        </td>
        <td style="width:25%;padding:5px;" class="desktop-column">
          <span v-if="data.reporting_manager_name != ''" class="mdi mdi-account">&nbsp;Reports to {{ data.reporting_manager_name }}</span><br/>
          <span v-if="data.direct_reportees > 0" class="mdi mdi-family-tree">&nbsp;</span><span v-if="data.direct_reportees > 0">{{ data.direct_reportees }} direct reports</span><br/>
        </td>
        <td class="mobile-column" >
          <span v-if="this.permissions.includes('View-Employee-Detail')" style="font-weight:bold;font-size: 12px;" class="emp-name" @click="reportingEmployeeDetails(data.empid, data.email)">{{ data.name }}</span>
          <span v-else style="font-weight:bold;font-size: 12px;">{{ data.name }}</span><br/>
          <a class="mdi mdi-email" :href="'mailto:'+data.email">&nbsp;{{ data.email }}</a><br/>
          <a class="mdi mdi-cellphone" :href="'tel:'+data.mobile_number">&nbsp;{{ data.mobile_number }}</a><br/>
           <img v-if="countryReformat(data.location ) == 'singapore'" class="flagMobile" :src="singapore">
          <img v-if="countryReformat(data.location ) == 'india'" class="flagMobile" :src="india">
          <img v-if="countryReformat(data.location ) == 'china'" class="flagMobile" :src="china">
          <img v-if="countryReformat(data.location ) == 'australia'" class="flagMobile" :src="australia">
          <img v-if="countryReformat(data.location ) == 'hongkong'" class="flagMobile" :src="hongkong">
          <img v-if="countryReformat(data.location ) == 'japan'" class="flagMobile" :src="japan">
          <img v-if="countryReformat(data.location ) == 'philippines'" class="flagMobile" :src="philippines">
          <img v-if="countryReformat(data.location ) == 'malaysia'" class="flagMobile" :src="malaysia">
          <span>{{ data.location == data.city ? data.location : data.city + ' - ' + data.location }}</span><br/>
        </td>
        <!-- <td v-if="this.permissions.includes('Edit-User')">
          <div class="row d-flex justify-content-center">
            <div class="icon">
              <span
                class="mdi mdi-pencil edit"
                @click="editemployee(data.empid)"
              ></span>
            </div>
          </div>
        </td> -->
      </tr>
      </table>
    </div>  
    <div class="preview" v-if="active" >
      <div class="viewer-overlay"></div>
      <div  class="pdf-background" v-if="pdfView"></div>
       <div class="" :class="`${pdfView ? 'close-btn' : 'close-btn1'}`">
        <button type="button" class="btn btn-sm close-button" @click="close()">
          <span class="mdi mdi-close-circle cancel"></span>
        </button>
      </div>
      <div class="pdf-holder" v-if="pdfView">
        <div class="pdf-topbar">
          <div class="page-navigation">
            <button type="button" class="btn btn-dark btn-sm nav-button mx-2 firstBtn" @click="changePage('first')">
              First Page
            </button>
            <button type="button" class="btn btn-dark btn-sm nav-button previousBtn" @click="changePage('previous')">
              <div class=" nav-icon mdi mdi-menu-left"></div>
            </button>
            <label class="page-index" style="color: #000">{{currentPage}}/{{numPages}}</label>
            <button type="button" class="btn btn-dark btn-sm nav-button nextBtn" @click="changePage('next')">
              <div class=" nav-icon mdi mdi-menu-right"></div>
            </button>
            <button type="button" class="btn btn-dark btn-sm nav-button mx-2 lastBtn" @click="changePage('last')">
              Last Page
            </button>
          </div>
        </div>
        <div class="pdf-viewer-holder">
          <pdf class="pdf-viewer" :src="pdfFile" :page="currentPage" :resize="true" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import pdfvuer from 'pdfvuer';
import all from '../../assets/all.png';
import australia from '../../assets/australia.png';
import china from '../../assets/china.png';
import hongkong from '../../assets/hong-kong.png';
import india from '../../assets/india.png';
import japan from '../../assets/japan.png';
import malaysia from '../../assets/malaysia.png';
import philippines from '../../assets/philippines.png';
import singapore from '../../assets/singapore.png';
import { encryptData } from '../../js/utils/encrypt';
import { decryptData } from '../../js/utils/encrypt';
import { routeNames } from '../../js/routeNames';

export default {
  data() {
    return {
      loading: false,
      employees: [],
      search: '',
      selected: 'All',
      filter: 'All',
      numPages: 0,
      currentPage: 1,
      pdfFile: this.pdfFile || '/example.pdf',
      pdfView: false,
      active: false,
      orgFilter: 'org',
      files: [],
      all: all,
      australia: australia,
      china: china,
      hongkong: hongkong,
      india: india,
      japan: japan,
      malaysia: malaysia,
      philippines: philippines,
      singapore: singapore,
      activeHolidayTab: 0,
      flags: [
        {flag: [all,'All']},
        {flag: [australia,'Australia']},
        {flag: [china,'China']},
        {flag: [hongkong,'Hong Kong']},
        {flag: [india,'India']},
        {flag: [japan,'Japan']},
        {flag: [malaysia,'Malaysia']},
        {flag: [philippines,'Philippines']},
        {flag: [singapore,'Singapore']}
      ],
      permissions: [],
    };
  },
  components: {
    pdf: pdfvuer
  },
  computed: {
    routeNames: () => {
      return routeNames;
    },
    filteredList() {
      var refined = this.employees;
      if (this.filter) {
        refined = this.searchEmployeesByLocation(this.filter);
      } 
      if (this.search) {        
        refined = this.searchEmployees(refined);
      } 
      // refined = this.sortEmployees(refined);
      return refined;
    },
    orgChartData() {
      var refined;
      if (this.orgFilter) {
        var allData = this.searchFilesByLocation('org');
        refined = allData;
      } 
      refined = this.sortFiles(refined);
      return refined;
    }
  },
  mounted(){
    let mkLocalData = localStorage.getItem('Permissions');
    if (!mkLocalData) {
      mkLocalData = 'null';
    }
    const secretPermissionKey = process.env.KEY || 'myscecretkey';
    this.permissions = decryptData(mkLocalData, secretPermissionKey);

    let mkLocalRole = localStorage.getItem('Role');
    if (!mkLocalRole) {
      mkLocalRole = 'null';
    }
    let role = decryptData(mkLocalRole, secretPermissionKey);

    this.loading = true;
    axios.get(this.$hostName + '/allemployees', { params: { role }}).then((response) => {
      this.employees = response.data.employees;
        axios.get(this.$hostName + '/files').then((response) => {
          response.data.forEach((element) => {
              this.files.push(element);
          });
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    editemployee(empid) {
      this.$router.push({
        name: routeNames.editEmployee,
        params: {
          empid
        },
      });
    },
    reportingEmployeeDetails(emp_id, emp_email) {
      if (emp_id != '' && emp_email != '') {
        const secretKey = process.env.KEY || 'myscecretkey';
        const encryptedEmpId = encryptData(emp_id, secretKey);
        let currentUrl = window.location.href;
        let directReportUrl = '';
        if (this.$route.name == routeNames.People) {
          directReportUrl = currentUrl.replace(routeNames.People, routeNames.directreport);
        } else {
          directReportUrl = currentUrl.substring(0, currentUrl.indexOf(routeNames.directreport) + routeNames.directreport.length);
        }
        window.open(`${directReportUrl}/${encodeURIComponent(encryptedEmpId)}`);
      }
    },
    countryReformat(data){
      var str = '';
      try {
        var countryName = data.toLowerCase();
        str = countryName.replace(/\s/g, '');
      } catch (error) {
        str = '';
      }
      return str;
    },
    sortFiles(fileList) {
      return fileList.sort((a, b) => {
        if(a[1] < b[1]) return -1;
        if(a[1] > b[1]) return 1;
        return 0; });
    },
    searchFilesByLocation(location) {
      return this.files.filter((file) => {
        var fileLoc = (
          file['foldername']
            .toString()
            .toLowerCase()
            .includes(location.toLowerCase())
        );
        return fileLoc;
      });
    },
    close(){
      this.active = false;
      this.pdfView = false;
      this.currentPage = 1;
    },
    openDocument(data) {
      var formData = {
            folderName: data['foldername'],
            fileName: data['filename']
      };
      axios({
        url: this.$hostName + '/files/download',
        method: 'POST',
        responseType: 'blob',
        data: formData,
        }).then((response) => {
            const binaryResponse = new Blob([response.data]);
            var fileURL = window.URL.createObjectURL(binaryResponse);
            this.pdfFile = fileURL;
            var self = this;
            self.pdfdata = pdfvuer.createLoadingTask(fileURL);
            self.pdfdata.then(pdf => {
            this.numPages = pdf.numPages;
      });
        }).finally(() => {
            this.active =  true;
            this.pdfView =  true;
        });  
    },
    getImgUrl(filename){
      try{
        if(filename != null && filename != '')
          return filename;
        else return require('../../assets/imagealt.png');
      }catch(_){
        return require('../../assets/imagealt.png');
      }
    },
    searchEmployees(employeeList) {
      if (!this.search) return employeeList;
      return employeeList.filter((employee) => {
        let searchString = `${employee.name} ${employee.position} in ${employee.department} ${employee.city} - ${employee.location} ${employee.email} ${employee.mobile_number} ${employee.land_ext}`;
        return (
          searchString
            .toString()
            .toLowerCase()
            .includes(this.search.toString().toLowerCase()) 
        );
      });
    },
    searchEmployeesByLocation(location) {
      if (location.toLowerCase() == 'all') return this.employees;
      return this.employees.filter((employee) => {
        return (
          employee.location
            .toString()
            .toLowerCase()
            .includes(location.toLowerCase())
        );
      });
    },
    sortEmployees(employeeList) {
      return employeeList.sort((a, b) => {
        if(a[4] < b[4]) return -1;
        if(a[4] > b[4]) return 1;
        return 0; });
    },
     resetActiveCountry(index,flag) {
      this.activeHolidayTab = index;
      this.filter = flag.flag[1];
      this.search = '';
    },
    changePage(type){
      if (type == 'next') {
        if(this.currentPage<this.numPages)
          this.currentPage = this.currentPage+1;
      } else if(type == 'previous') {
        if(this.currentPage>1)
          this.currentPage = this.currentPage-1;
      } else if (type == 'first') {
        this.currentPage = 1;
      } else if (type == 'last') {
        this.currentPage = this.numPages;
      }
    },
  }
};
</script>

<style scoped lang="scss">
.dashboard{
  font-family: 'Montserrat';
}
.title, .search , .country-filter{
  position: relative;
  left: 60px;
  width: calc(100% - 60px);
}
.flag-img{
  border-radius: 50%;
  -webkit-border-radius: 50%;
  height: 37px;
  width: 37px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}
.country-filter{
  width: calc(100% - 60px);
  text-align: center;
}

/* country flags */
.edit {
  color: blue;
  margin-right: 10px;
}

.flag-text-holder {
  position: absolute;
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
  display: none;
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
  z-index: 998;
  display: inline-block;
}

.countryFlags{
  padding: 2px;
  display: inline-flex;
  height: 45px;
  width: 45px;
  position: relative;
  margin: 2px 5px 2px 5px;
  cursor: pointer;
  border: 2px solid transparent;
}
.activeCountry {
    border-bottom: 2px #122e55 solid;
    font-weight: bold;
}
.flag{
  width: 30px;
  height: 30px;
}
.flagMobile{
  width: 20px;
  height: 20px;
  margin-right: 5px;
}
.viewer-overlay{
  background-color: rgba(0, 0, 0, 0.5);
  height: 100%;
  width: calc(100% - 60px);
  left: 60px;
  position: absolute;
  top: 0;
  backdrop-filter: blur(5px);
}
.pdf-background{
  background-color: #505257;
  height: 90vh;
  width: 80%;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%,-50%);
}
.close-btn{
  position: absolute;
  top: 76px;
  left: 88%;
  z-index: 999;
}
.close-btn1{
   position: absolute;
  top: 8px;
  left: 90%;
  z-index: 999;
}
.close-button{
  background-color: white;
  height: 20px;
  width: 20px;
  position: absolute;
  border-radius: 50%;
}
.cancel{
  font-size: 30px;
  color: #d82d28;
  left: 50%;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
}
.cancel:hover{
  color: #b60a04;
}
.page-index{
  width: 65px;
}
.pdf-topbar{
  width: 32%;
  height: 5vh;
  position: absolute;
  top: 3.5rem;
  left: 50%;
  transform: translate(-50%);
  background-color: white;
  text-align: center;
  border-radius: 5px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
}
.page-navigation{
  width: 100%;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  color: white;
  top: 50%;
  transform: translate(-50%,-50%);
}
.nav-button{
  height: 30px;
}
.pdf-viewer{
  width: 45%;
  position: absolute;
  left: 50%;
  top: 53%;
  transform: translate(-50%,-50%);
}
.org-chart{
  position: relative;
  width: 10%;
  text-align: center;
}
.org-button{
  height: 30px;
  line-height: 30px;
  padding: 0 5px 0 5px;
    background-color: #e8e8e8;
}
.org-chart-icon{
  font-size: 20px;
  padding: 2px;
}
.peopleImg{
  width: 100px;
  height: 100px;
  border-radius:50%;
}
.peopleShortName{
  width: 100px;
  height: 100px;
  border-radius:50%;
  // background-color: #122e55;
  font-size: 50px;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
}
a{
  color: black;
}
.audit-table {
  position: absolute;
  left: calc(60px + 2%);
  right: 2%;
  height: 71.7vh;
  overflow: auto;
  padding: 10px;
  font-size: 12px;
}
.radio-btn{
  margin-left: 5%;
}
.audit-reports {
  background-color: #F0F7FF;
  padding-top: 10px;
  padding-bottom: 10px;
  margin-bottom: 10px;
  margin-left: 1%;
  margin-right: 1%;
}
.report-title {
  margin-left: 2%;
}
.report-btn {
  margin-right: 2%;
}
th{
  margin: 0;
}
tr {
  text-align: left;
  border-width: 1px 0;
}
td {
  text-align: left;
}
td:first-child {
  vertical-align: middle !important;
}
thead {
  top: 0;
  position: sticky;
}

.table {
  width: 98%;
  margin-left: auto;
  margin-right: auto;
}

.icon {
  font-size: 25px;
}

.search {
  width: calc(100% - 120px);
  flex-grow: 1;
  height: 100%;
  position: relative;
  display: flex;
  margin-left: 2%;

  .icon {
    width: 40px;
    height: 100%;
    font-size: 24px;
    color: #000;
    position: relative;
    float: left;

    display: flex;
    align-items: center;
    justify-content: center;
  }

  input {
    min-width: 150px;
    height: 100%;
    position: relative;
    float: left;
    font-size: 14px;
    -webkit-appearance: none;
    outline: 0;
    border-width: 0 0 2px;
    border-color: rgb(40, 40, 102);
    background-color: transparent;
    color: $general-black-light;
    flex-grow: 1;

    &::placeholder {
      opacity: 0.6;
      font-style: italic;
    }
  }
}
.location {
  width: 10%;
  float: right;
}
.mobile-column{
  display: none;
  
}
.desktop-column{
  vertical-align:top;
}
@media (min-width: 768px) and (max-width: 991.98px) { 
   .previousBtn{
    width: 30px !important;
    height: 30px !important;
    font-size: 12px !important;
  }
  .nextBtn{
     width: 30px !important;
    height: 30px !important;
    font-size: 12px !important;
  }
  .firstBtn{
    width: 80px !important;
    height: 30px !important;
    font-size: 12px !important;
  }
  .lastBtn{
    width: 80px !important;
    height: 30px !important;
    font-size: 12px !important;
  }
.audit-table {
  height: 83vh !important;
  position: relative;
  width: calc(100% - 60px);
  left: 60px !important;
  right: 0% !important;
  overflow: auto;
  padding: 0px !important;
  font-size: 12px;
}
  .flag-text{
    display: none;
  }
   .location {
    margin-left: 4%;
  }
  .search {
  width: calc(85% - 60px) !important;
  height: 100%;
  position: relative;
  display: flex;
  margin-left: 2%;

  .icon {
    width: 40px;
    height: 100%;
    font-size: 24px;
    color: #000;
    // opacity: .5;
    position: relative;
    float: left;

    display: flex;
    align-items: center;
    justify-content: center;
  }

  input {
    min-width: 110px !important;
    height: 100%;
    position: relative;
    float: left;
    font-size: 14px;
    -webkit-appearance: none;
    outline: 0;
    border-width: 0 0 2px;
    border-color: rgb(40, 40, 102);
    background-color: transparent;
    color: $general-black-light;
    flex-grow: 1;

    &::placeholder {
      opacity: 0.6;
      font-style: italic;
    }
  }
  }
 .org-chart{
  position: absolute;
  left: 90%;
  top: -40px;
}
.org-button{
  height: 30px;
  line-height: 20px;
  width: 120px;
  padding: 0 5px 0 5px;
}
.pdf-background{
  z-index: -1;
}
.pdf-topbar{
  width: 70%;
  height: 40px;
  position: absolute;
  top: 300px;
  left: 50%;
  transform: translate(-50%);
  background-color: white;
  text-align: center;
  border-radius: 5px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
}
.page-navigation{
  width: 100%;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  color: white;
  top: 4px;
}

.pdf-viewer{
  width: 70%;
  position: absolute;
  left: 50%;
  top: 52%;
  transform: translate(-50%,-50%);
}
.close-btn{
  position: absolute;
  top: 200px !important;
  left: 88% !important;
  z-index: 999;
}

.close-button{
  background-color: white;
  height: 40px;
  width: 40px;
  position: absolute;
  border-radius: 50%;
}
.cancel{
  font-size: 50px;
  color: #d82d28;
  left: 50%;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
}
.cancel:hover{
  color: #b60a04;
}
}
@media only screen and (max-width: 575.98px) {
  .edit {
    font-size: 18px;
  }
  .flag-text-holder .flag-text {
    width: 60px !important;
    margin-left: -17px !important;
    font-size: 8px !important;
  }
  .flag-img{
    height: 26px !important;
    width: 26px !important;
  }
  .title{
    width: calc(90% - 60px) !important;
  }
  .previousBtn{
    width: 30px;
    height: 26px;
    font-size: 10px;
  }
  .nextBtn{
     width: 30px;
    height: 26px;
    font-size: 10px;
  }
  .firstBtn{
    width: 70px;
    height: 26px;
    font-size: 10px;
  }
  .lastBtn{
    width: 70px;
    height: 26px;
    font-size: 10px;
  }
  .page-navigation{
  width: 100%;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  color: white;
  top: 8px;
}
    .page-index{
  width: 65px !important; 
}
  .countryFlags{
  padding: 2px;
  display: inline-flex;
  height: 35px;
  width: 35px;
  position: relative;
  margin: 2px 0px 2px 0px;
  cursor: pointer;
  border: 2px solid transparent;
}
  .flag-text{
    display: none;
  }
  .activeCountry {
    border-bottom: 2px #122e55 solid;
    font-weight: bold;
}
  .pdf-background{
  z-index: -1;
}
.pdf-topbar{
  width: 82%;
    height: 44px;
    position: absolute;
    top: 140px;
    left: calc(50% + 30px) !important;
    transform: translate(-50%) !important;
    background-color: white;
    text-align: center;
    border-radius: 5px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
}

 .pdf-viewer{
    left: calc(50% - -30px) !important;
    top: 423px !important;
    width: 300px !important;
  }
  .close-btn{
    position: absolute;
    top: 10%;
    left: 88%;
    z-index: 999;
  }
  .close-btn1{
    position: absolute;
    top: 8px;
    left: 90%;
    z-index: 999;
  }
  .close-button{
    background-color: white;
    height: 20px;
    width: 20px;
    position: absolute;
    border-radius: 50%;
  }
.cancel{
  font-size: 30px;
  color: #d82d28;
  left: 50%;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
}
.cancel:hover{
  color: #b60a04;
}
  .org-chart{
  position: absolute;
  left: 55%;
  top: -40px;
}
.org-button{
  height: 26px;
  width: 115px !important;
  line-height: 15px;
  padding: 0 5px 0 5px;
  background-color: #e8e8e8;
}
.org-chart-icon{
  font-size: 16px;
  padding: 2px;
}
.audit-table {
  height: 600px !important;
  position: absolute;
  left: 60px !important;
  right: 0% !important;
  overflow: auto;
  padding: 0px !important;
  font-size: 12px;
}
.peopleImg{
  width: 50px;
  height: 50px;
}
.peopleShortName{
  width: 50px;
  height: 50px;
  font-size: 25px;
}
.location {
  margin-left: 4%;
}
.search {
width: calc(95% - 60px) !important;
height: 100%;
position: relative;
display: flex;
margin-left: 1%;

.icon {
  width: 40px;
  height: 100%;
  font-size: 24px;
  color: #000;
  position: relative;
  float: left;

  display: flex;
  align-items: center;
  justify-content: center;
}

input {
  min-width: 110px !important;
  height: 100%;
  position: relative;
  float: left;
  font-size: 14px;
  -webkit-appearance: none;
  outline: 0;
  border-width: 0 0 2px;
  border-color: rgb(40, 40, 102);
  background-color: transparent;
  color: $general-black-light;
  flex-grow: 1;

  &::placeholder {
    opacity: 0.6;
    font-style: italic;
  }
}
}
.mobile-column{
  display: block;
  vertical-align:top;
  width: 100%;
}
.desktop-column{
  display: none;
}
}
.emp-name {
  cursor: pointer;
}
.emp-name:hover{
    color: #EE3234;
    text-decoration: underline;
}
</style>