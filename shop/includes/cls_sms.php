<?php

/**
 * ECSHOP 短信模塊 之 模型（類庫）
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: cls_sms.php 17155 2010-05-06 06:29:05Z yehuaixiao $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

require_once(ROOT_PATH . 'includes/cls_transport.php');
require_once(ROOT_PATH . 'includes/shopex_json.php');

/* 短信模塊主類 */
class sms
{
    /**
     * 存放提供遠程服務的URL。
     *
     * @access  private
     * @var     array       $api_urls
     */
    var $api_urls   = array('register'          =>      'http://sms.ecshop.com/register.php',
                            'auth'              =>      'http://sms.ecshop.com/user_auth.php',
                            'send'              =>      'http://idx.sms.shopex.cn/service.php ',
                            'charge'            =>      'http://sms.ecshop.com/charge.php?act=charge_form',
                            'balance'           =>      'http://sms.ecshop.com/get_balance.php',
                            'send_history'      =>      'http://sms.ecshop.com/send_history.php',
                            'charge_history'    =>      'http://sms.ecshop.com/charge_history.php');
    /**
     * 存放MYSQL對象
     *
     * @access  private
     * @var     object      $db
     */
    var $db         = null;

    /**
     * 存放ECS對象
     *
     * @access  private
     * @var     object      $ecs
     */
    var $ecs        = null;

    /**
     * 存放transport對象
     *
     * @access  private
     * @var     object      $t
     */
    var $t          = null;

    /**
     * 存放程序執行過程中的錯誤信息，這樣做的一個好處是：程序可以支持多語言。
     * 程序在執行相關的操作時，error_no值將被改變，可能被賦為空或大等0的數字.
     * 為空或0表示動作成功；大於0的數字表示動作失敗，該數字代表錯誤號。
     *
     * @access  public
     * @var     array       $errors
     */
    var $errors  = array('api_errors'       => array('error_no' => -1, 'error_msg' => ''),
                         'server_errors'    => array('error_no' => -1, 'error_msg' => ''));

    /**
     * 構造函數
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        $this->sms();
    }

    /**
     * 構造函數
     *
     * @access  public
     * @return  void
     */
    function sms()
    {
        /* 由於要包含init.php，所以這兩個對象一定是存在的，因此直接賦值 */
        $this->db = $GLOBALS['db'];
        $this->ecs = $GLOBALS['ecs'];

        /* 此處最好不要從$GLOBALS數組裡引用，防止出錯 */
        $this->t = new transport(-1, -1, -1, false);
    }

    /**
     * 檢測是否已註冊或啟用短信服務
     *
     * @access  public
     * @return  boolean     已註冊或啟用短信服務返回true，否則返回false。
     */
    function has_registered()
    {
        $sql = 'SELECT `value`
                FROM ' . $this->ecs->table('shop_config') . "
                WHERE `code` = 'sms_shop_mobile'";

        $result = $this->db->getOne($sql);

        if (empty($result))
        {
            return false;
        }

        return true;
    }

    /**
     * 返回指定鍵名的URL
     *
     * @access  public
     * @param   string      $key        URL的名字，即數組的鍵名
     * @return  string or boolean       如果由形參指定的鍵名對應的URL值存在就返回該URL，否則返回false。
     */
    function get_url($key)
    {
        $url = $this->api_urls[$key];

        if (empty($url))
        {
            return false;
        }

        return $url;
    }

    /**
     * 獲得短信特服信息
     *
     * @access  public
     * @return  1-dimensional-array or boolean      成功返回短信特服信息，否則返回false。
     */
    function get_my_info()
    {
        $sql = 'SELECT `code`, `value`
                FROM ' . $this->ecs->table('shop_config') . "
                WHERE `code` LIKE '%sms\_%'";
        $result = $this->db->query($sql);

        $retval = array();
        if (!empty($result))
        {
            while ($temp_arr = $this->db->fetchRow($result))
            {
                $retval[$temp_arr['code']] = $temp_arr['value'];
            }

            return $retval;
        }

        return false;
    }

