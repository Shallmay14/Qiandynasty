<?php

/**
 * ECSHOP 奪寶奇兵前台頁面
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: snatch.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 如果用沒有指定活動id，將頁面重定向到即將結束的活動
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    //默認顯示頁面
    $_REQUEST['act'] = 'main';
}

/* 設置活動的SESSION */
if (empty($_REQUEST['id']))
{
    $id = get_last_snatch();
    if ($id)
    {
        $page = build_uri('snatch', array('sid'=>$id));
        ecs_header("Location: $page\n");
        exit;
    }
    else
    {
        /* 當前沒有任何可默認的活動 */
        $id = 0;
    }
}
else
{
   $id = intval($_REQUEST['id']);
}

/* 顯示頁面部分 */
if ($_REQUEST['act'] == 'main')
{
    $goods = get_snatch($id);
    if ($goods)
    {
        $position = assign_ur_here(0,$goods['snatch_name']);
        $myprice = get_myprice($id);
        if ($goods['is_end'])
        {
            //如果活動已經結束,獲取活動結果
            $smarty->assign('result',  get_snatch_result($id));
        }
        $smarty->assign('id',          $id);
        $smarty->assign('snatch_goods',       $goods); // 競價商品
        $smarty->assign('myprice',     get_myprice($id));
        if ($goods['product_id'] > 0)
        {
            $goods_specifications = get_specifications_list($goods['goods_id']);

            $good_products = get_good_products($goods['goods_id'], 'AND product_id = ' . $goods['product_id']);

            $_good_products = explode('|', $good_products[0]['goods_attr']);
            $products_info = '';
            foreach ($_good_products as $value)
            {
                $products_info .= ' ' . $goods_specifications[$value]['attr_name'] . '：' . $goods_specifications[$value]['attr_value'];
            }
            $smarty->assign('products_info',     $products_info);
            unset($goods_specifications, $good_products, $_good_products,  $products_info);
        }
    }
    else
    {
        show_message($_LANG['now_not_snatch']);
    }

    /* 調查 */
    $vote = get_vote();
    if (!empty($vote))
    {
        $smarty->assign('vote_id', $vote['id']);
        $smarty->assign('vote',    $vote['content']);
    }

    assign_template();
    assign_dynamic('snatch');
    $smarty->assign('page_title',  $position['title']);
    $smarty->assign('ur_here',     $position['ur_here']);
    $smarty->assign('categories',  get_categories_tree()); // 分類樹
    $smarty->assign('helps',       get_shop_help());       // 網店幫助
    $smarty->assign('snatch_list', get_snatch_list());     //所有有效的奪寶奇兵列表
    $smarty->assign('price_list',  get_price_list($id));
    $smarty->assign('promotion_info', get_promotion_info());
    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typesnatch.xml" : 'feed.php?type=snatch'); // RSS URL
    $smarty->display('snatch.dwt');

    exit;
}

/* 最新出價列表 */
if ($_REQUEST['act'] == 'new_price_list')
{
    $smarty->assign('price_list',  get_price_list($id));
    $smarty->display('library/snatch_price.lbi');

    exit;
}

