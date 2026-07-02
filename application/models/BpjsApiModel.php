<?php
require_once "vendor/autoload.php";

class BpjsApiModel extends CI_Model
{
    public function __construct()
    {
    }




    public function putToBpjsKunjungan($url, $data, $method = "PUT")
    {
        $session = curl_init();

        // Set timezone ke UTC
        date_default_timezone_set('UTC');

        // ID konsumen dan kunci rahasia BPJS
        $cid = BPJS_CID; // Konsumen ID BPJS
        $ckey = BPJS_CKEY; // Secret Key

        // Timestamp dan signature
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);
        $encodedSignature = base64_encode($signature);

        // User Key dan Authorization
        $user_key = BPJS_USER_KEY;
        $authorization = BPJS_AUTHORIZATION;

        // Set header dengan Content-Type: text/plain
        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            "Content-Type: text/plain",
            "user_key: {$user_key}",
        ];


        // Set cURL options
        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);

        // Dinamis POST atau PUT
        if (strtoupper($method) === "PUT") {
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, "PUT");
        } elseif (strtoupper($method) === "POST") {
            curl_setopt($session, CURLOPT_POST, true);
        }

        // Set data untuk POST/PUT
        curl_setopt($session, CURLOPT_POSTFIELDS, $data);

        // Validasi SSL
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

        // Eksekusi cURL
        $responseAll = curl_exec($session);
        // return var_dump($responseAll);
        // die;

        echo "<pre>";
        print_r($responseAll);
        echo "</pre>";

        // return var_dump($responseAll);
        // die;

        // Cek jika ada error
        if ($responseAll === false) {
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => 'cURL Error: ' . curl_error($session)
                ]
            ];
        }

        // Decode respons JSON
        $json = json_decode($responseAll, true);



        // $message = $response['response'][0]['message'];


        // Jika respons JSON valid
        if ($json !== null && isset($json['metaData']['code'])) {
            $code = $json['metaData']['code'];
            $message = $json['metaData']['message'];
            // $message2 = $json['response'][0]['message'];
            $response = $json['response'] ?? null;
            // return var_dump($message2);
            // die;
            if ($code === 201) {

                // Dekripsi respons jika code === 201
                $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
                $decompressedResponse = $this->decompress($decryptedResponse);

                $json['response'] = json_decode($decompressedResponse, true);

                // return [
                //     'metadata' => [
                //         'code' => $code,
                //         'message' => json_decode($decompressedResponse, true), // Decode hasil dekripsi
                //     ]
                // ];
                return $json;
            } else {
                return [
                    'metaData' => [
                        'code' => $code,
                        'message' => $message . " | " . ($response[0]['message'] ?? ""),
                    ]
                ];
            }
        } else {
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => 'Failed to Decode JSON response2',
                ]
            ];
        }
    }


    public function postToBpjsKunjungan($url, $data, $method = "POST")
    {
        $session = curl_init();

        // Set timezone ke UTC
        date_default_timezone_set('UTC');

        // ID konsumen dan kunci rahasia BPJS
        $cid = BPJS_CID; // Konsumen ID BPJS
        $ckey = BPJS_CKEY; // Secret Key

        // Timestamp dan signature
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);
        $encodedSignature = base64_encode($signature);

        // User Key dan Authorization
        $user_key = BPJS_USER_KEY;
        $authorization = BPJS_AUTHORIZATION;

        // Set header dengan Content-Type: text/plain
        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            "Content-Type: text/plain",
            "user_key: {$user_key}",
        ];


        // Set cURL options
        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);

        // Dinamis POST atau PUT
        if (strtoupper($method) === "PUT") {
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, "PUT");
        } elseif (strtoupper($method) === "POST") {
            curl_setopt($session, CURLOPT_POST, true);
        }

        // Set data untuk POST/PUT
        curl_setopt($session, CURLOPT_POSTFIELDS, $data);

        // Validasi SSL
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

        // Eksekusi cURL
        $responseAll = curl_exec($session);
        // return var_dump($data);
        // die;
        echo "<pre>";
        print_r($responseAll);
        echo "</pre>";

        // return var_dump($responseAll);
        // die;

        // Cek jika ada error
        if ($responseAll === false) {
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => 'cURL Error: ' . curl_error($session)
                ]
            ];
        }

        // Decode respons JSON
        $json = json_decode($responseAll, true);



        // $message = $response['response'][0]['message'];


        // Jika respons JSON valid
        if ($json !== null && isset($json['metaData']['code'])) {
            $code = $json['metaData']['code'];
            $message = $json['metaData']['message'];
            // $message2 = $json['response'][0]['message'];
            $response = $json['response'] ?? null;
            // return var_dump($message2);
            // die;
            if ($code === 201) {

                // Dekripsi respons jika code === 201
                $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
                $decompressedResponse = $this->decompress($decryptedResponse);

                $json['response'] = json_decode($decompressedResponse, true);

                // return [
                //     'metadata' => [
                //         'code' => $code,
                //         'message' => json_decode($decompressedResponse, true), // Decode hasil dekripsi
                //     ]
                // ];
                return $json;
            } else {
                return [
                    'metaData' => [
                        'code' => $code,
                        'message' => $message . " | " . ($response[0]['message'] ?? ""),
                    ]
                ];
            }
        } else {
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => 'Failed to Decode JSON response2',
                ]
            ];
        }
    }

    public function getApiRujukan($url)
    {


        $session = curl_init();

        date_default_timezone_set('UTC');

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;

        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);
        $encodedSignature = base64_encode($signature);

        $user_key = BPJS_USER_KEY;
        $authorization = BPJS_AUTHORIZATION;

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$user_key}",
        ];




        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'GET');

        // ------------------------------------VALID SSL------------------------------------
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        // ------------------------------------VALID SSL------------------------------------


        $responseAll = curl_exec($session);



        if ($responseAll === false) {
            // Handle cURL error
            return ['metadata' => ['responCode' => '96', 'responDesc' => 'cURL Error: ' . curl_error($session)]];
        }
        $json = json_decode($responseAll, true);

        // Validasi JSON
        if ($json !== null && isset($json['metaData']['code'])) {
            if ($json['metaData']['code'] == 200) {
                // Lanjutkan proses dekripsi
                if (isset($json['response'])) {
                    $response = $json['response'];
                    $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
                    $response_compress = $this->decompress($decryptedResponse);
                    $responseFinal = json_decode($response_compress, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return array_merge($responseFinal, ['metadata' => ['responCode' => '00', 'responDesc' => 'OK']]);
                    } else {
                        return [
                            'metadata' => [
                                'responCode' => '98',
                                'responDesc' => 'JSON Decoding Error'
                            ]
                        ];
                    }
                } else {
                    return [
                        'metadata' => [
                            'responCode' => '97',
                            'responDesc' => 'Invalid Response Format'
                        ]
                    ];
                }
            } else {
                return [
                    'metadata' => [
                        'responCode' => $json['metaData']['code'],
                        'responDesc' => $json['metaData']['message']
                    ]
                ];
            }
        } else {
            return [
                'metadata' => [
                    'responCode' => '99',
                    'responDesc' => 'Failed to Decode JSON'
                ]
            ];
        }
    }

    public function getApiBPJS($url)
    {


        $session = curl_init();

        date_default_timezone_set('UTC');

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;

        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);
        $encodedSignature = base64_encode($signature);

        $user_key = BPJS_USER_KEY;
        $authorization = BPJS_AUTHORIZATION;

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$user_key}",
        ];




        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'GET');

        // ------------------------------------VALID SSL------------------------------------
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        // ------------------------------------VALID SSL------------------------------------


        $responseAll = curl_exec($session);



        if ($responseAll === false) {
            // Handle cURL error
            return ['metadata' => ['responCode' => '96', 'responDesc' => 'cURL Error: ' . curl_error($session)]];
        }
        $json = json_decode($responseAll, true);

        // Validasi JSON
        if ($json !== null && isset($json['metaData']['code'])) {
            if ($json['metaData']['code'] == 200) {
                // Lanjutkan proses dekripsi
                if (isset($json['response'])) {
                    $response = $json['response'];
                    $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
                    $response_compress = $this->decompress($decryptedResponse);
                    $responseFinal = json_decode($response_compress, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return array_merge($responseFinal, ['metadata' => ['responCode' => '00', 'responDesc' => 'OK']]);
                    } else {
                        return [
                            'metadata' => [
                                'responCode' => '98',
                                'responDesc' => 'JSON Decoding Error'
                            ]
                        ];
                    }
                } else {
                    return [
                        'metadata' => [
                            'responCode' => '97',
                            'responDesc' => 'Invalid Response Format'
                        ]
                    ];
                }
            } else {
                return [
                    'metadata' => [
                        'responCode' => $json['metaData']['code'],
                        'responDesc' => $json['metaData']['message']
                    ]
                ];
            }
        } else {
            return [
                'metadata' => [
                    'responCode' => '99',
                    'responDesc' => 'Failed to Decode JSON'
                ]
            ];
        }
    }

    public function hapusPendaftaran($url)
    {
        $session = curl_init($url);

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;
        $userKey = BPJS_USER_KEY;

        date_default_timezone_set('UTC');
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);

        $encodedSignature = base64_encode($signature);
        $authorization = BPJS_AUTHORIZATION;

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$userKey}",
        ];


        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE'); // <<=== method DELETE

        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

        $responseAll = curl_exec($session);

        // return var_dump($responseAll);
        // die;
        // curl_close($session);


        $json = json_decode($responseAll, true);


        $message = $json['metaData'];
        $response = $json['response'];

        $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);

        $response_compress = $this->decompress($decryptedResponse);

        $responseFinal = json_decode($response_compress, true);



        // $message = $json['metaData'] ?? ['code' => 500, 'message' => 'No metadata response'];
        // // $response = $json['response'];
        // $responseFinal = null;

        // if (isset($json['response']) && !empty($json['response'])) {
        //     $response = $json['response'];

        //     $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
        //     $response_compress = $this->decompress($decryptedResponse);
        //     $responseFinal = json_decode($response_compress, true);
        // }

        // $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);

        // $response_compress = $this->decompress($decryptedResponse);

        // $responseFinal = json_decode($response_compress, true);

        return [
            'msg' => $message,
            'response' => $responseFinal
        ];

        // return var_dump($response);
        // die;
        // curl_close($ch);

        // return json_decode($response, true);





    }

    public function cekJdlDrBpjs($url)
    {
        // return var_dump($url);
        // die;
        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;
        $userKey = BPJS_USER_KEY_ANTROL;
        // $authorization = BPJS_AUTHORIZATION;

        date_default_timezone_set('UTC');
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);
        $encodedSignature = base64_encode($signature);

        $headers = [
            "X-cons-id: $cid",
            "X-Timestamp: $timestamp",
            "X-Signature: $encodedSignature",
            // "X-Authorization: $authorization",
            "user_key: $userKey",
            "Accept: application/json"
        ];



        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $json = json_decode($response, true);

        $message = $json['metadata'];
        // $response = $json['response'];
        $responseFinal = null;

        if (isset($json['response']) && !empty($json['response'])) {
            $response = $json['response'];

            $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
            $response_compress = $this->decompress($decryptedResponse);
            $responseFinal = json_decode($response_compress, true);
        }

        // $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);

        // $response_compress = $this->decompress($decryptedResponse);

        // $responseFinal = json_decode($response_compress, true);

        return [
            'msg' => $message,
            'response' => $responseFinal
        ];

        // return var_dump($response);
        // die;
        // curl_close($ch);

        // return json_decode($response, true);
    }


    public function hadirAntreanAntrol($url, $request)
    {
        $session = curl_init($url);

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;
        $userKey = BPJS_USER_KEY_ANTROL;

        date_default_timezone_set('UTC');

        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);

        $encodedSignature = base64_encode($signature);

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-timestamp: {$timestamp}",
            "X-signature: {$encodedSignature}",
            // "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$userKey}",
        ];
        $reqBody = $request;

        $myvars = json_encode($reqBody);

        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST'); // Metode permintaan POST
        curl_setopt($session, CURLOPT_POSTFIELDS, $myvars); // Data dalam format JSON

        // ------------------------------------VALID SSL------------------------------------
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        // ------------------------------------VALID SSL------------------------------------

        $responseAll = curl_exec($session);
        $json = json_decode($responseAll, true);

        $message = $json['metadata'];
        // $response = $json['response'];
        $responseFinal = null;

        if (isset($json['response']) && !empty($json['response'])) {
            $response = $json['response'];

            $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
            $response_compress = $this->decompress($decryptedResponse);
            $responseFinal = json_decode($response_compress, true);
        }


        return [
            'msg' => $message,
            'response' => $responseFinal
        ];
    }


    public function addAntreanAntrol($url, $request)
    {

        $session = curl_init($url);

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;
        $userKey = BPJS_USER_KEY_ANTROL;

        date_default_timezone_set('UTC');

        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);

        $encodedSignature = base64_encode($signature);
        // $authorization = BPJS_AUTHORIZATION;

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-timestamp: {$timestamp}",
            "X-signature: {$encodedSignature}",
            // "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$userKey}",
        ];
        $reqBody = $request;

        $myvars = json_encode($reqBody);

        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST'); // Metode permintaan POST
        curl_setopt($session, CURLOPT_POSTFIELDS, $myvars); // Data dalam format JSON

        // ------------------------------------VALID SSL------------------------------------
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        // ------------------------------------VALID SSL------------------------------------

        $responseAll = curl_exec($session);
        $json = json_decode($responseAll, true);

        $message = $json['metadata'];
        // $response = $json['response'];
        $responseFinal = null;

        if (isset($json['response']) && !empty($json['response'])) {
            $response = $json['response'];

            $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);
            $response_compress = $this->decompress($decryptedResponse);
            $responseFinal = json_decode($response_compress, true);
        }

        // $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);

        // $response_compress = $this->decompress($decryptedResponse);

        // $responseFinal = json_decode($response_compress, true);

        return [
            'msg' => $message,
            'response' => $responseFinal
        ];
        // return var_dump($message);
        // die;
    }
    public function simpanPendaftaran($url, $request)
    {
        $poli = $request['poli'];
        $getPoli = $this->db->query("select * from pc01_med_poli_ms where poli_id = '$poli' and aktif = '1'")->row();
        $explode = explode(' - ', $request['ppkumum']);
        $provider_peserta = $explode[0];

        $session = curl_init($url);

        $cid = BPJS_CID;
        $ckey = BPJS_CKEY;
        $userKey = BPJS_USER_KEY;

        date_default_timezone_set('UTC');
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cid . "&" . $timestamp, $ckey, true);

        $encodedSignature = base64_encode($signature);
        $authorization = BPJS_AUTHORIZATION;

        $reqHeader = [
            "X-cons-id: {$cid}",
            "X-Timestamp: {$timestamp}",
            "X-Signature: {$encodedSignature}",
            "X-Authorization: {$authorization}",
            'Accept: application/json',
            'Content-Type: Application/x-www-form-urlencoded',
            "user_key: {$userKey}",
        ];

        $tgl_berobat = date('d-m-Y', strtotime($request['tgl_berobat']));

        $reqBody = [
            "kdProviderPeserta" => $provider_peserta,
            // "tglDaftar" => date('d-m-Y'),
            "tglDaftar" => $tgl_berobat,
            "noKartu" => $request['nk'],
            "kdPoli" => $getPoli->poli_bpjsid,
            "keluhan" => $request['keluhan'],
            "kunjSakit" => $request['jenis_kunjungan'] == 1 ? true : false,
            "sistole" => intval($request['sistole']),
            "diastole" => intval($request['diastole']),
            "beratBadan" => intval($request['bb']),
            "tinggiBadan" => intval($request['tb']),
            "respRate" => intval($request['respiratory_rate']),
            "lingkarPerut" => intval($request['lp']),
            "heartRate" => intval($request['heart_rate']),
            "rujukBalik" => 0,
            "kdTkp" => "10"
        ];

        $myvars = json_encode($reqBody);

        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_HTTPHEADER, $reqHeader);
        curl_setopt($session, CURLOPT_VERBOSE, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST'); // Metode permintaan POST
        curl_setopt($session, CURLOPT_POSTFIELDS, $myvars); // Data dalam format JSON

        // ------------------------------------VALID SSL------------------------------------
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        // ------------------------------------VALID SSL------------------------------------

        $responseAll = curl_exec($session);

        $json = json_decode($responseAll, true);

        $message = $json['metaData'];
        $response = $json['response'];

        $decryptedResponse = $this->stringDecrypt($cid . $ckey . $timestamp, $response);

        $response_compress = $this->decompress($decryptedResponse);

        $responseFinal = json_decode($response_compress, true);

        return [
            'msg' => $message,
            'response' => $responseFinal
        ];

        // return ($myvars);
    }


    public function stringDecrypt($key, $string = '')
    {
        // print_r($string);die;
        $encrypted_method = 'AES-256-CBC';

        $key_hash = hex2bin(hash('sha256', $key));

        // taro iv sini
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypted_method, $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    function decompress($string)
    {
        return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
    }
}