    /**
     * 獲得當前處於會話狀態的管理員的郵箱
     *
     * @access  private
     * @return  string or boolean       成功返回管理員的郵箱，否則返回false。
     */
    function get_admin_email()
    {
        $sql = 'SELECT `email` FROM ' . $this->ecs->table('admin_user') . " WHERE `user_id` = '" . $_SESSION['admin_id'] . "'";
         $email = $this->db->getOne($sql);

         if (empty($email))
         {
            return false;
         }

         return $email;
    }

    /**
     * 取出管理員的郵箱及網店域名
     *
     * @access  public
     * @return  void
     */
    function get_site_info()
    {
        /* 獲得當前處於會話狀態的管理員的郵箱 */
        $email = $this->get_admin_email();
        $email = $email ? $email : '';
        /* 獲得當前網店的域名 */
        $domain = $this->ecs->get_domain();
        $domain = $domain ? $domain : '';

        /* 賦給smarty模板 */
        $sms_site_info['email'] = $email;
        $sms_site_info['domain'] = $domain;

        return $sms_site_info;
    }

    /**
     * 註冊短信服務
     *
     * @access  public
     * @param   string      $email          帳戶信息
     * @param   string      $password       密碼，未經MD5加密
     * @param   string      $domain         域名
     * @param   string      $phone          商家註冊時綁定的手機號碼
     * @return  boolean                     註冊成功返回true，失敗返回false。
     */
    function register($email, $password, $domain, $phone)
    {
        /* 檢查註冊信息 */
        if (!$this->check_register_info($email, $password, $domain, $phone))
        {
            $this->errors['server_errors']['error_no'] = 1;//註冊信息無效

            return false;
        }

        /* 獲取API URL */
        $url = $this->get_url('register');
        if (!$url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }

        $params = array('email' => $email,
                        'password' => $password,
                        'domain' => $domain);
        /* 發送HTTP請求 */
        $response = $this->t->request($url, $params);
        $http_body = $response['body'];
        if (!$response || !$http_body)
        {
            $this->errors['server_errors']['error_no'] = 7;//HTTP響應體為空

            return false;
        }

        /* 更新最後訪問API的時間 */
        $this->update_sms_last_request();

        /* 解析XML文本串 */
        $xmlarr = $this->xml2array($http_body);
        if (empty($xmlarr))
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        $elems = &$xmlarr[0]['elements'];
        $count = count($elems);//如果data沒有子元素，$count等於0
        if ($count === 0)
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        /* 提取信息 */
        $result = array();
        for ($i = 0; $i < $count; $i++)
        {
            $node_name = trim($elems[$i]['name']);
            switch ($node_name)
            {
                case 'user_name' :
                    $result['user_name'] = $elems[$i]['text'];
                    break;
                case 'password' :
                    $result['password'] = $elems[$i]['text'];
                    break;
                case 'auth_str' :
                    $result['auth_str'] = @$elems[$i]['text'];
                    break;
                case 'sms_num' :
                    $result['sms_num'] = @$elems[$i]['text'];
                    break;
                case 'error' :
                    $this->errors['api_errors']['error_no'] = @$elems[$i]['elements'][0]['text'];
                    break;
//                default :
//                    $this->errors['server_errors']['error_no'] = 9;//無效的節點名字
//
//                    return false;
            }
        }

        /* 如果API出錯了 */
        if (intval($this->errors['api_errors']['error_no']) !== 0)
        {
            return false;
        }

        $my_info = array('sms_user_name' => $result['user_name'],
                        'sms_password' => $result['password'],
                        'sms_auth_str' => $result['auth_str'],
                        'sms_domain' => $domain,
                        'sms_count' => 0,
                        'sms_total_money' => 0,
                        'sms_balance' => 0,
                        'sms_last_request' => gmtime());

        /* 存儲短信特服信息 */
        if (!$this->store_my_info($my_info))
        {
            $this->errors['server_errors']['error_no'] = 10;//存儲失敗

            return false;
        }

        return true;
    }