/* 用戶出價處理 */
if ($_REQUEST['act'] == 'bid')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    $json = new JSON();
    $result = array('error'=>0, 'content'=>'');

    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $price = round($price, 2);

    /* 測試是否登陸 */
    if (empty($_SESSION['user_id']))
    {
        $result['error'] = 1;
        $result['content'] = $_LANG['not_login'];
        die($json->encode($result));
    }

    /* 獲取活動基本信息用於校驗 */
    $sql = 'SELECT act_name AS snatch_name, end_time, ext_info FROM ' . $GLOBALS['ecs']->table('goods_activity') . " WHERE act_id ='$id'";
    $row = $db->getRow($sql, 'SILENT');

    if ($row)
    {
        $info = unserialize($row['ext_info']);
        if ($info)
        {
            foreach ($info as $key => $val)
            {
                $row[$key] = $val;
            }
        }
    }

    if (empty($row))
    {
        $result['error'] = 1;
        $result['content'] = $db->error();
        die($json->encode($result));
    }

    if ($row['end_time']< gmtime() )
    {
        $result['error'] = 1;
        $result['content'] = $_LANG['snatch_is_end'];
        die($json->encode($result));
    }

    /* 檢查出價是否合理 */
    if ($price < $row['start_price'] || $price > $row['end_price'])
    {
        $result['error'] = 1;
        $result['content'] = sprintf($GLOBALS['_LANG']['not_in_range'],$row['start_price'], $row['end_price']);
        die($json->encode($result));
    }

    /* 檢查用戶是否已經出同一價格 */
    $sql = 'SELECT COUNT(*) FROM '.$GLOBALS['ecs']->table('snatch_log'). " WHERE snatch_id = '$id' AND user_id = '$_SESSION[user_id]' AND bid_price = '$price'";
    if ($GLOBALS['db']->getOne($sql) > 0)
    {
        $result['error'] = 1;
        $result['content'] = sprintf($GLOBALS['_LANG']['also_bid'], price_format($price, false));
        die($json->encode($result));
    }

    /* 檢查用戶積分是否足夠 */
    $sql = 'SELECT pay_points FROM ' .$ecs->table('users'). " WHERE user_id = '" . $_SESSION['user_id']. "'";
    $pay_points = $db->getOne($sql);
    if ($row['cost_points'] > $pay_points)
    {
        $result['error'] = 1;
        $result['content'] = $_LANG['lack_pay_points'];
        die($json->encode($result));
    }

    log_account_change($_SESSION['user_id'], 0, 0, 0, 0-$row['cost_points'],sprintf($_LANG['snatch_log'], $row['snatch_name'])); //扣除用戶積分
    $sql = 'INSERT INTO ' .$ecs->table('snatch_log'). '(snatch_id, user_id, bid_price, bid_time) VALUES'.
           "('$id', '" .$_SESSION['user_id']. "', '" .$price."', " .gmtime(). ")";
    $db->query($sql);

    $smarty->assign('myprice',  get_myprice($id));
    $smarty->assign('id',       $id);
    $result['content'] = $smarty->fetch('library/snatch.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 購買商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'buy')
{
    if (empty($id))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    if (empty($_SESSION['user_id']))
    {
        show_message($_LANG['not_login']);
    }

    $snatch = get_snatch($id);


    if (empty($snatch))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 未結束，不能購買 */
    if (empty($snatch['is_end']))
    {
        $page = build_uri('snatch', array('sid'=>$id));
        ecs_header("Location: $page\n");
        exit;
    }

    $result = get_snatch_result($id);

    if ($_SESSION['user_id'] != $result['user_id'])
    {
        show_message($_LANG['not_for_you']);
    }

    //檢查是否已經購買過
    if ($result['order_count'] > 0)
    {
        show_message($_LANG['order_placed']);
    }

    /* 處理規格屬性 */
    $goods_attr = '';
    $goods_attr_id = '';
    if ($snatch['product_id'] > 0)
    {
        $product_info = get_good_products($snatch['goods_id'], 'AND product_id = ' . $snatch['product_id']);

        $goods_attr_id = str_replace('|', ',', $product_info[0]['goods_attr']);

        $attr_list = array();
        $sql = "SELECT a.attr_name, g.attr_value " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_attr_id " . db_create_in($goods_attr_id);
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
        }
        $goods_attr = join('', $attr_list);
    }
    else
    {
        $snatch['product_id'] = 0;
    }

    /* 清空購物車中所有商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_SNATCH_GOODS);

    /* 加入購物車 */
    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => SESS_ID,
        'goods_id'       => $snatch['goods_id'],
        'product_id'     => $snatch['product_id'],
        'goods_sn'       => addslashes($snatch['goods_sn']),
        'goods_name'     => addslashes($snatch['goods_name']),
        'market_price'   => $snatch['market_price'],
        'goods_price'    => $result['buy_price'],
        'goods_number'   => 1,
        'goods_attr'     => $goods_attr,
        'goods_attr_id'  => $goods_attr_id,
        'is_real'        => $snatch['is_real'],
        'extension_code' => addslashes($snatch['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_SNATCH_GOODS,
        'is_gift'        => 0
    );

    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 記錄購物流程類型：奪寶奇兵 */
    $_SESSION['flow_type'] = CART_SNATCH_GOODS;
    $_SESSION['extension_code'] = 'snatch';
    $_SESSION['extension_id'] = $id;

    /* 進入收貨人頁面 */
    ecs_header("Location: ./flow.php?step=consignee\n");
    exit;

}

/**
 * 取得用戶對當前活動的所出過的價格
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_myprice($id)
{
    $my_only_price  = array();
    $my_price       = array();
    $pay_points     = 0;
    $bid_price      = array();
    if (!empty($_SESSION['user_id']))
    {
        /* 取得用戶所有價格 */
        $sql = 'SELECT bid_price FROM '.$GLOBALS['ecs']->table('snatch_log'). " WHERE snatch_id = '$id' AND user_id = '$_SESSION[user_id]' ORDER BY bid_time DESC";
        $my_price = $GLOBALS['db']->GetCol($sql);

        if ($my_price)
        {
            /* 取得用戶唯一價格 */
            $sql = 'SELECT bid_price , count(*) AS num FROM '.$GLOBALS['ecs']->table('snatch_log'). "  WHERE snatch_id ='$id' AND bid_price " . db_create_in(join(',', $my_price)). ' GROUP BY bid_price HAVING num = 1';
            $my_only_price = $GLOBALS['db']->GetCol($sql);
        }

        for ($i = 0, $count = count($my_price); $i < $count; $i++)
        {
            $bid_price[] = array('price' => price_format($my_price[$i], false),
                                 'is_only' => in_array($my_price[$i],$my_only_price)
                                );
        }

        $sql = 'SELECT pay_points FROM '. $GLOBALS['ecs']->table('users')." WHERE user_id = '$_SESSION[user_id]'";
        $pay_points = $GLOBALS['db']->GetOne($sql);
        $pay_points = $pay_points.$GLOBALS['_CFG']['integral_name'];
    }

    /* 活動結束時間 */
    $sql = 'SELECT end_time FROM ' .$GLOBALS['ecs']->table('goods_activity').
           " WHERE act_id = '$id' AND act_type=" . GAT_SNATCH;
    $end_time = $GLOBALS['db']->getOne($sql);
    $my_price = array(
        'pay_points'    => $pay_points,
        'bid_price'     => $bid_price,
        'is_end'        => gmtime() > $end_time
        );

    return $my_price;
}

