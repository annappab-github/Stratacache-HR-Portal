<template>
 
    <div class="row folders" v-if="rootcall">
        <div class="" v-for="(folderlist,index) in filesList" :key="index" @dblclick="$emit('openListedFiles', folderlist)">
            <img class="folderImg"  :src="folderImg" alt="">
            <div class="font-weight-bold folder-Name text-center">{{folderlist.foldername}}</div>
        </div>
    </div>
    <div class="" v-if="!rootcall">
        <div class="title d-flex justify-content-between">
             <h1 class="folder-heading">{{foldername.toUpperCase()}}</h1>
             <input type="file" id="file" ref="file" style="display: none" @change="uploadFile($event,filesList)">
             <div class="uploadText" @click="$refs.file.click()">UPLOAD</div>
             <div class="loader" v-if="loading"></div>
                <div class="modal-mask" v-if="uploadloadingText">
                    <div class="modal-wrapper">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" >
                                    <button type="button" class="close" @click="uploadloadingText = false">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="confirmation pb-3" style="text-align: center">
                                       <img class="sentLoader mb-2" :src="sentgif">
                                        <h4>File Uploaded Succesfully</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-mask" v-if="uploadFailed">
                    <div class="modal-wrapper">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" >
                                    <button type="button" class="close" @click="uploadFailed = false">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="confirmation pb-3" style="text-align: center">
                                       <img class="sentLoader mb-2" :src="failedgif">
                                        <h4>Upload Failed <br> File name is not the same</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
            
        <div class="subfolders">
            <div class="row folders " v-if="filesList.subfolders.length != 0" >
                <div class="" v-for="(subfolder ,index) in filesList.subfolders" :key="index" @dblclick="$emit('openListedFiles', subfolder)">
                    <img class="folderImg" :src="folderImg" alt="">
                    <div class="font-weight-bold folder-Name text-center">{{subfolder.foldername}}</div>
                </div>
                </div>
                <div  class="row files text-center" v-if="filesList.files.length != 0" >
                <div class="files" v-for="(files,index) in filesList.files" :key="index" @click="$emit('downloadFile',files)">
                    <span v-if="files.name.endsWith('pdf')" class="mdi mdi-file-pdf-box fileImg " style="color: red; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('docx')" class="mdi mdi-file-word fileImg" style="color: blue; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('png')" class="mdi mdi-file-image fileImg" style="color: #B41CE6; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('jpg')" class="mdi mdi-file-image fileImg" style="color: #B41CE6; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('jpeg')" class="mdi mdi-file-image fileImg" style="color: #B41CE6; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('xlsx')" class="mdi mdi-file-excel fileImg" style="color: green; cursor: pointer"></span>
                    <span v-if="files.name.endsWith('txt')" class="mdi mdi-note-text fileImg" style="color: black; cursor: pointer"></span>
                    <div class="font-weight-bold file-Name text-center" style=" cursor: pointer">{{files.name}}</div>
                </div>                
            </div>
        </div>
    </div>
</template>

