<?php

/**
 * ECSHOP LICENSE 相關函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_article.php 16336 2009-06-24 07:09:13Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 獲得網店 license 信息
 *
 * @access  public
 * @param   integer     $size
 *
 * @return  array
 */
function get_shop_license()
{
    // 取出網店 license
    $sql = "SELECT code, value
            FROM " . $GLOBALS['ecs']->table('shop_config') . "
            WHERE code IN ('certificate_id', 'token', 'certi')
            LIMIT 0,3";
    $license_info = $GLOBALS['db']->getAll($sql);
    $license_info = is_array($license_info) ? $license_info : array();
    $license = array();
    foreach ($license_info as $value)
    {
        $license[$value['code']] = $value['value'];
    }

    return $license;
}

/**
 * 功能：生成certi_ac驗證字段
 * @param   string     POST傳遞參數
 * @param   string     證書token
 * @return  string
 */
function make_shopex_ac($post_params, $token)
{
    if (!is_array($post_params))
    {
        return;
    }

    // core
    ksort($post_params);
    $str = '';
    foreach($post_params as $key=>$value){
        if($key != 'certi_ac')
        {
            $str .= $value;
        }
    }

    return md5($str . $token);
}

/**
 * 功能：與 ECShop 交換數據
 *
 * @param   array     $certi    登錄參數
 * @param   array     $license  網店license信息
 * @param   bool      $use_lib  使用哪一個json庫，0為ec，1為shopex
 * @return  array
 */
function exchange_shop_license($certi, $license, $use_lib = 0)
{
    if (!is_array($certi))
    {
        return array();
    }

    include_once(ROOT_PATH . 'includes/cls_transport.php');
    include_once(ROOT_PATH . 'includes/cls_json.php');

    $params = '';
    foreach ($certi as $key => $value)
    {
        $params .= '&' . $key . '=' . $value;
    }
    $params = trim($params, '&');

    $transport = new transport;
    //$transport->connect_timeout = 1;
    $request = $transport->request($license['certi'], $params, 'POST');
    $request_str = json_str_iconv($request['body']);

    if (empty($use_lib))
    {
        $json = new JSON();
        $request_arr = $json->decode($request_str, 1);
    }
    else
    {
        include_once(ROOT_PATH . 'includes/shopex_json.php');
        $request_arr = json_decode($request_str, 1);
    }

    return $request_arr;
}

/**
 * 功能：處理登錄返回結果
 *
 * @param   array     $cert_auth    登錄返回的用戶信息
 * @return  array
 */
function process_login_license($cert_auth)
{
    if (!is_array($cert_auth))
    {
        return array();
    }

    $cert_auth['auth_str'] = trim($cert_auth['auth_str']);
    if (!empty($cert_auth['auth_str']))
    {
        $cert_auth['auth_str'] = $GLOBALS['_LANG']['license_' . $cert_auth['auth_str']];
    }

    $cert_auth['auth_type'] = trim($cert_auth['auth_type']);
    if (!empty($cert_auth['auth_type']))
    {
        $cert_auth['auth_type'] = $GLOBALS['_LANG']['license_' . $cert_auth['auth_type']];
    }

    return $cert_auth;
}

/**
 * 功能：license 登錄
 *
 * @param   array     $certi_added    配置信息補充數組 array_key 登錄信息的key；array_key => array_value；
 * @return  array     $return_array['flag'] = login_succ、login_fail、login_ping_fail、login_param_fail；
 *                    $return_array['request']；
 */
