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
        $data['user'] = $this->um->get_user_by_id($user_id);
        // $data['user'] = $this->db->get_where('pc01_gen_user_data', ['user_id' => $this->session->userdata('user_id_pc')])->row_array();
        $data['script'] = 'js/asessment.js';
        // return var_dump($data['user']);
        // die;
        $data['title'] = 'Asesment Pasien';
        // Ambil data pasien belum & sudah anamnesa
        $data['belum'] = $this->am->getBelumAnamnesa();
        $data['sudah'] = $this->am->getSudahAnamnesa();

        // Tambahkan detil pemeriksaan jika poli_id = APS
        foreach ($data['belum'] as &$row) {
            if ($row->poli_id == 'APS') {
                $row->detil_pemeriksaan = $this->am->getDetilPemeriksaanByEpisode($row->episode_id);
            } else {
                $row->detil_pemeriksaan = [];
            }
        }

        foreach ($data['sudah'] as &$row) {
            if ($row->poli_id == 'APS') {
                // $row->detil_pemeriksaan = $this->am->getDetilPemeriksaanByEpisode($row->episode_id);
                $detil = $this->am->getDetilPemeriksaanByEpisode($row->episode_id);

                // 🔥 tambahkan status per layanan
                foreach ($detil as &$d) {
                    $d->is_done = $this->am->cekAsesmenPerLayanan(
                        $row->episode_id,
                        $row->pasien_id,
                        $d->kategori_id // JKL-LAB / JKL-RAD
                    );
                }

                $row->detil_pemeriksaan = $detil;
            } else {
                $row->detil_pemeriksaan = [];
            }
        }

        // Ambil daftar poliklinik aktif
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

        if ($tab === 'Belum') {
            if ($jenis === 'PENUNJANG') {
                $data = $this->am->getBelumAnamnesaPenunjang($tanggal);
            } elseif ($jenis === 'POLIKLINIK') {
                $data = $this->am->getBelumAnamnesaFiltered($jenis, $poli_id, $dokter_id, $tanggal);
            } else {
                $data = $this->am->getBelumAnamnesa($tanggal);
            }
        } else {
            if ($jenis === 'PENUNJANG') {
                $data = $this->am->getSudahAnamnesaPenunjang($tanggal);
            } elseif ($jenis === 'POLIKLINIK') {
                $data = $this->am->getSudahAnamnesaFiltered($jenis, $poli_id, $dokter_id, $tanggal);
            } else {
                $data = $this->am->getSudahAnamnesa($tanggal);
            }
        }

        if (!empty($data)) {
            foreach ($data as &$row) {
                if ($row->poli_id === 'APS') {
                    $detil = $this->am->getDetilPemeriksaanByEpisode($row->episode_id);

                    if ($tab === 'Selesai') {
                        foreach ($detil as &$d) {
                            $d->is_done = $this->am->cekAsesmenPerLayanan(
                                $row->episode_id,
                                $row->pasien_id,
                                $d->kategori_id
                            );
                        }
                        unset($d);
                    }

                    $row->detil_pemeriksaan = $detil;
                } else {
                    $row->detil_pemeriksaan = [];
                }
            }
            unset($row);
        }

        if (!empty($data)) {
            $no = 1;

            foreach ($data as $row) {
                $tgl_daftar = date('d/m/Y', strtotime($tanggal));

                $status = ($tab === 'Belum')
                    ? "<span class='badge bg-warning text-dark'><i class='feather-clock me-1'></i> Belum Anamnesa</span>"
                    : "<span class='badge bg-success'><i class='feather-check me-1'></i> Sudah Asesmen</span>";

                $data_layanan_attr = htmlspecialchars(
                    json_encode($row->detil_pemeriksaan ?? []),
                    ENT_QUOTES,
                    'UTF-8'
                );

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
                    $kategori = array_column($row->detil_pemeriksaan ?? [], 'kategori_id');
                    $multi = in_array('JKL-LAB', $kategori) && in_array('JKL-RAD', $kategori);

                    if ($multi) {
                        $aksi = "<div class='d-flex flex-column gap-1 align-items-center'>";

                        foreach ($row->detil_pemeriksaan as $d) {
                            $episode = htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8');
                            $pasien  = htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8');
                            $layanan = htmlspecialchars($d->kategori_id, ENT_QUOTES, 'UTF-8');
                            $nama    = htmlspecialchars($d->nama_layan1, ENT_QUOTES, 'UTF-8');

                            if (!empty($d->is_done)) {
                                $aksi .= "
                                <button class='btn btn-sm btn-outline-success btnEditLayanan'
                                        data-episode='{$episode}'
                                        data-pasien='{$pasien}'
                                        data-layanan='{$layanan}'>
                                    <i class='feather-edit'></i> Edit {$nama}
                                </button>";
                            } else {
                                $aksi .= "
                                <button class='btn btn-sm btn-outline-danger btnAsesmenLayanan'
                                        data-episode='{$episode}'
                                        data-pasien='{$pasien}'
                                        data-layanan='{$layanan}'>
                                    <i class='feather-edit'></i> Isi {$nama}
                                </button>";
                            }
                        }

                        $aksi .= "</div>";
                    } else {
                        $aksi = "
                        <button class='btn btn-sm btn-info px-3 btnEditAsesmen'
                                data-episode='" . htmlspecialchars($row->episode_id, ENT_QUOTES, 'UTF-8') . "'
                                data-pasien='" . htmlspecialchars($row->pasien_id, ENT_QUOTES, 'UTF-8') . "'
                                data-poli='" . htmlspecialchars($row->poli_id, ENT_QUOTES, 'UTF-8') . "'
                                data-kunj='" . htmlspecialchars($row->kunjungan_rs, ENT_QUOTES, 'UTF-8') . "'
                                data-layanan='{$data_layanan_attr}'>
                            <i class='feather-eye me-1'></i> Lihat / Edit
                        </button>";
                    }
                }

                $detil = "";
                if (!empty($row->detil_pemeriksaan)) {
                    $detil .= "<ul class='mb-0 ps-3 small text-muted'>";
                    foreach ($row->detil_pemeriksaan as $d) {
                        $detil .= "<li>" . htmlspecialchars($d->nama_layan1, ENT_QUOTES, 'UTF-8') . "</li>";
                    }
                    $detil .= "</ul>";
                }

                echo "
                <tr>
                    <td class='text-center'>{$no}</td>
                    <td class='fw-semibold text-center'>" . htmlspecialchars($row->int_pasien_id, ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($row->nama_pas, ENT_QUOTES, 'UTF-8') . "</td>
                    <td>
                        <span class='fw-semibold text-dark'>" . htmlspecialchars($row->nama_poli, ENT_QUOTES, 'UTF-8') . "</span>
                        {$detil}
                    </td>
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
            echo "<tr><td colspan='11' class='text-center text-muted'>Tidak ada data ditemukan</td></tr>";
        }
    }

    public function getFormAsesmen()
    {
        $this->output->set_content_type('application/json');
        $episode_id = $this->input->post('episode_id');
        $poli_id = $this->input->post('poli_id');
        $pasien_id = $this->input->post('pasien_id');
        $jml_kunj = $this->input->post('jml_kunj');

        // return var_dump($jml_kunj);
        // die;

        if (empty($episode_id) || empty($poli_id)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
            return;
        }

        // === Jika POLI APS, cek layanan apa yang dilakukan ===
        if ($poli_id === 'APS') {
            $detil = $this->am->getDetilPemeriksaanByEpisode($episode_id);

            if (empty($detil)) {
                echo json_encode(['success' => false, 'message' => 'Tidak ada layanan penunjang ditemukan.']);
                return;
            }

            // Flag untuk mendeteksi layanan ganda
            $adaLab = false;
            $adaRad = false;

            foreach ($detil as $layanan) {
                if ($layanan->kategori_id === 'JKL-LAB') {
                    $adaLab = true;
                }
                if ($layanan->kategori_id === 'JKL-RAD') {
                    $adaRad = true;
                }
            }

            // === Kondisi gabungan: LAB + RAD ===
            if ($adaLab && $adaRad) {
                echo json_encode([
                    'success' => true,
                    'redirect_url' => "Asessment/formAsessLabRad/$episode_id/$pasien_id"
                ]);
                return;
            }

            // === Hanya LAB ===
            if ($adaLab) {
                echo json_encode([
                    'success' => true,
                    'redirect_url' => "Asessment/formAsessLab/$episode_id/$pasien_id"
                ]);
                return;
            }

            // === Hanya Radiologi ===
            if ($adaRad) {
                echo json_encode([
                    'success' => true,
                    'redirect_url' => "Asessment/formAsessRad/$episode_id/$pasien_id"
                ]);
                return;
            }

            // Tidak ditemukan jenis layanan yang dikenali
            echo json_encode([
                'success' => false,
                'message' => 'Tidak ada form asesmen yang cocok dengan layanan APS ini.'
            ]);
            return;
        }

        // return var_dump($jml_kunj);
        // die;

        // === Jika POLI selain APS ===
        if ($jml_kunj > 1) {
            // Sudah pernah berkunjung → Form lengkap
            $redirect = "Asessment/formKajiAwal/$episode_id/$pasien_id";
        } else {
            // Kunjungan pertama → Form dasar
            $redirect = "Asessment/formKajiAwalLengkap/$episode_id/$pasien_id";
        }

        echo json_encode([
            'success' => true,
            'redirect_url' => $redirect
        ]);
    }




    public function editAssesLab($episode_id, $pasien_id)
    {
        if (!$episode_id || !$pasien_id) show_404();

        $user_id = $this->session->userdata('user_id_pc');

        $data['user']        = $this->um->get_user_by_id($user_id);
        $data['agama']       = $this->am->getGlobalByJenis('SAGM');
        $data['pasien']      = $this->am->getPasienRingkas($pasien_id);
        $data['statusKawin'] = $this->pm->getStatus();
        $data['pendidikan']  = $this->pm->getPendidikan();

        // Data asesmen yang sudah disimpan sebelumnya
        $data['data_kaji']   = $this->am->getAssessLab($episode_id, $pasien_id);

        $data['episode_id']  = $episode_id;
        $data['pasien_id']   = $pasien_id;
        $data['title']       = 'Edit Assessment Laboratorium';
        $data['script']      = 'js/asessment.js';

        // Tampilkan
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_lab_edit', $data);
        $this->load->view('templates/footer');
    }

    public function viewAssesLab($episode_id, $pasien_id)
    {
        if (!$episode_id || !$pasien_id) show_404();

        $user_id = $this->session->userdata('user_id_pc');

        $data['user']        = $this->um->get_user_by_id($user_id);
        $data['agama']       = $this->am->getGlobalByJenis('SAGM');
        $data['pasien']      = $this->am->getPasienRingkas($pasien_id);
        $data['statusKawin'] = $this->pm->getStatus();
        $data['pendidikan']  = $this->pm->getPendidikan();

        $data['data_kaji']   = $this->am->getAssessLab($episode_id, $pasien_id);

        $data['episode_id']  = $episode_id;
        $data['pasien_id']   = $pasien_id;
        $data['title']       = 'View Assessment Laboratorium';

        // // TANPA HEADER SIDEBAR FOOTER
        // $this->load->view('asessment/form_assess_lab_view', $data);



        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_lab_view', $data);
        $this->load->view('templates/footer');
    }


    public function editAssesRad($episode_id, $pasien_id)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data = [
            'title'     => 'Edit Assessment Radiologi',
            'script'    => 'js/asessment.js',
            'user'      => $this->um->get_user_by_id($user_id),
            'pasien'    => $this->am->getPasienRingkas($pasien_id),
            'episode_id' => $episode_id,
            'data_kaji' => $this->am->getAssessRad($episode_id),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_rad_edit', $data);
        $this->load->view('templates/footer');
        // return var_dump($data['data_kaji']);
        // die;
    }

    public function editPengkajianAwal($episode_id, $pasien_id, $jml_kunj, $poli)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data = [
            'title'     => 'Edit Pengkajian Awal',
            'script'    => 'js/asessment.js',
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

    public function formAssessmentLab($episode_id, $pasien_id)
    {
        if (!$episode_id || !$pasien_id) show_404();

        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        //  'agama'         => $this->am->getGlobalByJenis('SAGM'),
        $data['agama']   = $this->am->getGlobalByJenis('SAGM');
        $data['pasien']   = $this->am->getPasienRingkas($pasien_id);
        $data['statusKawin'] = $this->pm->getStatus();
        $data['pendidikan'] = $this->pm->getPendidikan();
        $data['episode_id']  = $episode_id;
        $data['title']    = 'Form Asesement Laboratorium';
        $data['script']   = 'js/asessment.js'; // opsional, kalau mau add JS terpisah
        // $jenis = strtolower($this->input->get('jenis') ?? 'mammo'); // mammo|usg

        // return var_dump($data['agama']);
        // die;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_lab', $data);
        $this->load->view('templates/footer');
    }


    public function formAssessmentRad($episode_id, $pasien_id)
    {
        if (!$episode_id || !$pasien_id) show_404();

        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);

        $data['pasien']   = $this->am->getPasienRingkas($pasien_id);
        $data['episode_id']  = $episode_id;
        $data['title']    = 'Form Asesement Radiologi';
        $data['script']   = 'js/asessment.js'; // opsional, kalau mau add JS terpisah
        // $jenis = strtolower($this->input->get('jenis') ?? 'mammo'); // mammo|usg


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_rad', $data);
        $this->load->view('templates/footer');
        // return var_dump();
        // die;
    }

    public function formAssessmentLabRad($episode_id, $pasien_id)
    {
        if (!$episode_id || !$pasien_id) show_404();

        $user_id = $this->session->userdata('user_id_pc');


        $data = [
            'user'      => $this->um->get_user_by_id($user_id),
            'pasien'    => $this->am->getPasienRingkas($pasien_id),
            'episode_id'   => $episode_id,
            'statusKawin'    => $this->pm->getStatus(),
            'pendidikan'    => $this->pm->getPendidikan(),
            'title'     => 'Form Asesment Laboratorium & Radiologi',
            'script'    => 'js/asessment.js',
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_assess_lab_rad', $data);
        $this->load->view('templates/footer');
        // return var_dump();
        // die;
    }


    public function formKajiAwalFullLengkap($episode_id, $pasien_id)
    {
        $user_id = $this->session->userdata('user_id_pc');

        $data = [
            'title'         => 'Pengkajian Awal Pasien Rawat Jalan ',
            'script'         => 'js/asessment.js',
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

    public function formMammoUsg($episode_id, $pasien_id)
    {
        // guard
        if (!$episode_id || !$pasien_id) show_404();

        $jenis = strtolower($this->input->get('jenis') ?? 'mammo'); // mammo|usg
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        // ambil identitas pasien seperlunya
        $data['pasien']   = $this->am->getPasienRingkas($pasien_id); // siapkan model sederhana: nama, umur, telp, alamat, no_rm
        $data['episode']  = $episode_id;
        $data['jenis']    = in_array($jenis, ['mammo', 'usg']) ? $jenis : 'mammo';
        $data['title']    = ($data['jenis'] === 'mammo' ? 'Form Mammografi' : 'Form USG');
        $data['script']   = 'js/asessment.js'; // opsional, kalau mau add JS terpisah


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('asessment/form_mammo_usg', $data);
        $this->load->view('templates/footer');
    }
    public function saveAssessLabxx()
    {
        $post = $this->input->post();
        $user_id = $this->session->userdata('user_id_pc');
        // ============================
        // 1. VALIDASI
        // ============================
        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Episode ID dan Pasien ID tidak ditemukan.');
            redirect('Asessment');
            return;
        }


        // ============================
        // 2. KONVERSI CHECKBOX → STRING
        // ============================

        // KB Riwayat (checkbox)
        $post['fl_kb_riwayat'] = isset($post['fl_kb_riwayat']) ? implode(',', $post['fl_kb_riwayat']) : null;

        // KB Saat Ini (checkbox)
        $post['fl_kb_now'] = isset($post['fl_kb_now']) ? implode(',', $post['fl_kb_now']) : null;

        // Keluarga kena kanker "siapa"
        $post['fl_kel_siapa'] = isset($post['fl_kel_siapa']) ? implode(',', $post['fl_kel_siapa']) : null;

        // IVA negatif anjuran tahun
        $post['fl_iva_berkala'] = isset($post['fl_iva_berkala']) ? implode(',', $post['fl_iva_berkala']) : null;

        // Tindak radang
        $post['fl_tindak_radang'] = isset($post['fl_tindak_radang']) ? implode(',', $post['fl_tindak_radang']) : null;

        // IVA positif tindakan
        $post['fl_tindak_iva'] = isset($post['fl_tindak_iva']) ? implode(',', $post['fl_tindak_iva']) : null;

        // Probe kriotip, antibiotik, keadaan setelah tindakan
        $post['fl_probe_tip']      = isset($post['fl_probe_tip']) ? implode(',', $post['fl_probe_tip']) : null;
        $post['fl_antibiotik']     = isset($post['fl_antibiotik']) ? implode(',', $post['fl_antibiotik']) : null;
        $post['fl_tl_keadaan']     = isset($post['fl_tl_keadaan']) ? implode(',', $post['fl_tl_keadaan']) : null;
        $post['fl_tl_gejala']      = isset($post['fl_tl_gejala']) ? implode(',', $post['fl_tl_gejala']) : null;

        // IMS
        $post['fl_tindak_ims']     = isset($post['fl_tindak_ims']) ? implode(',', $post['fl_tindak_ims']) : null;

        // Tindakan Prosedur Krioterapi
        $post['fl_tl_prosedur']    = isset($post['fl_tl_prosedur']) ? implode(',', $post['fl_tl_prosedur']) : null;

        // Hasil IVA utama (Negatif, Radang, Positif)
        $post['fl_hasil_iva']      = isset($post['fl_hasil_iva']) ? implode(',', $post['fl_hasil_iva']) : null;


        // ============================
        // 3. CREATED_BY
        // ============================
        // $post['created_by'] = $this->session->userdata('username');

        // ============================
        // 4. WHITELIST FIELD
        // ============================
        $allowedFields = [

            // Identitas dasar
            'episode_id', 'pasien_id', 'created_by',
            'suku', 'agama', 'alamat', 'kecamatan', 'kab_kota', 'provinsi',
            'bb', 'tb', 'gol_darah', 'telp', 'nama_suami',

            // Informed consent
            'ic_hubungan', 'ic_nama', 'ic_jk', 'ic_umur', 'ic_saksi', 'ic_petugas', 'ttd_pasien',

            // Riwayat Identitas
            'status_kawin', 'seks_pranikah', 'suami_menikah_ke',
            'edu_klien', 'edu_suami', 'pekerjaan_klien', 'pekerjaan_suami',

            // Reproduksi
            'fl_usia_haid', 'fl_usia_kawin', 'fl_usia_hamil',
            'fl_hpht', 'fl_usia_menopause', 'fl_siklus_haid',
            'fl_jml_lahir', 'fl_cek_gugur', 'fl_jml_gugur',

            // KB
            'fl_kb_status', 'fl_kb_riwayat', 'fl_kb_now',

            // PAP & IVA
            'fl_pap_status', 'fl_pap_thn',
            'fl_iva_status', 'fl_iva_thn',

            // Merokok
            'fl_rokok_pasien', 'fl_rokok_pasien_jml',
            'fl_rokok_suami', 'fl_rokok_suami_jml',
            'fl_rokok_rumah', 'fl_rokok_rumah_jml',

            // Riwayat kanker
            'fl_kel_kanker', 'fl_kel_siapa', 'fl_kel_jenis',
            'fl_klien_kanker', 'fl_klien_jenis',

            // Keluhan
            'fl_kel_cairan', 'fl_lama_cairan',
            'fl_kel_nyeri', 'fl_lama_nyeri',
            'fl_kel_senggama', 'fl_lama_senggama',
            'fl_kel_nonhaid', 'fl_lama_nonhaid',
            'fl_kel_lain_nama', 'fl_kel_lain', 'fl_lama_lain',

            // Pemeriksaan Ginekologi
            'fl_kel_vulva', 'fl_kel_vagina', 'fl_curiga_klr', 'fl_pem_ssk',
            'fl_ambil_pap', 'fl_tgl_pap', 'fl_hasil_pap',
            'fl_tes_hpv', 'fl_tgl_hpv', 'fl_hasil_hpv',
            'fl_foto_doiva', 'fl_kode_file',

            // Hasil IVA
            'fl_hasil_iva', 'fl_iva_berkala',
            'fl_radang_grade', 'fl_tindak_radang',
            'fl_tindak_iva',

            // Diagram serviks base64
            'fl_gambar_serviks',

            // Tambahkan di bagian Pemeriksaan Ginekologi
            'fl_temuan_lain',

            // IMS & Bimanual
            'fl_dugaan_ims', 'fl_tindak_ims', 'fl_pem_bimanual',

            // Pemeriksaan
            'fl_tgl_periksa', 'fl_lokasi_periksa', 'fl_pemeriksa_nama',

            // Tindakan lanjut
            'fl_tl_prosedur', 'tgl_krio', 'loc_krio', 'dr_krio',
            'fl_probe_tip', 'fl_antibiotik', 'obat_lain',
            'fl_tl_keadaan', 'fl_tl_gejala', 'tgl_kontrol'
        ];

        // ============================
        // 5. FILTER DATA
        // ============================
        $dataInsert = [];
        foreach ($allowedFields as $f) {
            if (array_key_exists($f, $post)) {
                $dataInsert[$f] = ($post[$f] === "") ? null : $post[$f];
            }
        }

        // ============================
        // 6. INSERT DATABASE
        // ============================
        $simpan = $this->db->insert('pcare_manager.pc01_med_ases_laboratorium', $dataInsert);

        if ($simpan) {


            $episode_id = $post['episode_id'];
            $pasien_id  = $post['pasien_id'];

            // =========================================================
            // 1️⃣ UPDATE done_status ke pc01_med_prwt_tr
            // =========================================================
            $this->am->updateDoneStatus($episode_id, $pasien_id);




            // =========================================================
            // 2️⃣ CEK DATA LAB DI pc01_co_lab_dt (HANYA 1 BARIS)
            // =========================================================
            $query = $this->db->query("SELECT a.trans_co, a.trans_id, a.trans_bayar_id, b.rekanan_id,b.poli_id,b.dokter_id
                            FROM pc01_co_lab_dt a
                            join pc01_keu_episode b on a.episode_id =b.episode_id 
                            WHERE a.episode_id = ?
                            AND a.pasien_id  = ?
                            AND a.show_item = '1'
                            ORDER BY a.created_date ASC
                            LIMIT 1
                        ", [$episode_id, $pasien_id]);

            $row = $query->row();

            if ($row) {

                // ============================
                //  Buat 1 record worklist
                // ============================
                $insertWorklist = [
                    'lokasi_id'      => '001',
                    'episode_id'     => $episode_id,
                    'pasien_id'      => $pasien_id,
                    'trans_id'       => $row->trans_id,
                    'trans_co'       => $row->trans_co,
                    // 'trans_bayar_id' => $row->trans_bayar_id,
                    'rekanan_id'     => $row->rekanan_id,
                    'poli_id'        => $row->poli_id,
                    'dokter_id'      => $row->dokter_id,
                    'tanggal'        => date('Y-m-d'),
                    'status'         => '0',
                    'aktif'          => '1',
                    'created_by'     => $user_id,
                    'created_date'   => date('Y-m-d H:i:s')
                ];

                $this->am->insertWorklistLab($insertWorklist);
            }

            $this->session->set_flashdata('success', 'Data Asesmen Laboratorium berhasil disimpan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data Asesmen Laboratorium.');
        }

        redirect('Asessment');



        // return var_dump($post);
        // die;
    }


    public function saveAssessLab()
    {
        $post = $this->input->post();
        $user_id = $this->session->userdata('user_id_pc');

        // ============================
        // 1. VALIDASI
        // ============================
        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Episode ID dan Pasien ID tidak ditemukan.');
            redirect('Asessment');
            return;
        }

        $episode_id = $post['episode_id'];
        $pasien_id  = $post['pasien_id'];

        // ============================
        // 2. CHECKBOX → STRING
        // ============================
        $fieldsArray = [
            'fl_kb_riwayat', 'fl_kb_now', 'fl_kel_siapa',
            'fl_iva_berkala', 'fl_tindak_radang', 'fl_tindak_iva',
            'fl_probe_tip', 'fl_antibiotik', 'fl_tl_keadaan',
            'fl_tl_gejala', 'fl_tindak_ims', 'fl_tl_prosedur',
            'fl_hasil_iva'
        ];

        foreach ($fieldsArray as $f) {
            $post[$f] = isset($post[$f]) ? implode(',', $post[$f]) : null;
        }

        // ============================
        // 3. WHITELIST
        // ============================
        $allowedFields = [
            'episode_id', 'pasien_id',
            'suku', 'agama', 'alamat', 'kecamatan', 'kab_kota', 'provinsi',
            'bb', 'tb', 'gol_darah', 'telp', 'nama_suami',

            'ic_hubungan', 'ic_nama', 'ic_jk', 'ic_umur', 'ic_saksi', 'ic_petugas', 'ttd_pasien',

            'status_kawin', 'seks_pranikah', 'suami_menikah_ke',
            'edu_klien', 'edu_suami', 'pekerjaan_klien', 'pekerjaan_suami',

            'fl_usia_haid', 'fl_usia_kawin', 'fl_usia_hamil',
            'fl_hpht', 'fl_usia_menopause', 'fl_siklus_haid',
            'fl_jml_lahir', 'fl_cek_gugur', 'fl_jml_gugur',

            'fl_kb_status', 'fl_kb_riwayat', 'fl_kb_now',

            'fl_pap_status', 'fl_pap_thn',
            'fl_iva_status', 'fl_iva_thn',

            'fl_rokok_pasien', 'fl_rokok_pasien_jml',
            'fl_rokok_suami', 'fl_rokok_suami_jml',
            'fl_rokok_rumah', 'fl_rokok_rumah_jml',

            'fl_kel_kanker', 'fl_kel_siapa', 'fl_kel_jenis',
            'fl_klien_kanker', 'fl_klien_jenis',

            'fl_kel_cairan', 'fl_lama_cairan',
            'fl_kel_nyeri', 'fl_lama_nyeri',
            'fl_kel_senggama', 'fl_lama_senggama',
            'fl_kel_nonhaid', 'fl_lama_nonhaid',
            'fl_kel_lain_nama', 'fl_kel_lain', 'fl_lama_lain',

            'fl_kel_vulva', 'fl_kel_vagina', 'fl_curiga_klr', 'fl_pem_ssk',
            'fl_ambil_pap', 'fl_tgl_pap', 'fl_hasil_pap',
            'fl_tes_hpv', 'fl_tgl_hpv', 'fl_hasil_hpv',
            'fl_foto_doiva', 'fl_kode_file',

            'fl_hasil_iva', 'fl_iva_berkala',
            'fl_radang_grade', 'fl_tindak_radang',
            'fl_tindak_iva',

            'fl_gambar_serviks',
            'fl_temuan_lain',

            'fl_dugaan_ims', 'fl_tindak_ims', 'fl_pem_bimanual',

            'fl_tgl_periksa', 'fl_lokasi_periksa', 'fl_pemeriksa_nama',

            'fl_tl_prosedur', 'tgl_krio', 'loc_krio', 'dr_krio',
            'fl_probe_tip', 'fl_antibiotik', 'obat_lain',
            'fl_tl_keadaan', 'fl_tl_gejala', 'tgl_kontrol'
        ];

        // ============================
        // 4. FILTER
        // ============================
        $dataInsert = [];
        foreach ($allowedFields as $f) {
            if (array_key_exists($f, $post)) {
                $dataInsert[$f] = ($post[$f] === "") ? null : $post[$f];
            }
        }

        // Metadata
        $dataInsert['aktif']        = '1';
        $dataInsert['created_by']   = $user_id;
        $dataInsert['created_at'] = date('Y-m-d H:i:s');

        // ============================
        // 🔥 TRANSACTION
        // ============================
        $this->db->trans_start();

        // 5. NONAKTIFKAN DATA LAMA
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where('aktif', '1');
        $this->db->update('pcare_manager.pc01_med_ases_laboratorium', [
            'aktif' => '0',
            'last_updated_by'   => $user_id,
            'last_updated_date' => date('Y-m-d H:i:s')
        ]);

        // 6. INSERT BARU
        $this->db->insert('pcare_manager.pc01_med_ases_laboratorium', $dataInsert);

        // ============================
        // WORKLIST (TETAP)
        // ============================
        $query = $this->db->query("
                SELECT a.trans_co, a.trans_id, b.rekanan_id,b.poli_id,b.dokter_id
                FROM pc01_co_lab_dt a
                JOIN pc01_keu_episode b ON a.episode_id = b.episode_id
                WHERE a.episode_id = ?
                AND a.pasien_id = ?
                AND a.show_item = '1'
                ORDER BY a.created_date ASC
                LIMIT 1
            ", [$episode_id, $pasien_id]);

        $row = $query->row();

        if ($row) {
            $insertWorklist = [
                'lokasi_id'    => '001',
                'episode_id'   => $episode_id,
                'pasien_id'    => $pasien_id,
                'trans_id'     => $row->trans_id,
                'trans_co'     => $row->trans_co,
                'rekanan_id'   => $row->rekanan_id,
                'poli_id'      => $row->poli_id,
                'dokter_id'    => $row->dokter_id,
                'tanggal'      => date('Y-m-d'),
                'status'       => '0',
                'aktif'        => '1',
                'created_by'   => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            ];

            $this->am->insertWorklistLab($insertWorklist);
        }

        $this->db->trans_complete();

        // ============================
        // RESULT
        // ============================
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        } else {
            $this->am->updateDoneStatus($episode_id, $pasien_id);
            $this->session->set_flashdata('success', 'Data Asesmen Laboratorium berhasil disimpan.');
        }

        redirect('Asessment');
    }

    public function saveAssessRad()
    {
        $post = $this->input->post();
        $user_id = $this->session->userdata('user_id_pc');

        // 1. Validasi ID Wajib
        if (empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Episode ID dan Pasien ID tidak ditemukan.');
            redirect('Asessment'); // Sesuaikan redirect
            return;
        }

        // 2. PROSES ARRAY → STRING (Implode Checkbox)
        // Menggabungkan checkbox keluhan_payudara menjadi satu string koma
        $post['keluhan_payudara'] = isset($post['keluhan_payudara']) ? implode(',', $post['keluhan_payudara']) : null;

        // Menggabungkan checkbox tindakan (Informed Consent) menjadi satu string koma
        $post['tindakan'] = isset($post['tindakan']) ? implode(',', $post['tindakan']) : null;

        // 3. SET SESSION CREATED_BY (Jika ada library auth)
        // $post['created_by'] = $this->session->userdata('username');

        // 4. WHITELIST FIELD (Sesuaikan dengan kolom tabel SQL di atas)
        $allowedFields = [
            'episode_id', 'pasien_id', 'created_by',
            'tgl_pemeriksaan', 'lokasi',
            'nama_suami',
            //'riwayat_kesehatan',
            'haid_pertama', 'jumlah_anak',
            'jenis_kb', 'lama_kb',
            'tb', 'bb',
            'merokok_pasien', 'merokok_pasien_jml',
            'merokok_suami', 'merokok_suami_jml',
            'riwayat_kanker_keluarga', 'keluarga_sakit', 'keluarga_sakit_siapa', 'jenis_kanker',
            'keluhan_payudara', 'keluhan_lain',
            'tindakan', 'tindakan_lain',
            'saksi', 'pasien_ttd',
            'hasil_pemeriksaan'
        ];

        // 5. FILTER DATA
        $dataInsert = [];
        foreach ($allowedFields as $field) {
            // Cek key exists agar tidak error undefined index, izinkan null/kosong
            if (array_key_exists($field, $post)) {
                // Ubah string kosong "" menjadi NULL agar bersih di database
                $dataInsert[$field] = ($post[$field] === "") ? null : $post[$field];
            }
        }

        // Tambahan field wajib
        $dataInsert['aktif']        = '1';
        $dataInsert['created_by']   = $user_id;
        $dataInsert['created_at'] = date('Y-m-d H:i:s');

        $episode_id = $post['episode_id'];
        $pasien_id  = $post['pasien_id'];


        // =========================================================
        // 🔥 TRANSACTION START
        // =========================================================
        $this->db->trans_start();


        // 5. NONAKTIFKAN DATA LAMA
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);
        $this->db->where('aktif', '1');
        $this->db->update('pcare_manager.pc01_med_ases_radiologi', [
            'aktif' => '0',
            'last_updated_by'   => $user_id,
            'last_updated_date' => date('Y-m-d H:i:s')
        ]);


        // 6. INSERT DATA BARU
        $this->db->insert('pcare_manager.pc01_med_ases_radiologi', $dataInsert);


        // =========================================================
        // 7. WORKLIST (TETAP)
        // =========================================================
        $query = $this->db->query("
                                SELECT a.trans_co, a.trans_id, b.rekanan_id, b.poli_id, b.dokter_id
                                FROM pc01_co_rad_dt a
                                JOIN pc01_keu_episode b ON a.episode_id = b.episode_id
                                WHERE a.episode_id = ?
                                AND a.pasien_id = ?
                                AND a.show_item = '1'
                                ORDER BY a.created_date ASC
                                LIMIT 1
                            ", [$episode_id, $pasien_id]);

        $row = $query->row();



        if ($row) {
            $insertWorklist = [
                'lokasi_id'    => '001',
                'episode_id'   => $episode_id,
                'pasien_id'    => $pasien_id,
                'trans_id'     => $row->trans_id,
                'trans_co'     => $row->trans_co,
                'rekanan_id'   => $row->rekanan_id,
                'poli_id'      => $row->poli_id,
                'dokter_id'    => $row->dokter_id,
                'tanggal'      => date('Y-m-d'),
                'status'       => '0',
                'aktif'        => '1',
                'created_by'   => $user_id,
                'created_date' => date('Y-m-d H:i:s')
            ];

            $this->am->insertWorklistRad($insertWorklist);
        }

        // =========================================================
        // 🔥 TRANSACTION END
        // =========================================================

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        } else {
            $this->am->updateDoneStatus($episode_id, $pasien_id);
            $this->session->set_flashdata('success', 'Data Asesmen Radiologi berhasil disimpan.');
        }

        redirect('Asessment');



        // 6. MODEL INSERT
        // Pastikan Anda membuat function insertAsesmenRadiologi di AsesmenModel
        // $simpan = $this->am->insertAsesmenRadiologi($dataInsert);

        // Contoh simulasi insert menggunakan Query Builder CodeIgniter langsung (jika belum ada model khusus)
        // $simpan = $this->db->insert('pcare_manager.pc01_med_ases_radiologi', $dataInsert);

        // if ($simpan) {

        // $episode_id = $post['episode_id'];
        // $pasien_id  = $post['pasien_id'];

        // =========================================================
        // 🔥 TRANSACTION START
        // =========================================================
        // $this->db->trans_start();


        // // 5. NONAKTIFKAN DATA LAMA
        // $this->db->where('episode_id', $episode_id);
        // $this->db->where('pasien_id', $pasien_id);
        // $this->db->where('aktif', '1');
        // $this->db->update('pcare_manager.pc01_med_ases_radiologi', [
        //     'aktif' => '0',
        //     'last_updated_by'   => $user_id,
        //     'last_updated_date' => date('Y-m-d H:i:s')
        // ]);




        // $this->am->updateDoneStatus($episode_id, $pasien_id);
        // UPDATE status pemeriksaan jadi selesai (01)
        // $this->db->where('episode_id', $post['episode_id']);
        // $this->db->where('pasien_id', $post['pasien_id']);
        // $this->db->where('aktif', '1');
        // $this->db->update('pcare_manager.pc01_med_prwt_tr', [
        //     'done_status' => '01'
        // ]);



        // =========================================================
        // 2?? CEK DATA RAD DI pc01_co_rad_dt (HANYA 1 BARIS)
        // =========================================================
        // $query = $this->db->query("SELECT a.trans_co, a.trans_id, a.trans_bayar_id, b.rekanan_id,b.poli_id,b.dokter_id
        //                     FROM pc01_co_rad_dt a
        //                     join pc01_keu_episode b on a.episode_id =b.episode_id 
        //                     WHERE a.episode_id = ?
        //                     AND a.pasien_id  = ?
        //                     AND a.show_item = '1'
        //                     ORDER BY a.created_date ASC
        //                     LIMIT 1
        //                 ", [$episode_id, $pasien_id]);

        // $row = $query->row();

        // if ($row) {
        // ============================
        //  Buat 1 record worklist
        // ============================
        // $insertWorklist = [
        //     'lokasi_id'      => '001',
        //     'episode_id'     => $episode_id,
        //     'pasien_id'      => $pasien_id,
        //     'trans_id'       => $row->trans_id,
        //     'trans_co'       => $row->trans_co,
        //     // 'trans_bayar_id' => $row->trans_bayar_id,
        //     'rekanan_id'     => $row->rekanan_id,
        //     'poli_id'        => $row->poli_id,
        //     'dokter_id'      => $row->dokter_id,
        //     'tanggal'        => date('Y-m-d'),
        //     'status'         => '0',
        //     'aktif'          => '1',
        //     'created_by'     => $user_id,
        //     'created_date'   => date('Y-m-d H:i:s')
        // ];

        // $this->am->insertWorklistRad($insertWorklist);
        // }








        // $this->session->set_flashdata('success', 'Data Asesmen Radiologi berhasil disimpan.');
        // } else {
        // Cek error DB jika perlu: $error = $this->db->error();
        // $this->session->set_flashdata('error', 'Gagal menyimpan data Asesmen Radiologi.');
        // }

        // redirect('Asessment'); // Sesuaikan arah redirect


        // return var_dump($post);
        // die;
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
        $post = $this->input->post();

        // 1. Validasi Kunci Utama
        if (empty($post['form_id']) || empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Gagal Update: ID Form, Episode, atau Pasien tidak ditemukan.');
            redirect('Asessment');
            return;
        }

        // 2. Convert Checkbox ARRAY → STRING (implode)
        $checkboxFields = [
            'fl_kb_riwayat',
            'fl_kb_now',
            'fl_kel_siapa',
            'fl_iva_berkala',
            'fl_tindak_radang',
            'fl_tindak_iva',
            'fl_tindak_ims',
            'fl_probe_tip',
            'fl_antibiotik',
            'fl_tl_prosedur',
            'fl_tl_keadaan',
            'fl_tl_gejala',
            'fl_hasil_iva',     // Negatif | Radang | Positif
        ];

        foreach ($checkboxFields as $cb) {
            $post[$cb] = isset($post[$cb]) ? implode(",", $post[$cb]) : null;
        }

        // 3. Tentukan field yang boleh diupdate (WHITELIST)
        $allowedFields = [
            'ic_hubungan', 'ic_nama', 'ic_jk', 'ic_umur', 'ic_saksi', 'ic_petugas',
            'suku', 'agama', 'alamat', 'kecamatan', 'kab_kota', 'provinsi',
            'bb', 'tb', 'gol_darah', 'telp', 'nama_suami', 'status_kawin',
            'seks_pranikah', 'suami_menikah_ke', 'edu_klien', 'edu_suami',
            'pekerjaan_klien', 'pekerjaan_suami',

            'fl_usia_haid', 'fl_usia_kawin', 'fl_usia_hamil', 'fl_hpht',
            'fl_usia_menopause', 'fl_siklus_haid', 'fl_jml_lahir',
            'fl_cek_gugur', 'fl_jml_gugur', 'fl_kb_status', 'fl_kb_riwayat', 'fl_kb_now',

            'fl_pap_status', 'fl_pap_thn', 'fl_iva_status', 'fl_iva_thn',

            'fl_rokok_pasien', 'fl_rokok_pasien_jml',
            'fl_rokok_suami', 'fl_rokok_suami_jml',
            'fl_rokok_rumah', 'fl_rokok_rumah_jml',

            'fl_kel_kanker', 'fl_kel_siapa', 'fl_kel_jenis',
            'fl_klien_kanker', 'fl_klien_jenis',

            'fl_kel_cairan', 'fl_lama_cairan',
            'fl_kel_nyeri', 'fl_lama_nyeri',
            'fl_kel_senggama', 'fl_lama_senggama',
            'fl_kel_nonhaid', 'fl_lama_nonhaid',
            'fl_kel_lain', 'fl_kel_lain_nama', 'fl_lama_lain',

            'fl_kel_vulva', 'fl_kel_vagina', 'fl_curiga_klr', 'fl_pem_ssk',

            'fl_ambil_pap', 'fl_tgl_pap', 'fl_hasil_pap',
            'fl_tes_hpv', 'fl_tgl_hpv', 'fl_hasil_hpv',
            'fl_foto_doiva', 'fl_kode_file',

            'fl_hasil_iva', 'fl_iva_berkala', 'fl_radang_grade',
            'fl_tindak_radang', 'fl_tindak_iva',
            'fl_dugaan_ims', 'fl_tindak_ims',

            'fl_pem_bimanual',

            'fl_gambar_serviks',

            'fl_tl_prosedur', 'tgl_krio', 'loc_krio', 'dr_krio',
            'fl_probe_tip', 'fl_antibiotik', 'obat_lain',
            'fl_tl_keadaan', 'fl_tl_gejala', 'tgl_kontrol',

            'fl_lokasi_periksa', 'fl_pemeriksa_nama', 'fl_tgl_periksa',
        ];

        // 4. Susun data update
        $dataUpdate = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $post)) {

                // Kosong = NULL
                if ($post[$field] === "" || $post[$field] === []) {
                    $dataUpdate[$field] = null;
                } else {
                    $dataUpdate[$field] = $post[$field];
                }
            }
        }

        // 5. Eksekusi UPDATE
        $this->db->where('form_id', $post['form_id']);
        $this->db->where('episode_id', $post['episode_id']);

        $update = $this->db->update('pcare_manager.pc01_med_ases_laboratorium', $dataUpdate);

        // 6. Feedback
        if ($update) {
            $this->session->set_flashdata('success', 'Data Assessment Laboratorium berhasil diperbarui.');
            redirect('Asessment');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data. Coba lagi.');
            redirect('AsessmentController/editAssesLab/' . $post['episode_id'] . '/' . $post['pasien_id']);
        }
    }



    public function updateAssessLab()
    {
        $post = $this->input->post();
        $user_id = $this->session->userdata('user_id_pc');

        // 1. Validasi Kunci Utama
        if (empty($post['form_id']) || empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Gagal Update: ID Form, Episode, atau Pasien tidak ditemukan.');
            redirect('Asessment');
            return;
        }

        // 2. Convert Checkbox ARRAY → STRING (implode)
        $checkboxFields = [
            'fl_kb_riwayat',
            'fl_kb_now',
            'fl_kel_siapa',
            'fl_iva_berkala',
            'fl_tindak_radang',
            'fl_tindak_iva',
            'fl_tindak_ims',
            'fl_probe_tip',
            'fl_antibiotik',
            'fl_tl_prosedur',
            'fl_tl_keadaan',
            'fl_tl_gejala',
            'fl_hasil_iva',     // Negatif | Radang | Positif
        ];

        foreach ($checkboxFields as $cb) {
            $post[$cb] = isset($post[$cb]) ? implode(",", $post[$cb]) : null;
        }

        // 3. Tentukan field yang boleh diupdate (WHITELIST)
        $allowedFields = [
            'ic_hubungan', 'ic_nama', 'ic_jk', 'ic_umur', 'ic_saksi', 'ic_petugas',
            'suku', 'agama', 'alamat', 'kecamatan', 'kab_kota', 'provinsi',
            'bb', 'tb', 'gol_darah', 'telp', 'nama_suami', 'status_kawin',
            'seks_pranikah', 'suami_menikah_ke', 'edu_klien', 'edu_suami',
            'pekerjaan_klien', 'pekerjaan_suami',

            'fl_usia_haid', 'fl_usia_kawin', 'fl_usia_hamil', 'fl_hpht',
            'fl_usia_menopause', 'fl_siklus_haid', 'fl_jml_lahir',
            'fl_cek_gugur', 'fl_jml_gugur', 'fl_kb_status', 'fl_kb_riwayat', 'fl_kb_now',

            'fl_pap_status', 'fl_pap_thn', 'fl_iva_status', 'fl_iva_thn',

            'fl_rokok_pasien', 'fl_rokok_pasien_jml',
            'fl_rokok_suami', 'fl_rokok_suami_jml',
            'fl_rokok_rumah', 'fl_rokok_rumah_jml',

            'fl_kel_kanker', 'fl_kel_siapa', 'fl_kel_jenis',
            'fl_klien_kanker', 'fl_klien_jenis',

            'fl_kel_cairan', 'fl_lama_cairan',
            'fl_kel_nyeri', 'fl_lama_nyeri',
            'fl_kel_senggama', 'fl_lama_senggama',
            'fl_kel_nonhaid', 'fl_lama_nonhaid',
            'fl_kel_lain', 'fl_kel_lain_nama', 'fl_lama_lain',

            'fl_kel_vulva', 'fl_kel_vagina', 'fl_curiga_klr', 'fl_pem_ssk',

            'fl_ambil_pap', 'fl_tgl_pap', 'fl_hasil_pap',
            'fl_tes_hpv', 'fl_tgl_hpv', 'fl_hasil_hpv',
            'fl_foto_doiva', 'fl_kode_file',

            'fl_hasil_iva', 'fl_iva_berkala', 'fl_radang_grade',
            'fl_tindak_radang', 'fl_tindak_iva',
            'fl_dugaan_ims', 'fl_tindak_ims',

            'fl_pem_bimanual',

            'fl_gambar_serviks',

            'fl_tl_prosedur', 'tgl_krio', 'loc_krio', 'dr_krio',
            'fl_probe_tip', 'fl_antibiotik', 'obat_lain',
            'fl_tl_keadaan', 'fl_tl_gejala', 'tgl_kontrol',

            'fl_lokasi_periksa', 'fl_pemeriksa_nama', 'fl_tgl_periksa',
        ];

        // 4. Susun data update
        $dataUpdate = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $post)) {

                // Kosong = NULL
                if ($post[$field] === "" || $post[$field] === []) {
                    $dataUpdate[$field] = null;
                } else {
                    $dataUpdate[$field] = $post[$field];
                }
            }
        }


        // metadata update
        $dataUpdate['last_updated_by']   = $user_id;
        $dataUpdate['last_updated_date'] = date('Y-m-d H:i:s');


        // 5. Eksekusi UPDATE
        $this->db->where('form_id', $post['form_id']);
        $this->db->where('episode_id', $post['episode_id']);
        $this->db->where('pasien_id', $post['pasien_id']);

        $update = $this->db->update('pcare_manager.pc01_med_ases_laboratorium', $dataUpdate);

        // 6. Feedback
        if ($update) {
            $this->session->set_flashdata('success', 'Data Assessment Laboratorium berhasil diperbarui.');
            redirect('Asessment');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data. Coba lagi.');
            redirect('AsessmentController/editAssesLab/' . $post['episode_id'] . '/' . $post['pasien_id']);
        }
    }



    public function updateAssessRad()
    {
        $post = $this->input->post();
        $user_id = $this->session->userdata('user_id_pc');


        // 1. VALIDASI
        if (empty($post['form_id']) || empty($post['episode_id']) || empty($post['pasien_id'])) {
            $this->session->set_flashdata('error', 'Gagal Update: ID tidak lengkap.');
            redirect('Asessment');
            return;
        }

        $episode_id = $post['episode_id'];
        $pasien_id  = $post['pasien_id'];

        // 2. ARRAY → STRING
        $post['keluhan_payudara'] = isset($post['keluhan_payudara']) ? implode(',', $post['keluhan_payudara']) : null;
        $post['tindakan']         = isset($post['tindakan']) ? implode(',', $post['tindakan']) : null;



        // 3. WHITELIST
        $allowedFields = [
            'tgl_pemeriksaan', 'lokasi',
            'nama_suami',
            'haid_pertama', 'jumlah_anak',
            'jenis_kb', 'lama_kb',
            'tb', 'bb',
            'merokok_pasien', 'merokok_pasien_jml',
            'merokok_suami', 'merokok_suami_jml',
            'riwayat_kanker_keluarga',
            'keluarga_sakit', 'keluarga_sakit_siapa', 'jenis_kanker',
            'keluhan_payudara', 'keluhan_lain',
            'tindakan', 'tindakan_lain',
            'saksi', 'pasien_ttd',
            'hasil_pemeriksaan'
        ];

        // // 4. FILTER DATA
        // $dataInsert = [];
        // foreach ($allowedFields as $field) {
        //     if (array_key_exists($field, $post)) {
        //         $dataInsert[$field] = ($post[$field] === "") ? null : $post[$field];
        //     }
        // }

        // 4. FILTER DATA
        $dataUpdate = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $post)) {
                $dataUpdate[$field] = ($post[$field] === "") ? null : $post[$field];
            }
        }


        // metadata update
        $dataUpdate['last_updated_by']   = $user_id;
        $dataUpdate['last_updated_date'] = date('Y-m-d H:i:s');


        // =========================================================
        // 🔥 UPDATE LANGSUNG
        // =========================================================
        $this->db->where('form_id', $post['form_id']);
        $this->db->where('episode_id', $episode_id);
        $this->db->where('pasien_id', $pasien_id);

        $update = $this->db->update('pcare_manager.pc01_med_ases_radiologi', $dataUpdate);

        // =========================================================
        // RESULT
        // =========================================================
        if ($update) {
            $this->session->set_flashdata('success', 'Data Assessment berhasil diperbarui.');
            redirect('Asessment');
        } else {
            $this->session->set_flashdata('error', 'Gagal update data.');
            redirect('AsessmentController/editAssesRad/' . $episode_id . '/' . $pasien_id);
        }

        // Tambahan metadata
        // $dataInsert['episode_id']   = $episode_id;
        // $dataInsert['pasien_id']    = $pasien_id;
        // $dataInsert['aktif']        = '1';
        // $dataInsert['created_by']   = $user_id;
        // $dataInsert['created_at'] = date('Y-m-d H:i:s');

        // =========================================================
        // 🔥 TRANSACTION
        // // =========================================================
        // $this->db->trans_start();

        // // 5. NONAKTIFKAN DATA LAMA
        // $this->db->where('form_id', $post['form_id']);
        // $this->db->update('pcare_manager.pc01_med_ases_radiologi', [
        //     'aktif' => '0',
        //     'last_updated_by'   => $user_id,
        //     'last_updated_date' => date('Y-m-d H:i:s')
        // ]);



        // 6. INSERT DATA BARU (VERSI BARU)
        // $this->db->insert('pcare_manager.pc01_med_ases_radiologi', $dataInsert);


        // =========================================================
        // END TRANSACTION
        // =========================================================
        // $this->db->trans_complete();


        // if ($this->db->trans_status() === FALSE) {
        //     $this->session->set_flashdata('error', 'Gagal update data.');
        //     redirect('AsessmentController/editAssesRad/' . $episode_id . '/' . $pasien_id);
        // } else {
        //     $this->session->set_flashdata('success', 'Data Assessment berhasil diperbarui.');
        //     redirect('Asessment');
        // }
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
            'script'      => 'js/asessment.js', // ini yang kurang
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
