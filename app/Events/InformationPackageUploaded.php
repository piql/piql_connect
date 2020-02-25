<?php


namespace App\Events;


class InformationPackageUploaded
{

    public $informationPackage;

    /**
     * InformationPackageUploaded constructor.
     * @param $informationPackage
     */
    public function __construct($informationPackage)
    {
        $this->informationPackage = $informationPackage;
    }
}
