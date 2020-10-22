<?php

namespace Optimus\FineuploaderServer\Vendor;

/**
 * Do not use or reference this directly from your client-side code.
 * Instead, this should be required via the endpoint.php or endpoint-cors.php
 * file(s).
 */

class FineUploader {

    public $allowedExtensions = array();
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksFolder = 'chunks';

    protected $uploadName;

    // php.ini defaults
    const DEFAULT_POST_MAX_SIZE = '8M';
    const DEFAULT_UPLOAD_MAX_FILESIZE = '2M';

    function __construct(){
        $this->sizeLimit = $this->toBytes($this->iniGet('upload_max_filesize'));
    }

    /**
     * Get the original filename
     */
    public function getName(){
        if (isset($_REQUEST['qqfilename']))
            return $_REQUEST['qqfilename'];

        if (isset($_FILES[$this->inputName]))
            return $_FILES[$this->inputName]['name'];
    }

    private function iniGet($directive)
    {
        $val = ini_get($directive);

        if ($val !== false) {
            return $val;
        }

        $const = sprintf('DEFAULT_%s', strtoupper($directive));
        if (defined('self::' . $const)) {
            return constant('self::' . $const);
        }

        throw new \Exception('No valid ini values were found.');
    }

    /**
     * Get the name of the uploaded file
     */
    public function getUploadName(){
        return $this->uploadName;
    }

    /**
     * Process the upload.
     * @param string $uploadDirectory Target directory.
     * @param string $name Overwrites the name of the file.
     */
    public function handleUpload($uploadDirectory, $name = null){

        // Check that the max upload size specified in class configuration does not
        // exceed size allowed by server config
        if ($this->toBytes($this->iniGet('post_max_size')) < $this->sizeLimit ||
            $this->toBytes($this->iniGet('upload_max_filesize')) < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            return array('error'=>"Server error. Increase post_max_size and upload_max_filesize to ".$size);
        }

        if ($this->isInaccessible($uploadDirectory)){
            return array('error' => "Server error. Uploads directory isn't writable");
        }

        if(!isset($_SERVER['CONTENT_TYPE'])) {
            return array('error' => "No files were uploaded.");
        } else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') !== 0){
            return array('error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true).");
        }

        // Get size and name
        $file = $_FILES[$this->inputName];
        $size = $file['size'];

        if ($name === null){
            $name = $this->getName();
        }

        // Validate name
        if ($name === null || $name === ''){
            return array('error' => 'File name empty.');
        }

        // Validate file size
        if ($size == 0){
            return array('error' => 'File is empty.');
        }

        if ($size > $this->sizeLimit){
            return array('error' => 'File is too large.');
        }

        // Validate file extension
        $pathinfo = pathinfo($name);
        $ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';

        if($this->allowedExtensions && !in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions))){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        // Save a chunk
        $totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;

        $uuid = $_REQUEST['qquuid'];
        if ($totalParts > 1){
        # chunked upload

            $chunksFolder = $this->chunksFolder;
            $partIndex = (int)$_REQUEST['qqpartindex'];

            if (!is_writable($chunksFolder) && !is_executable($uploadDirectory)){
                return array('error' => "Server error. Chunks directory isn't writable or executable.");
            }

            // Append chunk to temp file
            $tmpTarget = $this->chunksFolder.DIRECTORY_SEPARATOR.$uuid;
            $chunkContent = file_get_contents($_FILES[$this->inputName]['tmp_name']);
            if ($chunkContent === false){
                return array('error'=> 'Failed to read chunk');
            }
            if (file_put_contents($tmpTarget, $chunkContent, FILE_APPEND | LOCK_EX) === false){
                return array('error'=> 'Failed to write chunk to file');
            }

            // Check if this is the final chunk
            if ($totalParts-1 == $partIndex){
                // Verify file
                if (filesize($tmpTarget) != (int)$_REQUEST['qqtotalfilesize']) {
                    return array('error'=> 'Upladed file is corrupt');
                }

                // Move to upload directory
                $target = join(DIRECTORY_SEPARATOR, array($uploadDirectory, $uuid, $name));
                $this->uploadName = $uuid.DIRECTORY_SEPARATOR.$name;
                if (!file_exists($target)){
                    if (!mkdir(dirname($target))){
                        return array('error'=> 'Failed to create upload directory');
                    }
                }
                if (!rename($tmpTarget, $target)){
                    return array('error'=> 'Failed to move file to final destination');
                }
            }

            return array("success" => true, "uuid" => $uuid);
        }
        else {
        # non-chunked upload

            $target = join(DIRECTORY_SEPARATOR, array($uploadDirectory, $uuid, $name));

            if ($target){
                $this->uploadName = basename($target);

                if (!is_dir(dirname($target))){
                    mkdir(dirname($target));
                }
                if (move_uploaded_file($file['tmp_name'], $target)){
                    return array('success'=> true, "uuid" => $uuid);
                }
            }

            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }

    /**
     * Converts a given size with units to bytes.
     * @param string $str
     */
    protected function toBytes($str){
        $val = substr(trim($str), 0, strlen($str)-1);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Determines whether a directory can be accessed.
     *
     * is_writable() is not reliable on Windows
     *  (http://www.php.net/manual/en/function.is-executable.php#111146)
     * The following tests if the current OS is Windows and if so, merely
     * checks if the folder is writable;
     * otherwise, it checks additionally for executable status (like before).
     *
     * @param string $directory The target directory to test access
     */
    protected function isInaccessible($directory) {
        $isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $folderInaccessible = ($isWin) ? !is_writable($directory) : ( !is_writable($directory) && !is_executable($directory) );
        return $folderInaccessible;
    }
}
