<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'um');
        $this->load->library('email');
        $this->load->helper(array('url', 'form'));
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('userIdTxt', 'User Name', 'required');
        $this->form_validation->set_rules('passwordTxt', 'Password', 'required');



        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login_form');
        } else {
            $userId = $this->input->post('userIdTxt');
            $password = $this->input->post('passwordTxt');
            $device_id = $this->input->post('device_id') ?? 'unknown_device';

            $user = $this->um->getUserByUserId($userId);
            // return var_dump($user);
            // die;

            if ($user && password_verify($password, $user->password)) {
                if ($user->aktif === '1') {
                    $userdata = [
                        'user_id_pc' => $user->user_id,
                        'nama_pc' => $user->nama,
                        'role_id_pc' => $user->role_id,
                        'logged_in_pc' => TRUE
                    ];



                    $this->session->set_userdata($userdata);



                    switch ($user->role_id) {
                        case "RU0001":
                            redirect('Admin');
                            // redirect('Admin_C');
                            break;
                        case "RU0002":
                            redirect('Admission');
                            // redirect('PetugasRegistrasi_C');
                            break;
                        case "RU0003":

                            if (!empty($user->dokter_id)) {
                                $dokter_id = $user->dokter_id;



                                // Ambil def_poli_id dari tabel master dokter
                                $q = $this->db->query("SELECT def_poli_id 
                                FROM pc01_med_dokter_ms 
                                WHERE dokter_id = '$dokter_id' AND aktif = '1'")->row();

                                if ($q && !empty($q->def_poli_id)) {

                                    $def_poli_id = $q->def_poli_id;

                                    // Siapkan data login
                                    $datalogin = [
                                        'poli_id' => $def_poli_id,
                                        'dokter_id' => $dokter_id,
                                        // 'ip' => $_SERVER['REMOTE_ADDR'],
                                        'ip' => $device_id

                                    ];

                                    // return var_dump($datalogin);
                                    // die;


                                    // Simpan login ke tabel
                                    $this->um->savelogin($datalogin);

                                    // return var_dump($test);
                                    // die;
                                }
                            }
                            redirect('Dokter-Dashboard');
                            // redirect('User_C');
                            break;
                        case "RU0004":
                            redirect('Validasi-Obat');
                            // redirect('User_C');
                            break;
                        case "RU0005":
                            redirect('Kasir');
                            // redirect('User_C');
                            break;
                        case "RU0006":
                            redirect('Perawat');
                            // redirect('User_C');
                            break;
                        default:
                            redirect('AuthController');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Account not activated. Please check your email.');
                    redirect('AuthController');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid username or password');
                redirect('AuthController');
            }
        }

        // $this->load->view('welcome_message');
    }


    public function forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/forgot_form');
        } else {
            $email = $this->input->post('email');
            $user = $this->um->get_user_by_email($email);
            if ($user) {
                $reset_code = md5(uniqid(rand(), true));

                $this->um->set_reset_code($user->user_id, $reset_code);
                if ($this->_sendEmail($email, $reset_code, 'forgot')) {
                    $this->session->set_flashdata('message', 'Password reset link sent to your email.');
                    redirect('Forgot-Password');
                } else {
                    $this->session->set_flashdata('error', 'Password reset failed. Unable to send reset password by email. Please try again.');
                    redirect('Forgot-Password');
                }
                // return var_dump($user->user_id);
                // die;
            } else {
                $this->session->set_flashdata('error', 'Email not found');
                redirect('Forgot-Password');
            }
        }
    }

    public function _sendEmail($email, $token, $type)
    {
        $mail = new PHPMailer(true);
        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            // $mail->Username = 'primacare@anjana.co.id';
            // $mail->Password = 'Primacare@123';
            $mail->Username = 'admin@anjana.co.id';
            $mail->Password = 'AsiahAmien@23';
            // $mail->SMTPSecure = 'tls'; // Gunakan 'tls' untuk port 587
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 'ssl'
            // $mail->Port = 587;
            $mail->Port       = 465;
            $mail->isHTML(true);

            // Aktifkan debug output SMTP
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';

            $mail->setFrom('admin@anjana.co.id', 'ANJANA BHAKTI NEGERI');
            $mail->addAddress($email);

            if ($type == 'forgot') {
                $mail->Subject = 'Password Reset';
                $mail->Body = 'Click this link to verify your account: <a href="' . base_url() . 'Reset-Password/' . urlencode($token) . '">Activate</a>';
            }

            // Kirim Email
            if ($mail->send()) {
                return true;
            } else {
                log_message('error', 'Failed to send activation email to ' . $email . '. ' . $mail->ErrorInfo);
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        }
    }

    public function reset_password($reset_code = null)
    {
        // $this->load->view('auth/reset_passwd_form');
        if (!$reset_code) {
            show_404();
        }
        $user = $this->um->get_user_by_reset_code($reset_code);
        if ($user) {
            $this->form_validation->set_rules('password', 'Password', 'required');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('auth/reset_passwd_form', ['reset_code' => $reset_code]);
            } else {
                $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
                $this->um->update_password($user->user_id, $password);
                $this->session->set_flashdata('message', 'Password reset successfully! You can now log in.');
                redirect('Login');
            }
        } else {
        }
    }

    public function register()
    {

        $this->form_validation->set_rules('fullNameTxt', 'Full Name', 'required');
        $this->form_validation->set_rules('userNameTxt', 'User Name', 'required|is_unique[pc01_gen_user_data.user_id]');
        $this->form_validation->set_rules('emailTxt', 'Email', 'required|valid_email|is_unique[pc01_gen_user_data.email]');

        $this->form_validation->set_rules('password', 'Password', 'required|callback_valid_password');
        $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|matches[password]');


        // return var_dump($this->form_validation->set_rules('password', 'Password', 'required|callback_valid_password'));
        // die;
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register_form');
        } else {


            $fullName = $this->input->post('fullNameTxt');
            $userName = $this->input->post('userNameTxt');
            $email = $this->input->post('emailTxt');
            $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            $activationCode = md5(uniqid(rand(), true));


            $data = [
                'nama' => $fullName,
                'user_id' => $userName,
                'email' => $email,
                'password' => $password
                // 'activation_code' => $activationCode
            ];

            if ($this->um->addUser($data)) {
                $this->session->set_flashdata('message', 'Registration successful! ');

                redirect('AuthController');
            } else {
                $this->session->set_flashdata('error', 'Registration failed. Please try again.');
                redirect('Register');
            }
        }
    }


    public function valid_password($password)
    {
        $password = trim($password);

        // return var_dump($password);
        // die;

        if (strlen($password) < 8) {
            $this->form_validation->set_message('valid_password', 'The {field} must be at least 8 characters in length.');
            return FALSE;
        }

        if (!preg_match('#[0-9]+#', $password)) {
            $this->form_validation->set_message('valid_password', 'The {field} must contain at least one number.');
            return FALSE;
        }

        if (!preg_match('#[a-zA-Z]+#', $password)) {
            $this->form_validation->set_message('valid_password', 'The {field} must contain at least one letter.');
            return FALSE;
        }

        return TRUE;
    }



    public function logout()
    {
        $this->session->unset_userdata(['user_id_pc', 'username_pc', 'role_id_pc', 'logged_in_pc']);
        $this->session->set_flashdata('message', 'Logged out successfully');
        redirect('AuthController');
    }



    public function send()
    {
        // Buat instance PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username = 'primacare@anjana.co.id';
            $mail->Password = 'Primacare@123';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->isHTML(true);

            // Pengaturan Email
            $mail->setFrom('primacare@anjana.co.id', 'ANJANA BHAKTI NEGERI');
            $mail->addAddress('syahputraprima@gmail.com', 'Recipient Name'); // Email tujuan

            $mail->Subject = 'Test Email dari PHPMailer';
            $mail->Body    = '<h1>Berhasil Mengirim Email!</h1>';
            $mail->isHTML(true);

            // Kirim Email
            if ($mail->send()) {
                echo "Email berhasil dikirim!";
            }
        } catch (Exception $e) {
            echo "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    }
}