function license_login($certi_added = '')
{
    // 登錄信息配置
    $certi['certi_app'] = ''; // 證書方法
    $certi['app_id'] = 'ecshop_b2c'; // 說明客戶端來源
    $certi['app_instance_id'] = ''; // 應用服務ID
    $certi['version'] = LICENSE_VERSION; // license接口版本號
    $certi['shop_version'] = VERSION . '#' .  RELEASE; // 網店軟件版本號
    $certi['certi_url'] = sprintf($GLOBALS['ecs']->url()); // 網店URL
    $certi['certi_session'] = $GLOBALS['sess']->get_session_id(); // 網店SESSION標識
    $certi['certi_validate_url'] = sprintf($GLOBALS['ecs']->url() . 'certi.php'); // 網店提供於官方反查接口
    $certi['format'] = 'json'; // 官方返回數據格式
    $certi['certificate_id'] = ''; // 網店證書ID
    // 標識
    $certi_back['succ']   = 'succ';
    $certi_back['fail']   = 'fail';
    // return 返回數組
    $return_array = array();

    if (is_array($certi_added))
    {
        foreach ($certi_added as $key => $value)
        {
            $certi[$key] = $value;
        }
    }

    // 取出網店 license
    $license = get_shop_license();

    // 檢測網店 license
    if (!empty($license['certificate_id']) && !empty($license['token']) && !empty($license['certi']))
    {
        // 登錄
        $certi['certi_app'] = 'certi.login'; // 證書方法
        $certi['app_instance_id'] = 'cert_auth'; // 應用服務ID
        $certi['certificate_id'] = $license['certificate_id']; // 網店證書ID
        $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

        $request_arr = exchange_shop_license($certi, $license);
        if (is_array($request_arr) && $request_arr['res'] == $certi_back['succ'])
        {
            $return_array['flag'] = 'login_succ';
            $return_array['request'] = $request_arr;
        }
        elseif (is_array($request_arr) && $request_arr['res'] == $certi_back['fail'])
        {
            $return_array['flag'] = 'login_fail';
            $return_array['request'] = $request_arr;
        }
        else
        {
            $return_array['flag'] = 'login_ping_fail';
            $return_array['request'] = array('res' => 'fail');
        }
    }
    else
    {
        $return_array['flag'] = 'login_param_fail';
        $return_array['request'] = array('res' => 'fail');
    }

    return $return_array;
}

/**
 * 功能：license 註冊
 *
 * @param   array     $certi_added    配置信息補充數組 array_key 登錄信息的key；array_key => array_value；
 * @return  array     $return_array['flag'] = reg_succ、reg_fail、reg_ping_fail；
 *                    $return_array['request']；
 */
function license_reg($certi_added = '')
{
    // 登錄信息配置
    $certi['certi_app'] = ''; // 證書方法
    $certi['app_id'] = 'ecshop_b2c'; // 說明客戶端來源
    $certi['app_instance_id'] = ''; // 應用服務ID
    $certi['version'] = LICENSE_VERSION; // license接口版本號
    $certi['shop_version'] = VERSION . '#' .  RELEASE; // 網店軟件版本號
    $certi['certi_url'] = sprintf($GLOBALS['ecs']->url()); // 網店URL
    $certi['certi_session'] = $GLOBALS['sess']->get_session_id(); // 網店SESSION標識
    $certi['certi_validate_url'] = sprintf($GLOBALS['ecs']->url() . 'certi.php'); // 網店提供於官方反查接口
    $certi['format'] = 'json'; // 官方返回數據格式
    $certi['certificate_id'] = ''; // 網店證書ID
    // 標識
    $certi_back['succ']   = 'succ';
    $certi_back['fail']   = 'fail';
    // return 返回數組
    $return_array = array();

    if (is_array($certi_added))
    {
        foreach ($certi_added as $key => $value)
        {
            $certi[$key] = $value;
        }
    }

    // 取出網店 license
    $license = get_shop_license();

    // 註冊
    $certi['certi_app'] = 'certi.reg'; // 證書方法
    $certi['certi_ac'] = make_shopex_ac($certi, ''); // 網店驗證字符串
    unset($certi['certificate_id']);

    $request_arr = exchange_shop_license($certi, $license);
    if (is_array($request_arr) && $request_arr['res'] == $certi_back['succ'])
    {
        // 註冊信息入庫
        $sql = "UPDATE " . $GLOBALS['ecs']->table('shop_config') . "
                SET value = '" . $request_arr['info']['certificate_id'] . "' WHERE code = 'certificate_id'";
        $GLOBALS['db']->query($sql);
        $sql = "UPDATE " . $GLOBALS['ecs']->table('shop_config') . "
                SET value = '" . $request_arr['info']['token'] . "' WHERE code = 'token'";
        $GLOBALS['db']->query($sql);

        $return_array['flag'] = 'reg_succ';
        $return_array['request'] = $request_arr;
    }
    elseif (is_array($request_arr) && $request_arr['res'] == $certi_back['fail'])
    {
        $return_array['flag'] = 'reg_fail';
        $return_array['request'] = $request_arr;
    }
    else
    {
        $return_array['flag'] = 'reg_ping_fail';
        $return_array['request'] = array('res' => 'fail');
    }

    return $return_array;
}
?>