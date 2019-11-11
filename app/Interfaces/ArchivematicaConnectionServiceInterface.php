<?php


namespace App\Interfaces;


interface ArchivematicaConnectionServiceInterface
{
    public function getServiceConnectionByUuid($uuid); /* todo: Query the service  location for contents, optional relative path */
}
