<?php
defined('BASEPATH') or exit('No direct script access allowed');


class ValResepController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0003", "RU0004"])) {
            redirect('AuthController');
        }
        $this->load->model('UserModel', 'um');
        $this->load->model('DokterModel', 'dm');
    }

    public function index()
    {
    }

    public function loadsigna()
    {
        $filtercari   = $this->input->post('pencarianobat');

        $hasil = $this->dm->carisigna($filtercari);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Signa Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Signa Obat Ditemukan';
            $json['Responresult'] = $hasil;
        }

        echo json_encode($json);
    }
}
