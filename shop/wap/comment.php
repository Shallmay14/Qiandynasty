<?php

/**
 * ECSHOP WAP評論頁
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: comment.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$goods_id = !empty($_GET['g_id']) ? intval($_GET['g_id']) : exit();
if ($goods_id <= 0)
{
    exit();
}
/* 讀取商品信息 */
$_LANG['kilogram'] = '千克';
$_LANG['gram'] = '克';
$_LANG['home'] = '首頁';
$smarty->assign('goods_id', $goods_id);
$goods_info = get_goods_info($goods_id);
$goods_info['goods_name'] = encode_output($goods_info['goods_name']);
$goods_info['goods_brief'] = encode_output($goods_info['goods_brief']);
$smarty->assign('goods_info', $goods_info);

/* 讀評論信息 */
$comment = assign_comment($goods_id, 'comments');

$num = $comment['pager']['record_count'];
if ($num > 0)
{
    $page_num = '10';
    $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
    $pages = ceil($num / $page_num);
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
    $i = 1;
    foreach ($comment['comments'] as $key => $data)
    {
        if (($i > ($page_num * ($page - 1 ))) && ($i <= ($page_num * $page)))
        {
            $re_content = !empty($data['re_content']) ? encode_output($data['re_content']) : '';
            $re_username = !empty($data['re_username']) ? encode_output($data['re_username']) : '';
            $re_add_time = !empty($data['re_add_time']) ? substr($data['re_add_time'], 5, 14) : '';
            $comment_data[] = array('i' => $i , 'content' => encode_output($data['content']) , 'username' => encode_output($data['username']) , 'add_time' => substr($data['add_time'], 5, 14) , 're_content' => $re_content , 're_username' => $re_username , 're_add_time' => $re_add_time);
        }
        $i++;
    }
    $smarty->assign('comment_data', $comment_data);
    $pagebar = get_wap_pager($num, $page_num, $page, 'comment.php?g_id='.$goods_id, 'page');
    $smarty->assign('pagebar' , $pagebar);
}

$smarty->assign('footer', get_footer());
$smarty->display('comment.wml');

?>