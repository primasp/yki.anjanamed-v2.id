<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * KasirUmumController
 * Menu Kasir UMUM khusus alur YKI Tahap 2.
 */
class KasirUmumController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ['RU0001', 'RU0002', 'RU0003'])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('KasirUmumModel', 'kum');
    }

    public function index()
    {
        $user_id   = $this->session->userdata('user_id_pc');
        $lokasi_id = $this->session->userdata('lokasi_id_pc') ?: '001';

        $data['user']     = $this->um->get_user_by_id($user_id);
        $data['title']    = 'Kasir Umum';
        $data['page_css'] = 'css/kasir-umum-yki.css';
        $data['script']   = 'js/kasir-umum-yki.js';

        $data['kasir_umum_data'] = [
            'kasir' => [
                'nama_petugas' => !empty($data['user']['nama']) ? $data['user']['nama'] : $user_id,
                'tanggal_bayar' => date('d-m-Y'),
                'lokasi_id' => $lokasi_id,
            ],
            'payment_methods' => $this->kum->get_payment_methods(),
            'patients' => $this->kum->get_worklist($lokasi_id, date('Y-m-d'), date('Y-m-d'), '', ''),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kasir_umum/index', $data);
        $this->load->view('templates/footer');
    }

    public function ajax_list_unpaid()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lokasi_id = $this->session->userdata('lokasi_id_pc') ?: '001';
        $tgl1 = $this->input->post('tanggal_mulai', true);
        $tgl2 = $this->input->post('tanggal_selesai', true);
        $keyword = $this->input->post('keyword', true);
        $kategori = $this->input->post('kategori', true);

        if (empty($tgl1)) $tgl1 = date('Y-m-d');
        if (empty($tgl2)) $tgl2 = $tgl1;

        return $this->_json([
            'success' => true,
            'data' => $this->kum->get_worklist($lokasi_id, $tgl1, $tgl2, $keyword, $kategori),
            'csrfHash' => $this->security->get_csrf_hash(),
        ]);
    }

    public function ajax_load_billing()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lokasi_id  = $this->session->userdata('lokasi_id_pc') ?: '001';
        $episode_id = $this->input->post('episode_id', true);

        if (empty($episode_id)) {
            return $this->_json([
                'success' => false,
                'message' => 'Episode ID tidak ditemukan.',
                'csrfHash' => $this->security->get_csrf_hash(),
            ], 400);
        }

        $billing = $this->kum->get_billing_patient($lokasi_id, $episode_id);

        if (empty($billing)) {
            return $this->_json([
                'success' => false,
                'message' => 'Tidak ada tagihan UMUM yang belum dibayar.',
                'csrfHash' => $this->security->get_csrf_hash(),
            ], 404);
        }

        return $this->_json([
            'success' => true,
            'patient' => $billing,
            'csrfHash' => $this->security->get_csrf_hash(),
        ]);
    }

    public function ajax_proses_bayar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $payload = json_decode($this->input->post('payload'), true);

        if (!is_array($payload)) {
            return $this->_json([
                'success' => false,
                'message' => 'Payload pembayaran tidak valid.',
                'csrfHash' => $this->security->get_csrf_hash(),
            ], 400);
        }

        $payload['lokasi_id']  = $this->session->userdata('lokasi_id_pc') ?: '001';
        $payload['created_by'] = $this->session->userdata('user_id_pc');

        $result = $this->kum->proses_bayar_umum($payload);
        $result['csrfHash'] = $this->security->get_csrf_hash();

        return $this->_json($result, !empty($result['success']) ? 200 : 409);
    }

    public function cetak_kwitansi()
    {
        $lokasi_id = $this->session->userdata('lokasi_id_pc') ?: '001';
        $bayar_id = $this->input->get('bayar_id', true);

        if (empty($bayar_id)) {
            show_error('Bayar ID tidak ditemukan.', 400);
            return;
        }

        $data['kwitansi'] = $this->kum->get_kwitansi($lokasi_id, $bayar_id);

        if (empty($data['kwitansi'])) {
            show_error('Data kwitansi tidak ditemukan.', 404);
            return;
        }

        $this->load->view('kasir_umum/print_kwitansi', $data);
    }

    private function _json($payload, $status = 200)
    {
        return $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
