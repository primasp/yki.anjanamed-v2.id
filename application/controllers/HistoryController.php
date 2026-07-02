<?php
defined('BASEPATH') or exit('No direct script access allowed');


class HistoryController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0003"])) {
            redirect('AuthController');
        }
        $this->load->model('UserModel', 'um');
        $this->load->model('HistoryModel', 'hm');
        $this->load->model('DokterModel', 'dm');
    }

    public function index($episode_id = null, $pasien_id = null)
    {
        $user_id = $this->session->userdata('user_id_pc');
        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';
        $data['user'] = $this->um->get_user_by_id($user_id, $lokasi_id);
        $data['script'] = 'js/resume-rj.js';

        // return var_dump("oke");
        // die;

        // Ambil list kunjungan pasien
        $data['listKunjungan'] = !empty($pasien_id) ? $this->hm->listHistKunjungan($pasien_id) : [];

        // return var_dump($data['listKunjungan']);
        // die;

        // Tidak memuat detail history di awal
        $data['detilHistory'] = null;

        // Simpan episode yang dipilih
        $data['selected_episode_id'] = $episode_id;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('history/index', $data);
        $this->load->view('templates/footer');
    }
}
