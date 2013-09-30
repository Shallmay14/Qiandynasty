<?php

/**
 * ECSHOP 商品分類管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: category.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("category"), $db, 'cat_id', 'cat_name');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 商品分類列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 獲取分類列表 */
    $cat_list = cat_list(0, 0, false);

    /* 模板賦值 */
    $smarty->assign('ur_here',      $_LANG['03_category_list']);
    $smarty->assign('action_link',  array('href' => 'category.php?act=add', 'text' => $_LANG['04_category_add']));
    $smarty->assign('full_page',    1);

    $smarty->assign('cat_info',     $cat_list);

    /* 列表頁面 */
    assign_query_info();
    $smarty->display('category_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分頁、查詢
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $cat_list = cat_list(0, 0, false);
    $smarty->assign('cat_info',     $cat_list);

    make_json_result($smarty->fetch('category_list.htm'));
}
/*------------------------------------------------------ */
//-- 添加商品分類
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 權限檢查 */
    admin_priv('cat_manage');



    /* 模板賦值 */
    $smarty->assign('ur_here',      $_LANG['04_category_add']);
    $smarty->assign('action_link',  array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品類型
    $smarty->assign('attr_list',        get_attr_list()); // 取得商品屬性

    $smarty->assign('cat_select',   cat_list(0, 0, true));
    $smarty->assign('form_act',     'insert');
    $smarty->assign('cat_info',     array('is_show' => 1));



    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('category_info.htm');
}

/*------------------------------------------------------ */
//-- 商品分類添加時的處理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 權限檢查 */
    admin_priv('cat_manage');

    /* 初始化變量 */
    $cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;

    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();

    if (cat_exists($cat['cat_name'], $cat['parent_id']))
    {
        /* 同級別下不能有重複的分類名稱 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['catname_exist'], 0, $link);
    }

    if($cat['grade'] > 10 || $cat['grade'] < 0)
    {
        /* 價格區間數超過範圍 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['grade_error'], 0, $link);
    }

    /* 入庫的操作 */
    if ($db->autoExecute($ecs->table('category'), $cat) !== false)
    {
        $cat_id = $db->insert_id();
        if($cat['show_in_nav'] == 1)
        {
            $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
            $vieworder += 2;
            //顯示在自定義導航欄中
            $sql = "INSERT INTO " . $ecs->table('nav') .
                " (name,ctype,cid,ifshow,vieworder,opennew,url,type)".
                " VALUES('" . $cat['cat_name'] . "', 'c', '".$db->insert_id()."','1','$vieworder','0', '" . build_uri('category', array('cid'=> $cat_id), $cat['cat_name']) . "','middle')";
            $db->query($sql);
        }
        insert_cat_recommend($cat['cat_recommend'], $cat_id);

        admin_log($_POST['cat_name'], 'add', 'category');   // 記錄管理員操作
        clear_cache_files();    // 清除緩存

        /*添加鏈接*/
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'category.php?act=add';

        $link[1]['text'] = $_LANG['back_list'];
        $link[1]['href'] = 'category.php?act=list';

        sys_msg($_LANG['catadd_succed'], 0, $link);
    }
 }

/*------------------------------------------------------ */
//-- 編輯商品分類信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('cat_manage');   // 權限檢查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查詢分類信息數據
    $attr_list = get_attr_list();
    $filter_attr_list = array();

    if ($cat_info['filter_attr'])
    {
        $filter_attr = explode(",", $cat_info['filter_attr']);  //把多個篩選屬性放到數組中

        foreach ($filter_attr AS $k => $v)
        {
            $attr_cat_id = $db->getOne("SELECT cat_id FROM " . $ecs->table('attribute') . " WHERE attr_id = '" . intval($v) . "'");
            $filter_attr_list[$k]['goods_type_list'] = goods_type_list($attr_cat_id);  //取得每個屬性的商品類型
            $filter_attr_list[$k]['filter_attr'] = $v;
            $attr_option = array();

            foreach ($attr_list[$attr_cat_id] as $val)
            {
                $attr_option[key($val)] = current ($val);
            }

            $filter_attr_list[$k]['option'] = $attr_option;
        }

        $smarty->assign('filter_attr_list', $filter_attr_list);
    }
    else
    {
        $attr_cat_id = 0;
    }

    /* 模板賦值 */
    $smarty->assign('attr_list',        $attr_list); // 取得商品屬性
    $smarty->assign('attr_cat_id',      $attr_cat_id);
    $smarty->assign('ur_here',     $_LANG['category_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_category_list'], 'href' => 'category.php?act=list'));

    //分類是否存在首頁推薦
    $res = $db->getAll("SELECT recommend_type FROM " . $ecs->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
    if (!empty($res))
    {
        $cat_recommend = array();
        foreach($res as $data)
        {
            $cat_recommend[$data['recommend_type']] = 1;
        }
        $smarty->assign('cat_recommend', $cat_recommend);
    }

    $smarty->assign('cat_info',    $cat_info);
    $smarty->assign('form_act',    'update');
    $smarty->assign('cat_select',  cat_list(0, $cat_info['parent_id'], true));
    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品類型

    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('category_info.htm');
}