    /**
     * 重新存儲短信特服信息
     *
     * @access  public
     * @param   string      $username       用戶名
     * @param   string      $password       密碼，已經用MD5加密
     * @return  boolean                     重新存儲成功返回true，失敗返回false。
     */
    function restore($username,  $password)
    {
        /* 檢查啟用服務時用戶信息的合法性 */
        if (!$this->check_enable_info($username, $password))
        {
            $this->errors['server_errors']['error_no'] = 2;//啟用信息無效

            return false;
        }

        /* 獲取API URL */
        $url = $this->get_url('auth');
        if (!$url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }

        $params = array('username' => $username,
                        'password' => $password);

        /* 發送HTTP請求 */
        $response = $this->t->request($url, $params);
        $http_body = $response['body'];
        if (!$response || !$http_body)
        {
            $this->errors['server_errors']['error_no'] = 7;//HTTP響應體為空

            return false;
        }

        /* 更新最後請求的時間 */
        $this->update_sms_last_request();

        /* 解析XML文本串 */
        $xmlarr = $this->xml2array($http_body);
        if (empty($xmlarr))
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        $elems = &$xmlarr[0]['elements'];
        $count = count($elems);
        if ($count === 0)
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        /* 提取信息 */
        $result = array();
        for ($i = 0; $i < $count; $i++)
        {
            $node_name = trim($elems[$i]['name']);
            switch ($node_name)
            {
                case 'user_name' :
                    $result['user_name'] = $elems[$i]['text'];
                    break;
                case 'password' :
                    $result['password'] = $elems[$i]['text'];
                    break;
                case 'auth_str' :
                    $result['auth_str'] = @$elems[$i]['text'];
                    break;
                case 'domain' :
                    $result['domain'] = @$elems[$i]['text'];
                    break;
                case 'count' :
                    $result['count'] = empty($elems[$i]['text']) ? 0 : $elems[$i]['text'];
                    break;
                case 'total_money' :
                    $result['total_money'] = empty($elems[$i]['text']) ? 0 : $elems[$i]['text'];
                    break;
                case 'balance' :
                    $result['balance'] = empty($elems[$i]['text']) ? 0 : $elems[$i]['text'];
                    break;
                case 'error' :
                    $this->errors['api_errors']['error_no'] = @$elems[$i]['elements'][0]['text'];
                    break;
                default :
                    $this->errors['server_errors']['error_no'] = 9;//無效的節點名字

                    return false;
            }
        }

        /* 如果API出錯了 */
        if (intval($this->errors['api_errors']['error_no']) !== 0)
        {
            return false;
        }

        $my_info = array('sms_user_name' => $result['user_name'],
                    'sms_password' => $result['password'],
                    'sms_auth_str' => $result['auth_str'],
                    'sms_domain' => $result['domain'],
                    'sms_count' => $result['count'],
                    'sms_total_money' => $result['total_money'],
                    'sms_balance' => $result['balance'],
                    'sms_last_request' => gmtime());

        /* 存儲短信特服信息 */
        if (!$this->store_my_info($my_info))
        {
            $this->errors['server_errors']['error_no'] = 10;//存儲失敗

            return false;
        }

        return true;
    }

    /**
     * 清除短信特服信息
     *
     * @access  public
     * @return  boolean     清除成功返回true，失敗返回false。
     */
    function clear_my_info()
    {
        $my_info = array('sms_user_name' => '',
            'sms_password' => '',
            'sms_auth_str' => '',
            'sms_domain' => '',
            'sms_count' => '',
            'sms_total_money' => '',
            'sms_balance' => '',
            'sms_last_request' => '');

        return $this->store_my_info($my_info);
    }

