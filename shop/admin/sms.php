<?php

/**
 * ECSHOP 短信模塊 之 控制器
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: sms.php 17155 2010-05-06 06:29:05Z yehuaixiao $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_sms.php');

$action = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'display_my_info';
$sms = new sms();

switch ($action)
{
//    /* 註冊短信服務。*/
//    case 'register' :
//        $email      = isset($_POST['email'])    ? $_POST['email']       : '';
//        $password   = isset($_POST['password']) ? $_POST['password']    : '';
//        $domain     = isset($_POST['domain'])   ? $_POST['domain']      : '';
//        $phone      = isset($_POST['phone'])    ? $_POST['phone']       : '';
//
//        $result = $sms->register($email, $password, $domain, $phone);
//
//        $link[] = array('text'  =>  $_LANG['back'],
//                        'href'  =>  'sms.php?act=display_my_info');
//
//        if ($result === true)//註冊成功
//        {
//            sys_msg($_LANG['register_ok'], 0, $link);
//        }
//        else
//        {
//            @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
//                          . $_LANG['api_errors']['register'][$sms->errors['api_errors']['error_no']];
//            sys_msg($_LANG['register_error'] . $error_detail, 1, $link);
//        }
//
//        break;
//
//    /* 啟用短信服務。 */
//    case 'enable' :
//        $username = isset($_POST['email'])      ? $_POST['email']       : '';
//        //由於md5函數對空串也加密，所以要進行判空操作
//        $password = isset($_POST['password']) && $_POST['password'] !== ''
//                ? md5($_POST['password'])
//                : '';
//
//        $result = $sms->restore($username, $password);
//
//        $link[] = array('text'  =>  $_LANG['back'],
//                        'href'  =>  'sms.php?act=display_my_info');
//
//        if ($result === true)//啟用成功
//        {
//            sys_msg($_LANG['enable_ok'], 0, $link);
//        }
//        else
//        {
//            @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
//                          . $_LANG['api_errors']['auth'][$sms->errors['api_errors']['error_no']];
//            sys_msg($_LANG['enable_error'] . $error_detail, 1, $link);
//        }
//
//        break;
//
//    /* 註銷短信特服信息 */
//    case 'disable' :
//        $result = $sms->clear_my_info();
//
//        $link[] = array('text'  =>  $_LANG['back'],
//                        'href'  =>  'sms.php?act=display_my_info');
//
//        if ($result === true)//註銷成功
//        {
//            sys_msg($_LANG['disable_ok'], 0, $link);
//        }
//        else
//        {
//            sys_msg($_LANG['disable_error'], 1, $link);
//        }
//
//        break;

    /* 顯示短信發送界面，如果尚未註冊或啟用短信服務則顯示註冊界面。 */
    case 'display_send_ui' :
        /* 檢查權限 */
         admin_priv('sms_send');

        if ($sms->has_registered())
        {
            $smarty->assign('ur_here', $_LANG['03_sms_send']);
            $special_ranks = get_rank_list();
            $send_rank['1_0'] = $_LANG['user_list'];
            foreach($special_ranks as $rank_key => $rank_value)
            {
                $send_rank['2_' . $rank_key] = $rank_value;
            }
            assign_query_info();
            $smarty->assign('send_rank',   $send_rank);
            $smarty->display('sms_send_ui.htm');
        }
        else
        {
            $smarty->assign('ur_here', $_LANG['register_sms']);
            $smarty->assign('sms_site_info', $sms->get_site_info());
            assign_query_info();
            $smarty->display('sms_register_ui.htm');
        }

        break;

    /* 發送短信 */
    case 'send_sms' :
        $send_num = isset($_POST['send_num'])   ? $_POST['send_num']    : '';

        if(isset($send_num))
        {
            $phone = $send_num.',';
        }

        $send_rank = isset($_POST['send_rank'])     ? $_POST['send_rank'] : 0;

        if ($send_rank != 0)
        {
            $rank_array = explode('_', $send_rank);

            if($rank_array['0'] == 1)
            {
                $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' ";
                $row = $db->query($sql);
                while ($rank_rs = $db->fetch_array($row))
                {
                    $value[] = $rank_rs['mobile_phone'];
                }
            }
            else
            {
                $rank_sql = "SELECT * FROM " . $ecs->table('user_rank') . " WHERE rank_id = '" . $rank_array['1'] . "'";
                $rank_row = $db->getRow($rank_sql);
                //$sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' AND rank_points > " .$rank_row['min_points']." AND rank_points < ".$rank_row['max_points']." ";

                if($rank_row['special_rank']==1) 
                {
                    $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . " WHERE mobile_phone <>'' AND user_rank = '" . $rank_array['1'] . "'";
                }
                else
                {
                    $sql = 'SELECT mobile_phone FROM ' . $ecs->table('users') . "WHERE mobile_phone <>'' AND rank_points > " .$rank_row['min_points']." AND rank_points < ".$rank_row['max_points']." ";
                }
                
                $row = $db->query($sql);
                
                while ($rank_rs = $db->fetch_array($row))
                {
                    $value[] = $rank_rs['mobile_phone'];
                }
            }
            if(isset($value))
            {
                $phone .= implode(',',$value);
            }
        }       
                
        $msg       = isset($_POST['msg'])       ? $_POST['msg']         : '';
        

        $send_date = isset($_POST['send_date']) ? $_POST['send_date']   : '';   
               
        $result = $sms->send($phone, $msg, $send_date, $send_num = 13);

        $link[] = array('text'  =>  $_LANG['back'] . $_LANG['03_sms_send'],
                        'href'  =>  'sms.php?act=display_send_ui');

        if ($result === true)//發送成功
        {
            sys_msg($_LANG['send_ok'], 0, $link);
        }
        else
        {
            @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
                          . $_LANG['api_errors']['send'][$sms->errors['api_errors']['error_no']];
            sys_msg($_LANG['send_error'] . $error_detail, 1, $link);
        }

        break;

