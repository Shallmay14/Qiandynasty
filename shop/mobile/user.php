<?php

/**
 * ECSHOP 用戶中心
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 16643 2009-09-08 07:02:13Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$act = isset($_GET['act']) ? $_GET['act'] : '';

/* 用戶登陸 */
if ($act == 'do_login')
{
    $user_name = !empty($_POST['username']) ? $_POST['username'] : '';
    $pwd = !empty($_POST['pwd']) ? $_POST['pwd'] : '';
    if (empty($user_name) || empty($pwd))
    {
        $login_faild = 1;
    }
    else
    {
        if ($user->check_user($user_name, $pwd) > 0)
        {
            $user->set_session($user_name);
            $user->set_cookie($user_name);
            update_user_info();
            show_user_center();
        }
        else
        {
            $login_faild = 1;
        }
    }
}

elseif ($act == 'order_list')
{
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = {$_SESSION['user_id']}");
    if ($record_count > 0)
    {
        include_once(ROOT_PATH . 'includes/lib_transaction.php');
        $page_num = '10';
        $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
        $pages = ceil($record_count / $page_num);

        if ($page <= 0)
        {
            $page = 1;
        }
        if ($pages == 0)
        {
            $pages = 1;
        }
        if ($page > $pages)
        {
            $page = $pages;
        }
        $pagebar = get_wap_pager($record_count, $page_num, $page, 'user.php?act=order_list', 'page');
        $smarty->assign('pagebar' , $pagebar);
        /* 訂單狀態 */
        $_LANG['os'][OS_UNCONFIRMED] = '未確認';
        $_LANG['os'][OS_CONFIRMED] = '已確認';
        $_LANG['os'][OS_SPLITED] = '已確認';
        $_LANG['os'][OS_SPLITING_PART] = '已確認';
        $_LANG['os'][OS_CANCELED] = '已取消';
        $_LANG['os'][OS_INVALID] = '無效';
        $_LANG['os'][OS_RETURNED] = '退貨';

        $_LANG['ss'][SS_UNSHIPPED] = '未發貨';
        $_LANG['ss'][SS_PREPARING] = '配貨中';
        $_LANG['ss'][SS_SHIPPED] = '已發貨';
        $_LANG['ss'][SS_RECEIVED] = '收貨確認';
        $_LANG['ss'][SS_SHIPPED_PART] = '已發貨(部分商品)';
        $_LANG['ss'][SS_SHIPPED_ING] = '配貨中'; // 已分單

        $_LANG['ps'][PS_UNPAYED] = '未付款';
        $_LANG['ps'][PS_PAYING] = '付款中';
        $_LANG['ps'][PS_PAYED] = '已付款';
        $_LANG['cancel'] = '取消訂單';
        $_LANG['pay_money'] = '付款';
        $_LANG['view_order'] = '查看訂單';
        $_LANG['received'] = '確認收貨';
        $_LANG['ss_received'] = '已完成';
        $_LANG['confirm_received'] = '你確認已經收到貨物了嗎？';
        $_LANG['confirm_cancel'] = '您確認要取消該訂單嗎？取消後此訂單將視為無效訂單';

        $orders = get_user_orders($_SESSION['user_id'], $page_num, $page_num * ($page - 1));
        if (!empty($orders))
        {
            foreach ($orders as $key => $val)
            {
                $orders[$key]['total_fee'] = encode_output($val['total_fee']);
            }
        }
        //$merge  = get_user_merge($_SESSION['user_id']);

        $smarty->assign('orders', $orders);
    }
    $smarty->assign('footer', get_footer());
    $smarty->display('order_list.html');
    exit;
}
/* 確認收貨 */
elseif ($act == 'affirm_received')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $_LANG['buyer'] = '買家';
    if (affirm_received($order_id, $_SESSION['user_id']))
    {
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }

}

