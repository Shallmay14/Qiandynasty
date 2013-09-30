<?php

/**
 * ECSHOP 用戶相關函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_clips.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 *  獲取指定用戶的收藏商品列表
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 * @param   int     $num            列表最大數量
 * @param   int     $start          列表其實位置
 *
 * @return  array   $arr
 */
function get_collection_goods($user_id, $num = 10, $start = 0)
{
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, '.
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.promote_price, g.promote_start_date,g.promote_end_date, c.rec_id, c.is_attention' .
            ' FROM ' . $GLOBALS['ecs']->table('collect_goods') . ' AS c' .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ".
                "ON g.goods_id = c.goods_id ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            " WHERE c.user_id = '$user_id' ORDER BY c.rec_id DESC";
    $res = $GLOBALS['db'] -> selectLimit($sql, $num, $start);

    $goods_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $goods_list[$row['goods_id']]['rec_id']        = $row['rec_id'];
        $goods_list[$row['goods_id']]['is_attention']  = $row['is_attention'];
        $goods_list[$row['goods_id']]['goods_id']      = $row['goods_id'];
        $goods_list[$row['goods_id']]['goods_name']    = $row['goods_name'];
        $goods_list[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $goods_list[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $goods_list[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $goods_list[$row['goods_id']]['url']           = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
    }

    return $goods_list;
}

/**
 *  查看此商品是否已進行過缺貨登記
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 * @param   int     $goods_id       商品ID
 *
 * @return  int
 */
function get_booking_rec($user_id, $goods_id)
{
    $sql = 'SELECT COUNT(*) '.
           'FROM ' .$GLOBALS['ecs']->table('booking_goods').
           "WHERE user_id = '$user_id' AND goods_id = '$goods_id' AND is_dispose = 0";

    return $GLOBALS['db']->getOne($sql);
}

/**
 *  獲取指定用戶的留言
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 * @param   int     $user_name      用戶名
 * @param   int     $num            列表最大數量
 * @param   int     $start          列表其實位置
 * @return  array   $msg            留言及回覆列表
 * @return  string  $order_id       訂單ID
 */
function get_message_list($user_id, $user_name, $num, $start, $order_id = 0)
{
    /* 獲取留言數據 */
    $msg = array();
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('feedback');
    if ($order_id)
    {
        $sql .= " WHERE parent_id = 0 AND order_id = '$order_id' AND user_id = '$user_id' ORDER BY msg_time DESC";
    }
    else
    {
        $sql .= " WHERE parent_id = 0 AND user_id = '$user_id' AND user_name = '" . $_SESSION['user_name'] . "' AND order_id=0 ORDER BY msg_time DESC";
    }

    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        /* 取得留言的回覆 */
        //if (empty($order_id))
        //{
            $reply = array();
            $sql   = "SELECT user_name, user_email, msg_time, msg_content".
                     " FROM " .$GLOBALS['ecs']->table('feedback') .
                     " WHERE parent_id = '" . $rows['msg_id'] . "'";
            $reply = $GLOBALS['db']->getRow($sql);

            if ($reply)
            {
                $msg[$rows['msg_id']]['re_user_name']   = $reply['user_name'];
                $msg[$rows['msg_id']]['re_user_email']  = $reply['user_email'];
                $msg[$rows['msg_id']]['re_msg_time']    = local_date($GLOBALS['_CFG']['time_format'], $reply['msg_time']);
                $msg[$rows['msg_id']]['re_msg_content'] = nl2br(htmlspecialchars($reply['msg_content']));
            }
        //}

        $msg[$rows['msg_id']]['msg_content'] = nl2br(htmlspecialchars($rows['msg_content']));
        $msg[$rows['msg_id']]['msg_time']    = local_date($GLOBALS['_CFG']['time_format'], $rows['msg_time']);
        $msg[$rows['msg_id']]['msg_type']    = $order_id ? $rows['user_name'] : $GLOBALS['_LANG']['type'][$rows['msg_type']];
        $msg[$rows['msg_id']]['msg_title']   = nl2br(htmlspecialchars($rows['msg_title']));
        $msg[$rows['msg_id']]['message_img'] = $rows['message_img'];
        $msg[$rows['msg_id']]['order_id'] = $rows['order_id'];
    }

    return $msg;
}

/**
 *  添加留言函數
 *
 * @access  public
 * @param   array       $message
 *
 * @return  boolen      $bool
 */
function add_message($message)
{
    $upload_size_limit = $GLOBALS['_CFG']['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $GLOBALS['_CFG']['upload_size_limit'];
    $status = 1 - $GLOBALS['_CFG']['message_check'];

    $last_char = strtolower($upload_size_limit{strlen($upload_size_limit)-1});

    switch ($last_char)
    {
        case 'm':
            $upload_size_limit *= 1024*1024;
            break;
        case 'k':
            $upload_size_limit *= 1024;
            break;
    }

    if ($message['upload'])
    {
        if($_FILES['message_img']['size'] / 1024 > $upload_size_limit)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['upload_file_limit'], $upload_size_limit));
            return false;
        }
        $img_name = upload_file($_FILES['message_img'], 'feedbackimg');

        if ($img_name === false)
        {
            return false;
        }
    }
    else
    {
        $img_name = '';
    }

    if (empty($message['msg_title']))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['msg_title_empty']);

        return false;
    }

    $message['msg_area'] = isset($message['msg_area']) ? intval($message['msg_area']) : 0;
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('feedback') .
            " (msg_id, parent_id, user_id, user_name, user_email, msg_title, msg_type, msg_status,  msg_content, msg_time, message_img, order_id, msg_area)".
            " VALUES (NULL, 0, '$message[user_id]', '$message[user_name]', '$message[user_email]', ".
            " '$message[msg_title]', '$message[msg_type]', '$status', '$message[msg_content]', '".gmtime()."', '$img_name', '$message[order_id]', '$message[msg_area]')";
    $GLOBALS['db']->query($sql);

    return true;
}

