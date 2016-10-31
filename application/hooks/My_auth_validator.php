<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class My_auth_validator {

    private $CI;
    private $url;
    private $origin_url;
    private $url_without_param;
    private $state;
    private $allow_access_with_auth = array();


    public function __construct() {
        $this->CI = &get_instance();

        $this->url = $this->CI->input->server('REQUEST_URI');
        $this->origin_url = $this->url;

        if ($pos = strpos($this->url, '?')) {
            $this->url = substr($this->url, 0, $pos);
        }
        $this->url_without_param = $this->url;
        $method = $this->CI->input->method();
        $this->url = $this->url.'/'.$method;

        if ('get' == $method) {
            $this->url = $this->url.'/'.$this->CI->input->get('type');
        }
        if ('post' == $method) {
            $this->url = $this->url.'/'.$this->CI->input->post('type');
        }

        $this->allow_access_with_auth = array(
            '/vote/get/',
            '/vote/post/'
        );
    }

    public function is_wechat_authorized() {
        if ( ! in_array($this->url, $this->allow_access_with_auth)) {
            return;
        }

        if ( ! $this->CI->session->userdata('user_id')) {
            $url = get_current_url();
            $this->state = $url;
            $this->auth();
        }
    }

    public function auth() {
        $param = array(
            'appid' => WECHAT_APP_ID,
            'redirect_uri' => WECHAT_AUTHORIZE_PROTOCOL.WECHAT_AUTHORIZED_DOMAIN.APP_DIFF_PREFIX.'/'.WECHAT_CONTROLLER.'/'.WECHAT_GET_USER_INFO_METHOD,
            'response_type' => 'code',
            'scope' => 'snsapi_userinfo',
            'state' => urlencode($this->state)
        );
        $url = WECHAT_GET_CODE_URL.'?'.http_build_query($param).WECHAT_GET_CODE_SUFFIX;
        redirect($url);
    }
}
