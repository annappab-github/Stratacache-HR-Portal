<template>
  <div class="manage-files">
    <div>
      <div class="title">
        <h1 class="">Files Upload</h1>
      </div>
      <div class="paths" style="border: 1px solid #D05959">
      <label class="mdi mdi-folder" style="background-color: #D05959; color: #fff; margin: 0px; padding: 5px 10px 5px 10px"> Directory: </label>&nbsp;&nbsp;
        <span  style="inline" v-for="(path,idx) in absolutePath" :key="idx"  >
          <span class="path" v-if="path.path != '' && path.path!=undefined" @click="navigate(path.data, path.path)">{{path.path}}/</span>
        </span>
      </div>
    </div>

    <div class="loader" v-if="loading"></div>
    <ListOfFilesView v-if="!loading" @openListedFiles="openListedFiles" @downloadFile="downloadFile" :absolutePath="absolutePath" />
  </div>
</template>

<script>
import axios from 'axios';
import { decryptData } from '../js/utils/encrypt';
import fileupload from '../assets/fileupload.png';
import folderImg from '../assets/folder.svg';
import ListOfFilesView from './ListOfFilesView';
import { computed } from 'vue';

export default {
  provide() {
    return {
      listOfFilesfolder: computed(() => this.tempfolderList)
    };
  },
  data() {
    return {
      absolutePath: [{
        path: '',
        data: []
      }],
      loading: false,
      files: [],
      filter: 'all',
      employees: [],
      loggedInUser: [],
      location: '',
      email: '',
      folderImg: folderImg,
      active: false,
      txtFile: this.txtFile || '/example.txt',
      employeeId: '',
      fileupload: fileupload,
      fileList: false,
      openFileUploadModal: false,
      folderList: [],
      tempfolderList: [],
      rootData: [],
    };
  },
  components: {
    ListOfFilesView
  },
  computed: {
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
    axios.get(this.$hostName + '/files/allUpload').then((response) => {
        response.data.forEach((element) => {
            this.tempfolderList.push(element);
        });
        this.rootData = this.tempfolderList;
          var pathdata = { 
           path: 'root',
           data: this.tempfolderList
        };
        this.absolutePath.push(pathdata);
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.loading = false;
      });
  },
  methods: {
    navigate(data, path){
       if(path == 'root'){
             this.absolutePath = [];
              this.tempfolderList =  this.rootData;
               var pathdata = { 
               path: 'root',
               data: this.tempfolderList
            };
            this.absolutePath.push(pathdata);
            this.openListedFiles(this.tempfolderList);
      } else {
          for(let i=0; i<this.absolutePath.length; i++){
            if(this.absolutePath[i].path == path){
              for(let j=i; j<this.absolutePath.length; j++){
              this.absolutePath.splice(j,this.absolutePath.length);
              }
            }
          }
            this.openListedFiles(data);
      }
     
    
    },
    openListedFiles(name){
        this.tempfolderList = name;
        var tempdata = { 
          path: name.foldername,
          data: name
        };
        this.absolutePath.push(tempdata);
    },
    downloadFile(data) {
        // this.loading = true;
        var formData = {
            filePath: data['path']
        };
        axios({
        url: this.$hostName + '/files/downloadAll',
        method: 'POST',
        responseType: 'blob',
        data: formData,
        }).then((response) => {
            const binaryResponse = new Blob([response.data]);
            var fileURL = window.URL.createObjectURL(binaryResponse);
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            var fileName = data['name'];
            fileLink.setAttribute('download', fileName);
            document.body.appendChild(fileLink);
            fileLink.click();
            fileLink.remove();
        }).finally(() => {
            // this.loading =  false;
        });  
    }
  }
};
</script>

<style scoped lang="scss">
.path:hover {
  cursor: pointer;
  color: red;
  z-index: 999;
}

.paths{
  margin-left: 2%;
  font-size: 20px;
  width: 96%;
}
.folders{
    margin-left: 2%;
}
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
.log-in{
  width: 100%;
  padding: 10px 15px 0 15px;
  text-align: center;
}
.linkName {
    font-size: 10px;
    margin: 20px 10px 20px 0px;
    background-color: red;
    color: #fff;
}
.resetText{
  font-size: 20px;
  margin-top: 60px;
}
.logo-container{
  width: 30%;
  height: 84%;
  text-align: center;
  margin: 40px 180px 10px 180px;
  border-radius: 50%;
  box-shadow: 0px 2px 22px grey;
}
.closeBtn{
    width: 12vh;
    height: 12vh;
    margin-top: 20px;
    border-radius: 50%;
    box-shadow: 0px 2px 10px grey;
}

.container {
  position: absolute;
  left: 0;
  right: 60px;
  top: 0;
  bottom: 0;
  width: 1140px; /* Assign a value */
  height: 400px; /* Assign a value */
  margin: auto;
}
.login-card {
  width: 40rem;
  height: 35rem;
  opacity: 0.8;
  border-top-left-radius: 0px;
  border-bottom-left-radius: 0px;
  
}
.card{
   box-shadow: 0px 2px 22px grey;
   width: 32rem;
   height: 26rem;
   border-radius: 15px;
}
.fade_rule {
        height: 4px;
        border-radius: 5px;
        width: 10rem;
        text-align: center;
        left: 35%;
        position: relative;
        background-color: #E6E6E6;
        background-image: -webkit-gradient( linear, left bottom, right bottom, color-stop(0.01, grey),color-stop(0.8, white) );
}
.errors{
    position: absolute;
    top: 205px;
    left: 40px;
}
.resetLoader{
  position: fixed;
  top: 0px;
  left: 0px;
  width: calc(100% - 0px);
  height: 100%;
  background-color: #eceaea;
  background-image: url(/img/loading.50d8d981.gif);
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
@media only screen and (max-width: 575.98px) {
  .card{
    width: 23rem;
    height: 30rem;
    position: relative;
    left: -14.5% !important;
    padding: -20%;
    padding-left: -9%;
  }
 .container {
  position: absolute;
  right: 0px;
  top: 0;
  width: 500px !important;
  bottom: 0;
  z-index: 999;
}
}
.folderImg{
    width: 150px;
    height: 150px;
}
</style>