//    /* 顯示發送記錄的查詢界面，如果尚未註冊或啟用短信服務則顯示註冊界面。 */
//    case 'display_send_history_ui' :
//        /* 檢查權限 */
//         admin_priv('send_history');
//        if ($sms->has_registered())
//        {
//            $smarty->assign('ur_here', $_LANG['05_sms_send_history']);
//            assign_query_info();
//            $smarty->display('sms_send_history_query_ui.htm');
//        }
//        else
//        {
//            $smarty->assign('ur_here', $_LANG['register_sms']);
//            $smarty->assign('sms_site_info', $sms->get_site_info());
//            assign_query_info();
//            $smarty->display('sms_register_ui.htm');
//        }
//
//        break;
//
//    /* 獲得發送記錄，如果客戶端支持XSLT，則直接發送XML格式的文本到客戶端；
//       否則在服務器端把XML轉換成XHTML後發送到客戶端。
//    */
//    case 'get_send_history' :
//        $start_date = isset($_POST['start_date'])   ? $_POST['start_date']  : '';
//        $end_date   = isset($_POST['end_date'])     ? $_POST['end_date']    : '';
//        $page_size  = isset($_POST['page_size'])    ? $_POST['page_size']   : 20;
//        $page       = isset($_POST['page'])         ? $_POST['page']        : 1;
//
//        $is_xslt_supported = isset($_POST['is_xslt_supported']) ? $_POST['is_xslt_supported'] : 'no';
//        if ($is_xslt_supported === 'yes')
//        {
//            $xml = $sms->get_send_history_by_xml($start_date, $end_date, $page_size, $page);
//            header('Content-Type: application/xml; charset=utf-8');
//            //TODO:判斷錯誤信息，鏈上XSLT
//            echo $xml;
//        }
//        else
//        {
//            $result = $sms->get_send_history($start_date, $end_date, $page_size, $page);
//
//            if ($result !== false)
//            {
//                $smarty->assign('sms_send_history', $result);
//                $smarty->assign('ur_here', $_LANG['05_sms_send_history']);
//
//                /* 分頁信息 */
//                $turn_page = array( 'total_records' => $result['count'],
//                                    'total_pages'   => intval(ceil($result['count']/$page_size)),
//                                    'page'          => $page,
//                                    'page_size'     => $page_size);
//                $smarty->assign('turn_page', $turn_page);
//                $smarty->assign('start_date', $start_date);
//                $smarty->assign('end_date', $end_date);
//
//                assign_query_info();
//
//                $smarty->display('sms_send_history.htm');
//            }
//            else
//            {
//                $link[] = array('text'  =>  $_LANG['back_send_history'],
//                                'href'  =>  'sms.php?act=display_send_history_ui');
//
//                @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
//                              . $_LANG['api_errors']['get_history'][$sms->errors['api_errors']['error_no']];
//
//                sys_msg($_LANG['history_query_error'] . $error_detail, 1, $link);
//            }
//        }
//
//        break;
//
//    /* 顯示充值頁面 */
//    case 'display_charge_ui' :
//        /* 檢查權限 */
//         admin_priv('sms_charge');
//        if ($sms->has_registered())
//        {
//            $smarty->assign('ur_here', $_LANG['04_sms_charge']);
//            assign_query_info();
//            $sms_charge = array();
//            $sms_charge['charge_url'] = $sms->get_url('charge');
//            $sms_charge['login_info'] = $sms->get_login_info();
//            $smarty->assign('sms_charge', $sms_charge);
//            $smarty->display('sms_charge_ui.htm');
//        }
//        else
//        {
//            $smarty->assign('ur_here', $_LANG['register_sms']);
//            $smarty->assign('sms_site_info', $sms->get_site_info());
//            assign_query_info();
//            $smarty->display('sms_register_ui.htm');
//        }
//
//        break;
//
//    /* 顯示充值記錄的查詢界面，如果尚未註冊或啟用短信服務則顯示註冊界面。 */
//    case 'display_charge_history_ui' :
//         /* 檢查權限 */
//         admin_priv('charge_history');
//        if ($sms->has_registered())
//        {
//            $smarty->assign('ur_here', $_LANG['06_sms_charge_history']);
//            assign_query_info();
//            $smarty->display('sms_charge_history_query_ui.htm');
//        }
//        else
//        {
//            $smarty->assign('ur_here', $_LANG['register_sms']);
//            $smarty->assign('sms_site_info', $sms->get_site_info());
//            assign_query_info();
//            $smarty->display('sms_register_ui.htm');
//        }
//
//        break;
//
//    /* 獲得充值記錄，如果客戶端支持XSLT，則直接發送XML格式的文本到客戶端；
//       否則在服務器端把XML轉換成XHTML後發送到客戶端。
//    */
//    case 'get_charge_history' :
//        $start_date = isset($_POST['start_date'])   ? $_POST['start_date']  : '';
//        $end_date   = isset($_POST['end_date'])     ? $_POST['end_date']    : '';
//        $page_size  = isset($_POST['page_size'])    ? $_POST['page_size']   : 20;
//        $page       = isset($_POST['page'])         ? $_POST['page']        : 1;
//
//        $is_xslt_supported = isset($_POST['is_xslt_supported']) ? $_POST['is_xslt_supported'] : 'no';
//        if ($is_xslt_supported === 'yes')
//        {
//            $xml = $sms->get_charge_history_by_xml($start_date, $end_date, $page_size, $page);
//            header('Content-Type: application/xml; charset=utf-8');
//            //TODO:判斷錯誤信息，鏈上XSLT
//            echo $xml;
//        }
//        else
//        {
//            $result = $sms->get_charge_history($start_date, $end_date, $page_size, $page);
//            if ($result !== false)
//            {
//                $smarty->assign('sms_charge_history', $result);
//
//                /* 分頁信息 */
//                $turn_page = array( 'total_records' => $result['count'],
//                                    'total_pages'   => intval(ceil($result['count']/$page_size)),
//                                    'page'          => $page,
//                                    'page_size'     => $page_size);
//                $smarty->assign('turn_page', $turn_page);
//                $smarty->assign('start_date', $start_date);
//                $smarty->assign('end_date', $end_date);
//
//                assign_query_info();
//
//                $smarty->display('sms_charge_history.htm');
//            }
//            else
//            {
//                $link[] = array('text'  =>  $_LANG['back_charge_history'],
//                                'href'  =>  'sms.php?act=display_charge_history_ui');
//
//                @$error_detail = $_LANG['server_errors'][$sms->errors['server_errors']['error_no']]
//                              . $_LANG['api_errors']['get_history'][$sms->errors['api_errors']['error_no']];
//
//                sys_msg($_LANG['history_query_error'] . $error_detail, 1, $link);
//            }
//        }
//
//        break;
//
//    /* 顯示我的短信服務個人信息 */
//    default :
//        /* 檢查權限 */
//         admin_priv('my_info');
//        $sms_my_info = $sms->get_my_info();
//        if (!$sms_my_info)
//        {
//            $link[] = array('text'  =>  $_LANG['back'], 'href'  =>  './');
//            sys_msg($_LANG['empty_info'], 1, $link);
//        }
//
//        if (!$sms_my_info['sms_user_name'])//此處不用$sms->has_registered()能夠減少一次數據庫查詢
//        {
//            $smarty->assign('ur_here', $_LANG['register_sms']);
//            $smarty->assign('sms_site_info', $sms->get_site_info());
//            assign_query_info();
//            $smarty->display('sms_register_ui.htm');
//        }
//        else
//        {
//            /* 立即更新短信特服信息 */
//            $sms->restore($sms_my_info['sms_user_name'], $sms_my_info['sms_password']);
//
//            /* 再次獲取個人數據，保證顯示的數據是最新的 */
//            $sms_my_info = $sms->get_my_info();//這裡不再進行判空處理，主要是因為如果前個式子不出錯，這裡一般不會出錯
//
//            /* 格式化時間輸出 */
//            $sms_last_request = $sms_my_info['sms_last_request']
//                    ? $sms_my_info['sms_last_request']
//                    : 0;//賦0防出錯
//            $sms_my_info['sms_last_request'] = local_date('Y-m-d H:i:s O', $sms_my_info['sms_last_request']);
//
//            $smarty->assign('sms_my_info', $sms_my_info);
//            $smarty->assign('ur_here', $_LANG['02_sms_my_info']);
//            assign_query_info();
//            $smarty->display('sms_my_info.htm');
//        }
}

?>