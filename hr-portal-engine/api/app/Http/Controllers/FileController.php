<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
Use File;
use Carbon\Carbon;

class FileController extends Controller
{
    const GET_ALL_FILES = [self::class, 'getAllFiles'];
    const DOWNLOAD_FILE_DOCUMENTS = [self::class, 'downloadFileDocuments'];
    const DOWNLOAD_FILE_ALL = [self::class, 'downloadFileAll'];
    const UPLOAD_FILE = [self::class, 'uploadFile'];
    const GET_ALL_FOLDERS_SUBFOLDERS_FILES = [self::class, 'getAllFoldersWithSubFoldersAndFiles'];
    private $files = [];

    public function getAllFiles() {
        $fileNames = [];
        $fileDirectory = config('constants.documents_base_path');
        $filesList = File::allFiles($fileDirectory);
        foreach($filesList as $file) {
            $folderName = explode("\\", pathinfo($file)['dirname'])[1];
            array_push($fileNames, ["foldername" => $folderName, "filename" => pathinfo($file)['basename']]);
        }
        return $fileNames;
    }

    public function downloadFileDocuments(Request $request) {
        $basePath = config('constants.documents_base_path');
        $folderName = $request->folderName;
        $fileName = $request->fileName;
        $path = $basePath."/".$folderName."/".$fileName;
        return response()->download($path);
    }

    public function getAllFoldersWithSubFoldersAndFiles() {
        $assetsBasePath = config('constants.assets_base_path');
        return $this->getAllFoldersAndFilesJSON($assetsBasePath);
    }

    public function getAllFoldersAndFilesJSON($folderPath) {
        $folders = [];
        $subFolders = [];
        
        $ffs = scandir($folderPath);
        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);
        unset($ffs[array_search('audits', $ffs, true)]);
        unset($ffs[array_search('checksum', $ffs, true)]);
        unset($ffs[array_search('upload backup', $ffs, true)]);

        if (count($ffs) < 1)
            return;

        foreach($ffs as $ff){
            if(is_dir($folderPath.'/'.$ff)) {
                $this->files[$ff] = [];
                $subFolders = $this->getAllFoldersAndFilesJSON($folderPath.'/'.$ff);
                if($subFolders == null)
                    $subFolders = [];
                array_push($folders, ["foldername" => $ff, "subfolders" => $subFolders, "files" => $this->files[$ff]]);
            } else {
                $exploded = explode('/', $folderPath);
                $key = end($exploded);
                $currentFiles = $this->files[$key];
                array_push($currentFiles, ["name" => $ff, "path" => $folderPath.'/'.$ff]);
                $this->files[$key] = $currentFiles;
            }
        }
        return $folders;
    }

    public function uploadFile(Request $request) {
        $attachment = '';
        if($request->hasFile('file')){
            $attachment = $request->File('file');
        }

        $empId = $request->empId;
        $uploadedFileName = $attachment->getClientOriginalName();

        $assetBasePath = config('constants.assets_base_path');
        $folderPath = $request->folderPath;
        $destinationPath = $assetBasePath."/".$folderPath;
        $empId = $request->empId;

        $this -> backupFile($destinationPath, $uploadedFileName, $empId);
        $attachment->move($destinationPath, $uploadedFileName);
    }

    public function backupFile($fileSource, $fileName, $empId) {
        if (!file_exists(config('constants.upload_file_backup_folder'))) {
            mkdir(config('constants.upload_file_backup_folder'), 0777, true);
        }
        $source = $fileSource."/".$fileName;
        $backupBasePath = config('constants.upload_file_backup_folder');
        $currentDateTime = $this->getFormattedCurrentDateTime();
        $backupDestination = $backupBasePath."/".$fileName."_".$currentDateTime."_".$empId;
        File::copy($source, $backupDestination);
    }

    public function getFormattedCurrentDateTime() {
        $date = Carbon::now();
        return $date->format('Y_m_d_H_i_s');
    }

    public function downloadFileAll(Request $request) {
        $filePath = $request->filePath;
        return response()->download($filePath);
    }
}
