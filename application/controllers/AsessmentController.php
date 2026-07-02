<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Sesuaokan mulai chat GPT dari bagian ini Siap. Kita rapikan skemanya supaya:
// Isinya saaya minta di buat masternya dari DB global MS dan struktur form di ambil dari DB selanjutnya saya minta sesuaikan view form kembali
class AsessmentController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('AsessmentModel', 'am');
        $this->load->model('PasienModel', 'pm');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data['user']     = $this->um->get_user_by_id($user_id);
        $data['script']   = 'js/asessment.js';
        $data['page_css'] = 'css/asessment-modern.css';
        $data['title']    = 'Asesmen Pasien';

        /*
         * ALUR BARU YKI TAHAP 2
         * Asesmen Perawat hanya untuk kunjungan poliklinik.
         * Pemeriksaan penunjang APS/Lab/Rad tidak dimunculkan di worklist perawat,
         * karena asesmen penunjang akan muncul setelah dokter menentukan/order pemeriksaan.
         */
        $data['belum'] = $this->am->getBelumAnamnesa();
        $data['sudah'] = $this->am->getSudahAnamnesa();

        // Ambil daftar poliklinik aktif untuk filter.
        $data['poli'] = $this->am->getPoliklinik();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/index', $data);
        $this->load->view('templates/footer');
    }

    public function getDokterByPoli()
    {
        $poli_id = $this->input->get('poli_id');

        $dokter = $this->am->getDokterByPoli($poli_id);
        // return var_dump($dokter);
        // die;
        echo json_encode($dokter);
    }


    public function filterData()
    {
        $jenis     = $this->input->post('jenis_layanan');
        $poli_id   = $this->input->post('poli_id');
        $dokter_id = $this->input->post('dokter_id');
        $tab       = $this->input->post('tab');
        $tanggal   = $this->input->post('tanggal');

        if (empty($tanggal)) {
            $tanggal = date('Y-m-d');
        }

        /*
         * ALUR BARU:
         * - Filter PENUNJANG tidak digunakan lagi di asesmen perawat.
         * - Worklist hanya menampilkan pasien POLIKLINIK.
         * - Jika request lama masih mengirim PENUNJANG, sistem kembalikan data kosong
         *   agar tidak membuka form Lab/Rad lama.
         */
        if ($jenis === 'PENUNJANG') {
            echo "<tr><td colspan='11' class='text-center text-muted py-4'>Alur baru: asesmen penunjang Lab/Radiologi tidak lagi diisi di menu Asesmen Perawat.</td></tr>";
            return;
        }

        if ($tab === 'Belum') {
            if ($jenis === 'POLIKLINIK') {
                $data = $this->am->getBelumAnamnesaFiltered($jenis, $poli_id, $dokter_id, $tanggal);
            } else {
                $data = $this->am->getBelumAnamnesa($tanggal);
            }
        } else {
            if ($jenis === 'POLIKLINIK') {
                $data = $this->am->getSudahAnamnesaFiltered($jenis, $poli_id, $dokter_id, $tanggal);
            } else {
                $data = $this->am->getSudahAnamnesa($tanggal);
            }
        }

        if (!empty($data)) {
            $no = 1;

            foreach ($data as $row) {
                $tgl_daftar = date('d/m/Y', strtotime($tanggal));

                $status = ($tab === 'Belum')
                    ? "<span class='assessment-badge assessment-badge-warning'><i class='feather-clock me-1'></i> Belum Anamnesa</span>"
                    : "<span class='assessment-badge assessment-badge-success'><i class='feather-check me-1'></i> Sudah Asesmen</span>";

                if ($tab === 'Belum') {
                    $aksi = "
                    <button class='btn btn-sm btn-primary px-3 btnMulaiAsesmen'
                            data-episode='" . htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8') . "'
                            data-pasien='" . htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8') . "'
                            data-poli='" . htmlspecialchars($row->poli_id, ENT_QUOTES, 'UTF-8') . "'
                            data-kunj='" . htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') . "'>
                        <i class='feather-edit-3 me-1'></i> Mulai
                    </button>";
                } else {
                    $aksi = "
                    <button class='btn btn-sm btn-info px-3 btnEditAsesmen'
                            data-episode='" . htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8') . "'
                            data-pasien='" . htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8') . "'
                            data-poli='" . htmlspecialchars($row->poli_id, ENT_QUOTES, 'UTF-8') . "'
                            data-kunj='" . htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') . "'>
                        <i class='feather-eye me-1'></i> Lihat / Edit
                    </button>";
                }

                echo "
                <tr>
                    <td class='text-center'>{$no}</td>
                    <td class='fw-semibold text-center'>" . htmlspecialchars($row->int_pasien_id, ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($row->nama_pas, ENT_QUOTES, 'UTF-8') . "</td>
                    <td><span class='fw-semibold text-dark'>" . htmlspecialchars($row->nama_poli, ENT_QUOTES, 'UTF-8') . "</span></td>
                    <td class='text-center'>" . htmlspecialchars($row->nama_dr, ENT_QUOTES, 'UTF-8') . "</td>
                    <td class='text-center'>{$tgl_daftar}</td>
                    <td class='text-center'>" . htmlspecialchars($row->rekanan_id, ENT_QUOTES, 'UTF-8') . "</td>
                    <td class='text-center'>" . htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') . "</td>
                    <td class='text-center'>" . htmlspecialchars($row->kunjungan_poli, ENT_QUOTES, 'UTF-8') . "</td>
                    <td class='text-center'>{$status}</td>
                    <td class='text-center'>{$aksi}</td>
                </tr>";

                $no++;
            }
        } else {
            echo "<tr><td colspan='11' class='text-center text-muted py-4'>Tidak ada data ditemukan</td></tr>";
        }
    }

    public function getFormAsesmen()
    {
        $this->output->set_content_type('application/json');

        $episode_id = $this->input->post('episode_id');
        $poli_id    = $this->input->post('poli_id');
        $pasien_id  = $this->input->post('pasien_id');
        $jml_kunj   = (int) $this->input->post('jml_kunj');

        if (empty($episode_id) || empty($poli_id) || empty($pasien_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Data episode, poli, atau pasien tidak lengkap.'
            ]);
            return;
        }

        /*
         * ALUR BARU:
         * Poliklinik => Pengkajian Awal saja.
         * Poli Paliatif tetap dimulai dari Pengkajian Awal, lalu JS akan menawarkan
         * pengisian Form Paliatif setelah Pengkajian Awal berhasil disimpan.
         * APS/Penunjang tidak lagi membuka form asesmen Lab/Rad di menu perawat.
         */
        if ($poli_id === 'APS') {
            echo json_encode([
                'success' => false,
                'message' => 'Alur baru: Pemeriksaan Penunjang tidak lagi diisi pada menu Asesmen Perawat. Form penunjang akan muncul setelah dokter menentukan pemeriksaan.'
            ]);
            return;
        }

        if ($jml_kunj > 1) {
            $redirect = "Asessment/formKajiAwal/$episode_id/$pasien_id";
        } else {
            $redirect = "Asessment/formKajiAwalLengkap/$episode_id/$pasien_id";
        }

        echo json_encode([
            'success' => true,
            'redirect_url' => $redirect
        ]);
    }




    public function editAssesLab($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function viewAssesLab($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }


    public function editAssesRad($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function editPengkajianAwal($episode_id, $pasien_id, $jml_kunj, $poli)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data = [
            'title'     => 'Edit Pengkajian Awal',
            'script'    => 'js/asessment.js',
            'page_css'      => 'css/asessment-modern.css',
            'user'      => $this->um->get_user_by_id($user_id),
            'pasien'    => $this->am->getPasienRingkas($pasien_id),
            'episode_id' => $episode_id,
            'poli_id'   => $poli,
            'data_kaji' => $this->am->getKajiAwal($episode_id),
            'agama'     => $this->am->getGlobalByJenis('SAGM'),
            'hubungan'  => $this->am->getGlobalByJenis('JHUB'),
            'cara_masuk' => $this->am->getGlobalByJenis('KJCRMASUK'),
        ];

        // return var_dump($data);
        // die;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        // return var_dump($jml_kunj);
        // die;
        if ($jml_kunj > 1) {
            // return var_dump("oke > 1");
            // die;
            $this->load->view('asessment/form_kaji_lanjutan_edit', $data);
        } else {
            // return var_dump("oke = 1");
            // die;
            $this->load->view('asessment/form_kaji_awal_edit', $data);
        }
        $this->load->view('templates/footer');
    }

    public function formAssessmentLab($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }


    public function formAssessmentRad($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function formAssessmentLabRad($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }


    public function formKajiAwalFullLengkap($episode_id, $pasien_id)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data = [
            'title'         => 'Pengkajian Awal Pasien Rawat Jalan ',
            'script'         => 'js/asessment.js',
            'page_css'      => 'css/asessment-modern.css',
            'user'         => $this->um->get_user_by_id($user_id),
            'episode_id'    => $episode_id,
            'pasien'     =>   $this->am->getPasienRingkas($pasien_id),
            'layan'     =>   $this->am->getDetilLayan($episode_id),
            'agama'         => $this->am->getGlobalByJenis('SAGM'),
            'hubungan'    => $this->am->getGlobalByJenis('JHUB'),
            'cara_masuk'    => $this->am->getGlobalByJenis('KJCRMASUK'),
        ];

        // return var_dump($data['layan']->poli_id);
        // die;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_kaji_awal_full', $data);
        $this->load->view('templates/footer');
    }



    // Form Pengkajian Awal (Poliklinik)
    public function formKajiAwalFull($episode_id, $pasien_id)
    {
        $user_id = $this->session->userdata('user_id_pc');
        // $data['pasien']   = $this->am->getPasienRingkas($pasien_id);

        $data = [
            'title'         => 'Pengkajian Awal Pasien Rawat Jalan ',
            'script'         => 'js/asessment.js',
            'page_css'      => 'css/asessment-modern.css',
            'user'         => $this->um->get_user_by_id($user_id),
            'episode_id'    => $episode_id,
            'pasien'     =>   $this->am->getPasienRingkas($pasien_id),
            'layan'     =>   $this->am->getDetilLayan($episode_id),
            'agama'         => $this->am->getGlobalByJenis('SAGM'),
            'hubungan'    => $this->am->getGlobalByJenis('JHUB'),
            'cara_masuk'    => $this->am->getGlobalByJenis('KJCRMASUK'),
        ];


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_kaji_awal', $data);
        $this->load->view('templates/footer');
    }

    public function formMammoUsg($episode_id = null, $pasien_id = null)
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }
    public function saveAssessLabxx()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }


    public function saveAssessLab()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function saveAssessRad()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function saveKajiAwal()
    {
        $post = $this->input->post();

        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $resp = ['success' => false, 'message' => 'Episode ID dan Pasien ID wajib ada.'];

            // Kalau AJAX → balikan JSON, kalau bukan → pakai flashdata lama
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($resp));
            } else {
                $this->session->set_flashdata('error', $resp['message']);
                redirect('Asessment');
                return;
            }
        }


        // if (empty($post['episode_id']) || empty($post['pasien_id'])) {
        //     echo json_encode(['success' => false, 'message' => 'Episode ID dan Pasien ID wajib ada.']);
        //     return;
        // }

        // ------ PROSES ARRAY → STRING --------
        $post['status_mental']        = isset($post['status_mental']) ? implode(',', $post['status_mental']) : null;
        if ($post['nyeri_skor'] === "") $post['nyeri_skor'] = null;

        // ================================
        // WHITELIST FIELD YANG ADA DI TABEL
        // ================================
        $allowedFields = [
            'episode_id', 'pasien_id', 'created_by', 'tiba_tanggal', 'tiba_jam', 'kaji_tanggal', 'kaji_jam',
            'diperoleh_dari', 'hubungan', 'hubungan_lain', 'cara_masuk', 'keluhan_utama', 'pengobatan_skrg',
            'pengobatan_ket', 'riw_operasi_ada', 'riw_operasi_ket', 'riw_penyakit_keluarga',
            'riw_alergi_ada', 'riw_alergi_kapan', 'riw_alergi_reaksi', 'merokok', 'merokok_jml', 'merokok_lama',
            'merokok_jenis', 'alkohol', 'alkohol_jml', 'alkohol_lama', 'fisik_ku', 'fisik_td', 'fisik_nadi',
            'fisik_rr', 'fisik_suhu', 'fisik_bb', 'fisik_tb', 'fisik_lila', 'nyeri_ada', 'nyeri_metode', 'nyeri_skor',
            'nyeri_kategori', 'morse_total', 'morse_kategori', 'status_fungsional', 'status_mental', 'gizi_masalah',
            'gizi_detail', 'gizi_lainnya_ket', 'edk_bicara', 'edk_bicara_ket', 'edk_bahasa', 'edk_penerjemah',
            'edk_penerjemah_bahasa', 'edk_isyarat', 'edk_hambatan', 'edk_hambatan_list',
            'edk_kebutuhan', 'edk_kebutuhan_lain', 'sos_agama', 'sos_pendidikan', 'sos_pendidikan_lain',
            'sos_kerja', 'sos_kerja_lain', 'sos_suku', 'sos_kewarganegaraan', 'emosi', 'masalah_keperawatan',
            'masalah_tambahan', 'rencana_keperawatan', 'kolaborasi', 'kolaborasi_ket',
            'perawat_tgljam', 'perawat_nama', 'perawat_ttd', 'morse_1', 'morse_2', 'morse_3', 'morse_4', 'morse_5', 'morse_6'
        ];

        $dataInsert = [];

        foreach ($allowedFields as $field) {
            if (isset($post[$field])) {
                $dataInsert[$field] = $post[$field];
            }
        }

        // return var_dump($dataInsert);
        // die;

        $simpan = $this->am->insertPengkajianAwal($dataInsert);
        if ($simpan) {
            // UPDATE status pemeriksaan jadi selesai (01)
            $this->db->where('episode_id', $post['episode_id']);
            $this->db->where('pasien_id', $post['pasien_id']);
            $this->db->where('aktif', '1');
            $this->db->update('pcare_manager.pc01_med_prwt_tr', [
                'done_status' => '01'
            ]);


            $resp = [
                'success'    => true,
                'message'    => 'Pengkajian berhasil disimpan.',
                'episode_id' => $post['episode_id'],
                'pasien_id'  => $post['pasien_id'],
                // poli_id langsung dari POST
                'poli_id'    => $post['poli_id'] ?? null,
            ];


            // $this->session->set_flashdata('success', 'Pengkajian berhasil disimpan.');
        } else {
            $resp = [
                'success' => false,
                'message' => 'Gagal menyimpan pengkajian.'
            ];

            // $this->session->set_flashdata('error', 'Gagal menyimpan pengkajian.');
        }

        // Kalau AJAX → balikan JSON
        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($resp));
        }


        // Fallback lama (kalau bukan AJAX)
        if ($resp['success']) {
            $this->session->set_flashdata('success', $resp['message']);
        } else {
            $this->session->set_flashdata('error', $resp['message']);
        }


        redirect('Asessment');

        // return var_dump($post);
        // die;
    }

    public function saveKajiAwalFull()
    {
        $post = $this->input->post();

        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $resp = ['success' => false, 'message' => 'Episode ID dan Pasien ID wajib ada.'];

            // Kalau AJAX → balikan JSON, kalau bukan → pakai flashdata lama
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($resp));
            } else {
                $this->session->set_flashdata('error', $resp['message']);
                redirect('Asessment');
                return;
            }
        }






        // if (empty($post['episode_id']) || empty($post['pasien_id'])) {
        //     echo json_encode(['success' => false, 'message' => 'Episode ID dan Pasien ID wajib ada.']);
        //     return;
        // }

        // ------ PROSES ARRAY → STRING --------
        $post['diperoleh_dari']       = isset($post['diperoleh_dari']) ? implode(',', $post['diperoleh_dari']) : null;
        $post['cara_masuk']           = isset($post['cara_masuk']) ? implode(',', $post['cara_masuk']) : null;
        $post['status_mental']        = isset($post['status_mental']) ? implode(',', $post['status_mental']) : null;
        $post['gizi_detail']          = isset($post['gizi_detail']) ? implode(',', $post['gizi_detail']) : null;
        $post['edk_hambatan_list']    = isset($post['edk_hambatan_list']) ? implode(',', $post['edk_hambatan_list']) : null;
        $post['edk_kebutuhan']        = isset($post['edk_kebutuhan']) ? implode(',', $post['edk_kebutuhan']) : null;
        $post['emosi']                = isset($post['emosi']) ? implode(',', $post['emosi']) : null;
        $post['masalah_keperawatan']  = isset($post['masalah_keperawatan']) ? implode(',', $post['masalah_keperawatan']) : null;
        $post['kolaborasi']           = isset($post['kolaborasi']) ? implode(',', $post['kolaborasi']) : null;

        // masalah tambahan → gabung jadi 1 string
        if (isset($post['masalah_tambahan']) && is_array($post['masalah_tambahan'])) {
            $post['masalah_tambahan'] = implode(',', array_filter($post['masalah_tambahan']));
        }
        // -------- MODEL INSERT ----------
        // $this->load->model('AsesmenModel', 'am');


        if ($post['nyeri_skor'] === "") $post['nyeri_skor'] = null;

        // ================================
        // WHITELIST FIELD YANG ADA DI TABEL
        // ================================
        $allowedFields = [
            'episode_id', 'pasien_id', 'created_by', 'tiba_tanggal', 'tiba_jam', 'kaji_tanggal', 'kaji_jam',
            'diperoleh_dari', 'hubungan', 'hubungan_lain', 'cara_masuk', 'keluhan_utama', 'pengobatan_skrg',
            'pengobatan_ket', 'riw_operasi_ada', 'riw_operasi_ket', 'riw_penyakit_keluarga',
            'riw_alergi_ada', 'riw_alergi_kapan', 'riw_alergi_reaksi', 'merokok', 'merokok_jml', 'merokok_lama',
            'merokok_jenis', 'alkohol', 'alkohol_jml', 'alkohol_lama', 'fisik_ku', 'fisik_td', 'fisik_nadi',
            'fisik_rr', 'fisik_suhu', 'fisik_bb', 'fisik_tb', 'fisik_lila', 'nyeri_ada', 'nyeri_metode', 'nyeri_skor',
            'nyeri_kategori', 'morse_total', 'morse_kategori', 'status_fungsional', 'status_mental', 'gizi_masalah',
            'gizi_detail', 'gizi_lainnya_ket', 'edk_bicara', 'edk_bicara_ket', 'edk_bahasa', 'edk_penerjemah',
            'edk_penerjemah_bahasa', 'edk_isyarat', 'edk_hambatan', 'edk_hambatan_list',
            'edk_kebutuhan', 'edk_kebutuhan_lain', 'sos_agama', 'sos_pendidikan', 'sos_pendidikan_lain',
            'sos_kerja', 'sos_kerja_lain', 'sos_suku', 'sos_kewarganegaraan', 'emosi', 'masalah_keperawatan',
            'masalah_tambahan', 'rencana_keperawatan', 'kolaborasi', 'kolaborasi_ket',
            'perawat_tgljam', 'perawat_nama', 'perawat_ttd', 'morse_1', 'morse_2', 'morse_3', 'morse_4', 'morse_5', 'morse_6'
        ];

        // FILTER DATA → hanya field yang ada di tabel
        $dataInsert = [];
        foreach ($allowedFields as $field) {
            if (isset($post[$field])) {
                $dataInsert[$field] = $post[$field];
            }
        }

        // return var_dump($dataInsert);
        // die;

        $simpan = $this->am->insertPengkajianAwal($dataInsert);


        if ($simpan) {
            // UPDATE status pemeriksaan jadi selesai (01)
            $this->db->where('episode_id', $post['episode_id']);
            $this->db->where('pasien_id', $post['pasien_id']);
            $this->db->where('aktif', '1');
            $this->db->update('pcare_manager.pc01_med_prwt_tr', [
                'done_status' => '01'
            ]);


            $resp = [
                'success'    => true,
                'message'    => 'Pengkajian berhasil disimpan.',
                'episode_id' => $post['episode_id'],
                'pasien_id'  => $post['pasien_id'],
                // poli_id langsung dari POST
                'poli_id'    => $post['poli_id'] ?? null,
            ];
        } else {
            $resp = [
                'success' => false,
                'message' => 'Gagal menyimpan pengkajian.'
            ];
        }

        // Kalau AJAX → balikan JSON
        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($resp));
        }

        // Fallback lama (kalau bukan AJAX)
        if ($resp['success']) {
            $this->session->set_flashdata('success', $resp['message']);
        } else {
            $this->session->set_flashdata('error', $resp['message']);
        }

        redirect('Asessment');
    }

    public function updateAssessLabxxx()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }



    public function updateAssessLab()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }



    public function updateAssessRad()
    {
        $this->session->set_flashdata('error', 'Alur baru: asesmen Lab/Radiologi tidak lagi tersedia di menu Asesmen Perawat. Form penunjang akan dikerjakan setelah dokter menentukan pemeriksaan.');
        redirect('Asessment');
    }

    public function updatePengkajianAwal()
    {
        // return var_dump("oke");
        // die;

        $post = $this->input->post();

        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Episode ID dan Pasien ID wajib ada.');
            redirect('Asessment');
            return;
        }

        // ================================
        // ARRAY → STRING (implode)
        // ================================
        $post['diperoleh_dari']      = isset($post['diperoleh_dari']) ? implode(',', $post['diperoleh_dari']) : null;
        $post['cara_masuk']          = isset($post['cara_masuk']) ? implode(',', $post['cara_masuk']) : null;
        $post['status_mental']       = isset($post['status_mental']) ? implode(',', $post['status_mental']) : null;
        $post['gizi_detail']         = isset($post['gizi_detail']) ? implode(',', $post['gizi_detail']) : null;
        $post['edk_hambatan_list']   = isset($post['edk_hambatan_list']) ? implode(',', $post['edk_hambatan_list']) : null;
        $post['edk_kebutuhan']       = isset($post['edk_kebutuhan']) ? implode(',', $post['edk_kebutuhan']) : null;
        $post['emosi']               = isset($post['emosi']) ? implode(',', $post['emosi']) : null;
        $post['masalah_keperawatan'] = isset($post['masalah_keperawatan']) ? implode(',', $post['masalah_keperawatan']) : null;
        $post['kolaborasi']           = isset($post['kolaborasi']) ? implode(',', $post['kolaborasi']) : null;

        // Masalah tambahan: gabungkan jadi string (kosongkan null bila tak ada)
        if (isset($post['masalah_tambahan']) && is_array($post['masalah_tambahan'])) {
            $post['masalah_tambahan'] = implode(',', array_filter($post['masalah_tambahan']));
        }

        // Kosongkan nilai nyeri_skor jika ""
        if ($post['nyeri_skor'] === "") {
            $post['nyeri_skor'] = null;
        }

        // ================================
        // ALLOWED FIELDS
        // ================================
        $allowedFields = [
            'tiba_tgl', 'tiba_jam', 'tgl_kaji', 'jam_kaji',
            'diperoleh_dari', 'hubungan', 'hubungan_lain', 'cara_masuk',
            'keluhan_utama', 'pengobatan_skrg', 'pengobatan_ket',
            'riw_operasi_ada', 'riw_operasi_ket', 'riw_penyakit_keluarga',
            'riw_alergi_ada', 'riw_alergi_kapan', 'riw_alergi_reaksi',
            'merokok', 'merokok_jml', 'merokok_lama', 'merokok_jenis',
            'alkohol', 'alkohol_jml', 'alkohol_lama',
            'fisik_ku', 'fisik_td', 'fisik_nadi', 'fisik_rr', 'fisik_suhu',
            'fisik_bb', 'fisik_tb', 'fisik_lila',
            'nyeri_ada', 'nyeri_metode', 'nyeri_skor', 'nyeri_kategori',
            'morse_total', 'morse_kategori', 'status_fungsional',
            'status_mental', 'gizi_masalah', 'gizi_detail', 'gizi_lainnya_ket',
            'edk_bicara', 'edk_bicara_ket', 'edk_bahasa', 'edk_penerjemah',
            'edk_penerjemah_bahasa', 'edk_isyarat',
            'edk_hambatan', 'edk_hambatan_list', 'edk_kebutuhan',
            'edk_kebutuhan_lain', 'sos_agama', 'sos_pendidikan',
            'sos_pendidikan_lain', 'sos_kerja', 'sos_kerja_lain',
            'sos_suku', 'sos_kewarganegaraan', 'emosi',
            'masalah_keperawatan', 'masalah_tambahan', 'rencana_keperawatan',
            'kolaborasi', 'kolaborasi_ket',
            'perawat_tgljam', 'perawat_nama', 'morse_1', 'morse_2', 'morse_3', 'morse_4', 'morse_5', 'morse_6'
        ];

        // FILTER post → hanya allowed field
        $dataUpdate = [];
        foreach ($allowedFields as $f) {
            if (isset($post[$f])) {
                $dataUpdate[$f] = $post[$f];
            }
        }

        // ================================
        // UPDATE KE TABEL KAJI AWAL
        // ================================
        $episode_id = $post['episode_id'];
        $pasien_id  = $post['pasien_id'];

        $simpan = $this->am->updatePengkajianAwal($episode_id, $pasien_id, $dataUpdate);

        // ================================
        // UPDATE KE PRWT_TR done_status = 01
        // ================================
        if ($simpan) {
            $this->db->where('episode_id', $episode_id);
            $this->db->where('pasien_id', $pasien_id);
            $this->db->update('pc01_med_prwt_tr', ['done_status' => '01']);
        }

        // ================================
        // FLASH MESSAGE
        // ================================
        if ($simpan) {
            $this->session->set_flashdata('success', 'Pengkajian berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pengkajian.');
        }

        redirect('Asessment');
    }


    public function paliatifForm()
    {
        $episode_id = $this->input->get('episode_id');
        $pasien_id  = $this->input->get('pasien_id');
        // $data['script'] = 'js/asessment.js';
        $data['page_css'] = 'css/asessment-modern.css';

        $user_id = $this->session->userdata('user_id_pc');
        // $data['user'] = $this->um->get_user_by_id($user_id);


        if (!$episode_id || !$pasien_id) {
            show_404();
        }

        $pasien = $this->am->getPasienRingkas($pasien_id); // SESUAIKAN

        $layan  = $this->am->getDetilLayan($episode_id);  // SESUAIKAN
        // return var_dump($layan);
        // die;


        // cek apakah sudah pernah diisi
        $dataPaliatif = $this->am->get_paliatif($episode_id, $pasien_id);

        // return var_dump($dataPaliatif);
        // die;



        $data = [
            'episode_id'   => $episode_id,
            'pasien_id'    => $pasien_id,
            'pasien'       => $pasien,
            'layan'        => $layan,
            'paliatif'     => $dataPaliatif,
            'is_edit'     => !empty($dataPaliatif) ? true : false,
            'script'      => 'js/asessment.js',
            'page_css'      => 'css/asessment-modern.css', // ini yang kurang
            'user'         => $this->um->get_user_by_id($user_id),
        ];

        // return var_dump($data);
        // die;




        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_paliatif', $data);
        $this->load->view('templates/footer');
    }



    public function savePaliatif()
    {
        $this->output->set_content_type('application/json');

        // kalau mau tetap khusus AJAX, biarkan
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $episode_id = trim((string) $this->input->post('episode_id', true));
        $pasien_id  = trim((string) $this->input->post('pasien_id', true));

        if ($episode_id === '' || $pasien_id === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Episode ID / Pasien ID kosong.'
            ]);
            return;
        }

        // =========================
        // NORMALISASI DATA DASAR
        // =========================
        $umur = $this->input->post('umur', true);
        $umur = ($umur === '' || $umur === null) ? null : (int) $umur;

        $tgl_kunjungan = trim((string) $this->input->post('tgl_kunjungan', true));
        $tgl_kunjungan = ($tgl_kunjungan === '') ? null : $tgl_kunjungan;

        // =========================
        // SYMPTOM (checkbox + keterangan)
        // =========================
        $symptom      = $this->input->post('symptom');
        $symptom_desc = $this->input->post('symptom_desc');
        $symptom_data = [];

        if (is_array($symptom)) {
            foreach ($symptom as $key => $val) {
                $symptom_data[$key] = [
                    'cek' => true,
                    'ket' => (is_array($symptom_desc) && isset($symptom_desc[$key]))
                        ? trim($symptom_desc[$key])
                        : ''
                ];
            }
        }

        // =========================
        // MENTAL STATE
        // =========================
        $mental_state = $this->input->post('mental_state');
        $mental_state_json = (is_array($mental_state) && count($mental_state) > 0)
            ? json_encode(array_values($mental_state))
            : null;

        // =========================
        // VALIDASI SERVER SIDE
        // =========================
        $errors = [];

        $petugas_hhpc  = trim((string) $this->input->post('petugas_hhpc', true));
        $nama_pasien   = trim((string) $this->input->post('nama_pasien', true));
        $jenis_kelamin = trim((string) $this->input->post('jenis_kelamin', true));
        $care_giver    = trim((string) $this->input->post('care_giver', true));
        $masalah_1     = trim((string) $this->input->post('masalah_1', true));
        $nyeri         = trim((string) $this->input->post('nyeri', true));
        $diagnosis     = trim((string) $this->input->post('diagnosis', true));

        if ($petugas_hhpc === '') {
            $errors[] = 'Oleh Tim / Relawan HHPC wajib diisi.';
        }

        if ($tgl_kunjungan === '') {
            $errors[] = 'Hari / Tanggal wajib diisi.';
        }

        if ($nama_pasien === '') {
            $errors[] = 'Nama pasien wajib diisi.';
        }

        if ($jenis_kelamin === '') {
            $errors[] = 'Jenis kelamin wajib dipilih.';
        }

        if ($umur !== null && ($umur < 0 || $umur > 150)) {
            $errors[] = 'Umur harus antara 0 sampai 150.';
        }

        if ($masalah_1 === '' && $nyeri === '' && $diagnosis === '') {
            $errors[] = 'Minimal isi salah satu: masalah paliatif, nyeri, atau diagnosis.';
        }

        if ($care_giver === '') {
            $errors[] = 'Nama care giver wajib diisi.';
        }

        // validasi symptom desc kalau dicentang
        if (is_array($symptom)) {
            foreach ($symptom as $key => $val) {
                $ket = (is_array($symptom_desc) && isset($symptom_desc[$key]))
                    ? trim($symptom_desc[$key])
                    : '';

                if ($val == '1' && $ket === '') {
                    $errors[] = 'Keterangan symptom "' . $key . '" wajib diisi.';
                }
            }
        }

        // minimal satu pemeriksaan terisi
        $pemeriksaanxxx = [
            trim((string) $this->input->post('keadaan_umum', true)),
            trim((string) $this->input->post('bicara', true)),
            trim((string) $this->input->post('pucat', true)),
            trim((string) $this->input->post('jaundice', true)),
            trim((string) $this->input->post('oedema', true)),
            trim((string) $this->input->post('respiratory_system', true)),
            trim((string) $this->input->post('abdomen', true)),
            trim((string) $this->input->post('pr', true)),
            trim((string) $this->input->post('pv', true)),
        ];


        $pemeriksaan = [
            trim((string) $this->input->post('keadaan_umum', true)),

            // Komunikasi kiri
            trim((string) $this->input->post('bicara', true)),
            trim((string) $this->input->post('pucat', true)),
            trim((string) $this->input->post('jaundice', true)),
            trim((string) $this->input->post('cyanosis', true)),

            // Komunikasi tengah
            trim((string) $this->input->post('pendengaran', true)),
            trim((string) $this->input->post('hydration', true)),
            trim((string) $this->input->post('mouth', true)),
            trim((string) $this->input->post('clubbing', true)),

            // Komunikasi kanan
            trim((string) $this->input->post('penglihatan', true)),
            trim((string) $this->input->post('skin', true)),
            trim((string) $this->input->post('sinus', true)),
            trim((string) $this->input->post('fistula', true)),
            trim((string) $this->input->post('dekubitus_exam', true)),

            // Pemeriksaan lanjutan
            trim((string) $this->input->post('oedema', true)),
            trim((string) $this->input->post('cardiovascular', true)),
            trim((string) $this->input->post('respiratory_system', true)),
            trim((string) $this->input->post('abdomen', true)),
            trim((string) $this->input->post('pr', true)),
            trim((string) $this->input->post('pv', true)),
        ];


        $hasPemeriksaan = false;
        foreach ($pemeriksaan as $v) {
            if ($v !== '') {
                $hasPemeriksaan = true;
                break;
            }
        }

        if (!$hasPemeriksaan) {
            $errors[] = 'Minimal isi salah satu bagian examination / pemeriksaan.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errors)
            ]);
            return;
        }


        // cek data aktif lama
        $existing = $this->am->get_paliatif($episode_id, $pasien_id);


        // =========================
        // DATA INSERT
        // =========================
        $insert = [
            'episode_id'            => $episode_id,
            'pasien_id'             => $pasien_id,
            'aktif'                 => '1',

            // data pasien
            'petugas_hhpc'          => $petugas_hhpc,
            'tgl_kunjungan'         => $tgl_kunjungan,
            'no_hhpc'               => $this->input->post('no_hhpc', true),
            'nama_pasien'           => $nama_pasien,
            'umur'                  => $umur,
            'agama'                 => $this->input->post('agama', true),
            'jenis_kelamin'         => $jenis_kelamin,
            'suku_bangsa'           => $this->input->post('suku_bangsa', true),
            'bahasa'                => $this->input->post('bahasa', true),
            'alamat'                => $this->input->post('alamat', true),
            'telp'                  => $this->input->post('telp', true),
            'fax'                   => $this->input->post('fax', true),
            'hp'                    => $this->input->post('hp', true),

            // masalah & keluarga
            'masalah_1'             => $this->input->post('masalah_1', true),
            'masalah_2'             => $this->input->post('masalah_2', true),
            'masalah_3'             => $this->input->post('masalah_3', true),
            'care_giver'            => $this->input->post('care_giver', true),
            'pasien_tahu'           => $this->input->post('pasien_tahu', true),
            'keluarga_sakit_sama'   => $this->input->post('keluarga_sakit_sama', true),
            'dukungan'              => $this->input->post('dukungan', true),
            'staff_perawat'         => $this->input->post('staff_perawat', true),

            // physical
            'nyeri'                 => $this->input->post('nyeri', true),
            'symptom_data'          => !empty($symptom_data) ? json_encode($symptom_data) : null,

            // mental
            'mental_state'          => $mental_state_json,
            'mental_note'           => $this->input->post('mental_note', true),
            'diagnosis'             => $this->input->post('diagnosis', true),
            'prognosis'             => $this->input->post('prognosis', true),

            // examination
            // 'keadaan_umum'          => $this->input->post('keadaan_umum', true),
            // 'bicara'                => $this->input->post('bicara', true),
            // 'pucat'                 => $this->input->post('pucat', true),
            // 'jaundice'              => $this->input->post('jaundice', true),
            // 'cyanosis'              => $this->input->post('cyanosis', true),
            // 'oedema'                => $this->input->post('oedema', true),
            // 'respiratory_system'    => $this->input->post('respiratory_system', true),
            // 'abdomen'               => $this->input->post('abdomen', true),
            // 'pr_note'               => $this->input->post('pr', true),
            // 'pv_note'               => $this->input->post('pv', true),



            // examination
            'keadaan_umum'          => $this->input->post('keadaan_umum', true),

            // Komunikasi kiri
            'bicara'                => $this->input->post('bicara', true),
            'pucat'                 => $this->input->post('pucat', true),
            'jaundice'              => $this->input->post('jaundice', true),
            'cyanosis'              => $this->input->post('cyanosis', true),

            // Komunikasi tengah
            'pendengaran'           => $this->input->post('pendengaran', true),
            'hydration'             => $this->input->post('hydration', true),
            'mouth'                 => $this->input->post('mouth', true),
            'clubbing'              => $this->input->post('clubbing', true),

            // Komunikasi kanan
            'penglihatan'           => $this->input->post('penglihatan', true),
            'skin'                  => $this->input->post('skin', true),
            'sinus'                 => $this->input->post('sinus', true),
            'fistula'               => $this->input->post('fistula', true),
            'dekubitus_exam'        => $this->input->post('dekubitus_exam', true),

            // Pemeriksaan lanjutan
            'oedema'                => $this->input->post('oedema', true),
            'cardiovascular'        => $this->input->post('cardiovascular', true),
            'respiratory_system'    => $this->input->post('respiratory_system', true),
            'abdomen'               => $this->input->post('abdomen', true),
            'pr_note'               => $this->input->post('pr', true),
            'pv_note'               => $this->input->post('pv', true),





            // gambar base64 - disesuaikan dengan JS terbaru
            'lung_left_img'         => $this->input->post('canvas_lung_left'),
            'lung_right_img'        => $this->input->post('canvas_lung_right'),
            'abdomen_img'           => $this->input->post('canvas_abdomen'),

            'created_by'            => $this->session->userdata('user_id_pc'),
            'created_at'            => date('Y-m-d H:i:s')
        ];

        // =========================
        // TRANSACTION
        // =========================
        $this->db->trans_begin();

        // $this->am->nonaktifkan_paliatif($episode_id, $pasien_id);
        // $this->am->insert_paliatif($insert);



        if (!empty($existing)) {
            // mode update versi: nonaktifkan lama lalu insert baru
            $this->am->nonaktifkan_paliatif($episode_id, $pasien_id);
            $aksi = 'diperbarui';
        } else {
            // mode insert baru
            $aksi = 'disimpan';
        }




        $this->am->insert_paliatif($insert);



        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            echo json_encode([
                'success' => false,
                'message' => 'Gagal menyimpan asesmen paliatif.'
            ]);
            return;
        }

        $this->db->trans_commit();

        echo json_encode([
            'success'  => true,
            'status'   => true,
            // 'message'  => 'Asesmen paliatif berhasil disimpan.',
            'message'  => 'Asesmen paliatif berhasil ' . $aksi . '.',
            'redirect' => base_url('Asessment')
        ]);
    }




    public function getPaliatif()
    {
        $episode_id = $this->input->get('episode_id');
        $pasien_id  = $this->input->get('pasien_id');

        if (!$episode_id || !$pasien_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $row = $this->am->get_paliatif($episode_id, $pasien_id);

        // return var_dump($row);
        // die;

        if ($row) {
            // ========== mental_state: string JSON → array ==========
            $row->mental_state = $row->mental_state
                ? json_decode($row->mental_state, true)
                : [];


            // ========== symptom_data: JSON → symptom + symptom_desc ==========
            $row->symptom      = [];
            $row->symptom_desc = [];


            if (!empty($row->symptom_data)) {
                $raw = json_decode($row->symptom_data, true);

                if (is_array($raw)) {
                    foreach ($raw as $key => $item) {
                        // cek: apakah checklist dicentang
                        if (!empty($item['cek'])) {
                            // JS hanya butuh "ada" → kasih 1/true
                            $row->symptom[$key] = 1;
                        }

                        // ket: keterangan
                        if (isset($item['ket'])) {
                            $row->symptom_desc[$key] = $item['ket'];
                        }
                    }
                }
            }


            // ========== mapping field yang beda nama antara DB & form ==========
            // di DB: pr_note, pv_note → di form: name="pr", name="pv"
            $row->pr = $row->pr_note;
            $row->pv = $row->pv_note;




            // di DB: kesimpulan_pasien, rencana_selanjutnya
            // di form kalau pakai name="kesimpulan" & "rencana"
            $row->kesimpulan = $row->kesimpulan_pasien;
            $row->rencana    = $row->rencana_selanjutnya;

            // decode field JSON kalau kamu simpan sebagai jsonb/text
            // $row->symptom       = $row->symptom ? json_decode($row->symptom, true) : [];
            // $row->symptom_desc  = $row->symptom_desc ? json_decode($row->symptom_desc, true) : [];
            // $row->mental_state  = $row->mental_state ? json_decode($row->mental_state, true) : [];

            echo json_encode([
                'success' => true,
                'data'    => $row,
            ]);
        } else {
            // tidak ada record → form tetap kosong
            echo json_encode([
                'success' => true,
                'data'    => null,
            ]);
        }
    }
}
