<?php
defined('BASEPATH') OR exit('No direct script access allowed');

const WECHAT_APP_ID = '';
const WECHAT_APP_SECRET = '';

const WECHAT_AUTHORIZED_DOMAIN = '';
const WECHAT_AUTHORIZE_PROTOCOL = 'http://';
const WECHAT_GET_INFO_PROTOCOL = 'https://';
const WECHAT_CONTROLLER = 'wechat';
const WECHAT_GET_USER_INFO_METHOD = 'get_user_info';

const WECHAT_GET_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
const WECHAT_GET_CODE_SUFFIX ='#wechat_redirect';

const WECHAT_GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
const WECHAT_GET_USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';