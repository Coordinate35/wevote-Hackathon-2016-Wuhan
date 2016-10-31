<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vote extends MY_Controller {

    private $day_second;
    private $hour_second;
    private $min_second;
    private $user_id;
    private $theory_sum;

    public function __construct() {
        parent::__construct();
        $this->day_second = 86400;
        $this->hour_second = 3600;
        $this->min_second = 60;
        $this->theory_sum = 100;
        $this->month_second = 2592000;
        $this->user_id = $this->session->userdata('user_id');

        $this->load->model('user_model', 'user');
        $this->load->model('vote_model', 'vote');
    }

    public function get() {
        $type = $this->input->get('type', TRUE);
        switch ($type) {
            case HTTP_GET_VOTE_SUBJECT:
                $this->_get_subject();
                break;
            case HTTP_GET_STATUS:
                $this->_get_status();
                break;
            case HTTP_GET_MY_LAUNCH:
                $this->_get_my_launch();
                break;
            case HTTP_GET_MY_VOTE:
                $this->_get_my_vote();
                break;
            default:
                $this->make_bad_request_response();
        }
    }

    public function post() {
        $type = $this->input->post('type', TRUE);
        switch ($type) {
            case HTTP_POST_TYPE_INIT_SUBJECT:
                $this->_init_vote();
                break;
            case HTTP_POST_TYPE_VOTE_ACTION:
                $this->_vote();
            default:
                $this->make_bad_request_response();
        }
    }

    private function _get_my_vote() {
        $this->response['my_votes'] = array();
        $votes = $this->vote->get_votes_by_user_id($this->user_id);
        if (FALSE === $votes) {
            $this->make_internal_server_error_response();
        }
        $items_id = $this->_filter_item_id($votes);
        $rooms_id = $this->vote->get_room_id_by_item_id($items_id);
        if (FALSE === $rooms_id) {
            $this->make_internal_server_error_response();
        }
        foreach ($rooms_id as $key => $room_id) {
            $room_info = $this->vote->get_room_info_by_id($room_id['room_id']);
            if (FALSE === $room_info) {
                $this->make_internal_server_error_response();
            }
            $is_over = $this->_is_over($room_id['room_id']);
            $launcher_info = $this->user->get_user_by_id($this->user_id);
            if (FALSE === $launcher_info) {
                $this->make_internal_server_error_response();
            }
            $launch_time = $room_info[0]['end_time'] - $room_info[0]['last_time'];
            $this->response['my_votes'][] = array(
                'is_over' => $is_over,
                'theme' => $room_info[0]['theme'],
                'theme_description' => $room_info[0]['theme_description'],
                'launch_time' => $launch_time,
                'launcher_img' => $launcher_info[0]['headimgurl'],
                'nickname' => $launcher_info[0]['nickname']
            );
        }
        api_output($this->response, HTTP_OK);
    }

    private function _get_my_launch() {
        $votes = $this->vote->get_my_launch($this->user_id);
        if (FALSE === $votes) {
            $this->make_internal_server_error_response();
        }
        $this->response['my_launch'] = array();
        foreach ($votes as $key => $vote) {
            $is_over = $this->_is_over($vote['room_id']);
            $launch_time = $vote['end_time'] - $vote['last_time'];
            $this->response['my_launch'][] = array(
                'is_over' => $is_over,
                'theme' => $vote['theme'],
                'theme_description' => $vote['theme_description'],
                'launch_time' => $launch_time
            );
        }

        api_output($this->response, HTTP_OK);
    }

    private function _get_status() {
        $get_method_data = $_GET;
        $get_status_rules = $this->config->item('get_status', 'form_rules');
        $this->form_validation->set_data($get_method_data);
        $this->form_validation->set_rules($get_status_rules);
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }

        $room_id = $this->input->get('room_id', TRUE);

        $room_info = $this->vote->get_room_info_by_id($room_id);
        if (FALSE === $room_info) {
            $this->make_internal_server_error_response();
        }

        $items = $this->vote->get_items_by_room_id($room_id);
        if (FALSE === $items) {
            $this->make_internal_server_error_response();
        }

        $items_id = $this->_filter_item_id($items);
        $vote_actions = $this->vote->get_user_items_by_items_id($items_id);
        if (FALSE === $vote_actions) {
            $this->make_internal_server_error_response();
        }
        $buffer = array();
        foreach ($items as $key => $value) {
            $this->response['items'][$key] = array(
                'item_id' => $value['item_id'],
                'content' => $value['content'],
                'user_value' => 0,
                'total_value' => 0
            );
        }
        foreach ($vote_actions as $vote_action_key => $vote_action) {
            foreach ($this->response as $item_key => $item_info) {
                if ($item_info['item_id'] == $vote_action['item_id']) {
                    if ($this->user_id == $vote_action['user_id']) {
                        $this->response[$item_key]['user_value'] = $vote_action['value'];
                    }
                    $this->response[$item_key]['total_value'] += $vote_action['value'];
                }
            }
        }
        $this->response['theme'] = $room_info[0]['theme'];
        $this->response['theme_description'] = $room_info[0]['theme_description'];
        $this->response['end_time'] = $room_info[0]['end_time'];
        api_output($this->response, HTTP_OK);
    }

    private function _vote() {
        $vote_rules = $this->config->item('vote', 'form_rules');
        $this->form_validation->set_rules($vote_rules);
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }

        $items = $this->input->post('items', TRUE);
        $sum = 0;
        $time = time();
        foreach ($items as $key => $item) {
            $sum += $item['value'];
        }
        foreach ($items as $key => $item) {
            $items[$key]['value'] = $items[$key]['value'] / $sum;
            $items[$key]['vote_time'] = $time;
            $items[$key]['user_id'] = $this->user_id;
        }
        if (FALSE === $this->vote->add_vote($items)) {
            $this->make_internal_server_error_response();
        }
        api_output($this->response, HTTP_OK);
    }

    private function _get_subject() {
        $get_subject_rules = $this->config->item('get_subject', 'form_rules');
        $get_method_data = $_GET;
        $this->form_validation->set_data($get_method_data);
        $this->form_validation->set_rules($get_subject_rules);   
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }

        $room_id = $this->input->get('room_id', TRUE);

        $is_voted = $this->_is_voted($room_id);
        if (TRUE === $is_voted) {
            $is_allowed_to_view = $this->_is_allow_to_view($room_id);
            if (FALSE === $is_allowed_to_view) {
                $this->make_not_viewable_response();
            } else {
                $this->_get_vote_result($room_id);
            }
        } else {
            $is_over = $this->_is_over($room_id);
            if (FALSE === $is_over) {
                $this->_get_vote_info($room_id);
            } else {
                $this->make_not_votable_for_over_response();
            }
        }
    }

    private function _get_vote_info() {
        $get_method_data = $_GET;
        $get_vote_info_rules = $this->config->item('get_vote_info');
        $this->form_validation->set_data($get_method_data);
        $this->form_validation->set_rules($get_vote_info_rules);
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }

        $room_id = $this->input->get('room_id', TRUE);

        $is_over = $this->_is_over($room_id);
        if (TRUE === $is_over) {
            $this->make_not_votable_for_over_respones();
        }

        $items = $this->vote->get_items_by_room_id($room_id);
        if (FALSE === $items) {
            $this->make_internal_server_error_response();
        }

        foreach ($items as $key => $value) {
            $this->response[$key] = array(
                'item_id' => $value['item_id'],
                'content' => $value['content']
            );
        }
        api_output($this->response, HTTP_OK);
    }

    public function make_not_votable_for_over_response() {
        $this->response = array(
            'code' => 1,
            'msg' => $this->lang->line('not_votable_for_over_response')
        );
        api_output($this->response, HTTP_FORBIDDEN);
    }

    private function _response_show_vote_info($room_id) {
        $this->response = array(
            'code' => 2,
            'msg' => $this->lang-line('show_vote_info_to_vote')
        );
        api_output($this->response, HTTP_OK);
    }

    private function _is_over($room_id) {
        $is_reach_member_uplimit = $this->_is_reach_member_uplimit($room_id);
        $is_reach_time_limit = $this->_is_reach_time_limit($room_id);
        if ((TRUE === $is_reach_member_uplimit) || (TRUE === $is_reach_time_limit)) {
            return TRUE;
        }
        return FALSE;
    }

    private function _is_reach_time_limit($room_id) {
        $room_info = $this->vote->get_room_info_by_id($room_id);
        if (FALSE === $room_info) {
            $this->make_internal_server_error_response();
        }
        $current_time = time();
        if ($current_time > $room_info[0]['end_time']) {
            return TRUE;
        }
        return FALSE;
    }

    private function _is_reach_member_uplimit($room_id) {
        $room_info = $this->vote->get_room_info_by_id($room_id);
        if (FALSE === $room_info) {
            $this->make_internal_server_error_response();
        }

        $items_info = $this->vote->get_items_by_room_id($room_id);
        if (count($items_info) > 0) {
            if (FALSE === $items_info) {
                $this->make_internal_server_error_response();
            }
            $items_id = $this->_filter_item_id($items_info);
            $totol_number = $this->vote->get_room_user_item_number($items_id);
            if (FALSE === $totol_number) {
                $this->make_internal_server_error_response();
            }
            if ($totol_number / count($items_id) > $room_info[0]['member_uplimit']) {
                return TRUE;
            }
            return FALSE;
        }
        return TRUE;
    }

    private function _response_show_vote_status($room_id) {
        $this->response = array(
            'code' => 4,
            'msg' => $this->lang->line('show_current_vote_status')
        );
        api_output($this->response, HTTP_OK);
    }

    public function make_not_viewable_response() {
        $this->response = array(
            'code' => 2,
            'msg' => $this->lang->line('not_viewable')
        );
        api_output($this->response, HTTP_FORBIDDEN);
    }

    private function _is_allow_to_view($room_id) {
        $room_info = $this->vote->get_room_info_by_id($room_id);
        if (FALSE === $room_info) {
            $this->make_internal_server_error();
        }
        if (($room_info['is_viewable'] == 0) && ($this->user_id != $room_info['creator_id'])) {
            return FALSE;
        }
        return TRUE;
    }

    private function _is_voted($room_id) {
        $items_info = $this->vote->get_items_by_room_id($room_id);
        if (FALSE === $items_info) {
            $this->make_internal_server_error();
        }
        $items_id = $this->_filter_item_id($items_info);
        $user_items_info = $this->vote->get_user_items_by_user_id_items_id($this->user_id, $items_id);
        if (FALSE === $user_items_info) {
            $this->make_internal_server_error();
        }
        if (count($user_items_info) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    private function _filter_item_id($items_info) {
        $item_ids = array();
        foreach ($items_info as $item_info) {
            $item_ids[] = $item_info['item_id'];
        }
        return $item_ids;
    }

    private function _init_vote() {
        //$json_str = file_get_contents('php://input');
		//$data = json_decode($json_str, TRUE);
        $init_vote_rules = $this->config->item('init_vote', 'form_rules');
        //$this->form_validation->set_data($data);
        $this->form_validation->set_rules($init_vote_rules);
        if (FALSE === $this->form_validation->run()) {
            $this->make_bad_request_response();
        }
        $option = $this->input->post('option[]');
        $day = $this->input->post('day', TRUE);
        $hour = $this->input->post('hour', TRUE);
        $minute = $this->input->post('minute', TRUE);

        $last_time = $this->_get_last_time($day, $hour, $minute);
        $end_time = time() + $last_time;

        $vote_info = array(
            'theme' => $this->input->post('theme', TRUE),
            'theme_description' => $this->input->post('theme_description', TRUE),
            'member_uplimit' => $this->input->post('member_uplimit', TRUE),
            'will_notice' => $this->input->post('will_notice', TRUE),
            'anonymous' => $this->input->post('anonymous', TRUE),
            'is_viewable' => $this->input->post('is_viewable', TRUE),
            'type' => $this->input->post('type', TRUE),
            'last_time' => $last_time,
            'end_time' => $end_time,
            'creator_id' => $this->user_id
        );

        $room_id = $this->vote->launch_vote($vote_info, $option);
        if (FALSE === $room_id) {
            $this->make_internal_server_error_response();
        }
        $this->response['room_id'] = $room_id;
        api_output($this->response, HTTP_OK);
    }

    private function _get_last_time($day, $hour, $minute) {
        $last_time = $day * $this->day_second + $hour * $this->hour_second + $minute * $this->min_second;
        return $last_time;
    }
}