/* 退出會員中心 */
elseif ($act == 'logout')
{
    if (!isset($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
    {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    $user->logout();
    $Loaction = 'index.php';
    ecs_header("Location: $Loaction\n");

}
/* 顯示會員註冊界面 */
elseif ($act == 'register')
{
    if (!isset($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
    {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    /* 取出註冊擴展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
    /* 密碼找回問題 */
    $_LANG['passwd_questions']['friend_birthday'] = '我最好朋友的生日？';
    $_LANG['passwd_questions']['old_address']     = '我兒時居住地的地址？';
    $_LANG['passwd_questions']['motto']           = '我的座右銘是？';
    $_LANG['passwd_questions']['favorite_movie']  = '我最喜愛的電影？';
    $_LANG['passwd_questions']['favorite_song']   = '我最喜愛的歌曲？';
    $_LANG['passwd_questions']['favorite_food']   = '我最喜愛的食物？';
    $_LANG['passwd_questions']['interest']        = '我最大的愛好？';
    $_LANG['passwd_questions']['favorite_novel']  = '我最喜歡的小說？';
    $_LANG['passwd_questions']['favorite_equipe'] = '我最喜歡的運動隊？';
    /* 密碼提示問題 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    $smarty->assign('footer', get_footer());
    $smarty->display('user_passport.html');
}
/* 註冊會員的處理 */
elseif ($act == 'act_register')
{
        include_once(ROOT_PATH . 'includes/lib_passport.php');

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
        $other['msn'] = isset($_POST['extend_field1']) ? $_POST['extend_field1'] : '';
        $other['qq'] = isset($_POST['extend_field2']) ? $_POST['extend_field2'] : '';
        $other['office_phone'] = isset($_POST['extend_field3']) ? $_POST['extend_field3'] : '';
        $other['home_phone'] = isset($_POST['extend_field4']) ? $_POST['extend_field4'] : '';
        $other['mobile_phone'] = isset($_POST['extend_field5']) ? $_POST['extend_field5'] : '';
        $sel_question = empty($_POST['sel_question']) ? '' : $_POST['sel_question'];
        $passwd_answer = isset($_POST['passwd_answer']) ? trim($_POST['passwd_answer']) : '';

        $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';

        if (m_register($username, $password, $email, $other) !== false)
        {
            /*把新註冊用戶的擴展信息插入數據庫*/
            $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //讀出所有自定義擴展字段的id
            $fields_arr = $db->getAll($sql);

            $extend_field_str = '';    //生成擴展字段的內容字符串
            foreach ($fields_arr AS $val)
            {
                $extend_field_index = 'extend_field' . $val['id'];
                if(!empty($_POST[$extend_field_index]))
                {
                    $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
                    $extend_field_str .= " ('" . $_SESSION['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
                }
            }
            $extend_field_str = substr($extend_field_str, 0, -1);

            if ($extend_field_str)      //插入註冊擴展數據
            {
                $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
                $db->query($sql);
            }

            /* 寫入密碼提示問題和答案 */
            if (!empty($passwd_answer) && !empty($sel_question))
            {
                $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
                $db->query($sql);
            }

            $ucdata = empty($user->ucdata)? "" : $user->ucdata;
            $Loaction = 'index.php';
            ecs_header("Location: $Loaction\n");
        }
}

/* 用戶中心 */
else
{
    if ($_SESSION['user_id'] > 0)
    {
        show_user_center();
    }
    else
    {
        $smarty->assign('footer', get_footer());
        $smarty->display('login.html');
    }
}

/**
 * 用戶中心顯示
 */
function show_user_center()
{
    $best_goods = get_recommend_goods('best');
    if (count($best_goods) > 0)
    {
        foreach  ($best_goods as $key => $best_data)
        {
            $best_goods[$key]['shop_price'] = encode_output($best_data['shop_price']);
            $best_goods[$key]['name'] = encode_output($best_data['name']);
        }
    }
    $GLOBALS['smarty']->assign('best_goods' , $best_goods);
    $GLOBALS['smarty']->assign('footer', get_footer());
    $GLOBALS['smarty']->display('user.html');
}

/**
 * 手機註冊
 */
function m_register($username, $password, $email, $other = array())
{
    /* 檢查username */
    if (empty($username))
    {
        echo '用戶名不能為空';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    /* 檢查email */
    if (empty($email))
    {
        echo 'emial不能為空';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    /* 檢查是否和管理員重名 */
    if (admin_registered($username))
    {
        echo '此用戶已存在！';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    if (!$GLOBALS['user']->add_user($username, $password, $email))
    {
        echo '註冊失敗！';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        //註冊失敗
        return false;
    }
    else
    {
        //註冊成功

        /* 設置成登錄狀態 */
        $GLOBALS['user']->set_session($username);
        $GLOBALS['user']->set_cookie($username);

     }

        //定義other合法的變量數組
        $other_key_array = array('msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone');
        $update_data['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
        if ($other)
        {
            foreach ($other as $key=>$val)
            {
                //刪除非法key值
                if (!in_array($key, $other_key_array))
                {
                    unset($other[$key]);
                }
                else
                {
                    $other[$key] =  htmlspecialchars(trim($val)); //防止用戶輸入javascript代碼
                }
            }
            $update_data = array_merge($update_data, $other);
        }
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $update_data, 'UPDATE', 'user_id = ' . $_SESSION['user_id']);

        update_user_info();      // 更新用戶信息

        return true;

}
?>