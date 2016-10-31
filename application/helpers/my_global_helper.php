<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('api_output')) {

    function api_output($output, $status_code, $method = array()) {
        $CI = &get_instance();
        $CI->output->set_status_header($status_code)->set_content_type("application/json", 'utf8');
        if ((is_array($method)) && (0 < count($method))) {
            $CI->output->set_header('Allow:'.implode(',', $method));
        }
        $CI->output->set_output(json_encode($output))->_display();
        exit;
    }
}

if ( ! function_exists('curl_get')) {
    
    function curl_get($url, array $get = array(), array $options = array()){
		$defaults = array(
			CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : '').
				http_build_query($get),
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 4
		);
		$ch = curl_init();
		curl_setopt_array($ch, ($options + $defaults));
		if( ! $result = curl_exec($ch))
		{
			log_message_error(LOG_TYPE_NETWORK_ERROR, curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}
}

if ( ! function_exists('curl_post')) {

    function curl_post($url, $post = array(), array $options = array()) {
		$defaults = array(
        	CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $post,
        	CURLOPT_HEADER => FALSE,
        	CURLOPT_FRESH_CONNECT => TRUE,
        	CURLOPT_FORBID_REUSE => TRUE,
        	CURLOPT_TIMEOUT => 4
    	);
    	$ch = curl_init();
    	curl_setopt_array($ch, ($defaults + $options));
    	if( ! $result = curl_exec($ch))
    	{
			log_message_error(LOG_TYPE_NETWORK_ERROR, curl_error($ch));
    	}
    	curl_close($ch);
    	return $result;
	}
}

if (! function_exists('get_current_url')) {
    
    function get_current_url() 
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}