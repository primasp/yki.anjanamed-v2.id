<?php
require_once APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php'; // Sesuaikan path dengan lokasi TCPDF di folder vendor

class Tcpdf_library extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}