/**
 *  獲取用戶的tags
 *
 * @access  public
 * @param   int         $user_id        用戶ID
 *
 * @return array        $arr            tags列表
 */
function get_user_tags($user_id = 0)
{
    if (empty($user_id))
    {
        $GLOBALS['error_no'] = 1;

        return false;
    }

    $tags = get_tags(0, $user_id);

    if (!empty($tags))
    {
        color_tag($tags);
    }

    return $tags;
}

/**
 *  驗證性的刪除某個tag
 *
 * @access  public
 * @param   int         $tag_words      tag的ID
 * @param   int         $user_id        用戶的ID
 *
 * @return  boolen      bool
 */
function delete_tag($tag_words, $user_id)
{
     $sql = "DELETE FROM ".$GLOBALS['ecs']->table('tag').
            " WHERE tag_words = '$tag_words' AND user_id = '$user_id'";

     return $GLOBALS['db']->query($sql);
}

/**
 *  獲取某用戶的缺貨登記列表
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 * @param   int     $num            列表最大數量
 * @param   int     $start          列表其實位置
 *
 * @return  array   $booking
 */
function get_booking_list($user_id, $num, $start)
{
    $booking = array();
    $sql = "SELECT bg.rec_id, bg.goods_id, bg.goods_number, bg.booking_time, bg.dispose_note, g.goods_name ".
           "FROM " .$GLOBALS['ecs']->table('booking_goods')." AS bg , " .$GLOBALS['ecs']->table('goods')." AS g". " WHERE bg.goods_id = g.goods_id AND bg.user_id = '$user_id' ORDER BY bg.booking_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if (empty($row['dispose_note']))
        {
            $row['dispose_note'] = 'N/A';
        }
        $booking[] = array('rec_id'       => $row['rec_id'],
                           'goods_name'   => $row['goods_name'],
                           'goods_number' => $row['goods_number'],
                           'booking_time' => local_date($GLOBALS['_CFG']['date_format'], $row['booking_time']),
                           'dispose_note' => $row['dispose_note'],
                           'url'          => build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']));
    }

    return $booking;
}

