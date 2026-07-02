<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RadDoctorController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('RadDoctorModel', 'rdm');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user']   = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/rad_dokter.js';
        $data['title']  = 'Dashboard Dokter Radiologi';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        // $this->load->view('radiologi/index', $data);
        $this->load->view('radiologi/dokter_index', $data);
        $this->load->view('templates/footer');
    }

    public function worklist_filterData()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $status  = $this->input->post('status', true) ?? '';
        $keyword = $this->input->post('keyword', true) ?? '';

        // return var_dump($status);
        // die;

        $dokter_rad_id = $this->session->userdata('user_id_pc'); // atau kode dokter rad

        $rows = $this->rdm->list_for_doctor($dokter_rad_id, $status, $keyword);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($rows));
    }

    public function print_resultxxxxxx()
    {
        $id = $this->input->get('id', true);

        if (!$id) {
            show_error('ID tidak valid');
            return;
        }

        // parsing id (format: episode|pasien|trans|trans_co)
        $arr = explode('|', $id);

        $episode_id = $arr[0] ?? null;
        $pasien_id  = $arr[1] ?? null;
        $trans_id   = $arr[2] ?? null;
        $trans_co   = $arr[3] ?? null;

        // 🔥 redirect ke PDF controller
        redirect("PdfController/radResult/{$episode_id}/{$pasien_id}/{$trans_id}/{$trans_co}");
    }

    public function print_result()
    {
        $id = trim((string)$this->input->get('id', true));

        if ($id === '') {
            show_error('ID tidak valid');
            return;
        }

        $paper = strtoupper(trim((string)$this->input->get('paper', true)));
        if (!in_array($paper, ['A4', 'F4'], true)) {
            $paper = 'A4';
        }

        // format: episode|pasien|trans|trans_co|rad_ke
        $arr = explode('|', $id, 5);

        $episode_id = $arr[0] ?? null;
        $pasien_id  = $arr[1] ?? null;
        $trans_id   = $arr[2] ?? null;
        $trans_co   = $arr[3] ?? '';
        $rad_ke     = $arr[4] ?? '';

        if (!$episode_id || !$pasien_id || !$trans_id) {
            show_error('Parameter cetak tidak lengkap');
            return;
        }

        $query = http_build_query([
            'episode_id' => $episode_id,
            'pasien_id'  => $pasien_id,
            'trans_id'   => $trans_id,
            'trans_co'   => $trans_co,
            'rad_ke'     => $rad_ke,
            'paper'      => $paper,
        ]);

        redirect('PdfController/radResult?' . $query);
    }


    public function print_resultx()
    {
        $ref = trim((string)$this->input->get('id', true));

        if ($ref === '') {
            show_404();
            return;
        }

        // =========================================
        // 1. TENTUKAN PARAMETER UNTUK MODEL
        // =========================================
        if (ctype_digit($ref)) {
            // kalau id numerik langsung
            $param = (int)$ref;
        } else {
            // format dari JS identifyId():
            // episode_id|pasien_id|trans_id|trans_co
            $parts = explode('|', $ref, 4);

            $episode_id = $parts[0] ?? null;
            $pasien_id  = $parts[1] ?? null;
            $trans_id   = $parts[2] ?? null;
            $trans_co   = $parts[3] ?? null;

            if (empty($episode_id) || empty($pasien_id) || empty($trans_id)) {
                show_404();
                return;
            }

            $param = [
                'episode_id' => $episode_id,
                'pasien_id'  => $pasien_id,
                'trans_id'   => $trans_id,
                'trans_co'   => ($trans_co !== '') ? $trans_co : null,
            ];
        }



        // =========================================
        // 2. AMBIL DATA HASIL RADIOLOGI
        // =========================================
        $data = $this->rdm->get_full_result($param);

        // return var_dump($data);
        // die;
        // atau kalau model Anda aliasnya lain:
        // $data = $this->RadDoctorModel->get_full_result($param);

        if (!$data || empty($data['result'])) {
            show_404();
            return;
        }

        // tambahan data untuk view PDF
        $data['print_time'] = date('d/m/Y H:i');
        $data['title'] = 'Hasil Expertise Radiologi';

        // =========================================
        // 3. RENDER PDF
        // =========================================
        $html = $this->load->view('radiologi/pdf_result', $data, true);

        $this->load->library('pdf'); // dompdf wrapper

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $filename = 'hasil_radiologi_' . ($data['result']['episode_id'] ?? 'print') . '.pdf';
        $this->pdf->stream($filename, ['Attachment' => 0]);
    }

    public function worklist_detail()
    {

        // return var_dump("121");
        // die;
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('id', true);
        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        $w = $this->rdm->get_one($id);
        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        $files    = $this->rdm->get_files_for_worklist($w);
        $lastExp  = $this->rdm->get_last_expertise($w);
        $history  = $this->rdm->get_expertise_history($w);

        $out = [
            'success' => true,
            'worklist' => $w,
            'files'    => $files,
            'last_expertise' => $lastExp,
            'history'  => $history,
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($out));
    }



    public function worklist_saveExpertise()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id          = $this->input->post('id', true);
        // $temuan      = $this->input->post('temuan', true);
        // $kesan       = $this->input->post('kesan', true);
        // $saran       = $this->input->post('saran', true);
        $hasil_bacaan  = $this->input->post('hasil_bacaan', true); // ✅ BARU
        $fileVersion = $this->input->post('file_version', true); // optional
        $mode        = $this->input->post('mode', true); // 'draft' atau 'final'

        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        $w = $this->rdm->get_one($id);
        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        $is_final = ($mode === 'final');



        // if ($is_final && trim((string)$kesan) === '') {
        //     return $this->output->set_content_type('application/json')
        //         ->set_output(json_encode(['success' => false, 'message' => 'Kesan wajib diisi untuk laporan final.']));
        // }

        // ✅ VALIDASI FINAL
        if ($is_final && trim((string)$hasil_bacaan) === '') {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Hasil bacaan wajib diisi untuk laporan final.'
                ]));
        }

        $dokter_rad_id = $this->session->userdata('user_id_pc');

        // $dataExpertise = [
        //     'temuan'       => $temuan,
        //     'kesan'        => $kesan,
        //     'saran'        => $saran,
        //     'file_version' => $fileVersion ?: null,
        // ];

        // ✅ DATA BARU (SINGLE FIELD)
        $dataExpertise = [
            'hasil_bacaan' => $hasil_bacaan,
            'file_version' => $fileVersion ?: null,
        ];

        $res = $this->rdm->save_expertise(
            $w,
            $dataExpertise,
            $dokter_rad_id,
            $is_final
        );



        // $res = $this->rdm->save_expertise($w, $dataExpertise, $dokter_rad_id, $is_final);

        // return var_dump($res);
        // die;

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }


    public function get_templates()
    {
        $data = $this->db->get_where('pc01_rad_template_master', ['aktif' => '1'])->result();

        // return var_dump($data);
        // die;
        echo json_encode(['success' => true, 'data' => $data]);
    }
}