    /**
     * 發送短消息
     *
     * @access  public
     * @param   string  $phone          要發送到哪些個手機號碼，多個號碼用半角逗號隔開
     * @param   string  $msg            發送的消息內容
     * @param   string  $send_date      定時發送時間
     * @return  boolean                 發送成功返回true，失敗返回false。
     */
    function send($phone, $msg, $send_date = '', $send_num = 1)
    {
        /* 檢查發送信息的合法性 */
        if (!$this->check_send_sms($phone, $msg, $send_date))
        {
            $this->errors['server_errors']['error_no'] = 3;//發送的信息有誤

            return false;
        }

        /* 獲取身份驗證信息 */
        $login_info = $this->get_login_info();
        if (!$login_info)
        {
            $this->errors['server_errors']['error_no'] = 5;//無效的身份信息

            return false;
        }

        /* 獲取API URL */
        $url = $this->get_url('send');

        if (!$url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }

        $version = $GLOBALS['_CFG']['ecs_version'];
        $submit_str['certi_id'] = $GLOBALS['_CFG']['certificate_id'];
        $submit_str['ac'] = md5($GLOBALS['_CFG']['certificate_id'].$GLOBALS['_CFG']['token']);
        $submit_str['version']=$version;
        
        /* 發送HTTP請求 */
        $response = $this->t->request($url, $submit_str);
        $result = explode('|',$response['body']);

        if($result[0] == '0')
        {
            $sms_url = $result[1];
        }
        if($result[0] == '1')
        {
            $sms_url = '';
        }
        if($result[0] == '2'){
            $sms_url = '';
        }
        if (EC_CHARSET != 'utf-8')
        {
        $send_arr =    Array(
            0 => Array(
                    0 => $phone,    //發送的手機號碼
                    1 => iconv('gb2312','utf-8',$msg),      //發送信息
                    2 => 'Now' //發送的時間
                )
        );
        }
        else
        {
            $send_arr =    Array(
            0 => Array(
                    0 => $phone,    //發送的手機號碼
                    1 => $msg,      //發送信息
                    2 => 'Now' //發送的時間
                )
        );
        }
        $send_str['certi_id'] = $GLOBALS['_CFG']['certificate_id'];
        $send_str['ex_type'] = $send_num;
        $send_str['content'] = json_encode($send_arr);
        $send_str['encoding'] = 'utf8';
        $send_str['version'] = $version;
        $send_str['ac'] = md5($send_str['certi_id'].$send_str['ex_type'].$send_str['content'].$send_str['encoding'].$GLOBALS['_CFG']['token']);
        
        if (!$sms_url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }
        
        /* 發送HTTP請求 */
        $response = $this->t->request($sms_url, $send_str);

        $result = explode('|' ,$response['body']);

        if($result[0] == 'true')
        {
            //發送成功
            return true;
        }
        elseif($result[0] == 'false')
        {
            //發送失敗
            return false;
        }
        
        
    }

    /**
     * 獲取XML格式的消息發送歷史記錄
     *
     * @access  public
     * @param   string  $start_date     開始日期
     * @param   string  $end_date       結束日期
     * @param   string  $page_size      每頁顯示多少條記錄，默認為20
     * @param   string  $page           顯示多少頁，默認為1頁
     * @return  string or boolean       查詢成功返回XML格式的文本串，失敗返回false。
     */
    function get_send_history_by_xml($start_date, $end_date, $page_size = 20, $page = 1)
    {
        /* 檢查查詢條件 */
        if (!$this->check_history_query($start_date, $end_date, $page_size, $page))
        {
            $this->errors['server_errors']['error_no'] = 4;//填寫的查詢信息有誤

            return false;
        }

        /* 獲取身份驗證信息 */
        $login_info = $this->get_login_info();
        if (!$login_info)
        {
            $this->errors['server_errors']['error_no'] = 5;//無效的身份信息

            return false;
        }

        /* 獲取API URL */
        $url = $this->get_url('send_history');
        if (!$url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }

        $params = array('login_info' => $login_info,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'page_size' => $page_size,
                        'page' => $page);

        /* 發送HTTP請求 */
        $response = $this->t->request($url, $params);
        $http_body = $response['body'];
        if (!$response || !$http_body)
        {
            $this->errors['server_errors']['error_no'] = 7;//HTTP響應體為空

            return false;
        }

        /* 更新最後請求API的時間 */
        $this->update_sms_last_request();

        return $http_body;//返回xml文本串
    }

