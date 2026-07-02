<?php
defined('BASEPATH') or exit('No direct script access allowed');


class PasienController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('PasienModel', 'pm');
        $this->load->model('BpjsApiModel', 'bm');
    }

    public function index()
    {
        $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id_pc')])->row_array();
        return var_dump($data['user']);
        die;
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/index');
        $this->load->view('templates/footer');
    }

    public function editPasienBaru()
    {
        // Pastikan ini adalah request POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_error('Metode yang digunakan tidak diizinkan', 405);
        }

        // Ambil data dari form
        $data_update = [
            'nama' => $this->input->post('edit_nama', true),
            'no_kk' => $this->input->post('edit_no_kakel', true),
            'no_identitas' => $this->input->post('edit_nik', true),
            'tempat_lahir_txt' => $this->input->post('edit_tempat_lahir', true),
            'tgl_lahir' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('edit_tgl_lahir', true)))),
            'sex_id' => $this->input->post('edit_sex_id', true),
            'nama_pasangan' => $this->input->post('edit_nama_pasangan', true),
            'email' => $this->input->post('edit_email', true),
            'nama_ibukandung' => $this->input->post('edit_nama_ibukandung', true),
            'marital_status_id' => $this->input->post('edit_marital_status_id', true),
            'pas_goldar_id' => $this->input->post('edit_pas_goldar_id', true),
            'no_selular' => $this->input->post('edit_no_selular', true),
            'mr_lama' => $this->input->post('edit_rm_lama', true),

            // Alamat KTP
            'propinsi_id' => $this->input->post('edit_propinsi_id', true),
            'kabupaten_id' => $this->input->post('edit_kabupaten_id', true),
            'kecamatan_id' => $this->input->post('edit_kecamatan_id', true),
            'kelurahan_id' => $this->input->post('edit_kelurahan_id', true),
            'alamat1' => $this->input->post('edit_alamat', true),
            'rt' => $this->input->post('edit_rt', true),
            'rw' => $this->input->post('edit_rw', true),
            'kode_pos' => $this->input->post('edit_kodepos', true),

            // Alamat Domisili
            'dms_propinsi' => $this->input->post('edit_dms_propinsi_id', true),
            'dms_kota' => $this->input->post('edit_dms_kabupaten_id', true),
            'dms_kecamatan' => $this->input->post('edit_dms_kecamatan_id', true),
            'dms_kelurahan' => $this->input->post('edit_dms_kelurahan_id', true),
            'alamat2' => $this->input->post('edit_dms_alamat', true),
            'dms_rt' => $this->input->post('edit_dms_rt', true),
            'dms_rw' => $this->input->post('edit_dms_rw', true),
            'dms_kodepos' => $this->input->post('edit_dms_kodepos', true),

            // Penjamin
            'akhir_rekanan_id' => $this->input->post('edit_rekanan_id', true),
            'no_kartuprov' => $this->input->post('edit_bpjs_no', true),

            // Data Identitas Penunjang
            'pekerjaan_id' => $this->input->post('edit_pekerjaan_id', true),
            'pendidikan_id' => $this->input->post('edit_pendidikan_id', true),
            'agama_id' => $this->input->post('edit_agama_id', true),
            'ethnic_id' => $this->input->post('edit_ethnic_id', true),

            // Penanggung Jawab
            'pjp_nama' => $this->input->post('edit_pjp_nama', true),
            'pjp_hp' => $this->input->post('edit_pjp_hp', true),
            'pjp_hubungan_id' => $this->input->post('edit_pjp_hubungan_id', true)
        ];

        // Ambil ID pasien
        $pasien_id = $this->input->post('edit_pasien_id', true);

        if (!$pasien_id) {
            $response = [
                'status' => false,
                'message' => 'ID Pasien tidak ditemukan!'
            ];
            echo json_encode($response);
            return;
        }

        // Update data pasien
        $update_result = $this->pm->updatePasien($pasien_id, $data_update);

        if ($update_result) {
            $response = [
                'message' => 'Data pasien berhasil diperbarui!',
                'success' => true

            ];
        } else {
            $response = [
                'message' => 'Terjadi kesalahan saat memperbarui data pasien!',
                'success' => false

            ];
        }

        echo json_encode($response);

        // return var_dump($pasien_id);
        // die;
    }

    public function savePasienBaru()
    {
        // Pastikan ini adalah request POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_error('Metode yang digunakan tidak diizinkan', 405);
        }

        $data = [
            'nama' => $this->input->post('nama'),
            'no_kakel' => $this->input->post('no_kakel'),
            'nik' => $this->input->post('nik'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tgl_lahir' => $this->input->post('tgl_lahir'),
            'sex_id' => $this->input->post('sex_id'),
            'nama_pasangan' => $this->input->post('nama_pasangan'),
            'email' => $this->input->post('email'),
            'nama_ibukandung' => $this->input->post('nama_ibukandung'),
            'marital_status_id' => $this->input->post('marital_status_id'),
            'pas_goldar_id' => $this->input->post('pas_goldar_id'),
            'no_selular' => $this->input->post('no_selular'),
            'rm_lama' => $this->input->post('rm_lama'),

            'dms_propinsi_id' => $this->input->post('dms_propinsi_id'),
            'propinsi_id' => $this->input->post('propinsi_id'),
            'kabupaten_id' => $this->input->post('kabupaten_id'),
            'dms_kabupaten_id' => $this->input->post('dms_kabupaten_id'),
            'kecamatan_id' => $this->input->post('kecamatan_id'),
            'dms_kecamatan_id' => $this->input->post('dms_kecamatan_id'),
            'kelurahan_id' => $this->input->post('kelurahan_id'),
            'dms_kelurahan_id' => $this->input->post('dms_kelurahan_id'),
            'alamat' => $this->input->post('alamat'),
            'rt' => $this->input->post('rt'),
            'rw' => $this->input->post('rw'),
            'kodepos' => $this->input->post('kodepos'),
            'dms_alamat' => $this->input->post('dms_alamat'),
            'dms_rt' => $this->input->post('dms_rt'),
            'dms_rw' => $this->input->post('dms_rw'),
            'dms_kodepos' => $this->input->post('dms_kodepos'),
            'rekanan_id' => $this->input->post('rekanan_id'),
            'bpjs_no' => $this->input->post('bpjs_no'),
            'pekerjaan_id' => $this->input->post('pekerjaan_id'),
            'pendidikan_id' => $this->input->post('pendidikan_id'),
            'agama_id' => $this->input->post('agama_id'),
            'ethnic_id' => $this->input->post('ethnic_id'),
            'pjp_nama' => $this->input->post('pjp_nama'),
            'pjp_hp' => $this->input->post('pjp_hp'),
            'pjp_hubungan_id' => $this->input->post('pjp_hubungan_id'),
        ];


        // Validasi data
        // if (empty($data['nama']) || empty($data['nik']) || empty($data['tgl_lahir'])) {

        if (
            empty($data['nama']) ||
            empty($data['nik']) ||
            empty($data['tgl_lahir']) ||

            empty($data['propinsi_id']) ||
            empty($data['kabupaten_id']) ||
            empty($data['kecamatan_id']) ||
            empty($data['kelurahan_id']) ||
            empty($data['alamat']) ||
            empty($data['rt']) ||
            empty($data['rw'])
        ) {





            echo json_encode([
                'status' => false,
                // 'message' => 'Nama, NIK, dan Tanggal Lahir wajib diisi.'
                'message' => 'Data identitas dan alamat KTP wajib diisi lengkap.'
            ]);
            return;
        }

        // =======================
        // VALIDASI NIK
        // =======================
        $nik = trim($data['nik']);

        if (!preg_match('/^[0-9]{16}$/', $nik)) {
            echo json_encode([
                'success' => false,
                'message' => 'NIK wajib terdiri dari 16 digit angka.'
            ]);
            return;
        }


        // Simpan data ke database melalui model
        $this->pm->insertPasien($data);

        echo json_encode([
            'success' => true,
            'message' => 'Registrasi pasien baru berhasil disimpan'
        ]);

        // $data = [
        //     'message' => 'Registrasi pasien baru berhasil disimpan',
        //     'success' => true
        // ];
        // echo json_encode($data);


        // if ($result) {
        //     echo json_encode([
        //         'status' => true,
        //         'message' => 'Data pasien berhasil disimpan.',
        //         'redirect_url' => base_url('Pasien-All') // Redirect setelah sukses
        //     ]);
        // } else {
        //     echo json_encode([
        //         'status' => false,
        //         'message' => 'Gagal menyimpan data pasien.'
        //     ]);
        // }
    }



    public function tambahPasienBaru()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id')])->row_array();
        $data['script'] = 'js/pasien.js';

        $goldar = $this->pm->getGoldar();
        $status_kawin = $this->pm->getStatus();
        $propinsi = $this->pm->getProvinsi();


        $penjamin = $this->pm->getPenjamin();
        $hubunganPJP = $this->pm->getHubPJP();

        $pekerjaan = $this->pm->getPekerjaan();
        $pendidikan = $this->pm->getPendidikan();
        $agama = $this->pm->getAgama();
        $suku = $this->pm->getSuku();

        $nik = $this->input->get('nik');
        $noBpjs = $this->input->get('noBpjs');
        $nama = $this->input->get('nama');
        // $tglLahir = $this->input->get('tglLahir');
        $tglLahir = date('d/m/Y', strtotime($this->input->get('tglLahir')));
        $noHp = $this->input->get('noHp');
        $sex = $this->input->get('sex');
        if (strtolower($sex) === 'laki-laki') {
            $sex = 'L';
        } else {
            $sex = 'P';
        }

        $data['dataPasBaru'] = null;

        // Periksa apakah ada data dari input
        if (!empty($nik) || !empty($noBpjs)) {



            $data['dataPasBaru'] = [
                'nik' => $nik,
                'noBpjs' => $noBpjs,
                'nama' => $nama,
                'tglLahir' => $tglLahir,
                'noHp' => $noHp,
                'sex' => $sex,
                'goldar' => $goldar,
                'statusKawin' => $status_kawin,
                'propinsi' => $propinsi,
                'rekanan' => $penjamin,
                'hubungan' => $hubunganPJP,
                'pekerjaan' => $pekerjaan,
                'pendidikan' => $pendidikan,
                'agama' => $agama,
                'suku' => $suku,
            ];


            // return var_dump($data['dataPasBaru']['sex']);
            // die;
        } else {
            $data['dataPasBaru'] = [
                // 'nik' => $nik,
                // 'noBpjs' => $noBpjs,
                // 'nama' => $nama,
                // 'tglLahir' => $tglLahir,
                // 'noHp' => $noHp,
                // 'sex' => $sex,
                'goldar' => $goldar,
                'statusKawin' => $status_kawin,
                'propinsi' => $propinsi,
                'rekanan' => $penjamin,
                'hubungan' => $hubunganPJP,
                'pekerjaan' => $pekerjaan,
                'pendidikan' => $pendidikan,
                'agama' => $agama,
                'suku' => $suku,
            ];
        }


        // return var_dump($data['dataPasBaru']['sex']);
        // die;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/addPasien', $data);
        $this->load->view('templates/footer');
    }


    public function get_kota()
    {
        $prov_id = $this->input->post('prov_id');



        $data = $this->pm->getKota($prov_id);
        // return var_dump($data);
        // die;


        echo json_encode($data);
    }


    public function get_kecamatan()
    {
        $kota_id = $this->input->post('kota_id');

        $data = $this->pm->getKecamatan($kota_id);

        echo json_encode($data);
    }


    public function get_kelurahan()
    {
        $kecamatan_id = $this->input->post('kecamatan_id');

        $data = $this->pm->getKelurahan($kecamatan_id);

        echo json_encode($data);
    }


    public function getPasienById()
    {
        $pasien_id = $this->input->post('pasien_id');


        $data = $this->pm->getPasienById($pasien_id);
        echo json_encode($data);
    }




    public function pasienAll()
    {
        $user_id = $this->session->userdata('user_id_pc');

        if (!$user_id) {
            redirect('AuthController');
        }

        $data['user'] = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/pasien.js';

        $data['pasien_list'] = $this->pm->get_all_pasien();

        $data['goldar']  = $this->pm->getGoldar();
        $data['status_kawin']  = $this->pm->getStatus();
        $data['propinsi']  = $this->pm->getProvinsi();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/allPasien', $data);
        $this->load->view('templates/footer');
    }

    public function editPasien($pasien_id)
    {
        $data['script'] = 'js/pasien.js';
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id')])->row_array();
        $data['pasien'] = $this->pm->getPasienById($pasien_id);
        $data['propinsi'] = $this->pm->getProvinsi();
        $data['statusKawin'] = $this->pm->getStatus();
        $data['goldar'] = $this->pm->getGoldar();
        $data['pekerjaan'] = $this->pm->getPekerjaan();
        $data['pendidikan'] = $this->pm->getPendidikan();
        $data['agama'] = $this->pm->getAgama();
        $data['suku'] = $this->pm->getSuku();
        $data['rekanan']  = $this->pm->getPenjamin();
        $data['hubunganpjp'] = $this->pm->getHubPJP();



        // return var_dump($data['rekanan']);
        // die;



        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/editPasien', $data);
        $this->load->view('templates/footer');
    }

    public function detailPasien($pasien_id)
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/pasien.js';

        // Data pasien
        $data['pasien'] = $this->pm->getDetilPasienById($pasien_id);


        // $data['propinsi'] = $this->pm->getProvinsi();
        // $data['statusKawin'] = $this->pm->getStatus();
        // $data['goldar'] = $this->pm->getGoldar();
        // $data['pekerjaan'] = $this->pm->getPekerjaan();
        // $data['pendidikan'] = $this->pm->getPendidikan();
        // $data['agama'] = $this->pm->getAgama();
        // $data['suku'] = $this->pm->getSuku();
        // $data['rekanan']  = $this->pm->getPenjamin();
        // $data['hubunganpjp'] = $this->pm->getHubPJP();

        $data['title'] = "Detail Pasien";

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/detailPasien', $data);
        $this->load->view('templates/footer');
    }


    public function ajax_pasien_list()
    {
        $lokasi_id = '001';

        if (!$lokasi_id) {
            echo json_encode([
                "draw" => intval($this->input->post("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            return;
        }

        $draw   = intval($this->input->post("draw"));
        $start  = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));

        // return var_dump($length);
        // die;

        $search = $this->input->post("search");
        $keyword = isset($search['value']) ? trim($search['value']) : "";

        // ✅ Wajib minimal 3 char (biar tidak full scan)
        if ($keyword !== "" && mb_strlen($keyword) < 3) {
            echo json_encode([
                "draw" => $draw,
                "recordsTotal" => $this->pm->count_all_pasien($lokasi_id),
                "recordsFiltered" => 0,
                "data" => [],
                "message" => "Minimal 3 karakter untuk pencarian."
            ]);
            return;
        }

        $list = $this->pm->get_pasien_datatable($lokasi_id, $start, $length, $keyword);
        $recordsTotal = $this->pm->count_all_pasien($lokasi_id);
        $recordsFiltered = $this->pm->count_filtered_pasien($lokasi_id, $keyword);

        $data = [];

        foreach ($list as $p) {

            if ($p->tgl_masuk_terakhir == '') {
                $p->tgl_masuk_terakhir = '-';
                $p->nama_poli_terakhir = '';
            } else {
                $p->tgl_masuk_terakhir = $p->tgl_masuk_terakhir;
                $p->nama_poli_terakhir = '(' . $p->nama_poli_terakhir . ')';
            };
            $nama = '
                    <div class="fw-semibold">' . htmlspecialchars($p->nama) . '</div>
                    <small class="text-muted fw-bold">No.MR : 
                        <span class="fw-bold text-danger">' . $p->int_pasien_id . '</span>
                    </small>
                    <br>
                    <small class="text-muted fw-bold">
                        Kunj. Terakhir : 
                        <span class="fw-bold text-danger">' . $p->tgl_masuk_terakhir . '</span>
                         <span class="fw-bold">' . $p->nama_poli_terakhir . '</span>
                    </small>
                    ';

            $aksi = '
                        <div class="chat-search-list">
                        <ul>
                            <li title="Buat Registrasi">
                            <a href="' . base_url('RajalController/registLayan/' . $p->pasien_id) . '"> 
                                <i class="fa-regular fa-address-card"></i>
                            </a>
                            </li>
                            <li title="Riwayat Rekam Medis">
                            <a href="' . base_url('HistoryController/index/0/' . $p->pasien_id) . '">
                                <i class="fa-solid fa-notes-medical"></i>
                            </a>
                            </li>
                            <div class="dropdown dropdown-action">
                            <li>
                                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-bars "></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                                           
                                <a class="dropdown-item" href="' . base_url('Pasien-Detil/' . $p->pasien_id) . '">
                                    <i class="fa-solid fa-circle-info m-r-5"></i>Detail Pasien
                                </a>
                                
                                <a class="dropdown-item edit-pasien" href="' . base_url('PasienController/editPasien/' . $p->pasien_id) . '"><i class="fa-solid fa-user-pen m-r-5"></i> Edit Pasien</a>
                                <a class="dropdown-item nonaktif-pasien" href="javascript:;" data-id="' . $p->pasien_id . '"><i class="fa-solid fa-user-slash m-r-5"></i>Nonaktifkan</a>
                                </div>
                            </li>
                            </div>
                        </ul>
                        </div>';

            $data[] = [
                "", // checkbox nanti di client
                // $p->nama,
                $nama,
                date('d M Y', strtotime($p->tgl_lahir)),
                $p->no_identitas,
                $p->no_kartuprov ?: "-",
                $aksi
            ];
        }



        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }


    public function pasienProfile()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pasien/profilePasien');
        $this->load->view('templates/footer');
    }


    public function getDataPeserta()
    {
        $no_pencarian = $this->input->post('noBpjs');

        // Validasi input
        if (empty($no_pencarian)) {
            echo json_encode([
                'status' => false,
                'message' => 'Nomor BPJS / KTP tidak boleh kosong.'
            ]);
            return;
        }

        // URL API untuk NIK
        $urlNik = BPJS_BASE_URL_PCARE . '/peserta/nik/' . $no_pencarian;

        // Coba pencarian pertama dengan NIK
        $dataPesertaByNik = $this->bm->getApiBPJS($urlNik);

        if ($dataPesertaByNik['metadata']['responCode'] == '00') {
            // Jika ditemukan dengan NIK
            echo json_encode([
                'status' => true,
                'message' => 'Data ditemukan dengan NIK.',
                'data' => $dataPesertaByNik,
                'metadata' => $dataPesertaByNik['metadata'],
                'noKTP' => $no_pencarian
            ]);
            return;
        }


        // URL API untuk Nomor Kartu BPJS (NOKA)
        $urlNoka = BPJS_BASE_URL_PCARE . '/peserta/noka/' . $no_pencarian;

        // Jika tidak ditemukan dengan NIK, coba dengan NOKA
        $dataPesertaByNoka = $this->bm->getApiBPJS($urlNoka);

        if ($dataPesertaByNoka['metadata']['responCode'] == '00') {
            // Jika ditemukan dengan NOKA
            echo json_encode([
                'status' => true,
                'message' => 'Data ditemukan dengan Nomor Kartu BPJS.',
                'data' => $dataPesertaByNoka,
                'metadata' => $dataPesertaByNoka['metadata'],
                'noKartu' => $no_pencarian
            ]);
            return;
        }


        // Jika keduanya tidak ditemukan
        echo json_encode([
            'status' => false,
            'message' => 'Data tidak ditemukan dengan NIK maupun Nomor Kartu BPJS.'
        ]);
    }

    public function cekPasienTerdaftar()
    {
        $nik = $this->input->post('nik');
        $noBpjs = $this->input->post('noBpjs');
        // $nama = $this->input->post('nama');

        if ($nik === '-') {
            $nik = null;
        }

        if ($noBpjs === '-') {
            $noBpjs = null;
        }

        // return print_r($nik . "-" .  $noBpjs);

        // Validasi input: salah satu harus diisi
        if (empty($nik) && empty($noBpjs)) {
            echo json_encode([
                'terdaftar' => false,
                'message' => 'NIK atau Nomor BPJS harus diisi.'
            ]);
            return;
        }

        $pasien = null;

        if (!empty($nik)) {
            $pasien = $this->pm->cekPasienByNIK($nik);
        } elseif (!empty($noBpjs)) {
            $pasien = $this->pm->cekPasienByNoBPJS($noBpjs);
        }

        // return var_dump($pasien);
        // die;

        if ($pasien) {
            echo json_encode([
                'terdaftar' => true,
                'message' => 'Pasien sudah terdaftar.',
                'data' => $pasien // Mengembalikan data pasien jika diperlukan
            ]);
        } else {
            echo json_encode([
                'terdaftar' => false,
                'message' => 'Pasien tidak terdaftar.'
            ]);
        }
    }

    public function getCountryCodes()
    {
        $countries = [
            ['code' => '+62', 'name' => 'Indonesia'],
            ['code' => '+1', 'name' => 'USA'],
            ['code' => '+44', 'name' => 'UK'],
            ['code' => '+91', 'name' => 'India'],
            ['code' => '+81', 'name' => 'Japan'],
            ['code' => '+86', 'name' => 'China']
        ];

        echo json_encode($countries);
    }
}
