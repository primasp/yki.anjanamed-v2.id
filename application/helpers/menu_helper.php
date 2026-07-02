<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_menu')) {
    function get_menu()
    {
        $ci = &get_instance();
        $role_id = $ci->session->userdata('role_id_pc'); // Role ID dari session
        $current_url = current_url(); // URL aktif

        $ci->load->database();
        $ci->db->select('user_menu_id, label, url, parent_id, icon');
        $ci->db->from('pc01_gen_user_menus');
        $ci->db->where('role_id', $role_id);
        $ci->db->where('aktif', '1');
        $ci->db->order_by('parent_id', 'asc');
        $ci->db->order_by('user_menu_id', 'asc');
        $query = $ci->db->get();

        // return var_dump($query->result());
        // die;

        $menu = [];
        $submenu = [];

        foreach ($query->result() as $row) {
            $url = base_url($row->url); // Mengonversi ke URL absolut
            $is_active = ($current_url == $url); // Menentukan apakah URL cocok dengan current_url

            if ($row->parent_id == NULL) {
                // Menu induk
                $menu[$row->user_menu_id] = [
                    'user_menu_id' => $row->user_menu_id,
                    'label' => $row->label,
                    'url' => $url,
                    'icon' => $row->icon,
                    'submenu' => [],
                    'is_active' => $is_active
                ];
            } else {
                // Submenu
                $submenu[$row->parent_id][] = [
                    'label' => $row->label,
                    'url' => $url,
                    'icon' => $row->icon,
                    'is_active' => $is_active
                ];
            }
        }

        // Menghubungkan submenu dengan menu induk dan menentukan aktif status
        foreach ($submenu as $parent_id => $items) {
            if (isset($menu[$parent_id])) {
                $menu[$parent_id]['submenu'] = $items;

                // Menandai menu induk sebagai aktif jika salah satu submenu aktif
                foreach ($items as $item) {
                    if ($item['is_active']) {
                        $menu[$parent_id]['is_active'] = true;
                        break;
                    }
                }
            }
        }

        return array_values($menu);
    }
}
