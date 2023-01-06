<template>
  <div class="manage-files">
    <div>
      <div class="title">
        <h1 class="">Files</h1>
      </div>
      <div class="search">
        <div class="icon">
          <span class="mdi mdi-magnify" style="color: #000"></span>
        </div>
        <input
              class="search-bar"
              placeholder="search by filename..."
              v-model="search"
        />
        <!-- <div class="location">
          <select name="fileLocation" v-model="selected" @change="updatefilter($event)">
              <option value="all">All</option>
              <option value="aus">Australia</option>
              <option value="ind">India</option>
              <option value="jap">Japan</option>
              <option value="sg">Singapore</option>
          </select>
        </div> -->
      </div>
    </div>
    <div class="loader" v-if="loading"></div>
    
    <div class="files-table">
      <table class="table">
      <tr v-for="data in filteredList" :key="data">
        <td style="width:80%;padding:0px;">
          <span v-if="data['filename'].endsWith('pdf')" class="mdi mdi-file-pdf-box mr-2 pdfIcon"></span>
          <span v-if="data['filename'].endsWith('docx')" class="mdi mdi-file-word mr-2 docxIcon"></span>
          <span v-if="data['filename'].endsWith('png')" class="mdi mdi-file-image mr-2 imgIcon"></span>
          <span v-if="data['filename'].endsWith('jpg')" class="mdi mdi-file-image mr-2 imgIcon"></span>
          <span v-if="data['filename'].endsWith('jpeg')" class="mdi mdi-file-image mr-2 imgIcon"></span>
          <span v-if="data['filename'].endsWith('xlsx')" class="mdi mdi-file-excel mr-2 excelIcon"></span>
          <span v-if="data['filename'].endsWith('txt')" class="mdi mdi-note-text mr-2 textIcon"></span>
          <label class="fileName">{{ data['filename'] }}</label>
          <div class="btn-holder">
            <div>
              <button type="button" v-if="data['filename'].endsWith('pdf')" class="btn  btn-sm mt-2 previewBtn" @click="openDocument(data)">Preview</button>
            </div>
            <div>
              <button type="button" v-if="data['filename'].endsWith('txt')" class="btn btn-sm mt-2 previewBtn" @click="openText(data)">Preview</button>
            </div>
            <div>
              <button type="button" v-if="data['filename'].endsWith('png') || data['filename'].endsWith('jpg') || data['filename'].endsWith('jpeg')" class="btn  btn-sm mt-2 previewBtn" @click="openImage(data)">Preview</button>
            </div>
            <div>
              <button type="button" class="btn btn-dark btn-sm mt-2 downloadBtn" @click="downloadFile(data)">Download</button>
            </div>
          </div>
          
        </td>
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
        <div class="doc-loader" v-if="docLoading"></div>
        <div class="pdf-topbar">
          <div class="page-navigation">
            <button type="button" class="btn btn-dark btn-sm nav-button mx-2" @click="changePage('first')">
              First Page 
            </button>
            <button type="button" class="btn btn-dark btn-sm nav-button" @click="changePage('previous')">
              <div class=" nav-icon mdi mdi-menu-left"></div>
            </button>
            <label class="page-index" >{{currentPage}}/{{numPages}}</label>
            <button type="button" class="btn btn-dark btn-sm nav-button" @click="changePage('next')">
              <div class=" nav-icon mdi mdi-menu-right"></div>
            </button>
            <button type="button" class="btn btn-dark btn-sm nav-button mx-2" @click="changePage('last')">
              Last Page
            </button>
          </div>
        </div>
        <div class="pdf-viewer-holder">
          <pdf class="pdf-viewer" :src="pdfFile" :page="currentPage" :resize="true" />
        </div>
      </div>

      <div v-if="imgView">
        <div class="image-holder">
          <img class="image" :src="imgFile" alt="">
          <div class="image-loader" v-if="imageLoading"></div>
        </div>
      </div>

      <div v-if="txtView">
        <div class="text-holder">
          <object class="text-container" :data="txtFile"></object>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';
import { decryptData } from '../js/utils/encrypt';
import pdfvuer from 'pdfvuer';

