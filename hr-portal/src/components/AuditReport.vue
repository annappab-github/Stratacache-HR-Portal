<template>
    <div class="audit-report-container">
      <div class="title">
        <h1 class="mt-4">Audits</h1>
      </div>
      <div class="gate-manager">
        <div class="top-header">
          <div class="search">
            <div class="icon">
              <span class="mdi mdi-magnify"></span>
            </div>
            <input
              class="search-bar"
              placeholder="Search Audits"
              v-model="search"
            />
          </div>
        </div>
      </div>
      <div class="loader" v-if="loading"></div>
      <div class="row audit-reports">
        <div class="col-sm-2">
          <h6 class="report-title">
            Audits:
          </h6>
        </div>
        <div class="col-sm-5">
          <Datepicker
            v-model="selectedDate"
            range
            :format="dateFormat"
            :placeholder="[[currentDate]] + ' ~ ' + [[currentDate]]"
          ></Datepicker>
        </div>
        <div class="col-sm-3">
          <input
            class="radio-btn"
            type="radio"
            id="PDF"
            value="pdf"
            v-model="fileFormat"
          />
          <label for="PDF">PDF</label>
          <input
            class="radio-btn"
            type="radio"
            id="CSV"
            value="csv"
            v-model="fileFormat"
          />
          <label for="CSV">CSV</label>
          <input
            class="radio-btn"
            type="radio"
            id="EXCEL"
            value="xlsx"
            v-model="fileFormat"
          />
          <label for="EXCEL">EXCEL</label>
        </div>
        <div class="col-sm-2">
          <button
            class="btn float-right report-btn"
            @click="downloadReports"
          >
            Download Reports
          </button>
        </div>
      </div>
      <div class="audit-table">
        <table class="table">
          <thead>
            <tr class="table-header">
              <th style="width: 7%">User</th>
              <th style="width: 5%">Audit Type</th>
              <th style="width: 7%">Audit ID</th>
              <th style="width: 10%">Old Values</th>
              <th style="width: 20%">New Values</th>
              <th style="width: 7%">Event</th>
              <th style="width: 10%">Time</th>
              <th style="width: 7%">IP Address</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in searchAudit" :key="idx">
              <td>{{ user(item.user_id) }}</td>
              <td>
                {{ auditType(item.auditable_type) }}
              </td>
              <td>{{ item.auditable_id }}</td>
              <td class="oldData">{{ dataFormat(item.old_values)}}</td>
              <td class="newData">{{ dataFormat(item.new_values)}}</td>
              <td>{{ item.event }}</td>
              <td>{{ timeFormat(item.created_at) }}</td>
              <td>{{ item.ip_address }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</template>
<script>
import axios from 'axios';
import moment from 'moment';
import Datepicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
import { decryptData } from '../js/utils/encrypt';
export default {
  components: {
    Datepicker,
  },
  data() {
    return {
      loading: false,
      data: {
        audit: '',
        user: '',
        role: '',
      },
      selectedDate: '',
      fileFormat: 'pdf',
      date: '',
      currentDate: '',
      dateFormat: 'yyyy-MM-dd',
      search: '',
      userName: '',
      email: '',
      employees: [],
      loggedInUser: []
    };
  },
  setup() {
  },
  computed: {
    searchAudit() {
      var tempData = this.data.audit;
      if (!this.search) return tempData;
      return tempData.filter((item) => {
        return (
          (item.event &&
            item.event
              .toString()
              .toLowerCase()
              .includes(this.search.toString().toLowerCase())) ||
          (item.ip_address &&
            item.ip_address
              .toString()
              .toLowerCase()
              .includes(this.search.toString().toLowerCase())) ||
          (item.updated_at &&
            item.updated_at
              .toString()
              .toLowerCase()
              .includes(this.search.toString().toLowerCase())) ||
          (item.auditable_type &&
            item.auditable_type
              .toString()
              .toLowerCase()
              .includes(this.search.toString().toLowerCase()))
        );
      });
    },
  },
  mounted() {
    let today = new Date();
    this.currentDate = moment(today).format(this.dateFormat);
    var encryptedEmailID = localStorage.getItem('Email');
    const secretKey = process.env.KEY || 'myscecretkey';
    this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');
    var loggedInUserArr = [];
    // this.loading = true;
    if(this.email!='admin@scala.com'){
      axios.get(this.$hostName + '/employees').then((response) => {
        response.data.forEach((element) => {
            this.employees.push(element);
            if(element[10]==this.email){
                loggedInUserArr.push(element);
            }
        });
        for (let k = 0; k < loggedInUserArr[0].length; k++) {
          this.loggedInUser.push(loggedInUserArr[0][k]);
        }
        this.userName = this.loggedInUser[4];
      })
      .catch((error) => {
        console.log(error);
      });
    } else {
      this.userName = 'Admin';
    }
    axios
      .get(this.$hostName + '/audit')
      .then((response) => {
        this.data.audit = response.data.audit;
        this.data.user = response.data.user;
        this.data.role = response.data.role;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
  },
   methods: {
    dataFormat(str){
      if (str == null){
        return;
      }
      else if(str.constructor.name == 'Array'){
        return;
      }
      else if(str.hasOwnProperty('password')){
        delete str['password'];
        // return 'Encrypted Password';
        str.password = 'Encrypted Password';
        const myJSON = JSON.stringify(str);
        var quoteString2 = myJSON.replaceAll('"','');
        var bracesString2 = quoteString2.replaceAll('{','');
        var braces2String2 = bracesString2.replaceAll('}','');
        var slashString2 = braces2String2.replaceAll('\\','');
        var commaString2 = slashString2.split(',').join('\n');
        return commaString2;
      }
      else if(Object.keys(str).length == 0){
        return 'Encrypted Password';
      }
      else{
        const myJSON = JSON.stringify(str);
        var quoteString = myJSON.replaceAll('"','');
        var bracesString = quoteString.replaceAll('{','');
        var braces2String = bracesString.replaceAll('}','');
        var slashString = braces2String.replaceAll('\\','');
        var commaString = slashString.split(',').join('\n');
        return commaString;
      }

    },
    user(id) {
      for (let index = 0; index < this.data.user.length; index++) {
        if (this.data.user[index].id == id) {
          return this.data.user[index].name;
        }
      }
    },
    downloadReports() {
      const fromDate = moment(this.selectedDate[0]).format('YYYY-MM-DD');
      const toDate = moment(this.selectedDate[1]).format('YYYY-MM-DD');
      const fileType = this.fileFormat;
      var formData = {
        fDate: fromDate,
        tDate: toDate,
        filter: this.filter,
        uName: this.userName,
        rName: 'Audit List',
        rType: 'audit',
        fType: fileType,
      };
      this.loading = true;
      axios({
        url: this.$hostName + '/downloadReport',
        method: 'POST',
        responseType: 'blob',
        data: formData,
      })
        .then((response) => {
          const binaryResponse = new Blob([response.data]);
          if (binaryResponse.size > 10) {
            var fileURL = window.URL.createObjectURL(binaryResponse);
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            var fileName =
              'Audit_' +
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
        })
        .finally(() => {
          this.loading = false;
        });
    },
    auditType(type) {
      if (type == 'App\\Models\\User') {
        return 'User';
      } else if (type == 'Spatie\\Permission\\Models\\Role') {
        return 'Role';
      }
    },
    timeFormat(time) {
      return moment(String(time)).format('YYYY-MM-DD hh:mm:ss');
    },
  },
};
</script>

<style scoped lang="scss">
.audit-report-container{
  font-family: 'Montserrat';
  position: absolute;
  left: 60px;
  width: calc(100% - 60px);
}
.table-header{
  border: none;
}
td.oldData{
  text-align: start;
}
td.newData{
  text-align: start;
}
.btn{
  background-color: #f85e5e;
  color: white;
}
.title {
  margin-left: 2%;
}
.audit-table {
  height: 475px;
  overflow: auto;
  margin-left: 2%;
  margin-right: 4.5%;
}
.radio-btn {
  margin-left: 5%;
}
.audit-reports {
  background-color: #ac0f11;
  color: white;
  padding-top: 10px;
  padding-bottom: 10px;
  margin-bottom: 10px;
  margin-left: 2%;
  margin-right: 4.5%;
}
.report-title {
  margin-left: 2%;
}
.report-btn {
  margin-right: 2%;
}
th {
  margin: 0;
}
tr {
  text-align: center;
}
td {
  text-align: center;
  vertical-align: baseline !important;
}
thead {
  top: 0;
  position: sticky;
}

.table {
  width: 100%;
  margin-left: auto;
  margin-right: auto;
}

.icon {
  font-size: 25px;
}

.gate-manager {
  width: 100%;
  height: 100%;
  position: relative;
  background-color: #fff;
  margin-left: 2%;
  display: flex;
  flex-direction: column;

  .top-header {
    width: 100%;
    height: auto;
    min-height: 60px;
    padding: 0 10px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;

    .search {
      width: auto;
      flex-grow: 1;
      height: 100%;
      position: relative;
      display: flex;

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
        width: 220px;
        height: 100%;
        position: relative;
        float: left;
        font-size: 14px;
        -webkit-appearance: none;
        outline: none;
        box-shadow: none;
        border: none;
        background-color: transparent;
        
        color: $general-black-light;
        flex-grow: 1;

        &::placeholder {
          opacity: 0.6;
          font-style: italic;
        }
      }
    }
  }
}
@media only screen and (max-width: 575.98px) {
.audit-table {
  height: 475px;
  overflow-x: auto;
  padding: 0px !important;
  position: relative;
  left: -20px !important;
  width: 100%;
}
.table {
  width: 100% !important;
  left: 20px !important;
  position: absolute !important;
}

}
</style>