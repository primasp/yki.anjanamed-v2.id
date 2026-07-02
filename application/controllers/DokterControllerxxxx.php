<?php
defined('BASEPATH') or exit('No direct script access allowed');


class DokterController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0003"])) {
            redirect('AuthController');
        }
        $this->load->model('UserModel', 'um');
        $this->load->model('DokterModel', 'dm');
        // $this->load->model('BpjsApiModel', 'bm');
    }
    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('dokter/dokter_home');
        $this->load->view('templates/footer');

        // return var_dump($user_id);
        // die;
    }

    public function dashboard()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        $data['script'] = 'js/dokter.js';
        $data['page_css'] = 'css/dokter-dashboard-v2.css';


        $dokter_id = $data['user']['dokter_id'];
        $data['iddokter'] = $dokter_id;



        // Nama dokter
        $data['namadokter'] = $this->db->get_where('pc01_med_dokter_ms', [
            'dokter_id' => $dokter_id,
            'aktif' => '1'
        ])->row_array();

        // Ambil list poli dokter
        $data['listPoli'] = $this->dm->getPoliDokter($dokter_id);

        // Jika punya >1 poli, default ke poli pertama
        $default_poli = "";
        if (count($data['listPoli']) > 1) {
            $default_poli = $data['listPoli'][0]->poli_id;
        }

        // List pasien awal (default poli jika lebih dr 1)
        $data['listPasien'] = $this->dm->listpasien($dokter_id, $default_poli);
        $data['penunjang_items'] = $this->dm->getMasterPenunjangDokter();

        // Load views
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('dokter/dokter_dashboard', $data);
        $this->load->view('dokter/icd_modal');
        $this->load->view('dokter/tindakan_modal');
        $this->load->view('dokter/resep_modal');
        $this->load->view('dokter/rujuk_modal');
        $this->load->view('dokter/paliatif_modal');
        $this->load->view('templates/footer');
    }

    public function getPasienByPoli()
    {
        $dokter_id = $this->input->post('dokter_id');
        $poli_id   = $this->input->post('poli_id');
        $tanggal   = $this->input->post('tanggal');


        $listPasien = $this->dm->listpasien($dokter_id, $poli_id, $tanggal);

        echo json_encode($listPasien);
    }


    public function loadDataPasien()
    {
        $episodeid = $this->input->post('episodeid');
        $pasienid = $this->input->post('pasienid');

        $hasil          = $this->dm->loaddatapasien($episodeid, $pasienid);

        // return var_dump($hasil);
        // die;



        $episoap        = $hasil->episode_soap;
        $poliid         = $hasil->poli_id;

        if (empty($episoap)) {
            $soaplama   = $this->dm->loadsoaplama($pasienid, $poliid);
        } else {
            $soaplama   = '';
        }

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Data Pasien Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Data Pasien Ditemukan';
            $json['Responresult']   = $hasil;
            $json['Responsoaplama'] = $soaplama;
        }
        echo json_encode($json);
    }


    function insertDisplay()
    {
        $EID       = $this->input->post('idEpisode') ?? 'EPISODE_ID';
        $idPasien  = $this->input->post('idPasien') ?? '';
        $device_id = $this->input->post('idDevice') ?? 'unknown';

        // return var_dump($device_id);
        // die;

        $dataDisplay = $this->dm->getDataDisplay($EID, $idPasien);
        $no = isset($dataDisplay['urut']) ? $dataDisplay['urut'] : '';
        $idPoli = isset($dataDisplay['poli_id']) ? $dataDisplay['poli_id'] : '';
        $poli = isset($dataDisplay['nama_poli']) ? $dataDisplay['nama_poli'] : '';
        $ruang = isset($dataDisplay['ruang']) ? $dataDisplay['ruang'] : '';
        $pasien = isset($dataDisplay['nama_pasien']) ? $dataDisplay['nama_pasien'] : '';
        $idDokter = isset($dataDisplay['dokter_id']) ? $dataDisplay['dokter_id'] : '';
        // $idPasien = isset($_POST['idPasien']) ? $_POST['idPasien'] : '';
        $ip = $_SERVER['REMOTE_ADDR'];
        // $this->dm->insertDisplay($EID, $no, $idPoli, $poli, $ruang, $pasien, $idDokter, $idPasien, $ip);
        $this->dm->insertDisplay($EID, $no, $idPoli, $poli, $ruang, $pasien, $idDokter, $idPasien, $device_id);
        echo "berhasil";

        // return var_dump($query);
        // die;
    }


    public function getAlergiApi($jenis)
    {
        if ($jenis == "01") {
            $jenis = "ALGMKN";
        } else if ($jenis == "02") {
            $jenis = "ALGUDR";
        } else {
            $jenis = "ALGOBT";
        }

        $data = $this->dm->getAlergiFromDB($jenis);

        // Set header untuk memastikan respons dalam format JSON
        header('Content-Type: application/json');

        if (!empty($data)) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data alergi tidak ditemukan di database"
            ]);
        }
    }

    public function getStatPlgApi()  //Dari DB
    {

        $data = $this->dm->getStatPlgFromDB();

        // Set header untuk memastikan respons dalam format JSON
        header('Content-Type: application/json');

        if (!empty($data)) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data Status Pulang tidak ditemukan di database"
            ]);
        }
    }

    public function getSadarApi()
    {
        $data = $this->dm->getSadarFromDB();

        header('Content-Type: application/json');

        if (!empty($data)) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data Kesadaran tidak ditemukan di database"
            ]);
        }
    }

    public function getPrognosaApi()
    {
        // Load model jika belum diload
        $data = $this->dm->getPrognosaFromDB();

        // Set header untuk memastikan respons dalam format JSON
        header('Content-Type: application/json');

        if (!empty($data)) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data Prognosa tidak ditemukan di database"
            ]);
        }
    }

    public function ceksudahmulai()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil          = $this->dm->ceksudahmulai($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Belum Mulai';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Sudah Mulai';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function cekstatusresep()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->cekstatusresep($data);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Resep Tidak Ada / Sudah Terkirim';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Resep Belum Terkirim';
            $json['Responresult'] = $hasil;
        }
        echo json_encode($json);
    }

    public function loadicd10utama()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loadicd10utama($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'ICD 10 Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'ICD 10 Ditemukan';
            $json['Responresult']   = $hasil;
        }
        echo json_encode($json);
    }

    public function loadicd10sek()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loadicd10sek($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'ICD 10 Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'ICD 10 Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }


    public function loadicd10()
    {
        $filtercari   = $this->input->post('pencarian');

        $hasil = $this->dm->loadicd10($filtercari);

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'ICD 10 Tidak Ditemukan';
        } else {
            $json['Responcode'] = '00';
            $json['Respondesc'] = 'ICD 10 Ditemukan dari Database Lokal';
            $json['Responresult'] = $hasil;
        }
        // }
        echo json_encode($json);
    }


    public function simpanicd10sek()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_CO']       = $this->input->post("transco");
        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['KODE']           = $this->input->post("kode");
        $data['KODE_ICD']       = $this->input->post("kodeicd");
        $data['DIAGNOSA']       = $this->input->post("nmdiag");
        $data['CREATED_BY']     = $this->input->post("createdby");

        $gettransdiag           = $this->dm->gettransinput();
        $transdiag              = $gettransdiag->trans_input;
        $data['TRANS_DIAG']     = $transdiag;

        $cek_icd10 = $this->dm->cekICD10($data['KODE_ICD']);

        if (empty($cek_icd10)) {
            // Jika kode ICD 10 belum ada, lakukan insert
            $insert_icd10 = [
                'lokasi_id'   => $data['LOKASI_ID'],
                'kode_icd'    => $data['KODE_ICD'],
                'nm_diag1'     => $data['DIAGNOSA'],
                'nm_diag2'     => $data['DIAGNOSA'],
                'nonspesialis' => false, // Atur sesuai kebutuhan, misalnya default false
                'show_item'   => '1', // Default
                'created_date' => date('Y-m-d H:i:s'),
            ];
            $this->dm->insertICD10($insert_icd10);
        }

        $hasil = $this->dm->simpanicd10sek($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Gagal Simpan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Tersimpan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function hapusicd10()
    {
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_DIAG']     = $this->input->post("transdiag");

        $hasil = $this->dm->hapusicd10sek($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Gagal Hapus';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Berhasil Hapus';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }


    public function loadicd9()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loadicd9($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'ICD 9 Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'ICD 9 Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function loadmastericd9()
    {
        $filtercari   = $this->input->post('pencarian');

        $hasil = $this->dm->loadmastericd9($filtercari);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'ICD 9 Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'ICD 9 Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }


    public function simpanicd9()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_CO']       = $this->input->post("transco");
        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['KODE']           = $this->input->post("kode");
        $data['KODE_ICD']       = $this->input->post("kodeicd");
        $data['TINDAKAN']       = $this->input->post("longdescription");
        $data['CREATED_BY']     = $this->input->post("createdby");

        $gettransdiag           = $this->dm->gettransinput();
        $transtin               = $gettransdiag->trans_input;
        $data['TRANS_TIN']      = $transtin;

        $hasil = $this->dm->simpanicd9($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Gagal Simpan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Tersimpan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function hapusicd9()
    {
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_TIN']      = $this->input->post("transtin");

        $hasil = $this->dm->hapusicd9($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Gagal Hapus';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Berhasil Hapus';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function loaditemharga()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loaditemharga($data);

        // return var_dump($hasil);
        // die;

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Tindakan/Obat Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Tindakan/Obat Obat Ditemukan';
            $json['Responresult'] = $hasil;
        }

        echo json_encode($json);
    }

    public function mulaiperiksa()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['POLI_ID']        = $this->input->post("poliid");
        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['CREATED_BY']     = $this->input->post("createdby");

        // return var_dump($data);
        // die;


        $hasil          = $this->dm->mulaiperiksa($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Gagal Periksa';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Mulai Periksa';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function simpansoap()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_CO']       = $this->input->post("transco");
        $data['POLI_ID']        = $this->input->post("poliid");
        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['REKANAN_ID']     = $this->input->post("rekananid");
        $data['TANGGAL']        = date('Y-m-d', strtotime($this->input->post("tanggal")));

        $data['S']              = $this->input->post("soap_s");
        $data['O']              = $this->input->post("soap_o");
        $data['A']              = $this->input->post("soap_a");
        $data['P']              = $this->input->post("soap_p");
        $data['CREATED_BY']     = $this->input->post("createdby");

        if ($data['TRANS_CO'] === '') {
            $gettransco         = $this->dm->gettransco();
            $transco            = $gettransco->trans_co;
            $data['TRANS_CO']   = $transco;
        }

        $gettransinput          = $this->dm->gettransinput();
        $transinput             = $gettransinput->trans_input;
        $data['TRANS_INPUT']    = $transinput;

        $hasil = $this->dm->simpansoap($data);

        /*
         * ALUR BARU YKI TAHAP 2
         * Order penunjang dokter dikirim bersama SOAP.
         * - Lab/Rad dipisahkan dari menu Tindakan.
         * - Menu Tindakan tetap khusus JKL-UMU.
         * - Jika tidak ada checklist penunjang, order penunjang lama pada episode ini dinonaktifkan.
         */
        $penunjangResult = [
            'success' => true,
            'message' => 'Tidak ada order penunjang yang dipilih.',
            'items'   => []
        ];

        if (!empty($hasil)) {
            $penunjangInput = $this->input->post('penunjang_order');
            $penunjangItems = [];

            if (is_array($penunjangInput)) {
                $penunjangItems = $penunjangInput;
            } elseif (!empty($penunjangInput)) {
                $decoded = json_decode($penunjangInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $penunjangItems = $decoded;
                }
            }

            if (!empty($penunjangItems)) {
                $penunjangResult = $this->dm->simpanOrderPenunjangDokter($data, $penunjangItems);
            } else {
                $this->dm->hapusOrderPenunjangDokter($data);
            }
        }

        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Soap Gagal Tersimpan';
        } else {
            $json['Responcode']       = '00';
            $json['Respondesc']       = 'Soap Tersimpan';
            $json['Responresult']     = $hasil;
            $json['PenunjangResult']  = $penunjangResult;
        }

        echo json_encode($json);
    }



    public function loadtindakanmodal()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loadtindakanmodal($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Tindakan Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Tindakan Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function loadmastertindakan()
    {
        $filtercari   = $this->input->post('pencarian');

        $hasil = $this->dm->loadmastertindakan($filtercari);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Tindakan Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Tindakan Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function simpantindakan()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        $data['TRANS_CO']       = $this->input->post("transco");

        $tglpoli = $this->input->post("tglpoli");
        $data['TANGGAL'] = !empty($tglpoli) ? date('Y-m-d', strtotime($tglpoli)) : date('Y-m-d');

        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['POLI_ID']        = $this->input->post("poliid");
        $data['REKANAN_ID']     = $this->input->post("rekananid");
        $data['KELAS_ID']       = $this->input->post("kelasid") ?: '6';
        $data['CREATED_BY']     = $this->input->post("createdby");

        $tindakanArray = $this->input->post("datatindakan");
        if (!is_array($tindakanArray)) {
            $tindakanArray = [];
        }

        /*
         * Alur lama tetap dipertahankan:
         * simpan pilihan tindakan dokter ke pc01_co_alkes_dt agar tampilan modal/history dokter tetap berjalan.
         */
        if (!empty($tindakanArray)) {
            $transAlkesArray = [];

            foreach ($tindakanArray as $tindakan) {
                if (!empty($tindakan['transalkes']) && $tindakan['transalkes'] !== '0') {
                    $transAlkesArray[] = $this->db->escape($tindakan['transalkes']);
                }
            }

            if (!empty($transAlkesArray)) {
                $vartransalkes = implode(', ', $transAlkesArray);
                $this->dm->hapustindakan($data, $vartransalkes);
            } else {
                $this->dm->hapustindakanall($data);
            }
        } else {
            $this->dm->hapustindakanall($data);
        }

        if (!empty($tindakanArray)) {
            foreach ($tindakanArray as $tindakan) {
                $tindakanData = [
                    'LAYAN_ID'      => $tindakan['layanid'] ?? '',
                    'NAMA_LAYAN'    => $tindakan['namalayan'] ?? '',
                    'QTY'           => $tindakan['qty'] ?? 1,
                    'TRANS_ALKES'   => $tindakan['transalkes'] ?? '0'
                ];

                if ($tindakanData['TRANS_ALKES'] == '0') {
                    $gettransalkes          = $this->dm->gettransinput();
                    $transalkes             = $gettransalkes->trans_input;
                    $data['TRANS_ALKES']    = $transalkes;

                    $this->dm->simpantindakan($data, $tindakanData);
                }
            }
        }

        /*
         * ALUR BARU KASIR:
         * Tindakan dokter wajib disinkronkan ke:
         * - pc01_keu_transctr_it
         * - pc01_keu_transaksi_hd dengan jenis_tr = 003
         *
         * Jika provider UMUM dan bayar_id masih NULL, maka muncul di Kasir Umum.
         */
        $billingResult = $this->dm->sinkronTindakanDokterKeBilling($data, $tindakanArray);

        if (empty($billingResult['success'])) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = $billingResult['message'] ?? 'Tindakan dokter gagal disinkronkan ke billing.';
            $json['BillingResult'] = $billingResult;
        } else {
            $json['Responcode'] = '00';
            $json['Respondesc'] = 'Berhasil simpan tindakan';
            $json['BillingResult'] = $billingResult;
        }

        echo json_encode($json);
    }

    public function loadinputobat()
    {
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");

        $hasil = $this->dm->loadinputobat($data);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Resep Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Resep Ditemukan';
            $json['Responresult']   = $hasil;
        }

        echo json_encode($json);
    }

    public function loadmasterobat()
    {
        $filtercari   = $this->input->post('pencarian');

        $hasil = $this->dm->loadmasterobat($filtercari);

        if (empty($hasil)) {
            $json['Responcode']     = '01';
            $json['Respondesc']     = 'Obat Tidak Ditemukan';
        } else {
            $json['Responcode']     = '00';
            $json['Respondesc']     = 'Obat Ditemukan';
            $json['Responresult']   = $hasil;
        }

        // echo json_encode($json);
        echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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

    public function loadpembungkus()
    {
        $filtercari   = $this->input->post('pencarianobat');

        $hasil = $this->dm->caribungkus($filtercari);


        if (empty($hasil)) {
            $json['Responcode'] = '01';
            $json['Respondesc'] = 'Pembungkus Tidak Ditemukan';
        } else {
            $json['Responcode']    = '00';
            $json['Respondesc']    = 'Pembungkus Obat Ditemukan';
            $json['Responresult'] = $hasil;
        }

        echo json_encode($json);
    }


    public function simpanresep()
    {
        // Ambil data dasar
        $data['LOKASI_ID']      = $this->input->post("lokasiid");
        $data['EPISODE_ID']     = $this->input->post("episodeid");
        $data['TRANS_ID']       = $this->input->post("transid");
        $data['PASIEN_ID']      = $this->input->post("pasienid");
        // $data['TANGGAL']        = $this->input->post("tanggal");
        $data['TANGGAL'] = date('Y-m-d', strtotime($this->input->post("tanggal")));
        $data['REKANAN_ID']     = $this->input->post("rekananid");
        $data['POLI_ID']        = $this->input->post("poliid");
        $data['DOKTER_ID']      = $this->input->post("dokterid");
        $data['CREATED_BY']     = $this->input->post("createdby");
        $data['JENIS_SIMPAN']   = $this->input->post("jenissimpan");
        $data['TRANS_CO']       = $this->input->post("transcoresep");

        $validinput = false;
        if (!empty($data['EPISODE_ID']) && !empty($data['PASIEN_ID'])) {
            $validinput = true;
        }

        if ($validinput) {
            $this->dm->hapusreseplama($data);

            $gettransco         = $this->dm->gettransco();
            $transco            = $gettransco->trans_co;

            // Ambil data obat dari input
            $obatArray = $this->input->post("dataObat");

            if (!empty($obatArray)) {
                foreach ($obatArray as $obat) {
                    // Proses setiap obat
                    $obatData = [
                        'KETTIPE'      => $obat['kettipe'],
                        'OBAT_ID'      => $obat['obatid'],
                        'NAMA_OBAT'    => $obat['namaobat'],
                        'QTY'          => $obat['qty'],
                        'STOK'         => $obat['stok'],
                        'SATUAN'       => $obat['satuan'],
                        'FREEDOSIS'    => $obat['freedosis'],
                        'SIGNA_NAMA'   => $obat['signanama'],
                        'SIGNA_DOKTER' => $obat['signadokter'],
                        'CATATAN'      => $obat['catatan'],
                        'URUT'         => $obat['urut'],
                        'TIPE_OBAT'    => $obat['tipeobat'],
                        'HEADER'       => $obat['header'],
                        'SATUAN_ID'    => $obat['satuanid'],
                        'SIGNA_ID'     => $obat['signaid'],
                        'NAMA_RACIKAN' => $obat['namaracikan']
                    ];

                    $this->dm->simpanresepit($data, $obatData, $transco);
                }
            }

            // Simpan header resep
            if ($this->dm->simpanresephd($data, $transco)) {
                $json["Responcode"] = "00";
                $json["Responhead"] = "success";
                $json["Respondesc"] = "Berhasil Menyimpan Resep";
            } else {
                $json["Responcode"] = "01";
                $json["Responhead"] = "error";
                $json["Respondesc"] = "Gagal Menyimpan Resep";
            }
        } else {
            $json["Responcode"] = "01";
            $json["Responhead"] = "error";
            $json["Respondesc"] = "Input tidak valid";
        }

        echo json_encode($json);
    }



    public function prosesselesai()
    {
        $data = [
            'LOKASI_ID' => $this->input->post('lokasiid'),
            'EPISODE_ID' => $this->input->post('episodeid'),
            'PASIEN_ID' => $this->input->post('pasienid'),
            'TRANS_CO' => $this->input->post('transco'),
            'TRANS_ID' => $this->input->post('transid'),
            'POLI_ID' => $this->input->post('poliid'),
            'TANGGAL' => date('Y-m-d', strtotime($this->input->post("tanggal"))),
            'DOKTER_ID' => $this->input->post('dokterid'),
            'REKANAN_ID' => $this->input->post('rekananid'),
            'CREATED_BY' => $this->input->post('createdby'),
            'ICD10' => $this->input->post('icd10'),
            'DIAGNOSA' => $this->input->post('diagnosa'),
            // 'DIAG_NON_SPS' => $this->input->post('diagnonspesialis'),
            'STATPLG' => $this->input->post('statPlg'),
            'KDSADAR' => $this->input->post('kdsadar'),
            'KDPROGNOSA' => $this->input->post('kdprognosa'),
            // 'NOKARTU' => $this->input->post('noKartu'),

        ];



        // Tambahan: Data TACC jika tersedia
        // $data['TACC_ALASAN'] = $this->input->post('alasanTacc') ?: null;
        // $data['KODE_TACC'] = $this->input->post('kdTacc') ?: null;
        // $data['CONF_TACC'] = $this->input->post('konfirmasiRujukan') ?: null;

        // Proses alergi makanan
        $alergiMakanan = $this->input->post("alergiMakanan") ?: "00|Tidak Ada";
        list($data['ALERGI_MKNN_KD'], $data['ALERGI_MKNN_NM']) = array_pad(explode('|', $alergiMakanan), 2, null);
        // Proses alergi udara
        $alergiUdara = $this->input->post("alergiUdara") ?: "00|Tidak Ada";
        list($data['ALERGI_UDARA_KD'], $data['ALERGI_UDARA_NM']) = array_pad(explode('|', $alergiUdara), 2, null);

        // Proses alergi obat
        $alergiObat = $this->input->post("alergiObat") ?: "00|Tidak Ada";
        list($data['ALERGI_OBAT_KD'], $data['ALERGI_OBAT_NM']) = array_pad(explode('|', $alergiObat), 2, null);






        $resepData = $this->dm->getResepDr($data['EPISODE_ID']);




        $tindakanData = $this->dm->gettindakan('001', $data['EPISODE_ID'], $data['PASIEN_ID']);


        // if ($tindakanData) {
        //     $terapiNonObatText = array_map(function ($tindakan, $index) {
        //         return ($index + 1) . ". " . $tindakan['nama_layan'] . " ( qty: " . $tindakan['qty'] . ")";
        //     }, $tindakanData, array_keys($tindakanData));

        //     $data['TERAPI_NON_OBAT'] = implode("\n", $terapiNonObatText);
        // } else {
        //     $data['TERAPI_NON_OBAT'] = "Tidak ada data Terapi Non Obat.";
        // }

        // if ($resepData) {
        //     // Format data terapi obat
        //     $terapiObatText = array_map(function ($resep, $index) {
        //         return ($index + 1) . ". " . $resep['nama_obat'] . " (dosis: " . $resep['free_dosis'] . ", qty: " . $resep['qty'] . ")";
        //     }, $resepData, array_keys($resepData));

        //     $data['TERAPI_OBAT'] = implode("\n", $terapiObatText);
        // } else {
        //     $data['TERAPI_OBAT'] = "Tidak ada data resep.";
        // }

        // Cek Rekanan
        // if ($data['REKANAN_ID'] === 'BPJS') {
        //     $cekNoKunjungan = $this->dm->cekNoKunjungan($data['EPISODE_ID']);
        //     $poliid = $data['POLI_ID'];

        //     // === AMBIL 2 ICD-10 SEKUNDER ===
        //     $sek = $this->dm->getIcd10Sekunder($data['EPISODE_ID']);
        //     $data['kdDiag2'] = isset($sek[0]['kode_icd']) ? trim($sek[0]['kode_icd']) : null;
        //     $data['kdDiag3'] = isset($sek[1]['kode_icd']) ? trim($sek[1]['kode_icd']) : null;

        //     // return var_dump($data['kdDiag3']);
        //     // die;

        //     $getDataPoli = $this->db->query("SELECT * FROM pc01_med_poli_ms WHERE poli_id = '$poliid' AND aktif = '1'")->row();
        //     $poliIdBpjs = $getDataPoli->poli_bpjsid;

        //     $no_kunjungan = isset($cekNoKunjungan['nomor_kunjungan']) ? $cekNoKunjungan['nomor_kunjungan'] : null;

        //     if ($data['STATPLG'] == '4') {

        //         if ($data['DIAG_NON_SPS'] == 'Ya' && $data['CONF_TACC'] === NULL) {
        //             ob_clean();

        //             echo json_encode([
        //                 'Responcode' => '99',
        //                 'Respondesc' => 'Diagnosa Non Spesialistik namun pasien dirujuk. Konfirmasi dibutuhkan.',
        //             ]);
        //             exit;
        //         }

        //         $rujukanData = $this->dm->getDtlKrmRujukan($data['EPISODE_ID']);

        //         if ($rujukanData) {
        //             $postData = $this->prepareBPJSRujukanData($data, $rujukanData, $no_kunjungan);
        //         } else {
        //             ob_clean();
        //             echo json_encode([
        //                 'Responcode' => '404',
        //                 'Respondesc' => 'Data rujukan tidak ditemukan.',
        //             ]);
        //             exit;
        //         }
        //     } else {
        //         $cekKunjungan = $this->dm->cekKunjungan($data['EPISODE_ID']);

        //         if ($cekKunjungan) {
        //             $this->dm->updateKunjungan($data['EPISODE_ID'], [
        //                 'nokartu' => $data['NOKARTU'],
        //                 'last_updated_by' => $data['CREATED_BY'],
        //                 'last_updated_date' => date('Y-m-d H:i:s'),
        //                 'rjkn_khusus' => null,
        //                 'rjkn_poli_khusus' => null,
        //                 'rjkn_spes_khusus' => null,
        //                 'rjkn_subspes_khusus' => null,
        //                 'rjkn_subspes_khusus' => null,
        //                 'rjkn_faskes_khusus' => null,
        //                 'rjkn_cttn' => null,
        //                 'rjkn_spes' => null,
        //                 'rjkn_subspes' => null,
        //                 'rjkn_faskes' => null,
        //                 'rjkn_sarana' => null,
        //                 'kd_stat_plg' => $data['STATPLG'],

        //             ]);
        //         } else {

        //             $this->dm->insertRujukan([
        //                 'lokasi_id' => '001',
        //                 'nokartu' => $data['NOKARTU'],
        //                 'episode_id' => $data['EPISODE_ID'],
        //                 'created_by' => $data['CREATED_BY'],
        //                 'created_date' => date('Y-m-d H:i:s'),
        //                 'aktif' => '1',
        //             ]);
        //         }

        //         $kunjunganData = $this->dm->getDtlKrmKunjungan($data['EPISODE_ID']);


        //         if ($kunjunganData) {
        //             $postData = $this->prepareBPJSKunjunganData($data, $kunjunganData, $no_kunjungan);
        //         } else {
        //             ob_clean();
        //             echo json_encode([
        //                 'Responcode' => '404',
        //                 'Respondesc' => 'Data kunjungan tidak ditemukan.',
        //             ]);
        //             exit;
        //         }
        //     }

        //     if (!empty($no_kunjungan)) {
        //         $url = BPJS_BASE_URL_PCARE . "/kunjungan/v1";

        //         $response = $this->bm->putToBpjsKunjungan($url, json_encode($postData), "PUT");
        //     } else {
        //         $url = BPJS_BASE_URL_PCARE . "/kunjungan/v1";
        //         $response = $this->bm->postToBpjsKunjungan($url, json_encode($postData), "POST");
        //     }


        //     if ($response && isset($response['metaData']['code'])) {
        //         $code = $response['metaData']['code'];
        //         $message = $response['metaData']['message'];
        //         $field_0 = $response['response'][0]['field'] ?? null; // Pastikan respons terdefinisi
        //         $message_0 = $response['response'][0]['message'] ?? null;

        //         if ($code === 201) {

        //             $noKunjungan = null;

        //             if (isset($message_0) && $field_0 === 'noKunjungan') {
        //                 $noKunjungan = $message_0 ?? null;
        //                 $this->dm->updateNoKunjungan($data['EPISODE_ID'], $noKunjungan, $data['STATPLG'], $data['KDSADAR'], $data['KDPROGNOSA'], $data['KODE_TACC'], $data['TACC_ALASAN'],  $data['ALERGI_MKNN_KD'], $data['ALERGI_UDARA_KD'], $data['ALERGI_OBAT_KD']);
        //             }

        //             $bpjsResult = [
        //                 'status' => 'success',
        //                 'code' =>  $code,
        //                 'message' => $noKunjungan ?? 'No message returned',
        //             ];
        //         } else if ($code === 200) {


        //             $bpjsResult = [
        //                 'status' => 'success',
        //                 'code' =>  $code,
        //                 'message' => $message . " Update Success",
        //             ];
        //         } else {
        //             $bpjsResult = [
        //                 'status' => 'failed',
        //                 'code' =>  $code,
        //                 'message' => $message,
        //             ];
        //         }
        //     } else {
        //         $bpjsResult = [
        //             'status' => 'failed',
        //             'code' => 500,
        //             'message' => 'Gagal mendapatkan respons dari API BPJS.',
        //         ];
        //     }
        // }

        $cekResumeRj = $this->dm->cekResume($data['EPISODE_ID']);



        if ($cekResumeRj) {
            $this->dm->updateResume($data['EPISODE_ID'], $data['STATPLG'], $data['KDSADAR'], $data['KDPROGNOSA']);
        } else {
            $this->dm->insertResume([
                'lokasi_id' => '001',
                'trans_id' => $data['TRANS_ID'],
                'episode_id' => $data['EPISODE_ID'],
                'poli_id' => $data['POLI_ID'],
                'tgl_kunjungan' => $data['TANGGAL'],
                'dokter_id' => $data['DOKTER_ID'],
                'kondisi_pulang' => $data['STATPLG'],
                'tingkat_sadar' => $data['KDSADAR'],
                'prognosis' => $data['KDPROGNOSA'],
                'created_by' => $data['CREATED_BY'],
                'created_date' => date('Y-m-d H:i:s'),
                'aktif' => '1',
            ]);
        }



        // $this->dm->updateSpBpjsPcare($data['EPISODE_ID'], $data['DIAG_NON_SPS']);



        $kirimalergi = $this->dm->kirimalergi($data);


        $hasil = $this->dm->selesaiperiksa($data);

        $kirimresep = $this->dm->kirimresep($data);

        ob_clean();

        // if (isset($data['REKANAN_ID']) && $data['REKANAN_ID'] === 'BPJS') {
        //     echo json_encode([
        //         // 'Responcode' => empty($hasil) ? '01' : '00',
        //         'Responcode' => (!empty($hasil) && $bpjsResult['status'] === 'success') ? '01' : '00',
        //         'Respondesc' => !empty($hasil) ? 'Selesai Periksa' : 'Gagal',
        //         'Responresult' => $hasil ?? null,
        //         'BPJSResult' => $bpjsResult ?? null, // Jika BPJS diproses, sertakan hasilnya
        //     ]);
        // } else {
        echo json_encode([
            'Responcode' => !empty($hasil) ? '01' : '00',
            'Respondesc' => !empty($hasil) ? 'Selesai Periksa' : 'Gagal',
            'Responresult' => $hasil ?? null,
            'BPJSResult' => $bpjsResult ?? null, // Jika BPJS diproses, sertakan hasilnya
        ]);
        // }
        exit;
    }



    private function getPatientIdFromSatusehat($nik, $token)
    {
        $nik = preg_replace('/\D+/', '', (string)$nik); // bersihkan non-digit
        if (empty($nik)) return null;

        // Search Patient by NIK identifier
        $identifier_system = 'http://sys-ids.kemkes.go.id/nik';
        $search_url = SATUSEHAT_BASE_URL . '/Patient?identifier=' .
            rawurlencode($identifier_system . '|' . $nik);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $search_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Accept: application/json"
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            // optional: log $err
            return null;
        }

        if ($http >= 400 || !$response) {
            // optional: log $response
            return null;
        }

        $json = json_decode($response, true);
        if (!$json || ($json['resourceType'] ?? '') !== 'Bundle') {
            return null;
        }

        $total = $json['total'] ?? 0;
        if ($total < 1 || empty($json['entry'])) {
            return null;
        }

        // Ambil Patient pertama yang valid
        foreach ($json['entry'] as $entry) {
            $res = $entry['resource'] ?? [];
            if (($res['resourceType'] ?? '') === 'Patient' && !empty($res['id'])) {
                return $res['id'];
            }
        }

        return null;
    }


    public function loadMasterPenunjang()
    {
        $this->output->set_content_type('application/json');

        $data = $this->dm->getMasterPenunjangDokter();

        if (empty($data)) {
            echo json_encode([
                'Responcode' => '01',
                'Respondesc' => 'Master penunjang dokter tidak ditemukan.',
                'Responresult' => []
            ]);
            return;
        }

        echo json_encode([
            'Responcode' => '00',
            'Respondesc' => 'Master penunjang dokter ditemukan.',
            'Responresult' => $data
        ]);
    }

    public function loadPenunjangOrder()
    {
        $this->output->set_content_type('application/json');

        $data['LOKASI_ID']  = $this->input->post("lokasiid");
        $data['EPISODE_ID'] = $this->input->post("episodeid");
        $data['PASIEN_ID']  = $this->input->post("pasienid");

        if (empty($data['EPISODE_ID']) || empty($data['PASIEN_ID'])) {
            echo json_encode([
                'Responcode' => '01',
                'Respondesc' => 'Episode / pasien belum dipilih.',
                'Responresult' => []
            ]);
            return;
        }

        $hasil = $this->dm->getOrderPenunjangDokter($data);

        echo json_encode([
            'Responcode' => empty($hasil) ? '01' : '00',
            'Respondesc' => empty($hasil) ? 'Order penunjang belum ada.' : 'Order penunjang ditemukan.',
            'Responresult' => $hasil
        ]);
    }

    public function simpanPenunjangDokter()
    {
        $this->output->set_content_type('application/json');

        $data['LOKASI_ID']   = $this->input->post("lokasiid");
        $data['EPISODE_ID']  = $this->input->post("episodeid");
        $data['TRANS_ID']    = $this->input->post("transid");
        $data['PASIEN_ID']   = $this->input->post("pasienid");
        $data['TRANS_CO']    = $this->input->post("transco");
        $data['POLI_ID']     = $this->input->post("poliid");
        $data['DOKTER_ID']   = $this->input->post("dokterid");
        $data['REKANAN_ID']  = $this->input->post("rekananid");
        $data['TANGGAL']     = date('Y-m-d', strtotime($this->input->post("tanggal")));
        $data['CREATED_BY']  = $this->input->post("createdby");

        if (empty($data['EPISODE_ID']) || empty($data['PASIEN_ID']) || empty($data['TRANS_ID'])) {
            echo json_encode([
                'Responcode' => '01',
                'Respondesc' => 'Data pasien / transaksi belum lengkap. Pilih pasien dan simpan SOAP terlebih dahulu.',
            ]);
            return;
        }

        if ($data['TRANS_CO'] === '') {
            $gettransco       = $this->dm->gettransco();
            $data['TRANS_CO'] = $gettransco->trans_co;
        }

        $penunjangInput = $this->input->post('penunjang_order');
        $penunjangItems = [];

        if (is_array($penunjangInput)) {
            $penunjangItems = $penunjangInput;
        } elseif (!empty($penunjangInput)) {
            $decoded = json_decode($penunjangInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $penunjangItems = $decoded;
            }
        }

        if (empty($penunjangItems)) {
            $this->dm->hapusOrderPenunjangDokter($data);
            echo json_encode([
                'Responcode' => '00',
                'Respondesc' => 'Order penunjang dikosongkan.',
                'Responresult' => []
            ]);
            return;
        }

        $hasil = $this->dm->simpanOrderPenunjangDokter($data, $penunjangItems);

        echo json_encode([
            'Responcode' => !empty($hasil['success']) ? '00' : '01',
            'Respondesc' => $hasil['message'] ?? 'Proses order penunjang selesai.',
            'Responresult' => $hasil
        ]);
    }
}
