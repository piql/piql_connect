<?php

namespace App\Traits;

trait CommandDisplayUtil
{
    public function  displayAipFull($aip) {
        $this->displayAip($aip);
        if($this->getOutput()->isVerbose()) {
            $this->displayDip($aip->storage_properties->dip);
            $this->displayBag($aip->storage_properties->bag);
        }
    }

    public function displayAip($aip) {
        if($this->getOutput()->isVerbose()) {
            $this->info("\nAip");
            $this->info("=====================================================");
            $this->info("Id                      : " . $aip->id);
            $this->info("External uuid           : " . $aip->external_uuid);
            $this->info("Name                    : " . $aip->storage_properties->bag->name);
            $this->info("Is connected to buckets : " . ($aip->jobs()->count() > 0 ? "true" : "false"));
            $this->info("Files                   : " . $aip->fileObjects()->count());
            if($this->getOutput()->isVeryVerbose()) {
                $aip->fileObjects->map(function ($file) {
                    $this->info($file->fullpath);
                });
            }
        } else {
            $this->info($aip->id." ".$aip->external_uuid." ".$aip->storage_properties->bag->name);
        }
    }

    public function displayDip($dip) {
        if($this->getOutput()->isVerbose()) {
            $this->info("\nDip");
            $this->info("=====================================================");
            $this->info("Id                      : " . $dip->id);
            $this->info("External uuid           : " . $dip->external_uuid);
            $this->info("AIP External uuid       : " . $dip->aip_external_uuid);
            $this->info("Files                   : " . $dip->fileObjects()->count());
            if($this->getOutput()->isVeryVerbose()) {
                $dip->fileObjects->map(function ($file) {
                    $this->info($file->fullpath);
                });
            }
        } else {
            $this->info($dip->id . " " . $dip->external_uuid . " " . $dip->storage_properties->bag->name);
        }
    }

    public function displayBag($bag) {
        if ($this->getOutput()->isVerbose()) {
            $this->info("\nBag");
            $this->info("=====================================================");
            $this->info("Id                      : " . $bag->id);
            $this->info("Name                    : " . $bag->name);
            $this->info("Status                  : " . $bag->status);
            $this->info("Files                   : " . $bag->files()->count());
            if($this->getOutput()->isVeryVerbose()) {
                $bag->files->map(function ($file) {
                    $this->info($file->filename);
                });
            }
        } else {
            $this->info($bag->id." ".$bag->status." ".$bag->name);
        }
    }

}
