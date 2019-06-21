<?php

require_once __DIR__ . '/../vendor/scholarslab/bagit/lib/bagit.php';
require_once __DIR__ . '/../vendor/scholarslab/bagit/lib/bagit_utils.php';

class BagitUtil
{
    private $m_InputFilesSource = array();
    private $m_InputFilesDestination = array();
    private $m_SystemTempDir;
    private $m_TempResources = array();
    private $m_ErrorMessage;
    
    public function __construct()
    {
        $this->m_SystemTempDir = sys_get_temp_dir();
        $this->m_ErrorMessage = '';
    }

    public function __destruct()
    {
        // \todo Delete temp files
    }

    public function errorMessage()
    {
        return $this->m_ErrorMessage;
    }

    public function addFile($filePathSource, $filePathDestination)
    {
        array_push($this->m_InputFilesSource, $filePathSource);
        array_push($this->m_InputFilesDestination, $filePathDestination);
    }

    public function createBag($outputFile)
    {
        $outputFileExtension = pathinfo($outputFile, PATHINFO_EXTENSION);
        $outputFileWithoutExtension = substr($outputFile, 0, strlen($outputFile) - strlen($outputFileExtension) - 1);
    
        // Create temp directory
        if (!$this->createTempDir($tempDir))
        {
            $this->m_ErrorMessage = 'Failed to create temp dir';
            return false;
        }

        // Create empty bag
        $bagPath = $tempDir . '/' . basename($outputFileWithoutExtension);
        $bag = new BagIt($bagPath);

        // Change bagit version
        $bag->bagVersion = array('major' => 0, 'minor' => 97);
        $bagitTxtPath = $bag->getDataDirectory() . '/../bagit.txt';
        $bagitTxtContent = file_get_contents($bagitTxtPath);
        $bagitTxtContent = str_replace('0.96', '0.97', $bagitTxtContent, $replaceCount);
        if ($replaceCount != 1)
        {
            $this->m_ErrorMessage = 'Failed to set bagit version';
            return false;
        }
        if (file_put_contents($bagitTxtPath, $bagitTxtContent, LOCK_EX) === false)
        {
            $this->m_ErrorMessage = 'Failed to write bagit.txt';
            return false;
        }

        // Add optional info to bag-info.txt
        $bag->setBagInfoData('Source-Organization', 'Piql');
        $bag->setBagInfoData('Bagging-Date', date("Y-m-d"));
        $bag->setBagInfoData('External-Description', 'Generated by Piql Connect');
        
        // Validate bag
        if (!$bag->isValid() || !$bag->isExtended())
        {
            $this->m_ErrorMessage = 'Bag is not valid';
            return false;
        }
        $bagInfo = $bag->getBagInfo();
        if ($bagInfo['version'] != '0.97' || $bagInfo['encoding'] != 'UTF-8' || $bagInfo['hash'] != 'sha1')
        {
            $this->m_ErrorMessage = 'Bag has wrong setup';
            return false;
        }

        // Add data
        for ($i = 0; $i < count($this->m_InputFilesSource); $i++)
        {
            $inputFileSource = $this->m_InputFilesSource[$i];
            $inputFileDestination = $this->m_InputFilesDestination[$i];
            $bag->addFile($inputFileSource, 'data/' . $inputFileDestination);
            if (!file_exists($bag->getDataDirectory() . '/' . $inputFileDestination))
            {
                $this->m_ErrorMessage = 'Failed to add file to bag. Source=' . $inputFileSource . ' destination=' . $bag->getDataDirectory() . '/' . $inputFileDestination;
                return false;
            }
        }

        // Update bag with added files
        $bag->update();

        if ($outputFileExtension != 'zip' && $outputFileExtension != 'tgz')
        {
            $this->m_ErrorMessage = 'Bag has wrong extension';
            return false;
        }

        // Output packaged file
        $bag->package($outputFile, $outputFileExtension);

        // Validate created bag
        $createdBag = new BagIt($outputFile);
        $expectedContent = "BagIt-Version: 0.97\n" . "Tag-File-Character-Encoding: UTF-8\n";
        if (file_get_contents($createdBag->bagitFile) != $expectedContent)
        {
            $this->m_ErrorMessage = 'Created bag is not valid';
            return false;
        }
        $createdBag->validate();
        if (count($createdBag->bagErrors) != 0)
        {
            $this->m_ErrorMessage = 'Bag has errors';
            return false;
        }

        return true;
    }

    private function createTempDir(&$retTempDir)
    {
        if (!is_dir($this->m_SystemTempDir))
        {
            return false;
        }

        $tempDir = $this->m_SystemTempDir . "/" . substr(md5(rand()), 0, 7);
        
        if (!mkdir($tempDir))
        {
            return false;
        }

        array_push($this->m_TempResources, $tempDir);
        $retTempDir = $tempDir;
        return true;
    }
}

?>