export default {
  data() {
    return {
      loading: false,
      files: [],
      search: '',
      selected: 'all',
      filter: 'all',
      employees: [],
      loggedInUser: [],
      location: '',
      email: '',
      pdfFile: this.pdfFile || '/example.pdf',
      imgFile: this.imgFile || '../assets/loading.gif',
      active: false,
      numPages: 0,
      currentPage: 1,
      pdfdata: undefined,
      pdfView: false,
      imgView: false,
      txtView: false,
      imageLoading: false,
      docLoading: false,
      txtFile: this.txtFile || '/example.txt',
      employeeId: ''
    };
  },
  components: {
    pdf: pdfvuer
  },
  computed: {
    filteredList() {
      var refined = this.files;
      if (this.filter) {
        var allData = this.searchFilesByLocation('all');
        refined = this.searchFilesByLocation(this.filter);
        var finalData = [refined.length+allData.length];
        finalData = refined;
        for (let i = 0; i < allData.length; i++) {
          finalData.push(allData[i]);
        }
        allData.concat(finalData);
        refined = finalData;
      } 
      if (this.search) {
        refined = this.searchFiles(refined);
      } 
      refined = this.sortFiles(refined);
      return refined;
    }
  },
  mounted(){
    this.loading = true;
    var encryptedEmailID = localStorage.getItem('Email');
    const secretKey = process.env.KEY || 'myscecretkey';
    this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');
    var encryptedEmployeeID = localStorage.getItem('EmployeeID');
    if(encryptedEmployeeID != null){
      this.employeeId = decryptData(encryptedEmployeeID, secretKey).replace(/['"]+/g, '');
    }
    this.filter = this.employeeId.substring(0, 2).toLowerCase();
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
  },
  methods: {
    imageLoad(){
       return this.imgFile;
    },
    close(){
      this.active = false;
      this.pdfView = false;
      this.imgView = false;
      this.txtView = false;
      this.currentPage = 1;
      this.imgFile = '';
    },
    openImage(data){
      this.imageLoading = true;
      this.imgView = true;
      this.active = true;
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
            this.imgFile = fileURL;
        }).finally(() => {
            this.active =  true;
            this.imgView =  true;
            this.imageLoading = false;
        }); 
    },
    openText(data){
      this.txtView = true;
      this.active = true;
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
            this.txtFile = fileURL;
        }).finally(() => {
            this.active =  true;
            this.txtView =  true;
        }); 
    },
    openDocument(data) {
      var formData = {
            folderName: data['foldername'],
            fileName: data['filename']
      };
      this.active =  true;
      this.docLoading = true;
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
            this.pdfView =  true;
        }).finally(() => {
            this.docLoading = false;
        });  
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
    getImgUrl(filename){
      try{ 
        return require(`C:/ProgramData/Scala/hrm-portal/assets/employee/pics/${filename}`);
      }catch(_){
        return require('../assets/imagealt.png');
      }
    },
    searchFiles(fileList) {
      if (!this.search) return fileList;
      return fileList.filter((file) => {
        return (
          file['filename']
            .toString()
            .toLowerCase()
            .includes(this.search.toString().toLowerCase())
        );
      });
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
    updatefilter(event) {
      this.filter = event.target.value;
    },
    sortFiles(fileList) {
      return fileList.sort((a, b) => {
        if(a[1] < b[1]) return -1;
        if(a[1] > b[1]) return 1;
        return 0; });
    },
    downloadFile(data) {
        this.loading = true;
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
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            var fileName = data['filename'];
            fileLink.setAttribute('download', fileName);
            document.body.appendChild(fileLink);
            fileLink.click();
            fileLink.remove();
        }).finally(() => {
            this.loading =  false;
        });  
    }
  }
};
</script>

