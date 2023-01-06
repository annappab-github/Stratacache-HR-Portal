<template>
    <div class="row topBg">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 align-self-center">
            <div class="text-center heading ">HI!, HOW CAN WE HELP YOU ?</div>
        </div>
        <div class="col-sm-4"></div>
    </div>
    
    <div class="container main">
        <div class="row d-flex justify-content-sm-around ">
          <div class="sentLoader" v-if="sentLoading"></div>
          <div class="errorLoader" v-else-if="errorLoading"></div>
          <div class="emailLoader" v-else-if="emailLoading"></div>
            <div class="col-sm-3">
                <div class="text-center faq">
                    <div v-for="data in filteredList" :key="data">
                        <img  :src="helpimg" class="round" alt="" @click="openDocument(data)">
                    </div>
                    <div class="font-weight-bold text-center support-text-title">FREQUENTLY ASKED QUESTIONS</div>
                    <div class="text-center  support-text-body">Frequently asked questions.</div>
                </div>
            </div>
            <div class="col-sm-3 reportIssue">
                <div class="text-center">
                    <img :src="newsimg" class="round" alt="" @click="showEmailModal('Staging')">
                    <div class="font-weight-bold text-center support-text-title">WRITE TO US</div>
                    <div class="text-center support-text-body">Write to Us.</div>
                </div>
            </div>
        </div>
    </div>


<div class="views" v-if="active">
      <div class="viewer-overlay"></div>
      <div class="pdf-background"></div>
      <div class="close-btn">
        <button type="button" class="btn btn-sm close-button" @click="close()">
          <span class="mdi mdi-close-circle cancel"></span>
        </button>
      </div>
      <div class="row" v-if="pdfView">
        <div class="pdf-topbar">
          <div class="page-navigation">
            <button type="button" class="btn btn-dark btn-sm nav-button mx-2" @click="changePage('first')">
              First Page
            </button>
            <button type="button" class="btn btn-dark btn-sm nav-button" @click="changePage('previous')">
              <div class=" nav-icon mdi mdi-menu-left"></div>
            </button>
            <label class="page-index" style="color: #000">{{currentPage}}/{{numPages}}</label>
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
   </div>

<!--mail overlay -->
<div id="overlay">
  <div id="text">
      <div class="">
        <div class="new-request text-white p-2 mb-3" >New Request
          <span class="mdi mdi-close close-icon"  @click="clearFormData()"></span>
        </div>
      </div>
      <div class="mb-1 d-flex">
        <label for="form3Example1cg" class="font-weight-normal ml-2">To:</label>
          <div class="">
            <input type="text" style="width: 500px" id="form3Example1cg" class="text-line opacityTo"  v-model="form.to" readonly autocomplete="off">
          </div>
      </div>
      <hr>
      <div class="mb-1 mt-2 row">
        <label for="form3Example3cg" class="font-weight-normal ml-2">Subject :</label>
          <div class="issuesList" v-if="this.form.type == 'Staging'">
              <select name="issues" class=" mt-1" v-model="form.subject" >
                <option value="" selected>Change Profile Image</option>
                <option>Change Address</option>
                <option>Change DOB</option>
                <option>Regarding Leaves</option>
                <option>Others</option>
              </select>
          </div>
      </div>
          <div class="" v-if="this.form.subject == 'Others'">
            <input type="text" id="form3Example4cg" v-model="otherSubject"  placeholder="Please enter subject" class="text-line otherSubField" autocomplete="off"/>
          </div>
      <hr>
        <div class="">
          <textarea rows="10"   v-model="form.message"></textarea> <br>
        </div>
        <div class="attach-files-container">
          <div class="attach-files" v-for="(name,index) in fileNames" :key="index">
            <span class="attach-files-text">{{ name }}</span>
            <span class="mdi mdi-delete delete-icon"  @click="removeAttachment(index)"> </span>
          </div>
        </div>
      <hr>
      <div class="d-flex justify-content-end mt-3 mr-3 ">
        <input type="file" id="file" ref="file" @change="handleFileUpload($event)" style="display: none">
        <div class="attachHover mr-3">
          <span @click="$refs.file.click()" class="mdi mdi-paperclip attach-icon" style="margin-right: 20px;"></span>
          <div class="attach">
            <div class="attach-text">Attach Files</div>
          </div>
        </div>
        <button @click="sendEmail()" type="button" class="p-1 sendBtn">send</button>
      </div>
      <br>
  </div>
</div>
    
    <div class="loader" v-if="loading"></div>
</template>

