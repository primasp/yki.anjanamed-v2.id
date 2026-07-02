<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\Builder\Builder;

// Sesuaokan mulai chat GPT dari bagian ini Siap. Kita rapikan skemanya supaya:
// Isinya saaya minta di buat masternya dari DB global MS dan struktur form di ambil dari DB selanjutnya saya minta sesuaikan view form kembali
class RadController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('RadModel', 'rm');
        $this->load->model('PasienModel', 'pm');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user']   = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/rad.js';
        $data['title']  = 'Worklist Radiologi';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('radiologi/index', $data);
        $this->load->view('templates/footer');
    }

    public function worklist_filterData()
    {


        if (!$this->input->is_ajax_request()) show_404();

        $status  = $this->input->post('status', true) ?? '0';
        $keyword = $this->input->post('keyword', true) ?? '';
        $tanggal = $this->input->post('tanggal', true);
        $semua_tanggal  = $this->input->post('semua_tanggal', true);

        if ($status === null || $status === '') $status = '0';
        $semua_tanggal = ($semua_tanggal === '1');
        // if (empty($tanggal)) $tanggal = date('Y-m-d');


        $rows = $this->rm->list_by_status((string)$status, (string)$keyword, $tanggal, $semua_tanggal);

        // return var_dump($rows);
        // die;
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }


    public function worklist_counts()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $out = $this->rm->counts();
        return $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    public function worklist_detail()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('id', true);


        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        $w = $this->rm->get_one($id);

        // return var_dump($w);
        // die;

        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        $orders = $this->rm->get_orders_for_worklist($w); // daftar tindakan radiologi


        $files  = $this->rm->get_result_files($w);        // daftar file hasil

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'worklist' => $w,
                'orders' => $orders,
                'files' => $files
            ]));
    }

    public function updateNote()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $fileId = $this->input->post('file_id', true);
        $catatan = $this->input->post('catatan', true);
        $user = $this->session->userdata('user_id_pc');

        if (!$fileId) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ID file tidak valid'
                ]));
        }

        if (!$catatan) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Catatan tidak boleh kosong'
                ]));
        }

        $res = $this->rm->update_note($fileId, $catatan, $user);

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }

    public function worklist_updateStatus()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id     = $this->input->post('id', true);
        $status = $this->input->post('status', true);
        // $user   = (string)($this->session->userdata('username_pc') ?? 'system');
        $user = $this->session->userdata('user_id_pc');

        if (!$id || $status === null) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']));
        }

        $out = $this->rm->update_status($id, (string)$status, $user);

        return $this->output->set_content_type('application/json')->set_output(json_encode($out));
        // return var_dump($user);
        // die;
    }

    public function worklist_uploadResult()
    {

        if (!$this->input->is_ajax_request()) show_404();

        $id   = $this->input->post('id', true);
        $user = $this->session->userdata('user_id_pc');

        // ✅ ambil catatan dari form
        $catatan = $this->input->post('catatan_file', true);

        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        if (!$catatan) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Catatan wajib diisi']));
        }


        // Ambil 1 row worklist
        $w = $this->rm->get_one($id);
        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        // Validasi file input
        if (
            !isset($_FILES['files']) ||
            empty($_FILES['files']['name']) ||
            $_FILES['files']['error'][0] === UPLOAD_ERR_NO_FILE
        ) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Tidak ada file yang diupload'
                ]));
        }

        // folder: assets/uploads/radiology/YYYYMM/
        $ym = date('Ym');
        $baseDir = FCPATH . "assets/upload/";
        $radDir  = $baseDir . "radiology/";
        $ymDir   = $radDir . $ym . "/";

        foreach ([$baseDir, $radDir, $ymDir] as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0775, true)) {
                    return $this->output->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Gagal menyiapkan folder upload radiologi'
                        ]));
                }
            }
        }

        $uploadDir = $ymDir;


        // $uploadDir = FCPATH . "assets/upload/radiology/{$ym}/";

        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        $maxMB = 20;

        $files = $_FILES['files'];
        $saved = [];
        $fileCount  = 0; // total file aktif setelah upload (untuk konfirmasi kirim ke dokter)


        for ($i = 0; $i < count($files['name']); $i++) {
            // if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                // bisa di-skip atau kalau mau kembalikan error khusus
                continue;
            }

            $tmp  = $files['tmp_name'][$i];
            $name = $files['name'][$i];
            $type = $files['type'][$i];
            $size = (int)$files['size'][$i];

            if (!in_array($type, $allowed, true)) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => "Tipe file tidak diizinkan: {$type}"]));
            }

            if ($size > ($maxMB * 1024 * 1024)) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => "Ukuran file melebihi {$maxMB}MB"]));
            }

            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $safeBase = preg_replace('/[^a-zA-Z0-9\-_]+/', '_', pathinfo($name, PATHINFO_FILENAME));

            $newName = "RAD_{$w['episode_id']}_{$w['trans_id']}_" . date('YmdHis') . "_{$i}." . strtolower($ext);
            $dest = $uploadDir . $newName;


            if (!move_uploaded_file($tmp, $dest)) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Gagal menyimpan file upload']));
            }

            $publicPath = "assets/upload/radiology/{$ym}/{$newName}";


            // === Simpan meta file ke DB melalui model ===
            // Versi yang kita buat: save_result_file() mengembalikan ['success'=>bool,'file_count'=>int]
            // ✅ simpan ke DB + catatan
            $resSave = $this->rm->save_result_file($w, [
                'file_name' => $name,
                'file_path' => $publicPath,
                'mime_type' => $type,
                'file_size' => $size,
                'catatan'   => $catatan // 🔥 INI YANG DITAMBAHKAN
            ], $user);

            if (!$resSave['success']) {
                // kalau mau lebih strict, bisa sekalian hapus file fisik yg barusan diupload
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => $resSave['message'] ?? 'Gagal menyimpan metadata hasil radiologi'
                    ]));
            }

            // ambil file_count terkini dari hasil model
            $fileCount = (int)($resSave['file_count'] ?? $fileCount);

            // $this->rm->save_result_file($w, [
            //     'file_name' => $name,
            //     'file_path' => $publicPath,
            //     'mime_type' => $type,
            //     'file_size' => $size,
            // ], $user);


            $saved[] = [
                'file_name' => $name,
                'file_path' => base_url($publicPath),
                'mime_type' => $type,
                'file_size' => $size,
                'catatan'   => $catatan // optional untuk response
            ];
        }


        if (empty($saved)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Tidak ada file yang berhasil diupload'
                ]));
        }

        // === Response ke frontend ===
        // Di sisi JS, kalau file_count > 0 → tampilkan confirm("Kirim ke dokter?")

        // return $this->output->set_content_type('application/json')
        //     ->set_output(json_encode(['success' => true, 'saved' => $saved]));
        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'success'     => true,
                'message'     => 'Upload hasil radiologi berhasil.',
                'saved'       => $saved,
                'file_count'  => $fileCount,
                'status_now'  => $w['status'] ?? null,
            ]));
    }


    public function worklist_resultList()
    {
        // return var_dump("122");
        // die;
        if (!$this->input->is_ajax_request()) show_404();
        $id = $this->input->post('id', true);
        if (!$id) return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));

        $w = $this->rm->get_one($id);
        if (!$w) return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));

        $files = $this->rm->get_result_files($w);
        return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'files' => $files]));
    }


    public function worklist_resultDelete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $fileId = trim((string) $this->input->post('file_id', true));
        if ($fileId === '') {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'file_id kosong'
                ]));
        }

        $user = trim((string) $this->session->userdata('user_id_pc'));
        if ($user === '') {
            $user = 'system';
        }

        $out = $this->rm->soft_delete_result_file($fileId, $user);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($out));
    }


    public function worklist_sendToDoctor()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = $this->input->post('id', true);

        $w = $this->rm->get_one($id);
        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        $this->rm->update_status_to_wait_doctor($w, $this->session->userdata('user_id_pc'));

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }
}
