<?php
defined('BASEPATH') or exit('No direct script access allowed');
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


$env = 'yki';

switch ($env) {
    case 'pro':
        define('BPJS_CID', '12473');
        define('BPJS_CKEY', '0tD9679365');
        define('BPJS_USER_KEY', '2f537fd407be450d11216521d6b80a2');
        define('BPJS_BASE_URL_PCARE', 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest');
        define('BPJS_AUTHORIZATION', 'Base64 a2FpbmE6S2FpbmEyMDI0ISEhOjA5NQ==');
        define('BPJS_FASKES', '0115U014');


        break;
    case 'yki':
        // define('SATUSEHAT_CLIENT_ID', 'cXRl9t2Ks0lzX8hAMGa96Hw9VzAAJEKIAXIFpXyINrV4o7mF'); // isi dengan yang sesuai
        define('SATUSEHAT_CLIENT_ID', 'cXRl9t2Ks0lzX8hAMGa96Hw9VzAAJEKIAXIFpXyINrV4o7m22F'); // Id DIsalahin Untuk Testing
        define('SATUSEHAT_CLIENT_SECRET', 'ZW9jCSZ0ewxMOa4KPg3QlwgMC794S3qpEDSzdR9oouEq3BYzpyAXrOkgKyIzcvc9'); // isi dengan yang sesuai
        define('SATUSEHAT_AUTH_URL', 'https://api-satusehat.kemkes.go.id/oauth2/v1');
        define('SATUSEHAT_BASE_URL', 'https://api-satusehat.kemkes.go.id/fhir-r4/v1');
        define('SATUSEHAT_CONSENT_URL', 'https://api-satusehat.kemkes.go.id/consent/v1');
        define('SATUSEHAT_ORG_ID', '100024498');


        break;
    case 'dev':
        define('BPJS_CID', '30917');
        define('BPJS_CKEY', '9pG4974010');
        define('BPJS_USER_KEY', '651e74ff1d0e906e4c3364eb05598ad0');
        define('BPJS_BASE_URL_PCARE', 'https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev');
        define('BPJS_AUTHORIZATION', 'Base64 ZHZscC5rYWluYTpLYWluYTIwMjQqOjA5NQ==');
        break;
}
