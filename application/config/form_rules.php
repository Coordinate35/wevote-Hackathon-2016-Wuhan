<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'get_user_info' => array( 
        array(
            'field' => 'code',
            'label' => 'Code',
            'rules' => 'trim|required|max_length[255]'
        ),
        array(
            'field' => 'state',
            'label' => 'State',
            'rules' => 'trim|max_length[255]'
        )
    ),
    'init_vote' => array(
        array(
            'field' => 'theme',
            'label' => 'Theme',
            'rules' => 'trim|required|max_length[20]'
        ),
        array(
            'field' => 'theme_description',
            'label' => 'Theme description',
            'rules' => 'trim|required|max_length[100]'
        ),
        array(
            'field' => 'day',
            'label' => 'Day',
            'rules' => 'trim|required|is_numeric'
        ),
        array(
            'field' => 'hour',
            'label' => 'Hour',
            'rules' => 'trim|required|is_numeric'
        ),
        array(
            'field' => 'minute',
            'label' => 'Minute',
            'rules' => 'trim|required|is_numeric'
        ),
        array(
            'field' => 'member_uplimit',
            'label' => 'Member uplimit',
            'rules' => 'trim|is_numeric'
        ),
        array(
            'field' => 'will_notice',
            'label' => 'Will notice',
            'rules' => 'trim|required|less_than_equal_to[1]|greater_than_equal_to[0]'
        ),
        array(
            'field' => 'is_viewable',
            'label' => 'Viewable',
            'rules' => 'trim|required|less_than_equal_to[1]|greater_than_equal_to[0]'
        ),
        array(
            'field' => 'type',
            'lable' => 'Type',
            'rules' => 'trim|required|less_than_equal_to[2]|greater_than_equal_to[0]'
        ),
        array(
            'field' => 'option[]',
            'label' => 'Option',
            'rules' => 'required'
        ),
        array(
            'field' => 'anonymous',
            'label' => 'Anonymous',
            'rules' => 'trim|required|less_than_equal_to[1]|greater_than_equal_to[0]'
        )
    ),
    'get_subject' => array(
        array(
            'field' => 'room_id',
            'label' => 'Room ID',
            'rules' => 'trim|required|is_numeric'
        )
    ),
    'vote' => array(
        array(
            'field' => 'items[]',
            'label' => 'Items',
            'rules' => 'required'
        )
    ),
    'get_status' => array(
        array(
            'field' => 'room_id',
            'label' => 'Room ID',
            'rules' => 'trim|required|is_numeric'
        )
    )
);
