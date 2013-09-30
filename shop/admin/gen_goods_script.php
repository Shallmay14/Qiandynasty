<?php
/**
 * ECSHOP 生成顯示商品的js代碼
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: gen_goods_script.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 生成代碼
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'setup')
{
    /* 檢查權限 */
    admin_priv('gen_goods_script');

    /* 編碼 */
    $lang_list = array(
        'UTF8'   => $_LANG['charset']['utf8'],
        'GB2312' => $_LANG['charset']['zh_cn'],
        'BIG5'   => $_LANG['charset']['zh_tw'],
    );

    /* 參數賦值 */
    $ur_here = $_LANG['16_goods_script'];
    $smarty->assign('ur_here',    $ur_here);
    $smarty->assign('cat_list',   cat_list());
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('intro_list', $_LANG['intro']);
    $smarty->assign('url',        $ecs->url());
    $smarty->assign('lang_list',  $lang_list);

    /* 顯示模板 */
    assign_query_info();
    $smarty->display('gen_goods_script.htm');
}

?>