/**
 *  獲取某用戶的缺貨登記列表
 *
 * @access  public
 * @param   int     $goods_id    商品ID
 *
 * @return  array   $info
 */
function get_goodsinfo($goods_id)
{
    $info = array();
    $sql  = "SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id = '$goods_id'";

    $info['goods_name']   = $GLOBALS['db']->getOne($sql);
    $info['goods_number'] = 1;
    $info['id']           = $goods_id;

    if (!empty($_SESSION['user_id']))
    {
        $row = array();
        $sql = "SELECT ua.consignee, ua.email, ua.tel, ua.mobile ".
               "FROM ".$GLOBALS['ecs']->table('user_address')." AS ua, ".$GLOBALS['ecs']->table('users')." AS u".
               " WHERE u.address_id = ua.address_id AND u.user_id = '$_SESSION[user_id]'";
        $row = $GLOBALS['db']->getRow($sql) ;
        $info['consignee'] = empty($row['consignee']) ? '' : $row['consignee'];
        $info['email']     = empty($row['email'])     ? '' : $row['email'];
        $info['tel']       = empty($row['mobile'])    ? (empty($row['tel']) ? '' : $row['tel']) : $row['mobile'];
    }

    return $info;
}

/**
 *  驗證刪除某個收藏商品
 *
 * @access  public
 * @param   int         $booking_id     缺貨登記的ID
 * @param   int         $user_id        會員的ID
 * @return  boolen      $bool
 */
function delete_booking($booking_id, $user_id)
{
    $sql = 'DELETE FROM ' .$GLOBALS['ecs']->table('booking_goods').
           " WHERE rec_id = '$booking_id' AND user_id = '$user_id'";

    return $GLOBALS['db']->query($sql);
}

/**
 * 添加缺貨登記記錄到數據表
 * @access  public
 * @param   array $booking
 *
 * @return void
 */
function add_booking($booking)
{
    $sql = "INSERT INTO " .$GLOBALS['ecs']->table('booking_goods').
            " VALUES ('', '$_SESSION[user_id]', '$booking[email]', '$booking[linkman]', ".
                "'$booking[tel]', '$booking[goods_id]', '$booking[desc]', ".
                "'$booking[goods_amount]', '".gmtime()."', 0, '', 0, '')";
    $GLOBALS['db']->query($sql) or die ($GLOBALS['db']->errorMsg());

    return $GLOBALS['db']->insert_id();
}

/**
 * 插入會員賬目明細
 *
 * @access  public
 * @param   array     $surplus  會員余額信息
 * @param   string    $amount   余額
 *
 * @return  int
 */
function insert_user_account($surplus, $amount)
{
    $sql = 'INSERT INTO ' .$GLOBALS['ecs']->table('user_account').
           ' (user_id, admin_user, amount, add_time, paid_time, admin_note, user_note, process_type, payment, is_paid)'.
            " VALUES ('$surplus[user_id]', '', '$amount', '".gmtime()."', 0, '', '$surplus[user_note]', '$surplus[process_type]', '$surplus[payment]', 0)";
    $GLOBALS['db']->query($sql);

    return $GLOBALS['db']->insert_id();
}

/**
 * 更新會員賬目明細
 *
 * @access  public
 * @param   array     $surplus  會員余額信息
 *
 * @return  int
 */
function update_user_account($surplus)
{
    $sql = 'UPDATE ' .$GLOBALS['ecs']->table('user_account'). ' SET '.
           "amount     = '$surplus[amount]', ".
           "user_note  = '$surplus[user_note]', ".
           "payment    = '$surplus[payment]' ".
           "WHERE id   = '$surplus[rec_id]'";
    $GLOBALS['db']->query($sql);

    return $surplus['rec_id'];
}

/**
 * 將支付LOG插入數據表
 *
 * @access  public
 * @param   integer     $id         訂單編號
 * @param   float       $amount     訂單金額
 * @param   integer     $type       支付類型
 * @param   integer     $is_paid    是否已支付
 *
 * @return  int
 */
