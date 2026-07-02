<?php
defined('BASEPATH') or exit('No direct script access allowed');


class AdminController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role_id_pc') !== "RU0001") {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('AdminModel', 'am');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        // return var_dump($data['user']);
        // die;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/index');
        $this->load->view('templates/footer');
    }

    public function ImportData()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        $data['script'] = 'js/admin.js';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/importData_v');
        $this->load->view('templates/footer');
    }

    public function download_template_tindakan()
    {
        $file_path = FCPATH . 'assets/template/template-tindakan.xlsx';

        if (file_exists($file_path)) {
            $this->load->helper('download');
            force_download($file_path, NULL);
        } else {
            show_404();
        }
    }

    public function download_template_obat()
    {
        $file_path = FCPATH . 'assets/template/template-obat.xlsx';

        if (file_exists($file_path)) {
            $this->load->helper('download');
            force_download($file_path, NULL);
        } else {
            show_404();
        }
    }

    public function uploadObat()
    {
        header('Content-Type: application/json');

        $json_data = json_decode(file_get_contents('php://input'), true);

        if (!isset($json_data['data']) || empty($json_data['data'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diterima']);
            return;
        }

        $data_to_insert = [];
        foreach ($json_data['data'] as $row) {
            if (count($row) < 8) continue;
            $data_to_insert[] = [
                'nama' => $row['nama'] ?? null,
                'satuan_kecil_id' => $row['satuan_kecil_id'] ?? null,
                'konversi_satuan'       => !empty($row['konversi_satuan']) ? $row['konversi_satuan'] : 1, // Ambil harga dari upload Excel
                'harga_hna'       => !empty($row['harga_hna']) ? $row['harga_hna'] : 0, // Ambil harga dari upload Excel
                'harga_hna_ppn'       => !empty($row['harga_hna_ppn']) ? $row['harga_hna_ppn'] : 0, // Ambil harga dari upload Excel
                'harga'       => !empty($row['harga']) ? $row['harga'] : 0, // Ambil harga dari upload Excel
                'harga_sat_ppn'       => !empty($row['harga_sat_ppn']) ? $row['harga_sat_ppn'] : 0, // Ambil harga dari upload Excel
                'harga_jual'       => !empty($row['harga_jual']) ? $row['harga_jual'] : 0, // Ambil harga dari upload Excel
            ];
        }


        $insert_result = $this->am->insert_batch_obat($data_to_insert);

        echo json_encode($insert_result);
    }


    public function uploadTindakan()
    {
        header('Content-Type: application/json');

        // Ambil data dari request JSON
        $json_data = json_decode(file_get_contents('php://input'), true);

        if (!isset($json_data['data']) || empty($json_data['data'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diterima']);
            return;
        }

        $data_to_insert = [];

        foreach ($json_data['data'] as $row) {


            if (count($row) < 4) continue; // Pastikan jumlah kolom sesuai template

            $data_to_insert[] = [
                'nama_layan1' => $row['nama_layan1'] ?? null,
                'nama_layan2' => $row['nama_layan2'] ?? null,
                'kategori_id' => !empty($row['kategori_id']) ? $row['kategori_id'] : 0,
                'harga'       => !empty($row['harga']) ? $row['harga'] : 0, // Ambil harga dari upload Excel
            ];
        }

        $insert_result = $this->am->insert_batch_tindakan($data_to_insert);

        echo json_encode($insert_result);
    }
}
