<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model', 'user');
    }

    public function get_user_info() {
        $get_method_data = $_GET;
        $get_user_info_rules = $this->config->item('get_user_info', 'form_rules');
        $this->form_validation->set_data($get_method_data);
        $this->form_validation->set_rules($get_user_info_rules);
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }
        $state = $this->input->get('state', TRUE);
        $code = $this->input->get('code', TRUE);

        $get_access_token_result = $this->_get_code($code);
        $access_token = $get_access_token_result['access_token'];
        $openid = $get_access_token_result['openid'];

        $user_info = $this->_get_user_info($access_token, $openid);

        $data = array(
            'openid' => $user_info['openid'],
            'nickname' => $user_info['nickname'],
            'sex' => $user_info['sex'],
            'city' => $user_info['city'],
            'province' => $user_info['province'],
            'country' => $user_info['country'],
            'headimgurl' => $user_info['headimgurl']
        );
        $user_id = $this->user->upsert_user($data);
        if (FALSE === $user_id) {
            $this->make_internal_server_error();
        }
        $this->session->set_userdata('user_id', $user_id);
        redirect($state);
    }

    private function _get_code($code) {
        $param = array(
            'appid' => WECHAT_APP_ID,
            'secret' => WECHAT_APP_SECRET,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );
        $url = WECHAT_GET_ACCESS_TOKEN_URL.'?'.http_build_query($param);
        $get_access_token_result = curl_get($url);
        if (FALSE === $get_access_token_result) {
            $this->make_internal_server_error();
        }
        return json_decode($get_access_token_result, TRUE);
    }

    private function _get_user_info($access_token, $openid) {
        $param = array(
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN'
        );
        $url = WECHAT_GET_USER_INFO_URL.'?'.http_build_query($param);
        $user_info = curl_get($url);
        if (FALSE === $user_info) {
            $this->make_internal_server_error();
        }
        return json_decode($user_info, TRUE);
    }
}
