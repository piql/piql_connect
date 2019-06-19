<?php

require_once './vendor/scholarslab/bagit/lib/bagit.php';
require_once './vendor/scholarslab/bagit/lib/bagit_utils.php';

class BagitUtil
{
    var $m_InputFiles = array();
    var $m_SystemTempDir;
    var $m_TempResources = array();
    
    public function __construct()
    {
        $this->m_SystemTempDir = sys_get_temp_dir();
    }

    public function __destruct()
    {
        // \todo Delete temp files
    }

    public function addFile($filePath)
    {
        array_push($this->m_InputFiles, $filePath);
    }

    public function createBag($outputFile)
    {
        $outputFileExtension = pathinfo($outputFile, PATHINFO_EXTENSION);
        $outputFileWithoutExtension = substr($outputFile, 0, strlen($outputFile) - strlen($outputFileExtension) - 1);
    
        // Create temp directory
        if (!$this->createTempDir($tempDir))
        {
            return false;
        }

        // Create empty bag
        $bagPath = $tempDir . '/' . basename($outputFileWithoutExtension);
        $bag = new BagIt($bagPath);
        if (!$bag->isValid() || !$bag->isExtended())
        {
            return false;
        }

        // Validate bag
        $bagInfo = $bag->getBagInfo();
        if ($bagInfo['version'] != '0.96' || $bagInfo['encoding'] != 'UTF-8' || $bagInfo['hash'] != 'sha1')
        {
            return false;
        }

        // Add data
        foreach ($this->m_InputFiles as $inputFile)
        {
            $bag->addFile($inputFile, 'data/' . basename($inputFile));
        }

        // Update bag with added files
        $bag->update();

        if ($outputFileExtension != 'zip' && $outputFileExtension != 'tgz')
        {
            return false;
        }

        // Output packaged file
        $bag->package($outputFile, $outputFileExtension);

        // Validate created bag
        $createdBag = new BagIt($outputFile);
        $expectedContent = "BagIt-Version: 0.96\n" . "Tag-File-Character-Encoding: UTF-8\n";
        if (file_get_contents($createdBag->bagitFile) != $expectedContent)
        {
            return false;
        }
        $createdBag->validate();
        if (count($createdBag->bagErrors) != 0)
        {
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