/**
 * 取得當前活動的前n個出價
 *
 * @access  public
 * @param   int  $num  列表個數(取前5個)
 *
 * @return void
 */
function get_price_list($id, $num = 5)
{
    $sql = 'SELECT t1.log_id, t1.bid_price, t2.user_name FROM '.$GLOBALS['ecs']->table('snatch_log').' AS t1, '.$GLOBALS['ecs']->table('users')." AS t2 WHERE snatch_id = '$id' AND t1.user_id = t2.user_id ORDER BY t1.log_id DESC LIMIT $num";
    $res = $GLOBALS['db']->query($sql);
    $price_list = array();
    while ($row = $GLOBALS['db']->FetchRow($res))
    {
        $price_list[] = array('bid_price'=>price_format($row['bid_price'], false),'user_name'=>$row['user_name']);
    }
    return $price_list;
}

/**
 * 取的最近的幾次活動。
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_snatch_list($num = 10)
{
    $now = gmtime();
    $sql = 'SELECT act_id AS snatch_id, act_name AS snatch_name, end_time '.
           ' FROM ' . $GLOBALS['ecs']->table('goods_activity').
           " WHERE start_time <= '$now' AND act_type=" . GAT_SNATCH .
           " ORDER BY end_time DESC LIMIT $num";
    $snatch_list = array();
    $overtime = 0;
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->FetchRow($res))
    {
        $overtime = $row['end_time'] > $now ? 0 : 1;
        $snatch_list[] = array(
            'snatch_id' => $row['snatch_id'],
            'snatch_name' => $row['snatch_name'],
            'overtime' => $overtime,
            'url'=>build_uri('snatch', array('sid'=>$row['snatch_id']))
                            );
    }
    return $snatch_list;

}

/**
 * 取得當前活動信息
 *
 * @access  public
 *
 * @return 活動名稱
 */
function get_snatch($id)
{
    $sql = "SELECT g.goods_id, g.goods_sn, g.is_real, g.goods_name, g.extension_code, g.market_price, g.shop_price AS org_price, product_id, " .
                    "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
                    "g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, " .
                    "ga.act_name AS snatch_name, ga.start_time, ga.end_time, ga.ext_info, ga.act_desc AS `desc` ".
                "FROM " .$GLOBALS['ecs']->table('goods_activity'). " AS ga " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods')." AS g " .
                    "ON g.goods_id = ga.goods_id " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE ga.act_id = '$id' AND g.is_delete = 0";

    $goods = $GLOBALS['db']->GetRow($sql);

    if ($goods)
    {
        $promote_price          = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
        $goods['formated_market_price']  = price_format($goods['market_price']);
        $goods['formated_shop_price']    = price_format($goods['shop_price']);
        $goods['formated_promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $goods['goods_thumb']   = get_image_path($goods['goods_id'], $goods['goods_thumb'], true);
        $goods['url']           = build_uri('goods', array('gid'=>$goods['goods_id']), $goods['goods_name']);
        $goods['start_time']    = local_date($GLOBALS['_CFG']['time_format'], $goods['start_time']);

        $info = unserialize($goods['ext_info']);
        if ($info)
        {
            foreach ($info as $key => $val)
            {
                $goods[$key] = $val;
            }
            $goods['is_end'] = gmtime() > $goods['end_time'];
            $goods['formated_start_price'] = price_format($goods['start_price']);
            $goods['formated_end_price'] = price_format($goods['end_price']);
            $goods['formated_max_price'] = price_format($goods['max_price']);
        }
        /* 將結束日期格式化為格林威治標準時間時間戳 */
        $goods['gmt_end_time']  = $goods['end_time'];
        $goods['end_time']      = local_date($GLOBALS['_CFG']['time_format'], $goods['end_time']);
        $goods['snatch_time']   = sprintf($GLOBALS['_LANG']['snatch_start_time'], $goods['start_time'], $goods['end_time']);

        return $goods;
    }
    else
    {
        return false;
    }
}

/**
 * 獲取最近要到期的活動id，沒有則返回 0
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_last_snatch()
{
    $now = gmtime();
    $sql = 'SELECT act_id FROM ' . $GLOBALS['ecs']->table('goods_activity').
           " WHERE  start_time < '$now' AND end_time > '$now' AND act_type = " . GAT_SNATCH .
           " ORDER BY end_time ASC LIMIT 1";
    return $GLOBALS['db']->GetOne($sql);
}

?>