<script>
import axios from 'axios';
import { decryptData } from '../js/utils/encrypt';
// import $ from 'jquery';
import pdfvuer from 'pdfvuer';
import helpDesk from '../assets/SG002.png';
import help from '../assets/help.png';
import news from '../assets/news.png';
import roadmap from '../assets/roadmap.png';

export default {
  data() {
    return {
    form: {
        to: process.env.VUE_APP_HR_GROUP_EMAIL,
        subject:'',
        message: '',
        from: '',
        type: ''
      },
      otherSubject: '',
      loading: false,
      emailLoading: false,
      sentLoading: false,
      errorLoading: false,
      attachedFiles: [],
      files: [],
      search: '',
      selected: 'all',
      filter: 'all',
      employees: [],
      loggedInUser: [],
      location: '',
      log: helpDesk,
      helpimg: help,
      newsimg: news,
      roadmapimg: roadmap,
      pdfFile: this.pdfFile || '/example.pdf',
      active: false,
      numPages: 0,
      currentPage: 1,
      pdfdata: undefined,
      showOverlay: false,
      pdfView: false,
    };
  },
   components: {
    pdf: pdfvuer
  },
  computed: {
    filteredList() {
      var refined;
      if (this.filter) {
        var allData = this.searchFilesByLocation('faq');
        refined = allData;
      } 
      if (this.search) {
        refined = this.searchFiles(refined);
      } 
      refined = this.sortFiles(refined);
      return refined;
    },
    fileNames() {
      const fileNames = [];
      for (let i = 0; i < this.attachedFiles.length; ++i) {
        fileNames.push(this.attachedFiles[i].name);
      }
      return fileNames;
    }
  },
 mounted(){
    var encryptedEmailID = localStorage.getItem('Email');
    const secretKey = process.env.KEY || 'myscecretkey';
    this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');
    // var loggedInUserArr = [];
    this.loading = true;
    if(this.email != 'admin@scala.com'){
      axios.get(this.$hostName + `/employeeinfobyemail/${this.email}`).then((response) => {
        response.data.forEach((element) => {
            this.loggedInUser.push(element);
        });
        this.location = this.loggedInUser[0]['location'];
        if (this.location == 'Singapore'){
          this.filter = 'sg';
        } else if (this.location == 'Japan'){
          this.filter = 'jap';
        } else if (this.location == 'India'){
          this.filter = 'ind';
        } else if (this.location == 'Australia'){
          this.filter = 'aus';
        }
      })
      .catch((error) => {
        console.log(error);
      });
    }
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
        close(){
      this.active = false;
      this.pdfView = false;
      this.imgView = false;
      this.txtView = false;
      this.currentPage = 1;
      this.imgFile = '';
    },
    showEmailModal(type) {
      this.form.type = type;
      document.getElementById('overlay').style.display = 'block';
    },

    hideEmailModal() {
      document.getElementById('overlay').style.display = 'none';
    },
    clearFormData() {
      this.form.subject = '';
      this.otherSubject = '',
      this.form.message = '';
      this.form.from = '';
      this.form.type = '';
      this.$refs.file.value=null;
      this.attachedFiles = [];
      document.getElementById('overlay').style.display = 'none';
    },
    closePDF(){
      this.active = false;
      this.currentPage = 1;
    },
    openDocument(data) {
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
            this.pdfFile = fileURL;
            var self = this;
            self.pdfdata = pdfvuer.createLoadingTask(fileURL);
            self.pdfdata.then(pdf => {
            this.numPages = pdf.numPages;
      });

        }).finally(() => {
            this.loading = false;
            this.active =  true;
            this.pdfView =  true;
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
       this.form.subject = event.target.value;
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
    },
    sendEmail() {
      this.hideEmailModal();
      this.emailLoading = true;
      this.form.from = this.loggedInUser[0]['email'];
      let formData = new FormData();
         formData.append('to', this.form.to);
         if(this.form.subject == 'Others'){
              if(this.otherSubject == ''){
                formData.append('subject', this.form.subject);
              } else {
                formData.append('subject', this.otherSubject);
              }
         }
         else {
          if(this.form.subject == ''){
            this.form.subject = 'Change Profile Image';
            formData.append('subject', this.form.subject);
          } else {
          formData.append('subject', this.form.subject);
          }
        }
         formData.append('message', this.form.message);
         formData.append('from', this.form.from);
         formData.append('type', this.form.type);
         
      for(let i=0; i<this.attachedFiles.length; i++){
        formData.append('file[]', this.attachedFiles[i]);
      }
    
      axios
        .post(this.$hostName + '/email', formData, {headers : {'Content-Type': 'multipart/form-data'}})
        .then((response) => {
          console.log(response+'Email sent successfully!!');
        this.emailLoading = false;
        this.sentLoading = true;
        setTimeout(() => {
            this.sentLoading = false;
            this.clearFormData();
        }, 2000);
        })
      .catch((error) => {
        console.log(error);
        this.errors = error.response.data.errors;
        this.emailLoading = false;
        this.errorLoading = true;
        setTimeout(() => {
            this.errorLoading = false;
            this.clearFormData();
        }, 2000);
      })
      .finally(() => {
        this.emailLoading = false;
      });
    },
    handleFileUpload(event) {
      this.attachedFiles.push(event.target.files[0]);
    },
    removeAttachment(index) {
      this.attachedFiles.splice(index, 1);
      this.$refs.file.value=null;
    }
  }
};
</script>

