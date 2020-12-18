<?php


namespace App\Interfaces;


interface ArchivematicaDashboardClientInterface
{

    public function initiateTransfer( $transferName, $accession, $directory );
    public function getUnapprovedList();
    public function getTransferStatus( $uuid = "" );
    public function getIngestStatus( $uuid = "" );
    public function approveTransfer( $directory );
    public function hideTransferStatus( $uuid );
    public function hideIngestStatus( $uuid );

}
