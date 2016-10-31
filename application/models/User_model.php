<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = TABLE_USER;
    }

    public function upsert_user($data) {
        $user_info = $this->get_user_by_openid($data['openid']);
        if (0 < count($user_info)) {
            $condition = array(
                'user_id' => $user_info['user_id']
            );
            if (FALSE === $this->update_entry($data, $condition)) {
                return FALSE;
            }
            return $user_info['user_id'];
        } else {
            if (FALSE === $this->insert_entry($data)) {
                return FALSE;
            }
            return $this->db->insert_id();
        }
    }

    public function get_user_by_openid($openid) {
        $condition = array(
            'openid' => $openid
        );
        $user_info = $this->db->get_where(TABLE_USER, $condition)->result_array();
		return $user_info;
    }

    public function get_user_by_id($user_id) {
        $condition = array(
            'user_id' => $user_id
        );
        $result = $this->db->get_where(TABLE_USER, $condition)->result_array();
        return $result;
    }
} 