    /**
     * 獲取解析後的消息發送歷史記錄
     *
     * @access  public
     * @param   string  $start_date                 開始日期
     * @param   string  $end_date                   結束日期
     * @param   string  $page_size                  每頁顯示多少條記錄，默認為20
     * @param   string  $page                       顯示多少頁，默認為1頁
     * @return  1-dimensional-array or boolean      查詢成功返回歷史記錄數組，失敗返回false。
     */
    function get_send_history($start_date, $end_date, $page_size = 20, $page = 1)
    {
        /* 獲取XML文本串 */
        $xml = $this->get_send_history_by_xml($start_date, $end_date, $page_size, $page);
        if (!$xml)
        {
            return false;
        }

        /* 解析XML文本串 */
        $xmlarr = $this->xml2array($xml);
        if (empty($xmlarr))
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        $result = array();

        $attrs = &$xmlarr[0]['attributes'];
        $result['count'] = $attrs['count'];

        $elems = &$xmlarr[0]['elements'];
        $count = count($elems);
        if ($count === 0)
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        /* 提取信息 */
        $send_num = $count - 1;
        for ($i = 0; $i < $send_num; $i++)
        {
            if (empty($elems[$i]['attributes']))//屬性為空則跳過
            {
                continue;
            }
            $result['send'][$i]['phone'] = $elems[$i]['attributes']['phone'];
            $result['send'][$i]['content'] = $elems[$i]['attributes']['content'];
            $result['send'][$i]['charge_num'] = $elems[$i]['attributes']['charge_num'];
            $result['send'][$i]['send_date'] = $elems[$i]['attributes']['send_date'];
            $result['send'][$i]['send_status'] = $elems[$i]['attributes']['send_status'];
        }
        $this->errors['api_errors']['error_no'] = @$elems[$send_num]['elements'][0]['text'];

        /* 如果API出錯了 */
        if (intval($this->errors['api_errors']['error_no']) !== 0)
        {
            return false;
        }

        return $result;
    }

    /**
     * 獲取XML格式的充值歷史記錄
     *
     * @access  public
     * @param   string  $start_date     開始日期
     * @param   string  $end_date       結束日期
     * @param   string  $page_size      每頁顯示多少條記錄，默認為20
     * @param   string  $page           顯示多少頁，默認為1頁
     * @return  string or boolean       查詢成功返回XML格式的文本串，失敗返回false。
     */
    function get_charge_history_by_xml($start_date, $end_date, $page_size = 20, $page = 1)
    {
        /* 檢查查詢條件的合法性 */
        if (!$this->check_history_query($start_date, $end_date, $page_size, $page))
        {
            $this->errors['server_errors']['error_no'] = 4;//填寫的查詢信息有誤

            return false;
        }

        /* 獲取身份驗證信息 */
        $login_info = $this->get_login_info();
        if (!$login_info)
        {
            $this->errors['server_errors']['error_no'] = 5;//無效的身份信息

            return false;
        }

        $params = array('login_info' => $login_info,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'page_size' => $page_size,
                        'page' => $page);

        /* 獲取API URL */
        $url = $this->get_url('charge_history');
        if (!$url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不對

            return false;
        }

        /* 發送HTTP請求 */
        $response = $this->t->request($url, $params);
        $http_body = $response['body'];
        if (!$response || !$http_body)
        {
            $this->errors['server_errors']['error_no'] = 7;//HTTP響應體為空

            return false;
        }

        /* 更新最後請求API的時間 */
        $this->update_sms_last_request();

        return $http_body;//返回xml文本串
    }