<script>
import folderImg from '../assets/folder.svg';
import sentgif from '../assets/sent.gif';
import failedgif from '../assets/error.gif';
import axios from 'axios';
import { decryptData } from '../js/utils/encrypt';
export default {
    inject: ['listOfFilesfolder'],
    props:['absolutePath'],
    data() {
        return {
            templistOfFilesfolder: this.listOfFilesfolder,
            rootcall: true,
            filesList: [],
            foldername: '',
            folderImg: folderImg,
            loading: false,
            uploadloadingText: false,
            uploadFailed: false,
            sentgif: sentgif,
            failedgif: failedgif,
        };
    },
    watch: {
        templistOfFilesfolder(newval) {
            this.checkViewType(newval);
        }
    },
    mounted() {
        this.filesList = this.templistOfFilesfolder;
        var encryptedEmailID = localStorage.getItem('Email');
    const secretKey = process.env.KEY || 'myscecretkey';
    this.email = decryptData(encryptedEmailID, secretKey).replace(/['"]+/g, '');
    var encryptedEmployeeID = localStorage.getItem('EmployeeID');
    if(encryptedEmployeeID != null){
      this.employeeId = decryptData(encryptedEmployeeID, secretKey).replace(/['"]+/g, '');
    }
    this.filter = this.employeeId.substring(0, 2).toLowerCase();
    },
    methods: {
        uploadFile(event, filesList){
            var tempfileName = [];
            for(let i in filesList.files) {
                tempfileName.push(filesList.files[i].name);
            }
                var rootCheck = false;
                var path = '';
              for(let i=0; i<this.absolutePath.length; i++){
                if(rootCheck == true) {
                    if(this.absolutePath[i].path!=undefined){
                        path = path.concat(this.absolutePath[i].path);
                        path = path.concat('/');
                    }
                }
                 if(this.absolutePath[i].path == 'root'){
                    rootCheck = true;
                 }
              }
              let formData = new FormData();
              formData.append('empId', this.employeeId);
              formData.append('file', event.target.files[0]); 
              formData.append('folderPath', path); 
              if(tempfileName.includes(event.target.files[0].name)) {
                    axios({
                        url: this.$hostName + '/files/upload',
                        method: 'POST',
                        responseType: 'blob',
                        data: formData,
                        }).then((response) => {
                            this.loading = true;
                            const binaryResponse = new Blob([response.data]);
                            var fileURL = window.URL.createObjectURL(binaryResponse);
                            setTimeout(() => {
                                this.loading = false;
                                this.uploadloadingText = true;
                                this.uploadFailed = false;
                            }, 2000);
                            
                            console.log(fileURL);
                        }).finally(() => {
                            this.uploadloadingText = false;
                            this.$refs.file.value = null;
                        }); 
              } 
              else {
                 this.uploadFailed = true;
                 this.uploadloadingText = false;
              }
    },
        checkViewType(newval) {
            this.foldername = '';
            try {
                if(newval.hasOwnProperty('foldername')){
                    this.foldername = newval.foldername;
                } else {
                    this.foldername = '';
                }
            }
            catch(error) { 
                console.log(error);
            }
            if( this.foldername != '') {
                this.rootcall = false;
            } else {
                this.rootcall = true;
            }
            this.filesList = newval;
        }
    }

};
</script>
<style scoped>
@media only screen and (max-width: 575.98px){
.uploadText {
  background-color: #EE3234;
  color: #fff;
  font-size: 10px;
  width: 80px;
  height: 30px;
  border-radius: 4px;
  padding: 4px 8px;
  margin-right: 2px !important;
  cursor: pointer;
}
.folder-heading{
  font-size: 24px;
    
}
}
.sentLoader {
    width: 50px;
    height: 50px;
    top: 0px;
}
.confirmation{
    font-size: 30px;
    text-align: center;
    margin-top: 20px;
}
.modal-body{
    padding: 0px;
}
.image-loader {
  position: fixed;
  background-size: 150px;
  top: 0px;
  width: 100%;
  height: 100%;
  background-color: #eceaea;
  background-image: url("../assets/loading.gif");
  background-repeat: no-repeat;
  background-position: 50%;
  z-index: 999;
}
.title{
    padding: 30px 0 0 0;
}
.uploadText {
  background-color: #EE3234;
  color: #fff;
  font-size: 14px;
  width: 80px;
  height: 30px;
  border-radius: 4px;
  padding: 4px 8px;
  margin-right: 80px;
  cursor: pointer;
}
.subfolders{
  width: 100%;
}
.folderImg{
    width: 150px;
    height: 150px;
}
.fileImg{
    font-size: 110px;
}
.folders{
    margin-left: 2%;
    cursor: pointer;
}
.files{
   margin-left: 2%;
}
.file-Name{
  position: relative;
  top: -18px;
  width: 150px;
  word-break: break-all;
}
</style>