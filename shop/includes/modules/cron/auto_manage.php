<?php

/**
 * ECSHOP 程序說明
 * ===========================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ==========================================================
 * $Author: sxc_shop $
 * $Id: auto_manage.php 17135 2010-04-27 04:58:23Z sxc_shop $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
$cron_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/cron/auto_manage.php';
if (file_exists($cron_lang))
{
    global $_LANG;

    include_once($cron_lang);
}

/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代碼 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述對應的語言項 */
    $modules[$i]['desc']    = 'auto_manage_desc';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 網址 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 版本號 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'auto_manage_count', 'type' => 'select', 'value' => '5'),
    );

    return;
}
$time = gmtime();
$limit = !empty($cron['auto_manage_count']) ? $cron['auto_manage_count'] : 5;
$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('auto_manage') . " WHERE starttime > '0' AND starttime <= '$time' OR endtime > '0' AND endtime <= '$time' LIMIT $limit";
$autodb = $db->getAll($sql);
foreach ($autodb as $key => $val)
{
    $del = $up = false;
    if ($val['type'] == 'goods')
    {
        $goods = true;
        $where = " WHERE goods_id = '$val[item_id]'";
    }
    else
    {
        $goods = false;
        $where = " WHERE article_id = '$val[item_id]'";
    }


    //上下架判斷
    if(!empty($val['starttime']) && !empty($val['endtime']))
    {
        //上下架時間均設置
        if($val['starttime'] <= $time && $time < $val['endtime'])
        {
            //上架時間 <= 當前時間 < 下架時間
            $up = true;
            $del = false;
        }
        elseif($val['starttime'] >= $time && $time > $val['endtime'])
        {
            //下架時間 <= 當前時間 < 上架時間
            $up = false;
            $del = false;
        }
        elseif($val['starttime'] == $time && $time == $val['endtime'])
        {
            //下架時間 == 當前時間 == 上架時間
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('auto_manage') . "WHERE item_id = '$val[item_id]' AND type = '$val[type]'";
            $db->query($sql);
            continue;
        }
        elseif($val['starttime'] > $val['endtime'])
        {
            // 下架時間 < 上架時間 < 當前時間
            $up = true;
            $del = true;
        }
        elseif($val['starttime'] < $val['endtime'])
        {
            // 上架時間 < 下架時間 < 當前時間
            $up = false;
            $del = true;
        }
        else
        {
            // 上架時間 = 下架時間 < 當前時間
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('auto_manage') . "WHERE item_id = '$val[item_id]' AND type = '$val[type]'";
            $db->query($sql);

            continue;
        }
    }
    elseif(!empty($val['starttime']))
    {
        //只設置了上架時間
        $up = true;
        $del = true;
    }
    else
    {
        //只設置了下架時間
        $up = false;
        $del = true;
    }

    if ($goods)
    {
        if ($up)
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . " SET is_on_sale = 1 $where";
        }
        else
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . " SET is_on_sale = 0 $where";
        }
    }
    else
    {
        if ($up)
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('article') . " SET is_open = 1 $where";
        }
        else
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('article') . " SET is_open = 0 $where";
        }
    }
    $db->query($sql);
    if ($del)
    {
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('auto_manage') . "WHERE item_id = '$val[item_id]' AND type = '$val[type]'";
        $db->query($sql);
    }
    else
    {
        if($up)
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('auto_manage') . " SET starttime = 0 WHERE item_id = '$val[item_id]' AND type = '$val[type]'";
        }
        else
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('auto_manage') . " SET endtime = 0 WHERE item_id = '$val[item_id]' AND type = '$val[type]'";
        }
        $db->query($sql);
    }
}
?>