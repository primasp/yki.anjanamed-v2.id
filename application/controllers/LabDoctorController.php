<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LabDoctorController extends CI_Controller
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
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id_pc');
        $data['user']   = $this->um->get_user_by_id($user_id);
        $data['script'] = 'js/lab_dokter.js';
        $data['title']  = 'Form PAP Dokter Laboratorium';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('laboratorium/pap_dokter_index', $data);
        $this->load->view('templates/footer');
    }

    // List worklist PAP untuk dokter
    public function pap_listxxx()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $keyword = $this->input->post('keyword', true);

        $rows = $this->lm->list_pap_for_dokter((string)$keyword);

        // return var_dump($rows);
        // die;

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($rows));
    }

    public function pap_list()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $keyword = $this->input->post('keyword', true);
        $tab     = $this->input->post('tab', true) ?: 'waiting';

        $rows = $this->lm->list_pap_for_dokter((string)$keyword, (string)$tab);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($rows));
    }


    // public function pap_printxx()
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
    //             'format' => [165.1, 215.9],
    //             'margin_left' => 8,
    //             'margin_right' => 8,
    //             'margin_top' => 8,
    //             'margin_bottom' => 8,
    //         ]);

    //         $mpdf->WriteHTML($html);
    //         $mpdf->Output($filename, 'I');
    //         return;
    //     }

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

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'success'  => true,
                'worklist' => $form['worklist'],
                'sections' => $form['sections'],
                'values'   => $form['values'],
                'result'   => $form['result'],
            ]));

        // return var_dump($form);
        // die;
    }

    public function pap_form_save()
    {


        if (!$this->input->is_ajax_request()) show_404();

        $worklist_id = $this->input->post('worklist_id', true);
        $mode        = $this->input->post('mode', true); // draft | submit (ACC)
        $values      = $this->input->post('values');




        if (!$worklist_id || !is_array($values)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Data tidak lengkap',
                ]));
        }

        $username = $this->session->userdata('user_id_pc') ?: 'system';

        $res = $this->lm->save_form_result_pap_dokter(
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
}
