<?php

namespace App\Traits;

trait CommandDisplayUtil
{
    public function  displayAipFull($aip) {
        $this->displayAip($aip);
        if($this->getOutput()->isVerbose()) {
            if($aip->storage_properties) {
                if ($aip->storage_properties->dip) {
                    $this->displayDip($aip->storage_properties->dip);
                }

                if ($aip->storage_properties->bag) {
                    $this->displayBag($aip->storage_properties->bag);
                }
            }
        }
    }

    public function displayAip($aip) {
        $bagName = "<no ref>";
        if(!$aip->storage_properties) {
            $this->warn("Aip have no storage properties");
        } else {
            $bagName = ($aip->storage_properties->bag) ? $aip->storage_properties->bag->name : $bagName;
        }
        if($this->getOutput()->isVerbose()) {
            $this->info("\nAip");
            $this->info("=====================================================");
            $this->info("Id                      : " . $aip->id);
            $this->info("External uuid           : " . $aip->external_uuid);
            $this->info("Name                    : " . $bagName);
            $this->info("Is connected to buckets : " . ($aip->jobs()->count() > 0 ? "true" : "false"));
            if($this->getOutput()->isVeryVerbose()) {
                $aip->jobs->map(function ($job) {
                    $this->info("$job->name ($job->id)");
                });
            }
            $this->info("Files                   : " . $aip->fileObjects()->count());
            if($this->getOutput()->isVeryVerbose()) {
                $aip->fileObjects->map(function ($file) {
                    $this->info($file->fullpath);
                });
            }
        } else {
            $this->info($aip->id." ".$aip->external_uuid." ".$bagName);
        }
    }

    public function displayDip($dip) {
        $bagName = "<no ref>";
        if(!$dip->storage_properties) {
            $this->warn("Aip have no storage properties");
        } else {
            $bagName = ($dip->storage_properties->bag) ? $dip->storage_properties->bag->name : $bagName;
        }
        if($this->getOutput()->isVerbose()) {
            $this->info("\nDip");
            $this->info("=====================================================");
            $this->info("Id                      : " . $dip->id);
            $this->info("External uuid           : " . $dip->external_uuid);
            $this->info("Name                    : " . $bagName);
            $this->info("AIP External uuid       : " . $dip->aip_external_uuid);
            $this->info("Files                   : " . $dip->fileObjects()->count());
            if($this->getOutput()->isVeryVerbose()) {
                $dip->fileObjects->map(function ($file) {
                    $this->info($file->fullpath);
                });
            }
        } else {
            $this->info($dip->id . " " . $dip->external_uuid . " " . $bagName);
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

    public function displayUser($user, bool $forceVerbose = false) {
        if ($this->getOutput()->isVerbose() || $forceVerbose) {
            $this->info("\nUser");
            $this->info("=====================================================");
            $this->info("Id                      : " . $user->id);
            $this->info("Name                    : " . $user->full_name);
            $this->info("Username                : " . $user->username);
            $this->info("e-mail                  : " . $user->email);
        } else {
            $this->info($user->id." ".$user->full_name);
        }
    }

    public function displayOrganization($organization, bool $forceVerbose = false) {
        if ($this->getOutput()->isVerbose() || $forceVerbose) {
            $this->info("\nOrganization");
            $this->info("=====================================================");
            $this->info("Id                      : " . $organization->id);
            $this->info("Uuid                    : " . $organization->uuid);
            $this->info("Name                    : " . $organization->name);
        } else {
            $this->info($organization->uuid." ".$organization->name);
        }
    }

}
