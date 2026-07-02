<?php

use PhpParser\Node\Stmt\Return_;

defined('BASEPATH') or exit('No direct script access allowed');


class SatuSehatController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('RajalModel', 'rm');
    }

    private function getPatientIdFromSatusehat($nik, $token)
    {
        $url = SATUSEHAT_BASE_URL . "/Patient?identifier=https://fhir.kemkes.go.id/id/nik|$nik";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token"
            ]
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'Error getPatientIdFromSatusehat: ' . $err);
            return null;
        }

        $json = json_decode($response, true);

        if (isset($json['entry'][0]['resource']['id'])) {
            return $json['entry'][0]['resource']['id'];
        }

        return null;
    }

    public function getTokenSatuSehat()
    {


        $client_id     = SATUSEHAT_CLIENT_ID;
        $client_secret = SATUSEHAT_CLIENT_SECRET;



        $auth_url = SATUSEHAT_AUTH_URL . '/accesstoken?grant_type=client_credentials';


        $curl = curl_init();


        curl_setopt_array($curl, [
            CURLOPT_URL => $auth_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => SATUSEHAT_CLIENT_ID,
                'client_secret' => SATUSEHAT_CLIENT_SECRET
            ]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ]
        ]);


        $response = curl_exec($curl);


        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Curl Error: ' . $err]));
        } else {
            $json = json_decode($response, true);

            if (isset($json['access_token'])) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'token' => $json['access_token'],
                        'expires_in' => $json['expires_in']
                    ]));
            } else {
                $this->output
                    ->set_status_header(500)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'error' => 'Gagal mengambil token',
                        'response_raw' => $response,
                        'parsed_json' => $json
                    ]));
            }
        }
    }


    public function kirimEncounter()
    {
        $json_input = file_get_contents('php://input');
        $payload = json_decode($json_input, true);

        $encounterData = $payload['encounter'] ?? null;
        $token = $payload['token'] ?? null;
        $episode_id     = $encounterData['episode_id'] ?? null;

        $detilByEpisode = $this->rm->get_dt_by_episode($episode_id);

        if (!$detilByEpisode || !$token) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Data tidak lengkap']));
        }

        // ? Ambil Patient ID dari SATUSEHAT berdasarkan NIK
        $nik = $detilByEpisode->no_identitas;
        $id_pas_satu_sehat = $this->getPatientIdFromSatusehat($nik, $token);
        // return var_dump($nik);
        // die;
        if (!$id_pas_satu_sehat) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Pasien dengan NIK tidak ditemukan di SATUSEHAT']));
        }



        // ? Siapkan Encounter (FHIR)
        // $episode_id = '125070003326';
        $encounterFhir = [
            "resourceType" => "Encounter",
            "status" => "arrived",
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                "reference" => "Patient/" . $id_pas_satu_sehat,
                "display" => $detilByEpisode->nama
            ],
            "participant" => [[
                "type" => [[
                    "coding" => [[
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                        "code" => "ATND",
                        "display" => "attender"
                    ]]
                ]],
                "individual" => [
                    "reference" => "Practitioner/" . $detilByEpisode->ihs_id,
                    "display" => $detilByEpisode->nama_dr
                ]
            ]],
            "period" => [
                "start" => $encounterData['tgl_berobat'] . "T07:00:00+07:00"
            ],
            "location" => [[
                "location" => [
                    "reference" => "Location/" . $detilByEpisode->satusehat_id,
                    "display" => $detilByEpisode->nama_poli
                ]
            ]],
            "statusHistory" => [[
                "status" => "arrived",
                "period" => [
                    "start" => $encounterData['tgl_berobat'] . "T07:00:00+07:00"
                ]
            ]],
            "serviceProvider" => [
                "reference" => "Organization/" . SATUSEHAT_ORG_ID
            ],
            "identifier" => [[
                "system" => "http://sys-ids.kemkes.go.id/encounter/" . SATUSEHAT_ORG_ID,
                "value" => "en-" . $episode_id
            ]],
        ];

        // return var_dump($encounterFhir);
        // die;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => SATUSEHAT_BASE_URL . '/Encounter',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($encounterFhir),
        ]);

        $response  = curl_exec($ch);
        // return var_dump($response);
        // die;
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => 'Curl error', 'message' => $err]));
        }


        $resultJson = json_decode($response, true);
        $encounter_uuid = $resultJson['id'] ?? null;

        $encounter_id_lcl = "en-" . $episode_id;

        if ($encounter_uuid) {
            // Simpan ke database (gunakan model)
            $this->rm->simpan_log_encounter([
                // 'episode_id' => $episode_id,
                'encounter_id_local' => $encounter_id_lcl,
                'encounter_id_satusehat' => $encounter_uuid,
                'response_json' => $response,
                'status' => 'success'
            ]);
        } else {
            $this->rm->simpan_log_encounter([
                // 'episode_id' => $episode_id,
                'encounter_id_local' => $encounter_id_lcl,
                'encounter_id_satusehat' => '',
                'response_json' => $response,
                'status' => 'failed'
            ]);
        }



        if ($encounter_uuid) {
            // simpan ke DB...
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'encounter_uuid' => $encounter_uuid,
                    'message' => 'Encounter berhasil dikirim ke SATUSEHAT'
                ]));
        } else {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Encounter tidak mendapatkan ID dari SATUSEHAT',
                    'response_raw' => $response
                ]));
        }
    }


    public function EncounterInProgreess()
    {

        $json_input = file_get_contents('php://input');
        $payload = json_decode($json_input, true);

        $encounterData = $payload['encounter'] ?? null;


        $token = $payload['token'] ?? null;

        $episode_id = $encounterData['episode_id'] ?? null;

        if (!$token || !$episode_id) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Data tidak lengkap (token/episode_id kosong)']));
        }

        // waktu mulai pemeriksaan (SOAP tersimpan) -> pakai dari payload kalau ada, kalau tidak pakai now Jakarta
        // $waktu_mulai = $encounterData['waktu_mulai'];
        $waktu_mulai = $encounterData['waktu_mulai'] ?? $this->_now_jakarta_iso();

        // 1) Ambil encounter_uuid dari DB log (hasil kirimEncounter sebelumnya)
        $encounter_id_lcl = 'en-' . $episode_id;


        // ✅ buat/siapkan method ini di model rm
        $encounter_uuid = $this->rm->get_encounter_uuid_by_local($encounter_id_lcl);


        if (!$encounter_uuid) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Encounter UUID tidak ditemukan di DB untuk ' . $encounter_id_lcl
                ]));
        }


        // 2) GET Encounter dari SATUSEHAT
        $get_url = SATUSEHAT_BASE_URL . '/Encounter/' . $encounter_uuid;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $get_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Accept: application/json"
            ],
        ]);


        $get_response = curl_exec($ch);


        $get_err = curl_error($ch);
        $get_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($get_err) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Curl error (GET)', 'detail' => $get_err]));
        }


        if ($get_http >= 400) {
            return $this->output
                ->set_status_header($get_http)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Gagal GET Encounter dari SATUSEHAT',
                    'response_raw' => $get_response
                ]));
        }


        $encounterFhir = json_decode($get_response, true);
        if (!$encounterFhir || empty($encounterFhir['id'])) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Response GET Encounter tidak valid',
                    'response_raw' => $get_response
                ]));
        }

        // Jika sudah in-progress, idempotent: anggap sukses
        if (($encounterFhir['status'] ?? '') === 'in-progress') {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Encounter sudah in-progress (tidak perlu update).',
                    'encounter_uuid' => $encounter_uuid,
                    'encounter_id_local' => $encounter_id_lcl
                ]));
        }


        // 3) Ubah status jadi in-progress
        $encounterFhir['status'] = 'in-progress';

        // ✅ Hapus meta agar aman saat PUT (server yang urus versionId/lastUpdated)
        if (isset($encounterFhir['meta'])) unset($encounterFhir['meta']);



        // 4) Update statusHistory:
        // - arrived: set period.end = waktu_mulai (kalau belum ada)
        // - tambah/replace in-progress: period.start = waktu_mulai
        if (!isset($encounterFhir['statusHistory']) || !is_array($encounterFhir['statusHistory'])) {
            $encounterFhir['statusHistory'] = [];
        }


        $arrived_idx = null;
        $inprog_idx = null;


        foreach ($encounterFhir['statusHistory'] as $i => $h) {
            if (($h['status'] ?? '') === 'arrived') $arrived_idx = $i;
            if (($h['status'] ?? '') === 'in-progress') $inprog_idx = $i;
        }



        // arrived
        if ($arrived_idx !== null) {

            if (!isset($encounterFhir['statusHistory'][$arrived_idx]['period'])) {
                $encounterFhir['statusHistory'][$arrived_idx]['period'] = [];
            }
            if (empty($encounterFhir['statusHistory'][$arrived_idx]['period']['end'])) {
                $encounterFhir['statusHistory'][$arrived_idx]['period']['end'] = $waktu_mulai;
            }
        } else {

            // kalau arrived belum ada, buat minimal arrived
            $arrived_start = $encounterFhir['period']['start'] ?? $waktu_mulai;
            $encounterFhir['statusHistory'][] = [
                'status' => 'arrived',
                'period' => [
                    'start' => $arrived_start,
                    'end'   => $waktu_mulai
                ]
            ];
        }


        // in-progress
        $in_progress_entry = [
            'status' => 'in-progress',
            'period' => [
                'start' => $waktu_mulai
                // end dikosongkan, nanti diisi saat finished
            ]
        ];

        if ($inprog_idx !== null) {
            $encounterFhir['statusHistory'][$inprog_idx] = $in_progress_entry;
        } else {
            $encounterFhir['statusHistory'][] = $in_progress_entry;
        }

        // 5) PUT update Encounter
        $put_url = SATUSEHAT_BASE_URL . '/Encounter/' . $encounter_uuid;

        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL => $put_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($encounterFhir),
        ]);







        $put_response = curl_exec($ch2);
        $put_err = curl_error($ch2);
        $put_http = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        if ($put_err) {
            // log gagal
            $this->rm->simpan_log_encounter([
                'encounter_id_local' => $encounter_id_lcl,
                'encounter_id_satusehat' => $encounter_uuid,
                'response_json' => $put_response ?: $put_err,
                'status' => 'failed_inprogress'
            ]);

            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Curl error (PUT)', 'detail' => $put_err]));
        }

        // simpan log
        $this->rm->simpan_log_encounter([
            'encounter_id_local' => $encounter_id_lcl,
            'encounter_id_satusehat' => $encounter_uuid,
            'response_json' => $put_response,
            'status' => ($put_http < 400 ? 'success_inprogress' : 'failed_inprogress')
        ]);

        if ($put_http >= 400) {
            return $this->output
                ->set_status_header($put_http)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Gagal update Encounter ke in-progress',
                    'response_raw' => $put_response
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Encounter berhasil diubah ke in-progress',
                'encounter_uuid' => $encounter_uuid,
                'encounter_id_local' => $encounter_id_lcl,
                'response_raw' => $put_response
            ]));
    }

    public function kirimCondition()
    {


        $json_input = file_get_contents('php://input');
        $payload = json_decode($json_input, true);

        $conditionData = $payload['condition'] ?? null;
        $token = $payload['token'] ?? null;

        $episode_id = $conditionData['episode_id'] ?? null;
        $icd10      = isset($conditionData['icd10']) ? trim($conditionData['icd10']) : null;
        $diagnosa   = $conditionData['diagnosa'] ?? null;

        // tgl_berobat bisa "YYYY-MM-DD" atau "DD-MM-YYYY"
        $tgl_input  = $conditionData['tgl_berobat'] ?? null;

        // waktu selesai periksa (opsional dari frontend) untuk recordedDate yang valid
        $waktu_input = $conditionData['waktu_selesai'] ?? null;


        // $tgl        = $conditionData['tgl_berobat'] ?? date('Y-m-d');


        if (!$token || !$episode_id || !$icd10) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Data tidak lengkap (token/episode_id/icd10)']));
        }

        $detilByEpisode = $this->rm->get_dt_by_episode($episode_id);


        if (!$detilByEpisode) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Detail episode tidak ditemukan']));
        }

        // 1) Patient SATUSEHAT dari NIK
        $nik = $detilByEpisode->no_identitas;


        $patient_id = $this->getPatientIdFromSatusehat($nik, $token);


        if (!$patient_id) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Pasien (NIK) tidak ditemukan di SATUSEHAT']));
        }

        // 2) Ambil Encounter UUID dari log (hasil kirimEncounter sebelumnya)
        $enc_local = 'en-' . $episode_id;
        $enc_uuid  = $this->rm->get_uuid_from_log('Encounter', $enc_local); // buat function ini di model

        if (!$enc_uuid) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Encounter UUID tidak ditemukan (belum kirim encounter?)']));
        }



        // ✅ 3) Normalisasi tanggal jadi YYYY-MM-DD
        $tgl_iso = $this->_normalize_date_ymd($tgl_input);
        if (!$tgl_iso) {
            // fallback aman: pakai tanggal hari ini (Jakarta)
            $tgl_iso = $this->_today_jakarta_ymd();
        }


        // ✅ 4) recordedDate harus ISO dateTime
        // Prioritas:
        // - kalau frontend kirim waktu_selesai (ISO) pakai itu
        // - kalau tidak, pakai now Jakarta
        $recordedDate = $this->_normalize_datetime_iso($waktu_input);
        if (!$recordedDate) {
            // default: tanggal kunjungan + jam sekarang Jakarta
            $recordedDate = $this->_now_jakarta_iso();
        }




        // 5) Build Condition (ICD saja di code)
        $conditionFhir = [
            "resourceType" => "Condition",
            "clinicalStatus" => [
                "coding" => [[
                    "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                    "code" => "active",
                    "display" => "Active"
                ]]
            ],
            "verificationStatus" => [
                "coding" => [[
                    "system" => "http://terminology.hl7.org/CodeSystem/condition-ver-status",
                    "code" => "confirmed",
                    "display" => "Confirmed"
                ]]
            ],
            "category" => [[
                "coding" => [[
                    "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                    "code" => "encounter-diagnosis",
                    "display" => "Encounter Diagnosis"
                ]]
            ]],
            "code" => [
                "coding" => [[
                    "system" => "http://hl7.org/fhir/sid/icd-10",
                    "code" => $icd10,
                    // display opsional, tapi bagus kalau ada
                    "display" => $diagnosa ?: null
                ]],
                "text" => $diagnosa ?: $icd10
            ],
            "subject" => [
                "reference" => "Patient/" . $patient_id,
                "display" => $detilByEpisode->nama
            ],
            "encounter" => [
                "reference" => "Encounter/" . $enc_uuid
            ],
            // "recordedDate" => $tgl . "T07:00:00+07:00"
            // ✅ FIX: recordedDate harus ISO dateTime, bukan DD-MM-YYYY
            "recordedDate" => $recordedDate
        ];


        // optional: asserter/recorder dokter kalau ihs_id ada
        if (!empty($detilByEpisode->ihs_id)) {
            $conditionFhir["asserter"] = [
                "reference" => "Practitioner/" . $detilByEpisode->ihs_id,
                "display" => $detilByEpisode->nama_dr
            ];
        }

        // 6) POST Condition
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => SATUSEHAT_BASE_URL . '/Condition',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($conditionFhir),
        ]);

        $response  = curl_exec($ch);


        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            // log failed
            $this->rm->insert_log_satusehat_fhir([
                'resource_type' => 'Condition',
                'resource_id_satusehat' => null,
                'resource_id_local' => 'cond-' . $episode_id . '-' . $icd10,
                'response_json' => null,
                'error_message' => $err,
                'status' => 'failed'
            ]);

            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'failed', 'message' => 'Curl error', 'detail' => $err]));
        }

        $resultJson = json_decode($response, true);
        $condition_uuid = $resultJson['id'] ?? null;

        // log
        $this->rm->insert_log_satusehat_fhir([
            'resource_type' => 'Condition',
            'resource_id_satusehat' => $condition_uuid ?: null,
            'resource_id_local' => 'cond-' . $episode_id . '-' . $icd10,
            'response_json' => $response,
            'error_message' => $condition_uuid ? null : 'No ID returned',
            'status' => $condition_uuid ? 'success' : 'failed'
        ]);

        if ($condition_uuid) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'condition_uuid' => $condition_uuid,
                    'message' => 'Condition (ICD) berhasil dikirim ke SATUSEHAT'
                ]));
        } else {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'failed',
                    'message' => 'Condition tidak mendapatkan ID dari SATUSEHAT',
                    'response_raw' => $response
                ]));
        }
    }


    private function _normalize_date_ymd($dateStr)
    {
        if (!$dateStr) return null;
        $dateStr = trim($dateStr);

        // sudah YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return $dateStr;
        }

        // DD-MM-YYYY
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateStr)) {
            $dt = DateTime::createFromFormat('d-m-Y', $dateStr, new DateTimeZone('Asia/Jakarta'));
            return $dt ? $dt->format('Y-m-d') : null;
        }

        // fallback coba parse umum
        $dt = date_create($dateStr);
        return $dt ? $dt->format('Y-m-d') : null;
    }

    private function _normalize_datetime_iso($dateTimeStr)
    {
        if (!$dateTimeStr) return null;
        $dateTimeStr = trim($dateTimeStr);

        // Kalau sudah ISO (mengandung T), coba parse
        try {
            $dt = new DateTime($dateTimeStr);
            return $dt->format('c');
        } catch (Exception $e) {
            return null;
        }
    }


    private function _now_jakarta_iso()
    {
        $dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        return $dt->format('c'); // contoh: 2025-12-17T20:10:11+07:00
    }

    private function _today_jakarta_ymd()
    {
        $dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        return $dt->format('Y-m-d');
    }
}