function insert_pay_log($id, $amount, $type = PAY_SURPLUS, $is_paid = 0)
{
    $sql = 'INSERT INTO ' .$GLOBALS['ecs']->table('pay_log')." (order_id, order_amount, order_type, is_paid)".
            " VALUES  ('$id', '$amount', '$type', '$is_paid')";
    $GLOBALS['db']->query($sql);

     return $GLOBALS['db']->insert_id();
}

/**
 * 取得上次未支付的pay_lig_id
 *
 * @access  public
 * @param   array     $surplus_id  余額記錄的ID
 * @param   array     $pay_type    支付的類型：預付款/訂單支付
 *
 * @return  int
 */
function get_paylog_id($surplus_id, $pay_type = PAY_SURPLUS)
{
    $sql = 'SELECT log_id FROM' .$GLOBALS['ecs']->table('pay_log').
           " WHERE order_id = '$surplus_id' AND order_type = '$pay_type' AND is_paid = 0";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 根據ID獲取當前余額操作信息
 *
 * @access  public
 * @param   int     $surplus_id  會員余額的ID
 *
 * @return  int
 */
function get_surplus_info($surplus_id)
{
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('user_account').
           " WHERE id = '$surplus_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 取得已安裝的支付方式(其中不包括線下支付的)
 * @param   bool    $include_balance    是否包含余額支付（衝值時不應包括）
 * @return  array   已安裝的配送方式列表
 */
function get_online_payment_list($include_balance = true)
{
    $sql = 'SELECT pay_id, pay_code, pay_name, pay_fee, pay_desc ' .
            'FROM ' . $GLOBALS['ecs']->table('payment') .
            " WHERE enabled = 1 AND is_cod <> 1";
    if (!$include_balance)
    {
        $sql .= " AND pay_code <> 'balance' ";
    }

    $modules = $GLOBALS['db']->getAll($sql);

    include_once(ROOT_PATH.'includes/lib_compositor.php');

    return $modules;
}

/**
 * 查詢會員余額的操作記錄
 *
 * @access  public
 * @param   int     $user_id    會員ID
 * @param   int     $num        每頁顯示數量
 * @param   int     $start      開始顯示的條數
 * @return  array
 */
function get_account_log($user_id, $num, $start)
{
    $account_log = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN)) .
           " ORDER BY add_time DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $num, $start);

    if ($res)
    {
        while ($rows = $GLOBALS['db']->fetchRow($res))
        {
            $rows['add_time']         = local_date($GLOBALS['_CFG']['date_format'], $rows['add_time']);
            $rows['admin_note']       = nl2br(htmlspecialchars($rows['admin_note']));
            $rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
            $rows['user_note']        = nl2br(htmlspecialchars($rows['user_note']));
            $rows['short_user_note']  = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
            $rows['pay_status']       = ($rows['is_paid'] == 0) ? $GLOBALS['_LANG']['un_confirm'] : $GLOBALS['_LANG']['is_confirm'];
            $rows['amount']           = price_format(abs($rows['amount']), false);

            /* 會員的操作類型： 衝值，提現 */
            if ($rows['process_type'] == 0)
            {
                $rows['type'] = $GLOBALS['_LANG']['surplus_type_0'];
            }
            else
            {
                $rows['type'] = $GLOBALS['_LANG']['surplus_type_1'];
            }

            /* 支付方式的ID */
            $sql = 'SELECT pay_id FROM ' .$GLOBALS['ecs']->table('payment').
                   " WHERE pay_name = '$rows[payment]' AND enabled = 1";
            $pid = $GLOBALS['db']->getOne($sql);

            /* 如果是預付款而且還沒有付款, 允許付款 */
            if (($rows['is_paid'] == 0) && ($rows['process_type'] == 0))
            {
                $rows['handle'] = '<a href="user.php?act=pay&id='.$rows['id'].'&pid='.$pid.'">'.$GLOBALS['_LANG']['pay'].'</a>';
            }

            $account_log[] = $rows;
        }

        return $account_log;
    }
    else
    {
         return false;
    }
}

