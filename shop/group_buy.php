<?php

/**
 * ECSHOP 團購商品前台文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: group_buy.php 17167 2010-05-28 06:10:40Z sxc_shop $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/*------------------------------------------------------ */
//-- act 操作項的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 團購商品 --> 團購活動商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得團購活動總數 */
    $count = group_buy_count();
    if ($count > 0)
    {
        /* 取得每頁記錄數 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 計算總頁數 */
        $page_count = ceil($count / $size);

        /* 取得當前頁 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 緩存id：語言 - 每頁記錄數 - 當前頁 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 緩存id：語言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果沒有緩存，生成緩存 */
    if (!$smarty->is_cached('group_buy_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得當前頁的團購活動 */
            $gb_list = group_buy_list($size, $page);
            $smarty->assign('gb_list',  $gb_list);

            /* 設置分頁鏈接 */
            $pager = get_pager('group_buy.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板賦值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 頁面標題
        $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置
        $smarty->assign('categories', get_categories_tree()); // 分類樹
        $smarty->assign('helps',      get_shop_help());       // 網店幫助
        $smarty->assign('top_goods',  get_top10());           // 銷售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typegroup_buy.xml" : 'feed.php?type=group_buy'); // RSS URL

        assign_dynamic('group_buy_list');
    }

    /* 顯示模板 */
    $smarty->display('group_buy_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 團購商品 --> 商品詳情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得參數：團購活動id */
    $group_buy_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得團購活動信息 */
    $group_buy = group_buy_info($group_buy_id);

    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }
//    elseif ($group_buy['is_on_sale'] == 0 || $group_buy['is_alone_sale'] == 0)
//    {
//        header("Location: ./\n");
//        exit;
//    }

    /* 緩存id：語言，團購活動id，狀態，（如果是進行中）當前數量和是否登錄 */
    $cache_id = $_CFG['lang'] . '-' . $group_buy_id . '-' . $group_buy['status'];
    if ($group_buy['status'] == GBS_UNDER_WAY)
    {
        $cache_id = $cache_id . '-' . $group_buy['valid_goods'] . '-' . intval($_SESSION['user_id'] > 0);
    }
    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果沒有緩存，生成緩存 */
    if (!$smarty->is_cached('group_buy_goods.dwt', $cache_id))
    {
        $group_buy['gmt_end_date'] = $group_buy['end_date'];
        $smarty->assign('group_buy', $group_buy);

        /* 取得團購商品信息 */
        $goods_id = $group_buy['goods_id'];
        $goods = goods_info($goods_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);
        $smarty->assign('gb_goods', $goods);

        /* 取得商品的規格 */
        $properties = get_goods_properties($goods_id);
        $smarty->assign('specification', $properties['spe']); // 商品規格

        //模板賦值
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 頁面標題
        $smarty->assign('ur_here',    $position['ur_here']);  // 當前位置

        $smarty->assign('categories', get_categories_tree()); // 分類樹
        $smarty->assign('helps',      get_shop_help());       // 網店幫助
        $smarty->assign('top_goods',  get_top10());           // 銷售排行
        $smarty->assign('promotion_info', get_promotion_info());
        assign_dynamic('group_buy_goods');
    }

    //更新商品點擊次數
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $group_buy['goods_id'] . "'";
    $db->query($sql);

    $smarty->assign('now_time',  gmtime());           // 當前系統時間
    $smarty->display('group_buy_goods.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 團購商品 --> 購買
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'buy')
{
    /* 查詢：判斷是否登錄 */
    if ($_SESSION['user_id'] <= 0)
    {
        show_message($_LANG['gb_error_login'], '', '', 'error');
    }

    /* 查詢：取得參數：團購活動id */
    $group_buy_id = isset($_POST['group_buy_id']) ? intval($_POST['group_buy_id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查詢：取得數量 */
    $number = isset($_POST['number']) ? intval($_POST['number']) : 1;
    $number = $number < 1 ? 1 : $number;

    /* 查詢：取得團購活動信息 */
    $group_buy = group_buy_info($group_buy_id, $number);
    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查詢：檢查團購活動是否是進行中 */
    if ($group_buy['status'] != GBS_UNDER_WAY)
    {
        show_message($_LANG['gb_error_status'], '', '', 'error');
    }

    /* 查詢：取得團購商品信息 */
    $goods = goods_info($group_buy['goods_id']);
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查詢：判斷數量是否足夠 */
    if (($group_buy['restrict_amount'] > 0 && $number > ($group_buy['restrict_amount'] - $group_buy['valid_goods'])) || $number > $goods['goods_number'])
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查詢：取得規格 */
    $specs = '';
    foreach ($_POST as $key => $value)
    {
        if (strpos($key, 'spec_') !== false)
        {
            $specs .= ',' . intval($value);
        }
    }
    $specs = trim($specs, ',');

    /* 查詢：如果商品有規格則取規格商品信息 配件除外 */
    if ($specs)
    {
        $_specs = explode(',', $specs);
        $product_info = get_products_info($goods['goods_id'], $_specs);
    }

    empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';

    /* 查詢：判斷指定規格的貨品數量是否足夠 */
    if ($specs && $number > $product_info['product_number'])
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查詢：查詢規格名稱和值，不考慮價格 */
    $attr_list = array();
    $sql = "SELECT a.attr_name, g.attr_value " .
            "FROM " . $ecs->table('goods_attr') . " AS g, " .
                $ecs->table('attribute') . " AS a " .
            "WHERE g.attr_id = a.attr_id " .
            "AND g.goods_attr_id " . db_create_in($specs);
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
    }
    $goods_attr = join(chr(13) . chr(10), $attr_list);

    /* 更新：清空購物車中所有團購商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_GROUP_BUY_GOODS);

    /* 更新：加入購物車 */
    $goods_price = $group_buy['deposit'] > 0 ? $group_buy['deposit'] : $group_buy['cur_price'];
    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => SESS_ID,
        'goods_id'       => $group_buy['goods_id'],
        'product_id'     => $product_info['product_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $goods_price,
        'goods_number'   => $number,
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $specs,
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_GROUP_BUY_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 更新：記錄購物流程類型：團購 */
    $_SESSION['flow_type'] = CART_GROUP_BUY_GOODS;
    $_SESSION['extension_code'] = 'group_buy';
    $_SESSION['extension_id'] = $group_buy_id;

    /* 進入收貨人頁面 */
    ecs_header("Location: ./flow.php?step=consignee\n");
    exit;
}

/* 取得團購活動總數 */
function group_buy_count()
{
    $now = gmtime();
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_type = '" . GAT_GROUP_BUY . "' " .
            "AND start_time <= '$now' AND is_finished < 3";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某頁的所有團購活動
 * @param   int     $size   每頁記錄數
 * @param   int     $page   當前頁
 * @return  array
 */
function group_buy_list($size, $page)
{
    /* 取得團購活動 */
    $gb_list = array();
    $now = gmtime();
    $sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, b.act_id AS group_buy_id, ".
                "b.start_time AS start_date, b.end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON b.goods_id = g.goods_id " .
            "WHERE b.act_type = '" . GAT_GROUP_BUY . "' " .
            "AND b.start_time <= '$now' AND b.is_finished < 3 ORDER BY b.act_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($group_buy = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($group_buy['ext_info']);
        $group_buy = array_merge($group_buy, $ext_info);

        /* 格式化時間 */
        $group_buy['formated_start_date']   = local_date($GLOBALS['_CFG']['time_format'], $group_buy['start_date']);
        $group_buy['formated_end_date']     = local_date($GLOBALS['_CFG']['time_format'], $group_buy['end_date']);

        /* 格式化保證金 */
        $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

        /* 處理價格階梯 */
        $price_ladder = $group_buy['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder))
        {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
        }
        else
        {
            foreach ($price_ladder as $key => $amount_price)
            {
                $price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
            }
        }
        $group_buy['price_ladder'] = $price_ladder;

        /* 處理圖片 */
        if (empty($group_buy['goods_thumb']))
        {
            $group_buy['goods_thumb'] = get_image_path($group_buy['goods_id'], $group_buy['goods_thumb'], true);
        }
        /* 處理鏈接 */
        $group_buy['url'] = build_uri('group_buy', array('gbid'=>$group_buy['group_buy_id']));
        /* 加入數組 */
        $gb_list[] = $group_buy;
    }

    return $gb_list;
}

?>