    /**
     * 獲取解析後的充值歷史記錄
     *
     * @access  public
     * @param   string  $start_date                 開始日期
     * @param   string  $end_date                   結束日期
     * @param   string  $page_size                  每頁顯示多少條記錄，默認為20
     * @param   string  $page                       顯示多少頁，默認為1頁
     * @return  1-dimensional-array or boolean      查詢成功返回歷史記錄數組，失敗返回false。
     */
    function get_charge_history($start_date, $end_date, $page_size, $page)
    {
        /* 獲取XML文本串 */
        $xml = $this->get_charge_history_by_xml($start_date, $end_date, $page_size, $page);
        if (!$xml)
        {
            return false;
        }

        /* 解析XML文本串 */
        $xmlarr = $this->xml2array($xml);
        if (empty($xmlarr))
        {
            $this->errors['server_errors']['error_no'] = 8;//無效的XML文件

            return false;
        }

        $result = array();

        $attrs = &$xmlarr[0]['attributes'];
        $result['count'] = $attrs['count'];

        $elems = &$xmlarr[0]['elements'];
        $count = count($elems);
        $charge_num = $count - 1;//數組的前N-1個元素存放充值記錄，最後一個元素存放錯誤信息
        /* 提取信息 */
        for ($i = 0; $i < $charge_num; $i++)
        {
            if (empty($elems[$i]['attributes']))
            {
                continue;
            }
            $result['charge'][$i]['order_id'] = $elems[$i]['attributes']['order_id'];
            $result['charge'][$i]['money'] = $elems[$i]['attributes']['money'];
            $result['charge'][$i]['log_date'] = $elems[$i]['attributes']['log_date'];
        }

        $this->errors['api_errors']['error_no'] = @$elems[$charge_num]['elements'][0]['text'];

        if (intval($this->errors['api_errors']['error_no']) !== 0)
        {
            return false;
        }

        return $result;
    }

