<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\Builder\Builder;

// Sesuaokan mulai chat GPT dari bagian ini Siap. Kita rapikan skemanya supaya:
// Isinya saaya minta di buat masternya dari DB global MS dan struktur form di ambil dari DB selanjutnya saya minta sesuaikan view form kembali
class LabController extends CI_Controller
{


    private $papFormCode = 'PAP_ANATOMIK';

    public function __construct()
    {
        parent::__construct();

        if (!in_array($this->session->userdata('role_id_pc'), ["RU0001", "RU0002"])) {
            redirect('AuthController');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('LabModel', 'lm');
        $this->load->model('PasienModel', 'pm');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user'] = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/lab.js';

        $data['title'] = 'Worklist Laboratorium';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('laboratorium/index', $data);
        $this->load->view('templates/footer');
    }

    // ===== COUNTS (POST) =====
    public function worklist_counts()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($this->lm->counts()));
    }

    // ===== FILTER DATA (POST) =====
    public function worklist_filterData()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $status  = $this->input->post('status', true);
        $keyword = $this->input->post('keyword', true);

        if ($status === null || $status === '') $status = '0';
        // return var_dump($status);
        // die;
        $rows = $this->lm->list_by_status((string)$status, (string)$keyword);
        // return var_dump($rows);
        // die;
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($rows));
    }





    public function worklist_detail()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('id', true);


        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        $w = $this->lm->get_one($id);
        // return var_dump($w);
        // die;

        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        $tests = $this->lm->get_tests_for_worklist($w);
        // return var_dump($tests);
        // die;
        $out = [
            'success'     => true,
            'id'          => $w['id'] ?? $id,
            'episode_id'  => $w['episode_id'] ?? null,
            'pasien_id'   => $w['pasien_id'] ?? null,
            'trans_id'    => $w['trans_id'] ?? null,
            'trans_co'    => $w['trans_co'] ?? null,
            'status'      => $w['status'] ?? null,
            'sampel_id'   => $w['sampel_id'] ?? null,
            'created_date' => $w['created_date'] ?? null,
            'tanggal'     => $w['tanggal'] ?? null,

            // kalau join sudah disiapkan di model, ini akan terisi:
            'nama_pasien' => $w['nama_pasien'] ?? null,
            'no_rm'       => $w['no_rm'] ?? null,
            'nama_poli'   => $w['nama_poli'] ?? null,
            'nama_dokter' => $w['nama_dokter'] ?? null,

            'tests'       => $tests,
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($out));
    }


    // ===== KERJAKAN (POST) =====
    public function worklist_kerjakan()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('id', true);
        $no_sitologi  = trim($this->input->post('no_sitologi', true));


        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        if (empty($no_sitologi)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No Sitologi wajib diisi'
                ]));
        }

        // $username = $this->session->userdata('user_id_pc') ?: ($this->session->userdata('username') ?: 'system');
        $username = $this->session->userdata('user_id_pc');


        $res = $this->lm->kerjakan($id, $username, $no_sitologi);
        // return var_dump($username);
        // die;
        // $this->output->set_content_type('application/json')
        // ->set_output(json_encode($res));

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }

    // ===== UPDATE STATUS (POST) =====
    public function worklist_updateStatus()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id     = $this->input->post('id', true);
        $status = $this->input->post('status', true);

        if (!$id || $status === null || $status === '') {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Param tidak lengkap']));
        }

        $username = $this->session->userdata('user_id_pc');
        $res = $this->lm->update_status($id, (string)$status, $username);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }





    public function label_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('id', true);
        if (!$id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ID kosong']));
        }

        $w = $this->lm->get_one($id);
        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Worklist tidak ditemukan']));
        }

        if (empty($w['sampel_id'])) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Sampel ID belum dibuat. Klik Kerjakan dulu.']));
        }

        $tests = $this->lm->get_tests_for_worklist($w); // sudah ada nama_pemeriksaan

        // QR encode: sampel_id (standar specimen id)
        $qrResult = Builder::create()
            ->data((string)$w['sampel_id'])
            ->size(220)
            ->margin(0)
            ->build();

        $qrDataUri = method_exists($qrResult, 'getDataUri')
            ? $qrResult->getDataUri()
            : ('data:image/png;base64,' . base64_encode($qrResult->getString()));

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'success'      => true,
                'sampel_id'    => $w['sampel_id'],
                'qr'           => $qrDataUri,
                'nama_pasien'  => $w['nama_pasien'] ?? null,
                'no_rm'        => $w['no_rm'] ?? ($w['pasien_id'] ?? null),
                'ambil'        => $w['tgl_barcode'] ?? ($w['created_date'] ?? ($w['tanggal'] ?? null)),
                'tests'        => $tests, // array: test_id, nama_pemeriksaan, dll
            ]));
    }



    public function pap_form_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $worklist_id = $this->input->post('worklist_id', true);
        // return var_dump($worklist_id);
        // die;

        if (!$worklist_id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'worklist_id kosong',
                ]));
        }

        $w = $this->lm->get_one($worklist_id);
        // return var_dump($w);
        // die;

        if (!$w) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Worklist tidak ditemukan',
                ]));
        }

        $form = $this->lm->get_form_with_values($this->papFormCode, $w);

        // return var_dump($form);
        // die;

        if (empty($form['values'])) {

            // tanggal periksa = hari ini
            $form['values']['tgl_periksa'] = date('Y-m-d');

            // tanggal diterima = last_updated_date worklist
            if (!empty($w['created_date'])) {
                // if (!empty($w['last_updated_date'])) {
                $form['values']['tgl_diterima'] = date(
                    'Y-m-d',
                    strtotime($w['created_date'])
                );
            }
        }

        // return var_dump($form);
        // die;

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'success'  => true,
                'worklist' => $form['worklist'],
                'sections' => $form['sections'],
                'values'   => $form['values'],
                'result'   => $form['result'],
            ]));
    }


    public function pap_form_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $worklist_id = $this->input->post('worklist_id', true);
        $mode        = $this->input->post('mode', true); // draft | submit
        // $values      = $this->input->post('values');
        $values_json = $this->input->post('values');
        $values = json_decode($values_json, true);

        // return var_dump($mode);
        // die;

        if (!$worklist_id || !is_array($values)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Data tidak lengkap',
                ]));
        }
        $username = $this->session->userdata('user_id_pc') ?: 'system';

        /**
         * Implementasi di LabModel:
         * save_form_result_pap($formCode, $worklist_id, $values, $mode, $username)
         * - insert/update pc01_lab_form_result_hdr + pc01_lab_form_result_value
         * - kalau $mode == 'submit':
         *     - set hdr.status_result = '1'
         *     - update pc01_worklist_lab.status = '9' (Menunggu Dokter)
         */

        $res = $this->lm->save_form_result_pap(
            $this->papFormCode,
            $worklist_id,
            $values,
            $mode,
            $username
        );

        // return var_dump($res);
        // die;

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }





    // public function pap_print()
    // {
    //     $id = $this->input->get('id', true);

    //     if (!$id) {
    //         show_error('ID worklist kosong.', 400);
    //         return;
    //     }

    //     $payload = $this->lm->get_pap_print_payload($this->papFormCode, $id);

    //     if (empty($payload['success'])) {
    //         show_error($payload['message'] ?? 'Data tidak dapat dicetak.', 403);
    //         return;
    //     }

    //     $html = $this->load->view('laboratorium/pap_print_pdf', $payload, true);

    //     $filename = 'HASIL_PAP_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payload['worklist']['no_sitologi'] ?? 'LAB') . '.pdf';

    //     if (class_exists('\Mpdf\Mpdf')) {
    //         $mpdf = new \Mpdf\Mpdf([
    //             'mode' => 'utf-8',
    //             'format' => [165.1, 215.9], // 6.5 inch x 8.5 inch dalam mm
    //             'margin_left' => 8,
    //             'margin_right' => 8,
    //             'margin_top' => 8,
    //             'margin_bottom' => 8,
    //         ]);

    //         $mpdf->WriteHTML($html);
    //         $mpdf->Output($filename, 'I');
    //         return;
    //     }

    //     // fallback kalau mPDF belum aktif
    //     $this->output
    //         ->set_content_type('text/html')
    //         ->set_output($html . '<script>window.onload=function(){setTimeout(function(){window.print();},300);}</script>');
    // }

    public function pap_print()
    {
        $id = trim((string)$this->input->get('id', true));

        if ($id === '') {
            show_error('ID worklist kosong.', 400);
            return;
        }

        $query = http_build_query([
            'id' => $id,
        ]);

        redirect('PdfController/papResult?' . $query);
    }
}