<style scoped lang="scss">
@media only screen and (max-width: 575.98px) {
  .files-table{
    height: 79vh !important;
  }
  .manage-files{
    height: 100% !important;
  }
  .pdf-background{
    z-index: -1;
  }
  .downloadBtn{
    right: 5%;
    position: initial !important;
    width: 100%;
    height: 6%;
    margin-bottom: 4px !important;
    margin-top: 4px !important;
  }
  .previewBtn{
    left: 10px;
    position: initial !important;
    margin-top: 20px;
    height: 6%;
    width: 100%;
    margin-top: 4px !important;
  }
  .btn-holder{
    float: right;
  }
  .fileName{
    font-size: 14px !important;
    width: calc(100% - 130px) !important;
    margin: 0 !important;
  }
  .pdfIcon{
    font-size: 26px !important;
    color: #F70000;
  }
  .docxIcon{
  font-size:  26px !important;
  color: #134AC2;
  }
  .imgIcon{
    font-size: 26px !important;
    color: #b41ce6;
  }
  .excelIcon{
  font-size:  26px !important;
  color: green;
  }
  .textIcon{
  font-size:  26px !important;
  }
  .nav-button{
    font-size: 12px;
  }
  .pdf-topbar{
    width: 96% !important;
    height: 54px;
    position: absolute;
    top: 140px !important;
    left: calc(50%) !important;
    transform: translate(-50%) !important;
    background-color: white;
    text-align: center;
    border-radius: 5px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }
  .page-navigation{
    width: 110% !important;
    color: black !important;
  }
  .pdf-viewer{
    left: calc(50%) !important;
    top: 423px !important;
    width: 300px !important;
  }
  .image-holder{
    left: calc(50%) !important ;
  }
  .text-holder{
    background-color: white;
    left: calc(50%) !important ;
    width: 80% !important;
    height: 80% !important;
  }
  .close-btn{
    position: absolute;
    top: 50px !important;
    left: 90% !important;
    z-index: 999;
  }
  .close-btn1{
    position: absolute !important;
    top: 36px !important;
  }
  .cancel{
    font-size: 40px;
    color: #d82d28;
    position: absolute;
    top: 50% ;
  }
  .cancel:hover{
    color: #b60a04;
  }
}
@media (min-width: 768px) and (max-width: 991.98px) { 
.close-btn1{
  position: absolute !important;
  top: 36px !important;
}
.cancel{
  font-size: 40px !important;
  color: #d82d28;
  position: absolute !important;
}
.cancel:hover{
  color: #b60a04;
}
 .close-btn{
  position: absolute !important;
  top: 50px !important;
  left: 85% !important;
  z-index: 999;
}
.pdf-background{
  z-index: -1;
}
  .pdf-topbar{
    width: 70% !important;
    height: 48px !important;
    position: absolute;
    top: 140px !important;
    background-color: white;
    text-align: center;
    border-radius: 5px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }
  .page-navigation{
     width: 100% !important;
    color: black !important;
    left: 50%;
    transform: translate(-50%);
    position: absolute;
    color: white;
    top: 8px !important;
  }
 
  .pdf-viewer{
    top: 600px !important;
    width: 500px !important;
  }
  
 }
 .pdf-background{
  background-color: #505257;
  height: 97%;
  width: 36%;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%,-50%);
}
.image-loader {
  position: fixed;
  top: 0px;
  width: 100%;
  height: 100%;
  background-color: #eceaea;
  background-image: url("../assets/loading.gif");
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: 50%;
  z-index: 999;
}
.doc-loader{
  // background-color: rgba(0, 0, 0, 0.5);
  background-image: url("../assets/loading.gif");
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: 50%;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  backdrop-filter: blur(5px);
  z-index: 1;
}
.image-holder{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  width: 80%;
  height: 90%;
}
.text-holder{
  position: absolute;
  top: 52%;
  left: 50%;
  background-color: white;
  transform: translate(-50%,-50%);
  width: 60%;
  height: 90%;
}
.text-container{
  height: 100%;
  width: 100%;
}
.image{
  width: 100%;
  height: 100%;
  object-fit: contain
}
.btn-holder{
    float: right;
    position: relative;
  }
.nav-icon{
  font-size: 26px;
  position: relative;
  top: -10px;
}
.close-icon{
  font-size: 26px;
  position: relative;
  left: -7px;
  top: -9px;
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
.nav-button{
  height: 30px;
}
.viewer-overlay{
  background-color: rgba(0, 0, 0, 0.5);
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  backdrop-filter: blur(5px);
}

.pdf-topbar{
    width: 32%;
    height: 40px;
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translate(-50%);
    background-color: white;
    text-align: center;
    border-radius: 5px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }
.pdf-viewer{
  width: 30%;
  position: absolute;
  left: 50%;
  top: 52%;
  transform: translate(-50%,-50%);
}
.page-navigation{
  width: 100%;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  color: white;
  top: 4px;
}

.page-index{
  width: 60px;
  text-align: center;
  color: #000;
}
.close-btn{
  position: absolute;
  top: 4px;
  left: 67.5%;
  z-index: 999;
}
.close-btn1{
   position: absolute;
  top: 8px;
  left: 90%;
  z-index: 999;
}
.fileName{
  font-size: 18px;
  word-break: break-all;
}
.pdfIcon{
  font-size: 30px;
  color: #F70000;
}
.docxIcon{
  font-size: 30px;
  color: #134AC2;
}
.imgIcon{
  font-size: 30px !important;
  color: #b41ce6;
}
.excelIcon{
  font-size: 30px;
  color: green;
}
.textIcon{
font-size: 30px;
}
.downloadBtn{
  right: 5%;
  position: absolute;
}
.previewBtn{
  right: calc(5% + 100px);
  position: absolute;
  background-color: #EE3234;
  color: #fff;
}
.files-table {
  height: 86vh;
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
}
td {
  text-align: left;
  vertical-align: baseline !important;
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
  width: 92.9%;
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
    // opacity: .5;
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
  margin-left: 20%;
}
.manage-files{
  font-family: 'Montserrat';
  position: relative;
  padding-top: 1.5rem;
  height: 100vh;
  left: 60px;
  width: calc(100% - 60px);
  // overflow: hidden;
}
</style>