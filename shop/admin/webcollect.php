<?php

/**
 * ECSHOP 網羅天下
 * ===========================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ==========================================================
 * $Author: wangleisvn $
 * $Id: webcollect.php 16131 2009-05-31 08:21:41Z wangleisvn $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_license.php');

/* 檢查權限 */
admin_priv('webcollect_manage');
$smarty->assign('ur_here', $_LANG['ur_here']);

$license = get_shop_license();  // 取出網店 license

if (!empty($license['certificate_id']) && !empty($license['token']) && !empty($license['certi']))
{
    /* 先做登錄驗證 */
    $certi_login['certi_app'] = 'certi.login'; // 證書方法
    $certi_login['app_id'] = 'ecshop_b2c'; // 說明客戶端來源
    $certi_login['app_instance_id'] = 'cert_auth'; // 應用服務ID
    $certi_login['version'] = VERSION . '#' .  RELEASE; // 網店軟件版本號
    $certi_login['certi_url'] = sprintf($GLOBALS['ecs']->url()); // 網店URL
    $certi_login['certi_session'] = $GLOBALS['sess']->get_session_id(); // 網店SESSION標識
    $certi_login['certi_validate_url'] = sprintf($GLOBALS['ecs']->url() . 'certi.php'); // 網店提供於官方反查接口
    $certi_login['format'] = 'json'; // 官方返回數據格式
    $certi_login['certificate_id'] = $license['certificate_id']; // 網店證書ID
    $certi_login['certi_ac'] = make_shopex_ac($certi_login, $license['token']); // 網店驗證字符串

    $request_login_arr = exchange_shop_license($certi_login, $license, 1);

    /* 通用的驗證變量 */
    $certi['certificate_id'] = $license['certificate_id']; // 網店證書ID
    $certi['app_id'] = 'ecshop_b2c'; // 說明客戶端來源
    $certi['app_instance_id'] = 'webcollect'; // 應用服務ID
    $certi['version'] = VERSION . '#' .  RELEASE; // 網店軟件版本號
    $certi['format'] = 'json'; // 官方返回數據格式

    if (is_array($request_login_arr) && $request_login_arr['res'] == 'succ')    //查看是否開啟了網羅天下服務
    {
        if (isset($_GET['act']) && $_GET['act'] == 'open')  //開啟服務
        {
            $certi['certi_app'] = 'co.open_se'; // 證書方法
            $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

            exchange_shop_license($certi, $license, 1);
        }
        elseif (isset($_GET['act']) && $_GET['act'] == 'close') //暫停服務
        {
            $certi['certi_app'] = 'co.close_se'; // 證書方法
            $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

            exchange_shop_license($certi, $license, 1);
        }

        $certi['certi_app'] = 'co.valid_se'; // 證書方法
        $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

        $request_arr = exchange_shop_license($certi, $license, 1);

        if ($request_arr['res'] == 'succ')
        {
            $now = time();
            if ($request_arr['info']['service_status'] == 'expire')
            {
                $smarty->assign('case', 2);    //已過期頁面
                $smarty->assign('open', 2);    //過期重新開啟
            }
            elseif ($request_arr['info']['service_close_time'] - $now < 1296000)
            {
                $smarty->assign('case', 1);    //將過期頁面
                $smarty->assign('open', $request_arr['info']['service_status'] == 'open' ? 1 : 0);

                $out_days = floor(($request_arr['info']['service_close_time'] - $now) / 86400);
                $smarty->assign('out_notice', sprintf($_LANG['soon_out'], $out_days));  //過期時限提示
            }
            else
            {
                $smarty->assign('case', 3);    //正常頁面
                $smarty->assign('open', $request_arr['info']['service_status'] == 'open' ? 1 : 0);
            }

            $smarty->assign('lic_code', $license['certificate_id']);    //證書ID
            $smarty->assign('lic_btime', local_date('Y-m-d', $request_arr['info']['service_open_time']));   //服務開始時間
            $smarty->assign('lic_etime', local_date('Y-m-d', $request_arr['info']['service_close_time']));   //服務結束時間
            $smarty->assign('col_goods_num', $request_arr['info']['collect_num']);   //收錄商品數量
            $smarty->assign('col_goods', $request_arr['info']['collect_se']);   //收錄商品詳情
        }
        else
        {
            $smarty->assign('msg', $request_arr['info']);    //提示信息
            $smarty->assign('case', 0);    //開通服務頁面
        }
    }
    else
    {
        $smarty->assign('msg', $_LANG['no-open']);    //提示信息
        $smarty->assign('case', 0);    //開通服務頁面
    }

    //合作網站列表
    $certi['certi_app'] = 'co.show_se'; // 證書方法
    $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

    $request_arr = exchange_shop_license($certi, $license, 1);

    if ($request_arr['res'] == 'succ')    //成功獲取合作網站信息
    {
        $smarty->assign('site_arr', $request_arr['info']['se']);
    }
    else
    {
        $smarty->assign('site_msg', $request_arr['info']);
    }
}
else
{
    $smarty->assign('msg', $_LANG['no-open']);    //提示信息
    $smarty->assign('case', 0);    //開通服務頁面
}

$smarty->display('webcollect.htm');
?>