<style scoped lang="scss">
.new-request{
  background-color: #d05959;
}
select{
  border: none;
  outline: none;
  border: 1px solid #000;
  width: 430px;
  margin: 0px 0px 0px 10px;
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
.close-icon{
  float: right;
}
.close-icon:hover{
  cursor: pointer;
}
.attach {
  transition: .5s ease;
  position: absolute;
  opacity: 0;
  text-align: center;
  width: 80px;
  background-color: #fff;
}
.attach-icon{
  position: relative;
  left: 10px;
  top: 3px;
  transition: .5s ease;
}
.attachHover{
  transition: .5s ease;
  border-radius: 50%;
}
.attachHover:hover .attach{
  opacity: 1;
}
.attachHover:hover .attach-icon{
  color: white;
}
.attachHover:hover {
  background-color: rgb(140, 140, 140);
  cursor: pointer;
}
.attach-text {
  background-color: rgb(111, 111, 111);
  color: rgb(255, 255, 255);
  padding: 1px 5px 1px 5px;
  border-radius: 20px;
  top: -18px;
  left: -45px;
  transform: translate(-50%);
  width: 100%;
  font-size: 8px;
  position: absolute;
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
.pdf-viewer{
  width: 30%;
  position: absolute;
  left: 50%;
  top: 54%;
  transform: translate(-50%,-50%);
}
.nav-button{
  height: 30px;
}
.page-navigation{
  width: 80%;
  left: 50%;
  transform: translate(-50%);
  position: absolute;
  color: white;
  top: 4px;
}
.close-button{
  background-color: white;
  height: 20px;
  width: 20px;
  position: absolute;
  border-radius: 50%;
}
.viewer-overlay{
  background-color: rgba(0, 0, 0, 0.5);
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  backdrop-filter: blur(5px);
}
.sendBtn{
background-color: #EE3234; 
width: 15%;
height: 100%;
color: #fff;
border-radius: 4px;
border: none;

}
.delete-icon{
font-size: 20px;
 cursor: pointer;
 transition: .3s ease;
}
.delete-icon:hover{
  color: red;
}

.text-line {
    background-color: transparent;
    color: #000;
    outline: none;
    outline-style: none;
    border-top: none;
    border-left: none;
    border-right: none;
    border-bottom: none;
    padding: 3px 10px;
}
textarea {
  resize: none;
  border: none;
  outline: none;
  width:100%;
}
.attach-files-container{
  width: 100%;
  min-height: 40px;
  padding: 0 5px 0 5px;
}
.attach-files{
  border: black solid 1px;
  display: inline-block;
  padding: 0 7px 0 7px;
  margin: 2px 2px 2px 2px;
  border-radius: 18px;
}
.attach-files-text{
  position: relative;
  top: -1px;
}
hr{
    margin: 0px;
}
.container{
  padding: 0px;
  
}
#overlay {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 2;
}

#text{
  background-color: #fff;
  position: absolute;
  top: 50%;
  left: 50%;
  color: #000;
  transform: translate(-50%,-50%);
  -ms-transform: translate(-50%,-50%);
}
.page-index{
  width: 65px;
}
.close-btn{
  position: absolute;
  top: 4px;
  left: 67.5%;
  z-index: 999;
}
.main{
  font-family: 'Montserrat';
  position: absolute;
  top: 60%;
  left: calc(50% + 30px);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}

.btns{
    background-color: #1B4989;
    color: #fff;
}

