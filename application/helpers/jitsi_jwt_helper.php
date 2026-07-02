<?php

use Firebase\JWT\JWT;

/**
 * Generate Jitsi JWT Token untuk digunakan pada JAAS (Jitsi as a Service)
 *
 * @param string $room_name       Nama ruangan video call
 * @param string $user_name       Nama pengguna (dokter, faskes, tamu)
 * @param bool   $moderator       Apakah user moderator atau tamu
 * @param string $email           Email pengguna
 * @param string $user_id         ID unik pengguna (bisa dari sistem login Anda)
 * @param int    $duration        Durasi token aktif dalam detik (default 3600 = 1 jam)
 *
 * @return string                 JWT token yang siap digunakan di URL
 */

// function generate_jitsi_token($room_name, $user_name = 'Guest', $moderator = false)
function generate_jitsi_token($room_name, $user_name = 'Guest', $moderator = false, $email = '', $user_id = '', $duration = 3600)
{
    // App ID dari JAAS (digunakan di field 'sub')
    $app_id = 'vpaas-magic-cookie-e3b2bebde19e48619854b40100e97c96';

    // Key ID dari API Key (untuk JWT header 'kid')
    $api_key_id = 'vpaas-magic-cookie-e3b2bebde19e48619854b40100e97c96/03eb1a';

    // Arahkan ke file private key Anda (format .pem)
    $private_key_path = APPPATH . 'keys/private_key.pem';
    // $private_key = file_get_contents(APPPATH . 'keys/private_key.pem');


    if (!file_exists($private_key_path)) {
        throw new Exception("Private key tidak ditemukan di: " . $private_key_path);
    }
    $private_key = file_get_contents($private_key_path);

    $now = time();

    $payload = [
        "aud" => "jitsi",
        "iss" => "chat",
        "sub" => $app_id,
        "room" => $room_name,
        "exp" => $now + $duration,
        "nbf" => $now,
        "context" => [
            "features" => [
                // "livestreaming" => true,
                // "recording" => true,
                // "transcription" => true,
                "outbound-call" => true,
                "sip-outbound-call" => false,
                // "file-upload" => true,
                "list-visitors" => false,
                "hidden-from-recorder" => false,
                "flip" => false
            ],
            "user" => [
                "name" => $user_name,
                "moderator" => $moderator,
                "email" => $email,
                "id" => $user_id,
                "avatar" => ""
            ]
        ]
    ];

    // return JWT::encode($payload, $private_key, 'RS256');
    return JWT::encode($payload, $private_key, 'RS256', $api_key_id);
}
