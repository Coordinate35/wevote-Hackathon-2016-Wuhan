<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $_table;

    public function __construct() {
        parent::__construct();

        $this->load->database();
    }

    public function insert_entry($data) {
        return $this->db->insert($this->_table, $data);
    }

    public function update_entry($data, $condition) {
        $this->db->where($condition);
        return $this->db->update($this->_table, $data);
    }
}
