<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include(APPPATH.'config/database_const.php');

include(APPPATH.'config/http_const.php');

include(APPPATH.'config/http_type_const.php');

include(APPPATH.'config/wechat_const.php');

include(APPPATH.'config/server_const.php');

class MY_Controller extends CI_Controller {

    protected $response;

    public function __construct() {
        parent::__construct();

        date_default_timezone_set('Asia/Shanghai');

        $this->config->load('form_rules', TRUE);

        $this->load->library('form_validation');

        $this->load->helper('MY_global');
        $this->load->helper('url');

        $this->lang->load('prompt', 'chinese');

        $this->load->library('session');

        $this->response = array();
        //$this->session->set_userdata('user_id', 1);
    }

    public function make_bad_request_response() {
        $this->response['error'] = $this->lang->line('prompt_bad_request');
        api_output($this->response, HTTP_BAD_REQUEST);
    }

    public function make_internal_server_error_response() {
        $this->response['error'] = $this->lang->line('prompt_internal_server_error');
        api_output($this->response, HTTP_INTERNAL_SERVER_ERROR);
    }
}