/**
 *  刪除未確認的會員帳目信息
 *
 * @access  public
 * @param   int         $rec_id     會員余額記錄的ID
 * @param   int         $user_id    會員的ID
 * @return  boolen
 */
function del_user_account($rec_id, $user_id)
{
    $sql = 'DELETE FROM ' .$GLOBALS['ecs']->table('user_account').
           " WHERE is_paid = 0 AND id = '$rec_id' AND user_id = '$user_id'";

    return $GLOBALS['db']->query($sql);
}

/**
 * 查詢會員余額的數量
 * @access  public
 * @param   int     $user_id        會員ID
 * @return  int
 */
function get_user_surplus($user_id)
{
    $sql = "SELECT SUM(user_money) FROM " .$GLOBALS['ecs']->table('account_log').
           " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 獲取用戶中心默認頁面所需的數據
 *
 * @access  public
 * @param   int         $user_id            用戶ID
 *
 * @return  array       $info               默認頁面所需資料數組
 */
function get_user_default($user_id)
{
    $user_bonus = get_user_bonus();

    $sql = "SELECT pay_points, user_money, credit_line, last_login, is_validated FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_id = '$user_id'";
    $row = $GLOBALS['db']->getRow($sql);
    $info = array();
    $info['username']  = stripslashes($_SESSION['user_name']);
    $info['shop_name'] = $GLOBALS['_CFG']['shop_name'];
    $info['integral']  = $row['pay_points'] . $GLOBALS['_CFG']['integral_name'];
    /* 增加是否開啟會員郵件驗證開關 */
    $info['is_validate'] = ($GLOBALS['_CFG']['member_email_validate'] && !$row['is_validated'])?0:1;
    $info['credit_line'] = $row['credit_line'];
    $info['formated_credit_line'] = price_format($info['credit_line'], false);

    //如果$_SESSION中時間無效說明用戶是第一次登錄。取當前登錄時間。
    $last_time = !isset($_SESSION['last_time']) ? $row['last_login'] : $_SESSION['last_time'];

    if ($last_time == 0)
    {
        $_SESSION['last_time'] = $last_time = gmtime();
    }

    $info['last_time'] = local_date($GLOBALS['_CFG']['time_format'], $last_time);
    $info['surplus']   = price_format($row['user_money'], false);
    $info['bonus']     = sprintf($GLOBALS['_LANG']['user_bonus_info'], $user_bonus['bonus_count'], price_format($user_bonus['bonus_value'], false));

    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('order_info').
            " WHERE user_id = '" .$user_id. "' AND add_time > '" .local_strtotime('-1 months'). "'";
    $info['order_count'] = $GLOBALS['db']->getOne($sql);

    include_once(ROOT_PATH . 'includes/lib_order.php');
    $sql = "SELECT order_id, order_sn ".
            " FROM " .$GLOBALS['ecs']->table('order_info').
            " WHERE user_id = '" .$user_id. "' AND shipping_time > '" .$last_time. "'". order_query_sql('shipped');
    $info['shipped_order'] = $GLOBALS['db']->getAll($sql);

    return $info;
}

/**
 * 添加商品標籤
 *
 * @access  public
 * @param   integer     $id
 * @param   string      $tag
 * @return  void
 */
function add_tag($id, $tag)
{
    if (empty($tag))
    {
        return;
    }

    $arr = explode(',', $tag);

    foreach ($arr AS $val)
    {
        /* 檢查是否重複 */
        $sql = "SELECT COUNT(*) FROM ". $GLOBALS['ecs']->table("tag").
                " WHERE user_id = '".$_SESSION['user_id']."' AND goods_id = '$id' AND tag_words = '$val'";

        if ($GLOBALS['db']->getOne($sql) == 0)
        {
            $sql = "INSERT INTO ".$GLOBALS['ecs']->table("tag")." (user_id, goods_id, tag_words) ".
                    "VALUES ('".$_SESSION['user_id']."', '$id', '$val')";
            $GLOBALS['db']->query($sql);
        }
    }
}

/**
 * 標籤著色
 *
 * @access   public
 * @param    array
 * @author   Xuan Yan
 *
 * @return   none
 */
function color_tag(&$tags)
{
    $tagmark = array(
        array('color'=>'#666666','size'=>'0.8em','ifbold'=>1),
        array('color'=>'#333333','size'=>'0.9em','ifbold'=>0),
        array('color'=>'#006699','size'=>'1.0em','ifbold'=>1),
        array('color'=>'#CC9900','size'=>'1.1em','ifbold'=>0),
        array('color'=>'#666633','size'=>'1.2em','ifbold'=>1),
        array('color'=>'#993300','size'=>'1.3em','ifbold'=>0),
        array('color'=>'#669933','size'=>'1.4em','ifbold'=>1),
        array('color'=>'#3366FF','size'=>'1.5em','ifbold'=>0),
        array('color'=>'#197B30','size'=>'1.6em','ifbold'=>1),
    );

    $maxlevel = count($tagmark);
    $tcount = $scount = array();

    foreach($tags AS $val)
    {
        $tcount[] = $val['tag_count']; // 獲得tag個數數組
    }
    $tcount = array_unique($tcount); // 去除相同個數的tag

    sort($tcount); // 從小到大排序

    $tempcount = count($tcount); // 真正的tag級數
    $per = $maxlevel >= $tempcount ? 1 : $maxlevel / ($tempcount - 1);

    foreach ($tcount AS $key => $val)
    {
        $lvl = floor($per * $key);
        $scount[$val] = $lvl; // 計算不同個數的tag相對應的著色數組key
    }

    $rewrite = intval($GLOBALS['_CFG']['rewrite']) > 0;

    /* 遍歷所有標籤，根據引用次數設定字體大小 */
    foreach ($tags AS $key => $val)
    {
        $lvl = $scount[$val['tag_count']]; // 著色數組key

        $tags[$key]['color'] = $tagmark[$lvl]['color'];
        $tags[$key]['size']  = $tagmark[$lvl]['size'];
        $tags[$key]['bold']  = $tagmark[$lvl]['ifbold'];
        if ($rewrite)
        {
            if (strtolower(EC_CHARSET) !== 'utf-8')
            {
                $tags[$key]['url'] = 'tag-' . urlencode(urlencode($val['tag_words'])) . '.html';
            }
            else
            {
                $tags[$key]['url'] = 'tag-' . urlencode($val['tag_words']) . '.html';
            }
        }
        else
        {
            $tags[$key]['url'] = 'search.php?keywords=' . urlencode($val['tag_words']);
        }
    }
    shuffle($tags);
}

/**
 * 取得用戶等級信息
 * @access   public
 * @author   Xuan Yan
 *
 * @return array
 */
function get_rank_info()
{
    global $db,$ecs;

    if (!empty($_SESSION['user_rank']))
    {
        $sql = "SELECT rank_name, special_rank FROM " . $ecs->table('user_rank') . " WHERE rank_id = '$_SESSION[user_rank]'";
        $row = $db->getRow($sql);
        if (empty($row))
        {
            return array();
        }
        $rank_name = $row['rank_name'];
        if ($row['special_rank'])
        {
            return array('rank_name'=>$rank_name);
        }
        else
        {
            $user_rank = $db->getOne("SELECT rank_points FROM " . $ecs->table('users') . " WHERE user_id = '$_SESSION[user_id]'");
            $sql = "SELECT rank_name,min_points FROM " . $ecs->table('user_rank') . " WHERE min_points > '$user_rank' ORDER BY min_points ASC LIMIT 1";
            $rt  = $db->getRow($sql);
            $next_rank_name = $rt['rank_name'];
            $next_rank = $rt['min_points'] - $user_rank;
            return array('rank_name'=>$rank_name,'next_rank_name'=>$next_rank_name,'next_rank'=>$next_rank);
        }
    }
    else
    {
        return array();
    }
}

/**
 *  獲取用戶參與活動信息
 *
 * @access  public
 * @param   int     $user_id        用戶id
 *
 * @return  array
 */
function get_user_prompt ($user_id)
{
    $prompt = array();
    $now = gmtime();
    /* 奪寶奇兵 */
    $sql = "SELECT act_id, goods_name, end_time " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            " WHERE act_type = '" . GAT_SNATCH . "'" .
            " AND (is_finished = 1 OR (is_finished = 0 AND end_time <= '$now'))";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $act_id = $row['act_id'];
        $result = get_snatch_result($act_id);
        if (isset($result['order_count']) && $result['order_count'] == 0 && $result['user_id'] == $user_id)
        {
            $prompt[] = array(
                   'text'=>sprintf($GLOBALS['_LANG']['your_snatch'],$row['goods_name'], $row['act_id']),
                   'add_time'=> $row['end_time']
            );
        }
        if (isset($auction['last_bid']) && $auction['last_bid']['bid_user'] == $user_id && $auction['order_count'] == 0)
        {
            $prompt[] = array(
                'text' => sprintf($GLOBALS['_LANG']['your_auction'], $row['goods_name'], $row['act_id']),
                'add_time' => $row['end_time']
            );
        }
    }


    /* 競拍 */

    $sql = "SELECT act_id, goods_name, end_time " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            " WHERE act_type = '" . GAT_AUCTION . "'" .
            " AND (is_finished = 1 OR (is_finished = 0 AND end_time <= '$now'))";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $act_id = $row['act_id'];
        $auction = auction_info($act_id);
        if (isset($auction['last_bid']) && $auction['last_bid']['bid_user'] == $user_id && $auction['order_count'] == 0)
        {
            $prompt[] = array(
                'text' => sprintf($GLOBALS['_LANG']['your_auction'], $row['goods_name'], $row['act_id']),
                'add_time' => $row['end_time']
            );
        }
    }

    /* 排序 */
    $cmp = create_function('$a, $b', 'if($a["add_time"] == $b["add_time"]){return 0;};return $a["add_time"] < $b["add_time"] ? 1 : -1;');
    usort($prompt, $cmp);

    /* 格式化時間 */
    foreach ($prompt as $key => $val)
    {
        $prompt[$key]['formated_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
    }

    return $prompt;
}

