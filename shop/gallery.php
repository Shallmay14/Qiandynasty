<?php

/**
 * ECSHOP 商品相冊
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: gallery.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 參數 */
$_REQUEST['id']  = isset($_REQUEST['id'])  ? intval($_REQUEST['id'])  : 0; // 商品編號
$_REQUEST['img'] = isset($_REQUEST['img']) ? intval($_REQUEST['img']) : 0; // 圖片編號

/* 獲得商品名稱 */
$sql = 'SELECT goods_name FROM ' . $ecs->table('goods') . "WHERE goods_id = '$_REQUEST[id]'";
$goods_name = $db->getOne($sql);

/* 如果該商品不存在，返回首頁 */
if ($goods_name === false)
{
    ecs_header("Location: ./\n");

    exit;
}

/* 獲得所有的圖片 */
$sql = 'SELECT img_id, img_desc, thumb_url, img_url'.
       ' FROM ' .$ecs->table('goods_gallery').
       " WHERE goods_id = '$_REQUEST[id]' ORDER BY img_id";
$img_list = $db->getAll($sql);

$img_count = count($img_list);

$gallery = array('goods_name' => htmlspecialchars($goods_name, ENT_QUOTES), 'list' => array());
if ($img_count == 0)
{
    /* 如果沒有圖片，返回商品詳情頁 */
    ecs_header('Location: goods.php?id=' . $_REQUEST['id'] . "\n");
    exit;
}
else
{
    foreach ($img_list AS $key => $img)
    {
        $gallery['list'][] = array(
            'gallery_thumb' => get_image_path($_REQUEST['id'], $img_list[$key]['thumb_url'], true, 'gallery'),
            'gallery' => get_image_path($_REQUEST['id'], $img_list[$key]['img_url'], false, 'gallery'),
            'img_desc' => $img_list[$key]['img_desc']
        );
    }
}

$smarty->assign('shop_name',  $_CFG['shop_name']);
$smarty->assign('watermark', str_replace('../', './', $_CFG['watermark']));
$smarty->assign('gallery',  $gallery);
$smarty->display('gallery.dwt');

?>