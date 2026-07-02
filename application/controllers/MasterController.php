<?php
defined('BASEPATH') or exit('No direct script access allowed');


class MasterController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", 'RU0004'])) {
            redirect('AuthController');
        }
        $this->load->model('UserModel', 'um');
        $this->load->model('MasterModel', 'mm');
    }

    public function obatStok()
    {
        $depo_obat = $this->input->get('depo_obat');



        $data['script'] = 'js/master.js';
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        $data['depo_obat'] = $this->mm->getDepoObat();

        $data['stok_list'] = $this->mm->getAllStok($depo_obat);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('master/allStok_v', $data);
        $this->load->view('templates/footer');
    }

    public function adjust_stok()
    {
        $obat_id = $this->input->post('obat_id');
        $gudang_id = $this->input->post('gudang_id');
        $adjustment_value = $this->input->post('adjustment_value');

        // Validasi input
        if (empty($obat_id) || empty($gudang_id) || $adjustment_value === null) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
            return;
        }

        // Cek apakah stok dengan obat_id & gudang_id ada
        $stok_data = $this->mm->getStockByObatGudang($obat_id, $gudang_id);

        if (!$stok_data) {
            echo json_encode(['status' => 'error', 'message' => 'Data stok tidak ditemukan']);
            return;
        }

        // Lakukan update adjustment
        $result = $this->mm->updateAdjustment($obat_id, $gudang_id, $adjustment_value);


        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Adjustment stok berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan adjustment stok']);
        }
    }


    public function obatMaster()
    {


        $gol_obat = $this->input->get('gol_obat');
        $search = $this->input->get('search');

        $data['script'] = 'js/master.js';
        $user_id = $this->session->userdata('user_id_pc');

        $data['user'] = $this->um->get_user_by_id($user_id);
        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id')])->row_array();

        $data['golongan_obat'] = $this->mm->getGolonganObat();

        $data['sediaan_obat'] = $this->mm->getSediaanObat();

        $data['satuan_obat'] = $this->mm->getSatuanObat();

        $data['generik_obat'] = $this->mm->getGenerikObat();

        $data['pabrik_obat'] = $this->mm->getPabrikObat();

        $data['route_obat'] = $this->mm->getRouteObat();

        $data['obat_list'] = $this->mm->getAllObat($gol_obat, $search);

        // return var_dump($data['obat_list']);
        // die;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('master/allObat_v', $data);
        $this->load->view('templates/footer');
    }


    public function tambahObatxxxx()
    {
        $data = $this->input->post();

        // Hapus koma dari nilai harga sebelum disimpan
        $data['hna_besar'] = str_replace(',', '', $data['hna_besar']);
        $data['hna_ppn_besar'] = str_replace(',', '', $data['hna_ppn_besar']);
        $data['jual_sat'] = str_replace(',', '', $data['jual_sat']);
        $data['jual_sat_hna_ppn'] = str_replace(',', '', $data['jual_sat_hna_ppn']);
        $data['harga_final'] = str_replace(',', '', $data['harga_final']);

        $result = $this->mm->insertObat($data);

        echo json_encode(
            $result
                ? ['status' => 'success', 'message' => 'Obat berhasil ditambahkan']
                : ['status' => 'error', 'message' => 'Gagal menambahkan obat']
        );
    }

    public function tambahObat()
    {
        header('Content-Type: application/json');

        $data = $this->input->post();

        $lokasi_id = '001';
        $user_id   = $this->session->userdata('user_id_pc');

        if (empty($lokasi_id) || empty($user_id)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Session lokasi/user tidak ditemukan. Silakan login ulang.'
            ]);
            return;
        }

        /*
     * Field wajib minimal
     */
        $required = [
            'nama_obat'          => 'Nama Obat',
            'satuan_jual'        => 'Satuan Jual',
            'satuan_beli'        => 'Satuan Beli',
            'kon_satuan'         => 'Konversi Satuan',
            'hna_besar'          => 'Harga HNA',
            'hna_ppn_besar'      => 'Harga HNA PPN',
            'jual_sat'           => 'Harga Satuan',
            'jual_sat_hna_ppn'   => 'Harga Satuan + PPN',
            'harga_final'        => 'Harga Jual',
            'gudang_id'          => 'Depo / Gudang',
        ];

        foreach ($required as $key => $label) {
            if (!isset($data[$key]) || trim($data[$key]) === '') {
                echo json_encode([
                    'status'  => 'error',
                    'message' => $label . ' wajib diisi.'
                ]);
                return;
            }
        }

        /*
     * Normalisasi angka.
     * Mendukung input:
     * 1,200
     * 1.200
     * 1200
     */
        $numericFields = [
            'hna_besar',
            'hna_ppn_besar',
            'jual_sat',
            'jual_sat_hna_ppn',
            'harga_final',
            'kon_satuan',
            'stok_awal'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->_cleanNumber($data[$field]);
            }
        }

        /*
     * Default tambahan untuk model insertObat baru
     */
        $data['lokasi_id']   = $lokasi_id;
        $data['created_by']  = $user_id;

        $data['gudang_id'] = !empty($data['gudang_id'])
            ? $data['gudang_id']
            : 'DEPO00000000APT';

        $data['stok_awal'] = isset($data['stok_awal']) && $data['stok_awal'] !== ''
            ? $data['stok_awal']
            : 0;

        /*
     * Optional field agar tidak undefined index di model
     */
        $optionalDefaults = [
            'generik_obat'            => null,
            'gol_obat'                => null,
            'pabrik'                  => null,
            'route'                   => null,
            'ecatalogue'              => null,
            'batasan_fornas'          => null,
            'nomor_registrasi'        => null,
            'harga_final_checkbox'    => null,
            'obat_fornas'             => null,
            'obat_produksi'           => null,
            'obat_hialert'            => null,
            'obat_bpjs'               => null,
            'semuaykn_obat'           => null,
            'alkes'                   => null,
            'is_pembungkus'           => null,
            'sediaan_obat'            => null,
        ];

        foreach ($optionalDefaults as $key => $defaultValue) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $defaultValue;
            }
        }

        // return var_dump($data);
        // die;

        $result = $this->mm->insertObat($data);

        if ($result) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Obat berhasil ditambahkan.',
                'data'    => is_array($result) ? $result : null
            ]);
            return;
        }

        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menambahkan obat.'
        ]);
    }

    public function getObatByIdxxxx()
    {
        $obat_id = $this->input->post('obat_id');
        $data = $this->mm->getObatById($obat_id);

        if ($data) {
            // Format harga_hna (dan harga lainnya jika perlu)
            $data->harga_hna = number_format($data->harga_hna, 2, ',', '.');

            $data->harga_hna_ppn = number_format($data->harga_hna_ppn, 2, ',', '.');
            $data->harga = number_format($data->harga, 2, ',', '.');
            $data->harga_sat_ppn = number_format($data->harga_sat_ppn, 2, ',', '.');
            $data->harga_jual = number_format($data->harga_jual, 2, ',', '.');
            // $data->harga_hna = number_format($data->harga_hna, 2, ',', '.');
            // Jika ingin format juga field lain:
            // $data->harga_jual = number_format($data->harga_jual, 2, ',', '.');
        }

        echo json_encode($data);
    }

    public function getObatById()
    {
        header('Content-Type: application/json');

        $lokasi_id = '001';
        $obat_id   = $this->input->post('obat_id', true);



        if (empty($obat_id)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Obat ID kosong.'
            ]);
            return;
        }

        $data = $this->mm->getObatById($lokasi_id, $obat_id);
        // return var_dump($data);
        // die;
        if (empty($data)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data obat tidak ditemukan.'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function updateObatxxxx()
    {
        $data = $this->input->post();

        // Hapus koma dari nilai harga sebelum disimpan
        $data['edit_hna_besar'] = str_replace(',', '', $data['edit_hna_besar']);
        $data['edit_hna_ppn_besar'] = str_replace(',', '', $data['edit_hna_ppn_besar']);
        $data['edit_jual_sat'] = str_replace(',', '', $data['edit_jual_sat']);
        $data['edit_jual_sat_hna_ppn'] = str_replace(',', '', $data['edit_jual_sat_hna_ppn']);
        $data['harga_edit_final'] = str_replace(',', '', $data['harga_edit_final']);

        $result = $this->mm->updateObat($data);

        // return var_dump($data);
        // die;

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Obat berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui obat']);
        }
    }

    public function updateObat()
    {
        header('Content-Type: application/json');

        $data = $this->input->post();

        $lokasi_id = '001';
        $user_id   = $this->session->userdata('user_id_pc');

        if (empty($data['obat_id'])) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Obat ID tidak ditemukan.'
            ]);
            return;
        }

        if (empty($data['nama_obat'])) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Nama obat wajib diisi.'
            ]);
            return;
        }

        $numericFields = [
            'kon_satuan',
            'hna_besar',
            'hna_ppn_besar',
            'jual_sat',
            'jual_sat_hna_ppn',
            'harga_final'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->_cleanNumber($data[$field]);
            }
        }

        $data['lokasi_id']       = $lokasi_id;
        $data['last_updated_by'] = $user_id;

        $result = $this->mm->updateObat($data);

        if ($result) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Obat berhasil diperbarui.'
            ]);
            return;
        }

        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui obat.'
        ]);
    }

    public function tindakanMaster()
    {
        $gol_tindakan = $this->input->get('gol_tindakan');
        $search = $this->input->get('search');

        $data['script'] = 'js/master.js';

        $user_id = $this->session->userdata('user_id_pc');
        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';



        $data['user'] = $this->um->get_user_by_id($user_id, $lokasi_id);



        $data['tindakan_list'] = $this->mm->getAllTindakan($lokasi_id, $gol_tindakan, $search);

        // return var_dump($data['tindakan_list']);
        // die;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('master/allTindakan_v', $data);
        $this->load->view('templates/footer');
    }

    public function getTindakanById()
    {
        $layan_id = $this->input->post('id');
        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';

        $sql = "SELECT a.*, b.harga
                    FROM pc01_keu_layan_ms a
                    LEFT JOIN pc01_keu_harga_dt b
                        ON a.layan_id = b.layan_id
                        AND a.lokasi_id = b.lokasi_id
                        AND b.kelas_id='6'
                    WHERE a.layan_id = ?
                    AND a.lokasi_id = ?
                ";

        $data = $this->db->query($sql, [$layan_id, $lokasi_id])->row();

        echo json_encode($data);
    }


    public function updateTindakan()
    {
        $layan_id = $this->input->post('layan_id');
        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';

        $harga = preg_replace('/[^0-9]/', '', $this->input->post('harga'));

        $this->db->trans_start();


        // update master tindakan
        $this->db->where('layan_id', $layan_id);
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->update('pc01_keu_layan_ms', [
            'nama_layan1' => $this->input->post('nama_layan1'),
            'nama_layan2' => $this->input->post('nama_layan2'),
            'kategori_id' => $this->input->post('kategori_id')
        ]);

        // update harga
        $this->db->where('layan_id', $layan_id);
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->where('kelas_id', '6');
        $this->db->update('pc01_keu_harga_dt', [
            'harga' => $harga
        ]);


        $this->db->trans_complete();



        echo json_encode([
            "Responcode" => "00",
            "Respondesc" => "Data tindakan berhasil diperbarui"
        ]);
    }

    public function deleteTindakan()
    {
        $layan_id  = $this->input->post('id');
        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';

        $this->db->where('layan_id', $layan_id);
        $this->db->where('lokasi_id', $lokasi_id);
        $this->db->update('pc01_keu_layan_ms', [
            'aktif' => '0'
        ]);

        echo json_encode([
            "Responcode" => "00",
            "Respondesc" => "Tindakan berhasil dinonaktifkan"
        ]);
    }

    public function simpanTindakan()
    {

        // $lokasi_id = $this->session->userdata('lokasi_id_pc');
        $lokasi_id = '001';

        $nama_layan1 = $this->input->post('nama_layan1');
        $nama_layan2 = $this->input->post('nama_layan2');
        $kategori_id = $this->input->post('kategori_id');

        $harga = preg_replace('/[^0-9]/', '', $this->input->post('harga'));

        // ambil layan_id dari fungsi DB
        $sql = "SELECT generate_id('TDK'::character varying, 15, 
            'pc01_keu_layan_ms'::character varying, 
            'layan_id'::character varying) AS layan_id";
        $result = $this->db->query($sql)->row();


        $layan_id = $result->layan_id;

        $this->db->trans_start();

        // INSERT MASTER TINDAKAN
        $this->db->insert('pc01_keu_layan_ms', [
            'lokasi_id'   => $lokasi_id,
            'layan_id'    => $layan_id,
            'nama_layan1' => $nama_layan1,
            'nama_layan2' => $nama_layan2,
            'kategori_id' => $kategori_id,
            'aktif'       => '1'
        ]);

        // ambil ms_skema_id
        $skema = $this->db
            ->where('lokasi_id', $lokasi_id)
            ->get('pc01_keu_harga_ms')
            ->row();


        if ($skema) {

            $this->db->insert('pc01_keu_harga_dt', [
                'lokasi_id'   => $lokasi_id,
                'ms_skema_id' => $skema->ms_skema_id,
                'kelas_id'    => '6',
                'layan_id'    => $layan_id,
                'harga'       => $harga
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            echo json_encode([
                "Responcode" => "01",
                "Respondesc" => "Gagal menyimpan tindakan"
            ]);
        } else {

            echo json_encode([
                "Responcode" => "00",
                "Respondesc" => "Tindakan berhasil disimpan",
                "layan_id" => $layan_id
            ]);
        }
    }


    public function ManajemenObat()
    {
        $data['script'] = 'js/manajemen-obat.js';
        $data['page_css'] = 'css/manajemen-obat.css';

        $user_id   = $this->session->userdata('user_id_pc');
        $lokasi_id = '001';

        $data['user'] = $this->um->get_user_by_id($user_id, $lokasi_id);
        $data['default_gudang_id'] = 'DEPO00000000APT';

        $filter = [
            'depo_obat'   => $this->input->get('depo_obat'),
            'gol_obat'    => $this->input->get('gol_obat'),
            'status_stok' => $this->input->get('status_stok'),
            'search'      => $this->input->get('search'),
        ];

        $data['filter'] = $filter;

        $data['summary']        = $this->mm->getSummaryManajemenObat($lokasi_id);

        $data['obat_list']      = $this->mm->getManajemenObat($lokasi_id, $filter);

        $data['depo_obat']      = $this->mm->getDepoObat();

        $data['golongan_obat']  = $this->mm->getGolonganObat();

        $data['sediaan_obat']   = $this->mm->getSediaanObat();

        $data['satuan_obat']    = $this->mm->getSatuanObat();

        $data['generik_obat']   = $this->mm->getGenerikObat();

        $data['pabrik_obat']    = $this->mm->getPabrikObat($lokasi_id);

        $data['route_obat']     = $this->mm->getRouteObat();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('master/manajemenObat_v', $data);
        $this->load->view('templates/footer');
        // return var_dump($data['user']);
        // die;
    }


    public function adjust_stok_fisik()
    {
        header('Content-Type: application/json');

        $lokasi_id = '001';
        $user_id   = $this->session->userdata('user_id_pc');

        $obat_id    = trim((string) $this->input->post('obat_id', true));
        $gudang_id  = trim((string) $this->input->post('gudang_id', true));
        $stok_fisik = $this->input->post('stok_fisik', true);

        if (empty($gudang_id) || strtolower($gudang_id) === 'semua' || $gudang_id === '-') {
            $gudang_id = 'DEPO00000000APT';
        }

        if (empty($obat_id) || $stok_fisik === null || $stok_fisik === '') {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Obat dan stok fisik wajib diisi.',
                'debug'   => [
                    'obat_id'    => $obat_id,
                    'gudang_id'  => $gudang_id,
                    'stok_fisik' => $stok_fisik,
                    'lokasi_id'  => $lokasi_id
                ]
            ]);
            return;
        }

        if (!is_numeric($stok_fisik)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Stok fisik harus angka.'
            ]);
            return;
        }

        try {
            $result = $this->mm->adjustStokFisik(
                $lokasi_id,
                $obat_id,
                $gudang_id,
                (float) $stok_fisik,
                $user_id
            );

            echo json_encode([
                'status'  => 'success',
                'message' => 'Adjustment stok berhasil disimpan.',
                'data'    => $result
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'debug'   => [
                    'obat_id'    => $obat_id,
                    'gudang_id'  => $gudang_id,
                    'stok_fisik' => $stok_fisik,
                    'lokasi_id'  => $lokasi_id
                ]
            ]);
        }
    }

    private function _cleanNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim((string) $value);
        $value = preg_replace('/[^0-9,.\-]/', '', $value);

        $hasComma = strpos($value, ',') !== false;
        $hasDot   = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return is_numeric($value) ? $value : 0;
        }

        if ($hasDot && preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
            return is_numeric($value) ? $value : 0;
        }

        if ($hasDot && preg_match('/^\d+\.\d{1,2}$/', $value)) {
            return is_numeric($value) ? $value : 0;
        }

        if ($hasComma && preg_match('/^\d{1,3}(,\d{3})+$/', $value)) {
            $value = str_replace(',', '', $value);
            return is_numeric($value) ? $value : 0;
        }

        if ($hasComma) {
            $value = str_replace(',', '.', $value);
            return is_numeric($value) ? $value : 0;
        }

        return is_numeric($value) ? $value : 0;
    }
}