/**
 *  獲取用戶評論
 *
 * @access  public
 * @param   int     $user_id        用戶id
 * @param   int     $page_size      列表最大數量
 * @param   int     $start          列表起始頁
 * @return  array
 */
function get_comment_list($user_id, $page_size, $start)
{
    $sql = "SELECT c.*, g.goods_name AS cmt_name, r.content AS reply_content, r.add_time AS reply_time ".
           " FROM " . $GLOBALS['ecs']->table('comment') . " AS c ".
           " LEFT JOIN " . $GLOBALS['ecs']->table('comment') . " AS r ".
           " ON r.parent_id = c.comment_id AND r.parent_id > 0 ".
           " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ".
           " ON c.comment_type=0 AND c.id_value = g.goods_id ".
           " WHERE c.user_id='$user_id'";
    $res = $GLOBALS['db']->SelectLimit($sql, $page_size, $start);

    $comments = array();
    $to_article = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['formated_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        if ($row['reply_time'])
        {
            $row['formated_reply_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['reply_time']);
        }
        if ($row['comment_type'] == 1)
        {
            $to_article[] = $row["id_value"];
        }
        $comments[] = $row;
    }

    if ($to_article)
    {
        $sql = "SELECT article_id , title FROM " . $GLOBALS['ecs']->table('article') . " WHERE " . db_create_in($to_article, 'article_id');
        $arr = $GLOBALS['db']->getAll($sql);
        $to_cmt_name = array();
        foreach ($arr as $row)
        {
            $to_cmt_name[$row['article_id']] = $row['title'];
        }

        foreach ($comments as $key=>$row)
        {
            if ($row['comment_type'] == 1)
            {
                $comments[$key]['cmt_name'] = isset($to_cmt_name[$row['id_value']]) ? $to_cmt_name[$row['id_value']] : '';
            }
        }
    }

    return $comments;
}
?>