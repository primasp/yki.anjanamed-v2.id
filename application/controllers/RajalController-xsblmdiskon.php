<?php
defined('BASEPATH') or exit('No direct script access allowed');


class RajalController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('RajalModel', 'rm');
    }

    public function registLayan($pasien_id = null)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data['user'] = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/regist-poli.js';

        if ($pasien_id) {
            $data['pasien'] = $this->rm->get_pasien_v2($pasien_id, 'id');
            // Ambil data pertama dari array untuk menghindari error
            $data['pasien'] = !empty($data['pasien']) ? $data['pasien'][0] : null;
        } else {
            $data['pasien'] = null;
        }

        $data['lab_items'] = $this->rm->get_lab_items();
        $data['rad_items'] = $this->rm->get_radiologi_items();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('Rajal/registPoli_v', $data);
        $this->load->view('templates/footer');
    }



    public function cariPasien()
    {
        $term = $this->input->post('term');
        $type = $this->input->post('type');

        if (empty($term)) {
            echo json_encode([]); // Jika tidak ada input, kembalikan array kosong
            return;
        }

        $result = $this->rm->get_pasien($term, $type);

        // return var_dump($result);
        // die;
        if (!empty($result)) {
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
    }

    public function check_poli()
    {
        $pembiayaan = $this->input->post('pembiayaan');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan');

        $data = $this->rm->getPoliByPembiayaan($pembiayaan);
        echo json_encode($data);
    }

    public function hari_indo($day)
    {
        // Mapping nama hari dari Inggris ke Indonesia
        $days = [
            "Sunday" => "Minggu",
            "Monday" => "Senin",
            "Tuesday" => "Selasa",
            "Wednesday" => "Rabu",
            "Thursday" => "Kamis",
            "Friday" => "Jumat",
            "Saturday" => "Sabtu"
        ];

        // Kembalikan hasil terjemahan, atau nama asli jika tidak ditemukan
        return $days[$day] ?? $day;
    }

    public function md_hari($keterangan)
    {
        $query = "select * from pc01_gen_global_ms where jenis_id = 'JDOWEEK' and keterangan = '$keterangan';";

        return $this->db->query($query)->row();
    }


    public function check_dokter()
    {

        $tgl_berobat = $this->input->post('tgl_berobat');
        $poli_id = $this->input->post('poli_id');
        $hari = $this->hari_indo($this->input->post('hari'));
        $hari_id = $this->md_hari(strtoupper($hari))->global_id;


        $data = $this->rm->getDokterbyTgl($poli_id, $hari_id);

        if (empty($tgl_berobat) || empty($poli_id)) {
            echo json_encode([]);
            return;
        }
        echo json_encode($data);
    }

    public function check_jam_slot()
    {
        $tgl_berobat = $this->input->post('tgl_berobat');
        $poli_id = $this->input->post('poli_id');
        $dokter_id = $this->input->post('dokter_id');
        $hari = $this->hari_indo($this->input->post('day'));
        $hari_id = $this->md_hari(strtoupper($hari))->global_id;

        if (empty($tgl_berobat) || empty($poli_id) || empty($dokter_id) || empty($hari_id)) {
            echo json_encode(['error' => 'Data tidak lengkap.']);
            return;
        }

        $jam_slots = $this->rm->get_jam_slot($tgl_berobat, $poli_id, $dokter_id, $hari_id);

        // Jika tidak ada slot yang tersedia
        if (empty($jam_slots)) {
            echo json_encode([]);
            return;
        }

        // Kembalikan data slot dalam format JSON
        echo json_encode($jam_slots);
    }

    public function proses_regis_layan()
    {
        $this->output->set_content_type('application/json');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan'); // 1 = Poliklinik, 2 = Penunjang
        $pasien_id = $this->input->post('pasien_id');
        $pembiayaan = $this->input->post('pembiayaan');
        $user_id = $this->session->userdata('user_id_pc');

        if (empty($pasien_id)) {
            echo json_encode(['success' => false, 'message' => 'Pasien tidak ditemukan.']);
            return;
        }

        // Ambil nominal pembayaran dari frontend
        $bayar_nanti = $this->input->post('bayar_nanti') ?? 0;
        $metode_bayar   = $this->input->post('metode_bayar') ?? 'TUNDA';
        $uang_bayar     = (float) ($this->input->post('uang_bayar') ?? 0);
        $total_bayar    = (float) ($this->input->post('total_bayar') ?? 0);
        $total_kembali  = (float) ($this->input->post('total_kembali') ?? 0);

        $transaksi_id = $this->rm->buat_transaksi_id()->buat_id_transaksi_id;

        if ($jenis_kunjungan == '2') {


            $idAps = $this->rm->generateIdAps();

            $episode_id = $idAps['episode_id'];
            $trans_id   = $idAps['trans_id'];
            $trans_co   = $idAps['trans_co'];
            $shift_id   = $idAps['shift_id'];

            // ⚙️ Logika baru: jika pembiayaan selain UMUM → bayar_id NULL
            if ($pembiayaan == 'UMUM' && $bayar_nanti != 1) {
                $bayar_id = $idAps['bayar_id'];
            } else {
                $bayar_id = null;
            }

            $lokasi_id = '001';
            $kelas_id  = '6';
            $poli_id   = 'APS';
            $dokter_id = 'APS';
            $rekanan_id = $pembiayaan;
            $total_harga = 0;
            $status_tr = '0';

            $this->db->trans_begin();

            try {
                // === 1. Episode & Medis ===
                $this->rm->insertEpisodeAps($lokasi_id, $episode_id, $pasien_id, $kelas_id, $poli_id, $dokter_id, $rekanan_id, $status_tr, $user_id);
                $this->rm->insertMedPrwt($lokasi_id, $episode_id, $trans_id, $pasien_id, $poli_id, $dokter_id, $rekanan_id, $user_id);

                // === 2. Pendaftaran ===
                $tarif_daftar = $this->rm->getTarifPenunjang();

                foreach ($tarif_daftar as $t) {
                    $harga = (int)$t['harga'];
                    $total_harga += $harga;

                    $data_it = [
                        'lokasi_id' => $lokasi_id,
                        'episode_id' => $episode_id,
                        'trans_id' => $trans_id,
                        'pasien_id' => $pasien_id,
                        'layan_id' => $t['layan_id'],
                        'dokter_id' => $dokter_id,
                        'qty' => 1,
                        'harga_satuan' => $harga,
                        'harga_total' => $harga,
                        'aktif' => '1',
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'tgl_transaksi' => date('Y-m-d')
                    ];

                    if ($pembiayaan == 'UMUM') $data_it['total_pribadi'] = $harga;
                    else $data_it['total_rekanan'] = $harga;

                    $this->rm->insertTransDetail($data_it);
                }

                // === 3. LAB & RAD ===
                $lab_item = $this->input->post('lab_item') ?? [];
                $lab_qty  = $this->input->post('lab_qty') ?? [];
                $rad_item = $this->input->post('rad_item') ?? [];
                $rad_qty  = $this->input->post('rad_qty') ?? [];


                // LAB
                foreach ($lab_item as $i => $layan_id) {
                    if (!$layan_id) continue;
                    $qty = (int)($lab_qty[$i] ?? 1);
                    $harga = $this->rm->getHargaLayan($layan_id);

                    $subtotal = $harga * $qty;
                    $total_harga += $subtotal;

                    $data_it = [
                        'lokasi_id' => $lokasi_id,
                        'episode_id' => $episode_id,
                        'trans_id' => $trans_id,
                        'pasien_id' => $pasien_id,
                        'layan_id' => $layan_id,
                        'dokter_id' => $dokter_id,
                        'qty' => $qty,
                        'harga_satuan' => $harga,
                        'harga_total' => $subtotal,
                        'aktif' => '1',
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'tgl_transaksi' => date('Y-m-d')
                    ];

                    if ($pembiayaan == 'UMUM') $data_it['total_pribadi'] = $subtotal;
                    else $data_it['total_rekanan'] = $subtotal;

                    $this->rm->insertTransDetail($data_it);

                    if ($this->rm->getKategoriLayan($layan_id) == 'JKL-LAB') {
                        $this->rm->insertLab([
                            'trans_co' => $trans_co,
                            'trans_id' => $trans_id,
                            'pasien_id' => $pasien_id,
                            'tanggal' => date('Y-m-d'),
                            'test_id' => $layan_id,
                            'show_item' => 1,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'episode_id' => $episode_id,
                            'trans_bayar_id' => $bayar_id ?? null,
                            'cito' => 'N',
                            'dikerjakan' => 'T',
                            'iscover' => 'T',
                            'nilcover' => 0
                        ]);
                    }
                }

                // RAD
                foreach ($rad_item as $i => $layan_id) {
                    if (!$layan_id) continue;

                    $qty = (int)($rad_qty[$i] ?? 1);
                    $harga = $this->rm->getHargaLayan($layan_id);
                    $subtotal = $harga * $qty;
                    $total_harga += $subtotal;


                    $data_it = [
                        'lokasi_id' => $lokasi_id,
                        'episode_id' => $episode_id,
                        'trans_id' => $trans_id,
                        'pasien_id' => $pasien_id,
                        'layan_id' => $layan_id,
                        'dokter_id' => $dokter_id,
                        'qty' => $qty,
                        'harga_satuan' => $harga,
                        'harga_total' => $subtotal,
                        'aktif' => '1',
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'tgl_transaksi' => date('Y-m-d'),
                    ];

                    if ($pembiayaan == 'UMUM') $data_it['total_pribadi'] = $subtotal;
                    else $data_it['total_rekanan'] = $subtotal;

                    $this->rm->insertTransDetail($data_it);

                    if ($this->rm->getKategoriLayan($layan_id) == 'JKL-RAD') {
                        $this->rm->insertRad([
                            'trans_co' => $trans_co,
                            'trans_id' => $trans_id,
                            'pasien_id' => $pasien_id,
                            'tanggal' => date('Y-m-d'),
                            'test_id' => $layan_id,
                            'kanan' => 'N',
                            'kiri' => 'N',
                            'show_item' => 1,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'episode_id' => $episode_id,
                            'trans_bayar_id' => $bayar_id ?? null,
                            'iscover' => 'T',
                            'nilcover' => 0
                        ]);
                    }
                }

                // === 4. Header Transaksi
                $data_hd = [
                    'lokasi_id' => $lokasi_id,
                    'episode_id' => $episode_id,
                    'trans_id' => $trans_id,
                    'pasien_id' => $pasien_id,
                    'shift_id' => $shift_id,
                    'bayar_id' => $bayar_id,
                    'jenis_tr' => '009',
                    'kelas_id' => $kelas_id,
                    'poli_id' => $poli_id,
                    'dokter_id' => $dokter_id,
                    'rekanan_id' => $rekanan_id,
                    'perjanjian_yn' => 'T',
                    'status_tr' => '00',
                    'tgl_transaksi' => date('Y-m-d'),
                    'total_harga' => $total_harga,
                    'aktif' => '1',
                    'created_by' => $user_id,
                    'created_date' => date('Y-m-d H:i:s'),
                    'total_tanggung' => 0,
                    'sdh_rehitung' => 'T'
                ];

                if ($pembiayaan == 'UMUM') $data_hd['total_pribadi'] = $total_harga;
                else $data_hd['total_rekanan'] = $total_harga;

                $this->rm->insertTransHeader($data_hd);

                if ($pembiayaan == 'UMUM') {

                    if ($bayar_nanti == 1) {
                        $uang_bayar = 0;
                        $total_harga = 0;
                        $total_kembali = 0;
                    } else {
                        $uang_bayar = $uang_bayar ?: $total_harga;
                        $total_kembali = $total_kembali ?: ($uang_bayar - $total_harga);
                    }

                    $this->rm->insertEpisodeBayar([
                        'lokasi_id' => $lokasi_id,
                        'episode_id' => $episode_id,
                        'pasien_id' => $pasien_id,
                        'bayar_id' => $bayar_id,
                        'shift_id' => $shift_id,
                        'rekanan_id' => $rekanan_id,
                        'tgl_transaksi' => date('Y-m-d H:i:s'),
                        'tgl_lunas' => date('Y-m-d H:i:s'),
                        'total_harga' => $total_harga,
                        'total_pribadi' => $total_harga,
                        'bayar_pribadi' => $uang_bayar,
                        'total_kembali' => $total_kembali,
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'jenis_tr' => 1,
                        'issplit' => 'T'
                    ]);
                } else {
                    $this->rm->insertEpisodeBayar([
                        'lokasi_id' => $lokasi_id,
                        'episode_id' => $episode_id,
                        'pasien_id' => $pasien_id,
                        'bayar_id' => $bayar_id,
                        'shift_id' => $shift_id,
                        'rekanan_id' => $rekanan_id,
                        'tgl_transaksi' => date('Y-m-d H:i:s'),
                        'tgl_lunas' => date('Y-m-d H:i:s'),
                        'total_harga' => $total_harga,
                        'total_rekanan' => $total_harga,
                        'bayar_rekanan' => $total_harga,
                        'created_by' => $user_id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'jenis_tr' => 1,
                        'issplit' => 'T'
                    ]);
                }
                $this->db->trans_commit();


                echo json_encode([
                    'success' => true,
                    'message' => "Registrasi penunjang berhasil (pembiayaan: $pembiayaan)",
                    'episode_id' => $episode_id,
                    'NoAntrianKlinik' => '-', // placeholder agar URL sama
                    'tgl_berobat' => date('Y-m-d'),
                    'total_harga' => $total_harga,
                    'uang_bayar' => $uang_bayar,
                    'total_kembali' => $total_kembali,
                    'metode_bayar' => $metode_bayar
                ]);
            } catch (Exception $e) {
                $this->db->trans_rollback();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            // === Poliklinik ===
            $poli = $this->input->post('poli');
            $dokter_id = $this->input->post('dokter');
            // $tgl_berobat = $this->input->post('tgl_berobat');
            $tgl_berobat = convertDateTime($this->input->post('tgl_berobat'), 'isoDate', 'slashDate');
            $jam_slot = $this->input->post('jam_slot');
            $episode_id = $this->rm->buat_episode_id('1')->buat_id_episode_id;
            $transaksi_id = $this->rm->buat_transaksi_id()->buat_id_transaksi_id;

            $bayar_id = ($pembiayaan == 'UMUM') ? $this->rm->buat_bayar_id('1')->buat_id_bayar_id : null;

            $jam_slot_raw = $this->input->post('jam_slot');

            //    return var_dump($jam_slot_raw);
            //                 die;


            if (!empty($jam_slot_raw)) {
                $jam_slot = json_decode($jam_slot_raw, true);


                if (json_last_error() === JSON_ERROR_NONE) {
                    $antrian     = $jam_slot['antrian'];
                    $jam_awal   = $jam_slot['jam_mulai'];
                    $jam_akhir = $jam_slot['jam_selesai'];
                    $hari_id = $jam_slot['hari_id'];
                    $antrian_id = $jam_slot['antrian_id'];

                    log_message('debug', "Antrian: $antrian, Jam Mulai: $jam_awal, Jam Selesai: $jam_akhir");
                }
            }
            // return var_dump($jam_awal);
            // die;

            $data = [
                'jenis_kunjungan' => $jenis_kunjungan,
                'tgl_berobat' => $tgl_berobat,
                'poli' => $poli,
                'dokter_id' => $dokter_id,
                'jam_awal' => $jam_awal,
                'jam_akhir' => $jam_akhir,
                'hari_id' => $hari_id,
                'antrian_id' => $antrian_id,
                'antrian' => $antrian,
            ];
            $this->db->trans_begin();
            try {
                $antrian = $this->rm->getAntrian($data['tgl_berobat'], $data['hari_id'], $data['antrian_id'], $data['poli'], $data['dokter_id']);
                $antrianKlinik = $antrian->antrian;

                $noUrut =  "";
                $this->rm->registPoliPasien($user_id, $data, $antrian, $noUrut, $episode_id, $transaksi_id, $pasien_id, $pembiayaan, ''); //ORI
                // return var_dump("oke");
                // die;
                $this->db->trans_commit();

                $data = [
                    'metadata' => [
                        'responCode' => '00',
                        'responDesc' => 'Registrasi Berhasil No. Antrian : ' .  $antrianKlinik,
                    ],
                    // 'NoAntrianPCare' => $bpjs['response']['message'],
                    'NoAntrianKlinik' =>  $antrianKlinik,
                    'episode_id' => $episode_id,
                    'tgl_berobat' => $data['tgl_berobat']
                ];
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                $data = [
                    'message' => $e->getMessage(),
                    'status' => false
                ];
            }
            echo json_encode($data);
        }
    }


    public function cekRiwayatProgram()
    {
        $pasien_id = $this->input->post('pasien_id');
        $jenis_kunjungan = $this->input->post('jenis_kunjungan');

        $lab_item = $this->input->post('lab_item');
        $rad_item = $this->input->post('rad_item');
        $layan_ids = array_merge($lab_item ?? [], $rad_item ?? []);
        $tahun = date('Y');

        $ada = $this->db->query("SELECT COUNT(*) AS jml 
            FROM pc01_keu_transctr_it a 
            join pc01_keu_episode b on a.episode_id =b.episode_id 
            WHERE a.pasien_id = ? 
              AND a.layan_id IN ?
              AND EXTRACT(YEAR FROM a.tgl_transaksi) = ? and b.status_episode in ('55','00') 
        ", [$pasien_id, $layan_ids, $tahun])->row()->jml ?? 0;

        echo json_encode([
            'sudah_pernah' => $ada > 0,
            'message' => $ada > 0 ? 'Pasien sudah pernah melakukan pemeriksaan ini tahun ini.' : ''
        ]);
    }


    public function getTarifByJenis($jenis_kunjungan)
    {
        $poli_id = $this->input->get('poli_id');

        if ($jenis_kunjungan == '1') {
            // Poliklinik → pendaftaran + pemeriksaan
            // $data = $this->rm->getTarifPoli();

            $data = $this->rm->getTarifPoliByJenis($poli_id);
        } elseif ($jenis_kunjungan == '2') {
            // Penunjang → hanya pendaftaran
            $data = $this->rm->getTarifPenunjang();
        } else {
            $data = [];
        }

        echo json_encode(['data' => $data]);
    }
}