elseif($_REQUEST['act'] == 'add_category')
{
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $category = empty($_REQUEST['cat']) ? '' : json_str_iconv(trim($_REQUEST['cat']));

    if(cat_exists($category, $parent_id))
    {
        make_json_error($_LANG['catname_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('category') . "(cat_name, parent_id, is_show)" .
               "VALUES ( '$category', '$parent_id', 1)";

        $db->query($sql);
        $category_id = $db->insert_id();

        $arr = array("parent_id"=>$parent_id, "id"=>$category_id, "cat"=>$category);

        clear_cache_files();    // 清除緩存

        make_json_result($arr);
    }
}

/*------------------------------------------------------ */
//-- 編輯商品分類信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 權限檢查 */
    admin_priv('cat_manage');

    /* 初始化變量 */
    $cat_id              = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $old_cat_name        = $_POST['old_cat_name'];
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();

    /* 判斷分類名是否重複 */

    if ($cat['cat_name'] != $old_cat_name)
    {
        if (cat_exists($cat['cat_name'],$cat['parent_id'], $cat_id))
        {
           $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
           sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }

    /* 判斷上級目錄是否合法 */
    $children = array_keys(cat_list($cat_id, 0, false));     // 獲得當前分類的所有下級分類
    if (in_array($cat['parent_id'], $children))
    {
        /* 選定的父類是當前分類或當前分類的下級分類 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG["is_leaf_error"], 0, $link);
    }

    if($cat['grade'] > 10 || $cat['grade'] < 0)
    {
        /* 價格區間數超過範圍 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['grade_error'], 0, $link);
    }

    $dat = $db->getRow("SELECT cat_name, show_in_nav FROM ". $ecs->table('category') . " WHERE cat_id = '$cat_id'");

    if ($db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
    {
        if($cat['cat_name'] != $dat['cat_name'])
        {
            //如果分類名稱發生了改變
            $sql = "UPDATE " . $ecs->table('nav') . " SET name = '" . $cat['cat_name'] . "' WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'";
            $db->query($sql);
        }
        if($cat['show_in_nav'] != $dat['show_in_nav'])
        {
            //是否顯示於導航欄發生了變化
            if($cat['show_in_nav'] == 1)
            {
                //顯示
                $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
                if(empty($nid))
                {
                    //不存在
                    $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
                    $vieworder += 2;
                    $uri = build_uri('category', array('cid'=> $cat_id), $cat['cat_name']);

                    $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $cat['cat_name'] . "', 'c', '$cat_id','1','$vieworder','0', '" . $uri . "','middle')";
                }
                else
                {
                    $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'";
                }
                $db->query($sql);
            }
            else
            {
                //去除
                $db->query("UPDATE " . $ecs->table('nav') . " SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
            }
        }

        //更新首頁推薦
        insert_cat_recommend($cat['cat_recommend'], $cat_id);
        /* 更新分類信息成功 */
        clear_cache_files(); // 清除緩存
        admin_log($_POST['cat_name'], 'edit', 'category'); // 記錄管理員操作

        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'category.php?act=list');
        sys_msg($_LANG['catedit_succed'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 批量轉移商品分類頁面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move')
{
    /* 權限檢查 */
    admin_priv('cat_drop');

    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    /* 模板賦值 */
    $smarty->assign('ur_here',     $_LANG['move_goods']);
    $smarty->assign('action_link', array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    $smarty->assign('cat_select', cat_list(0, $cat_id, true));
    $smarty->assign('form_act',   'move_cat');

    /* 顯示頁面 */
    assign_query_info();
    $smarty->display('category_move.htm');
}

/*------------------------------------------------------ */
//-- 處理批量轉移商品分類的處理程序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move_cat')
{
    /* 權限檢查 */
    admin_priv('cat_drop');

    $cat_id        = !empty($_POST['cat_id'])        ? intval($_POST['cat_id'])        : 0;
    $target_cat_id = !empty($_POST['target_cat_id']) ? intval($_POST['target_cat_id']) : 0;

    /* 商品分類不允許為空 */
    if ($cat_id == 0 || $target_cat_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=move');
        sys_msg($_LANG['cat_move_empty'], 0, $link);
    }

    /* 更新商品分類 */
    $sql = "UPDATE " .$ecs->table('goods'). " SET cat_id = '$target_cat_id' ".
           "WHERE cat_id = '$cat_id'";
    if ($db->query($sql))
    {
        /* 清除緩存 */
        clear_cache_files();

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=list');
        sys_msg($_LANG['move_cat_success'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 編輯排序序號
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除緩存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 編輯數量單位
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_measure_unit')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv($_POST['val']);

    if (cat_update($id, array('measure_unit' => $val)))
    {
        clear_cache_files(); // 清除緩存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 編輯排序序號
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_grade')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if($val > 10 || $val < 0)
    {
        /* 價格區間數超過範圍 */
        make_json_error($_LANG['grade_error']);
    }

    if (cat_update($id, array('grade' => $val)))
    {
        clear_cache_files(); // 清除緩存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切換是否顯示在導航欄
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_show_in_nav')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('show_in_nav' => $val)) != false)
    {
        if($val == 1)
        {
            //顯示
            $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
            $vieworder += 2;
            $catname = $db->getOne("SELECT cat_name FROM ". $ecs->table('category') . " WHERE cat_id = '$id'");
            //顯示在自定義導航欄中
            $_CFG['rewrite'] = 0;
            $uri = build_uri('category', array('cid'=> $id), $catname);

            $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
            if(empty($nid))
            {
                //不存在
                $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $catname . "', 'c', '$id','1','$vieworder','0', '" . $uri . "','middle')";
            }
            else
            {
                $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'";
            }
            $db->query($sql);
        }
        else
        {
            //去除
            $db->query("UPDATE " . $ecs->table('nav') . "SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
        }
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切換是否顯示
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_show')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('is_show' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 刪除商品分類
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('cat_manage');

    /* 初始化分類ID並取得分類名稱 */
    $cat_id   = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT cat_name FROM ' .$ecs->table('category'). " WHERE cat_id='$cat_id'");

    /* 當前分類下是否有子分類 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('category'). " WHERE parent_id='$cat_id'");

    /* 當前分類下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('goods'). " WHERE cat_id='$cat_id'");

    /* 如果不存在下級子分類和商品，則刪除之 */
    if ($cat_count == 0 && $goods_count == 0)
    {
        /* 刪除分類 */
        $sql = 'DELETE FROM ' .$ecs->table('category'). " WHERE cat_id = '$cat_id'";
        if ($db->query($sql))
        {
            $db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
            clear_cache_files();
            admin_log($cat_name, 'remove', 'category');
        }
    }
    else
    {
        make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
    }

    $url = 'category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */
//
///**
// * 檢查分類是否已經存在
// *
// * @param   string      $cat_name       分類名稱
// * @param   integer     $parent_cat     上級分類
// * @param   integer     $exclude        排除的分類ID
// *
// * @return  boolean
// */
//function cat_exists($cat_name, $parent_cat, $exclude = 0)
//{
//    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('category').
//           " WHERE parent_id = '$parent_cat' AND cat_name = '$cat_name' AND cat_id<>'$exclude'";
//    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
//}

/**
 * 獲得商品分類的所有信息
 *
 * @param   integer     $cat_id     指定的分類ID
 *
 * @return  mix
 */
function get_cat_info($cat_id)
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id='$cat_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 添加商品分類
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $args, 'update', "cat_id='$cat_id'");
}


/**
 * 獲取屬性列表
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_attr_list()
{
    $sql = "SELECT a.attr_id, a.cat_id, a.attr_name ".
           " FROM " . $GLOBALS['ecs']->table('attribute'). " AS a,  ".
           $GLOBALS['ecs']->table('goods_type') . " AS c ".
           " WHERE  a.cat_id = c.cat_id AND c.enabled = 1 ".
           " ORDER BY a.cat_id , a.sort_order";

    $arr = $GLOBALS['db']->getAll($sql);

    $list = array();

    foreach ($arr as $val)
    {
        $list[$val['cat_id']][] = array($val['attr_id']=>$val['attr_name']);
    }

    return $list;
}

/**
 * 插入首頁推薦擴展分類
 *
 * @access  public
 * @param   array   $recommend_type 推薦類型
 * @param   integer $cat_id     分類ID
 *
 * @return void
 */
function insert_cat_recommend($recommend_type, $cat_id)
{
    //檢查分類是否為首頁推薦
    if (!empty($recommend_type))
    {
        //取得之前的分類
        $recommend_res = $GLOBALS['db']->getAll("SELECT recommend_type FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
        if (empty($recommend_res))
        {
            foreach($recommend_type as $data)
            {
                $data = intval($data);
                $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") . "(cat_id, recommend_type) VALUES ('$cat_id', '$data')");
            }
        }
        else
        {
            $old_data = array();
            foreach($recommend_res as $data)
            {
                $old_data[] = $data['recommend_type'];
            }
            $delete_array = array_diff($old_data, $recommend_type);
            if (!empty($delete_array))
            {
                $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=$cat_id AND recommend_type " . db_create_in($delete_array));
            }
            $insert_array = array_diff($recommend_type, $old_data);
            if (!empty($insert_array))
            {
                foreach($insert_array as $data)
                {
                    $data = intval($data);
                    $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") . "(cat_id, recommend_type) VALUES ('$cat_id', '$data')");
                }
            }
        }
    }
    else
    {
        $GLOBALS['db']->query("DELETE FROM ". $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
    }
}

?>