.emailLoader {
  position: fixed;
  top: 0px;
  width: 150%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/sendemail.gif");
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.sentLoader {
  position: fixed;
  top: 0px;
  width: 150%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/sent.gif");
  background-size: 60px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.errorLoader {
  position: fixed;
  top: 0px;
  width: 150%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/error.gif");
  background-size: 60px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.opacityTo {
  opacity: 0.4;
  pointer-events: none;
}
.pdf-topbar{
    width: 32%;
    height: 40px;
    position: absolute;
    top: 42px;
    left: 50%;
    transform: translate(-50%,-50%);
    background-color: white;
    text-align: center;
    border-radius: 5px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 10px 15px;
  }
  .otherSubField{
   width: 500px;
}
@media only screen and (max-width: 575.98px) {
  .emailLoader {
  top: 0px;
  width: 110%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/sendemail.gif");
  background-size: 150px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.sentLoader {
  top: 0px;
  width: 110%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/sent.gif");
  background-size: 60px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
.errorLoader {
  top: 0px;
  width: 110%;
  height: 100%;
  background-color: white;
  background-image: url("../assets/error.gif");
  background-size: 60px;
  background-repeat: no-repeat;
  background-position: calc(calc(50%) - 30px);
  z-index: 999;
}
  select{
  border: none;
  outline: none;
  border: 1px solid #000;
  width: 220px;
  margin: 0px 0px 0px 10px;
}
.otherSubField{
   width: 310px !important;
}
  .reportIssue{
    margin: 90px;
  }
  .faq{
    margin-top: 40px;
  }
   #text{
  width: 82% !important;
  background-color: #fff;
  position: absolute;
  top: 50%;
  left: 58% !important;
  color: #000;
}
  .pdf-background{
  z-index: -1;
}
  .nav-button{
    font-size: 12px;
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
  .page-navigation{
    width: 100% !important;
    color: black !important;
    left: 50%;
    transform: translate(-50%);
    position: absolute;
    color: white;
    top: 6px;
  }
  .close-btn{
  position: absolute !important;
  right: 10% !important;
  left: auto !important;
  top: 8% !important;
  z-index: 999;
}
  .nav-button{
    font-size: 12px;
  }
  .pdf-viewer{
    left: calc(50% - -30px) !important;
    top: 423px !important;
    width: 300px !important;
  }
  .col-sm-3{
    padding: 0px;
  }
  .main{
  top: 70%;
  left: calc(50% + 30px);
  transform: translate(-50%, -60%);
  position: absolute !important;
}
.heading{
    height: 56px;
    color:  #fff;
    font-size: 26px !important;
    font-weight: 700px;
    letter-spacing: 0px;
}
.round{
    border-radius: 50%;
    width: 120px !important;
    height: 120px !important;
    cursor: pointer;
}
}
@media (min-width: 768px) and (max-width: 991.98px) { 
select{
  border: none;
  outline: none;
  border: 1px solid #000;
  width: 260px;
  margin: 0px 0px 0px 10px;
}
#text{
  background-color: #fff;
  position: absolute;
  top: 50%;
  left: 50% !important;
  color: #000;
  transform: translate(-45%,-50%) !important;
  -ms-transform: translate(-50%,-50%);
}
.pdf-background{
  z-index: -1;
}
.heading{
    height: 56px;
    color:  #fff;
    font-size: 26px !important;
    font-weight: 700px;
    letter-spacing: 0px;
}
.round{
    border-radius: 50%;
    width: 160px !important;
    height: 160px !important;
    cursor: pointer;
}
  .pdf-topbar{
    width: 70%;
    height: 50px;
    position: absolute;
    top: 120px;
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
    top: 8px;
  }
  .close-btn{
    position: absolute !important;
    left: 90% !important;
    top: 35px !important;
    z-index: 999;
  }
  .cancel{
  font-size: 40px;
  color: #d82d28;
  left: 50%;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
}
.cancel:hover{
  color: #b60a04;
}
  .pdf-viewer{
    top: 580px !important;
    width: 500px !important;
  }
  .col-sm-3{
    padding: 0px !important;
  }
}
.round{
    border-radius: 50%;
    width: 250px;
    height: 250px;
    background-color: #FFDFDF;
     cursor: pointer;
     transition:.5s;
}
.round:hover{
  transform:scale(1.06);
}
.support-text-body{
    font-size: 14px;
    letter-spacing: 0px;
}
.support-text-title{
  margin-top: 20px;
}
.row{
    margin-left: 0px;
    margin-right: 0px;
}
.topBg{
  font-family: 'Montserrat';
  position: absolute;
  height: 23%;
  width: calc(100% - 60px);
  left: 60px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}
.heading{
    color:  #fff;
    font-size: 32px;
    font-weight: 700px;
    letter-spacing: 0px;
}
</style>