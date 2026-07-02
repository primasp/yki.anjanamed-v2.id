<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function getAllUser()
    {
        $query = $this->db->get('pc01_gen_user_data');
        return $query->result_array();
    }

    public function savelogin($data)
    {

        $ip = $data['ip'];

        $sysdisplay = $this->db->query(
            "SELECT COUNT(ip_pc) AS ada FROM pc01_co_sys_display WHERE ip_pc = ? AND aktif = '1'",
            [$ip]
        )->row_array();
        // return var_dump($sysdisplay['ada']);
        // die;

        if ($sysdisplay['ada'] > 0) {
            // Cek apakah ada login aktif hari ini
            $loginip = $this->db->query(
                "SELECT * FROM pc01_co_login_dokter 
             WHERE ip = ? AND DATE(created_date) = CURRENT_DATE AND aktif = '1'",
                [$ip]
            )->row_array();

            if (!empty($loginip)) {
                // Update aktif = 0 jika sudah ada login
                $this->db->where('ip', $ip);
                $this->db->where('aktif', '1');
                $this->db->where("DATE(created_date) = CURRENT_DATE", null, false); // raw SQL
                $this->db->update('pc01_co_login_dokter', ['aktif' => '0']);
            }

            // return var_dump($loginip);
            // die;





            $this->db->insert('pc01_co_login_dokter', $data);
        }
    }


    public function getUserByUserId($userId)
    {
        return $this->db->get_where('pc01_gen_user_data', ['user_id' => $userId])->row();
    }

    public function addUser($data)
    {
        return $this->db->insert('pc01_gen_user_data', $data);
    }

    public function get_user_by_email($email)
    {
        return $this->db->get_where('pc01_gen_user_data', ['email' => $email])->row();
    }

    public function get_user_by_reset_code($reset_code)
    {
        return $this->db->get_where('pc01_gen_user_data', ['reset_code' => $reset_code])->row();
    }

    public function update_password($user_id, $password)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('pc01_gen_user_data', ['password' => $password, 'reset_code' => NULL]);
    }

    public function set_reset_code($user_id, $reset_code)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('pc01_gen_user_data', ['reset_code' => $reset_code]);
    }

    public function get_user_by_id($user_id)
    {
        $this->db->select('a.*, b.role_name'); // Ganti 'role_name' sesuai kebutuhan  
        $this->db->from('pc01_gen_user_data a');
        $this->db->join('pc01_gen_role_ms b', 'a.role_id = b.role_id', 'inner');
        $this->db->where('a.aktif', '1');
        $this->db->where('a.user_id', $user_id);
        return $this->db->get()->row_array();
    }
}
