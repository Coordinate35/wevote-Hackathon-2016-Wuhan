<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vote_model extends MY_Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function add_vote($items) {
        return $this->db->insert_batch(TABLE_USER_ITEM, $items);
    }

    public function launch_vote($vote_info, $option) {
        $this->db->trans_start();
        
        $this->_table = TABLE_ROOM;
        $this->insert_entry($vote_info);
        $room_id = $this->db->insert_id();

        $options = array();
        foreach ($option as $key => $content) {
            $options[] = array(
                'room_id' => $room_id,
                'content' => $content['content']
            );
        }
        $this->db->insert_batch(TABLE_ITEM, $options);

        $this->db->trans_complete();
        if (FALSE === $this->db->trans_status()) {
            return FALSE;
        }
        return $room_id;
    }

    public function get_room_info_by_id($room_id) {
        $condition = array(
            'room_id' => $room_id
        );
        return $this->db->get_where(TABLE_ROOM, $condition)->result_array();
    }

    public function get_room_user_item_number($items_id) {
        $this->db->from(TABLE_USER_ITEM);
        $this->db->where_in('item_id', $items_id);
        return $this->db->count_all_results();
    }

    public function get_items_by_room_id($room_id) {
        $condition = array(
            'room_id' => $room_id
        );
        return $this->db->get_where(TABLE_ITEM, $condition)->result_array();
    }
    
    public function get_user_items_by_user_id_items_id($user_id, $items_id) {
        $this->db->select('item_id, user_id, value, vote_time');
        $this->db->from(TABLE_USER_ITEM);
        $this->db->where('user_id', $user_id);
        $this->db->where_in('item_id', $items_id);
        return $this->db->get()->result_array();
    }

    public function get_user_items_by_items_id($items_id) {
        $this->db->select('item_id, user_id, value, vote_time');
        $this->db->from(TABLE_USER_ITEM);
        $this->db->where_in('item_id', $items_id);
        return $this->db->get()->result_array();
    }

    public function get_my_launch($user_id) {
        $condition = array(
            'creator_id' => $user_id
        );
        $result = $this->db->get_where(TABLE_ROOM, $condition)->result_array();
        return $result;
    }

    public function get_votes_by_user_id($user_id) {
        $condition = array(
			'user_id' => $user_id
        );
        $result = $this->db->get_where(TABLE_USER_ITEM, $condition)->result_array();
        return $result;
    }

    public function get_room_id_by_item_id($items_id) {
	    $this->db->distinct();
        $this->db->select('room_id');
        $this->db->from(TABLE_ITEM);
        $result = $this->db->get()->result_array();
        return $result;
    }
}