    /**
     * 檢測用戶註冊信息是否合法
     *
     * @access  private
     * @param   string      $email          郵箱，充當短信用戶的用戶名
     * @param   string      $password       密碼
     * @param   string      $domain         網店域名
     * @param   string      $phone          商家綁定的手機號碼
     * @return  boolean                     如果註冊信息格式合法返回true，否則返回false。
     */
    function check_register_info($email, $password, $domain, $phone)
    {
        /*
         * 遠程API會做相應的過濾處理，但如果有一值為空，API會直接退出，
         * 這不利於我們進一步處理，
         * 因此此處僅需簡單地判斷這三個值是否為空。
         * 以下凡是涉及到遠程API已有相應處理措施的代碼，一律只進行簡單地判空檢測。
         */
        if (empty($email) || empty($password) || empty($domain))
        {
            return false;
        }

        if (!empty($phone))
        {
            if (preg_match('/^\d+$/', $phone))
            {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . "
                        SET `value` = '$phone'
                        WHERE `code` =  'sms_shop_mobile'";
                $this->db->query($sql);
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 存儲短信特服信息
     *
     * @access  private
     * @param   1-dimensional-array     $my_info    短信特服信息數組
     * @return  boolean                             存儲成功返回true，失敗返回false。
     */
    function store_my_info($my_info)
    {
        /* 形參如果不是數組，返回false */
        if (!is_array($my_info))
        {
            return false;
        }

        foreach ($my_info AS $key => $value)
        {
            $sql = 'UPDATE ' . $this->ecs->table('shop_config') . " SET `value` = '$value' WHERE `code` = '$key'";
            $result = $this->db->query($sql);

            if (empty($result))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 更新數據庫中的最後請求記錄
     *
     * @access  private
     * @return  boolean             更新成功返回true，失敗返回false。
     */
    function update_sms_last_request()
    {
        $sql = 'UPDATE ' . $this->ecs->table('shop_config') . " SET `value` = '" . gmtime() . "' WHERE `code` = 'sms_last_request'";
        $result = $this->db->query($sql);

        if (empty($result))
        {
            return false;
        }

        return true;
    }

    /**
     * 檢測啟用短信服務需要的信息
     *
     * @access  private
     * @param   string      $email          郵箱
     * @param   string      $password       密碼
     * @return  boolean                     如果啟用信息格式合法就返回true，否則返回false。
     */
    function check_enable_info($email, $password)
    {
        if (empty($email) || empty($password))
        {
            return false;
        }

        return true;
    }

    /**
     * 檢測發送的短消息格式是否合法
     *
     * @access  private
     * @param   string      $phone          發送到哪些個電話號碼
     * @param   string      $msg            消息內容
     * @param   string      $send_date      定時發送時間
     * @return  boolean                     短消息格式合法返回true，否則返回false。
     */
    function check_send_sms($phone, $msg, $send_date)
    {
        if (empty($phone) || empty($msg))
        {
            return false;
        }

        if (!empty($send_date) && $this->check_date_format($send_date))
        {
            return false;
        }

        return true;
    }

    /**
     * 獲得用於驗證身份的信息
     *
     * @access  private
     * @return  string or boolean   成功返回用於登錄短信服務的帳號信息，失敗返回false。
     */
    function get_login_info()
    {
        $sql = 'SELECT `code`, `value` FROM ' . $this->ecs->table('shop_config') . " WHERE `code` = 'sms_user_name' OR `code` = 'sms_password'";
        $result = $this->db->query($sql);

        $retval = array();
        if (!empty($result))
        {
            while ($temp_arr = $this->db->fetchRow($result))
            {
                $retval[$temp_arr['code']] = $temp_arr['value'];
            }

            return base64_encode($retval['sms_user_name'] . "\t" . $retval['sms_password']);
        }

        return false;
    }

    /**
     * 檢測用於查詢歷史記錄條件的格式是否合法
     *
     * @access  private
     * @param   string      $start_date         開始日期，可為空
     * @param   string      $end_date           結束日期，可為空
     * @param   string      $page_size          每頁顯示數量，默認為20
     * @param   string      $page               頁數，默認為1
     * @return  boolean                         查詢條件格式合法就返回true，否則返回false。
     */
    function check_history_query($start_date, $end_date, $page_size =  20, $page = 1)
    {
        /* 檢查日期格式 */
        if (!empty($start_date) && !$this->check_date_format($start_date))
        {
            return false;
        }
        if (!empty($end_date) && !$this->check_date_format($end_date))
        {
            return false;
        }

        /* 檢查數字參數 */
        if (!is_numeric($page_size) || !is_numeric($page))
        {
            return false;
        }

        return true;
    }

    /**
     * 日期的格式是否符合遠程API所要求的格式
     *
     * @access  private
     * @param   1-dimensional-array or string       $date           日期
     * @return  boolean                                             格式合法就返回true，否則返回false。
     */
    function check_date_format($date)
    {
        $pattern = '/\d{4}-\d{2}-\d{2}/';
        if (is_array($date))
        {
            foreach ($date AS $value)
            {
                if (!preg_match($pattern, $value))
                {
                    return false;
                }
            }
        }
        elseif (!preg_match($pattern, $date))
        {
            return false;
        }

        return true;
    }

    /**
     * 把XML串轉換成PHP關聯數組
     *
     * @access  private
     * @param   string      $xml    XML串
     * @author  www.google.com
     *
     * @return  array       PHP關聯數組
     */
    function xml2array($xml)
    {
        $xmlary = array();

        $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\1\s*>)/s';
        $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

        preg_match_all($reels, $xml, $elements);

        foreach ($elements[1] AS $ie => $xx)
        {
            $xmlary[$ie]['name'] = $elements[1][$ie];

            if ($attributes = trim($elements[2][$ie]))
            {
                preg_match_all($reattrs, $attributes, $att);
                foreach ($att[1] AS $ia => $xx)
                {
                    $xmlary[$ie]['attributes'][$att[1][$ia]] = $att[2][$ia];
                }
            }

            $cdend = strpos($elements[3][$ie], '<');
            if ($cdend > 0)
            {
                $xmlary[$ie]['text'] = substr($elements[3][$ie], 0, $cdend - 1);
            }

            if (preg_match($reels, $elements[3][$ie]))
            {
                $xmlary[$ie]['elements'] = $this->xml2array($elements[3][$ie]);
            }
            elseif ($elements[3][$ie])
            {
                $xmlary[$ie]['text'] = $elements[3][$ie];
            }
        }

        //如果找不到任何匹配，則返回空數組
        return $xmlary;
    }
}

?>