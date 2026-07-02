<?php
defined('BASEPATH') or exit('No direct script access allowed');


class ResumeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('ResumeModel', 'rm');
        $this->load->model('AsessmentModel', 'am');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['poli'] = $this->am->getPoliklinik();
        // return var_dump($data['poli']);
        // die;
        $data['user'] = $this->um->get_user_by_id($user_id);

        $data['script'] = 'js/resume-rj.js';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('resume/resumeRj_view', $data);
        $this->load->view('templates/footer');
    }
    public function getDokterByPoli()
    {
        $poli_id = $this->input->get('poli_id');

        $dokter = $this->am->getDokterByPoli($poli_id);
        echo json_encode($dokter);
    }

    public function cariData()
    {
        $jenis = $this->input->post('jenisLayanan');
        $poliId = $this->input->post('poliId');
        $dokterId = $this->input->post('dokterId');
        $tanggal_mulai = $this->input->post('tanggal_mulai');
        $tanggal_selesai = $this->input->post('tanggal_selesai');

        // ubah format dd/mm/yyyy → yyyy-mm-dd
        $tanggal_mulai = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal_mulai)));
        $tanggal_selesai = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal_selesai)));

        if ($jenis === 'PENUNJANG') {
            $data = $this->rm->getKunjPasienPenunjang($tanggal_mulai, $tanggal_selesai);
        } elseif ($jenis === 'POLIKLINIK') {
            $data = $this->rm->getKunjPasienPoli($jenis, $poliId, $dokterId, $tanggal_mulai, $tanggal_selesai);
        } else {
            $data = $this->rm->getKunjPasienAll($tanggal_mulai, $tanggal_selesai);
        }



        foreach ($data as &$row) {
            // return var_dump($row['poli_id']);
            // die;
            if ($row['poli_id'] === 'APS') {

                $row['detil_pemeriksaan'] =
                    $this->am->getDetilPemeriksaanByEpisode($row['episode_id']);
                // return var_dump($row['detil_pemeriksaan']);
                // die;
            } else {

                $row['detil_pemeriksaan'] = [];
            }
        }

        echo json_encode($data);

        // if (!empty($data)) {
        //     foreach ($data as &$row) {
        //         if ($row->poli_id === 'APS') {
        //             $row->detil_pemeriksaan = $this->am->getDetilPemeriksaanByEpisode($row->episode_id);
        //         } else {
        //             $row->detil_pemeriksaan = [];
        //         }
        //     }
        //     unset($row); // good practice setelah foreach by reference
        // }


    }
}
