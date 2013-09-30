<?php

/**
 * ECSHOP 管理中心語言項編輯(前台語言項)
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: edit_languages.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作項的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

admin_priv('lang_edit');

/*------------------------------------------------------ */
//-- 列表編輯 ?act=list
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //從languages目錄下獲取語言項文件
    $lang_arr    = array();
    $lang_path   = '../languages/' .$_CFG['lang'];
    $lang_dir    = @opendir($lang_path);

    while ($file = @readdir($lang_dir))
    {
        if (substr($file, -3) == "php")
        {
            $filename = substr($file, 0, -4);
            $lang_arr[$filename] = $file. ' - ' .@$_LANG['language_files'][$filename];
        }
    }

    ksort($lang_arr);
    @closedir($lang_dir);

    /* 獲得需要操作的語言包文件 */
    $lang_file = isset($_POST['lang_file']) ? trim($_POST['lang_file']) : '';
    if ($lang_file == 'common')
    {
        $file_path = '../languages/'.$_CFG['lang'].'/common.php';
    }
    elseif ($lang_file == 'shopping_flow')
    {
        $file_path = '../languages/'.$_CFG['lang'].'/shopping_flow.php';
    }
    else
    {
        $file_path = '../languages/'.$_CFG['lang'].'/user.php';
    }

    $file_attr = '';
    if (file_mode_info($file_path) < 7)
    {
        $file_attr = $lang_file .'.php：'. $_LANG['file_attribute'];
    }

    /* 搜索的關鍵字 */
    $keyword = !empty($_POST['keyword']) ? trim(stripslashes($_POST['keyword'])) : '';

    /* 調用函數 */
    $language_arr = get_language_item_list($file_path, $keyword);

    /* 模板賦值 */
    $smarty->assign('ur_here',      $_LANG['edit_languages']);
    $smarty->assign('keyword',      $keyword);  //關鍵字
    $smarty->assign('action_link',  array());
    $smarty->assign('file_attr',    $file_attr);//文件權限
    $smarty->assign('lang_arr',     $lang_arr); //語言文件列表
    $smarty->assign('file_path',    $file_path);//語言文件
    $smarty->assign('lang_file',    $lang_file);//語言文件
    $smarty->assign('language_arr', $language_arr); //需要編輯的語言項列表

    assign_query_info();
    $smarty->display('language_list.htm');
}

/*------------------------------------------------------ */
//-- 編輯語言項
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 語言項的路徑 */
    $lang_file = isset($_POST['file_path']) ? trim($_POST['file_path']) : '';

    /* 替換前的語言項 */
    $src_items = !empty($_POST['item']) ? stripslashes_deep($_POST['item']) : '';

    /* 修改過後的語言項 */
    $dst_items = array();
    $_POST['item_id'] = stripslashes_deep($_POST['item_id']);

    for ($i = 0; $i < count($_POST['item_id']); $i++)
    {
        /* 語言項內容如果為空，不修改 */
        if (trim($_POST['item_content'][$i]) == '')
        {
            unset($src_items[$i]);
        }
        else
        {
            $_POST['item_content'][$i] = str_replace('\\\\n', '\\n', $_POST['item_content'][$i]);
            $dst_items[$i] = $_POST['item_id'][$i] .' = '. '"' .$_POST['item_content'][$i]. '";';
        }
    }

    /* 調用函數編輯語言項 */
    $result = set_language_items($lang_file, $src_items, $dst_items);

    if ($result === false)
    {
        /* 修改失敗提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['edit_languages_false'], 0, $link);
    }
    else
    {
        /* 記錄管理員操作 */
        admin_log('', 'edit', 'languages');

        /* 清除緩存 */
        clear_cache_files();

        /* 成功提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'edit_languages.php?act=list');
        sys_msg($_LANG['edit_languages_success'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 語言項的操作函數
/*------------------------------------------------------ */

/**
 * 獲得語言項列表
 * @access  public
 * @exception           如果語言項中包含換行符，將發生異常。
 * @param   string      $file_path   存放語言項列表的文件的絕對路徑
 * @param   string      $keyword    搜索時指定的關鍵字
 * @return  array       正確返回語言項列表，錯誤返回false
 */
function get_language_item_list($file_path, $keyword)
{
    if (empty($keyword))
    {
        return array();
    }

    /* 獲取文件內容 */
    $line_array = file($file_path);
    if (!$line_array)
    {
        return false;
    }
    else
    {
        /* 防止用戶輸入敏感字符造成正則引擎失敗 */
        $keyword = preg_quote($keyword, '/');

        $matches    = array();
        $pattern    = '/\\[[\'|"](.*?)'.$keyword.'(.*?)[\'|"]\\]\\s|=\\s?[\'|"](.*?)'.$keyword.'(.*?)[\'|"];/';
        $regx       = '/(?P<item>(?P<item_id>\\$_LANG\\[[\'|"].*[\'|"]\\])\\s*?=\\s*?[\'|"](?P<item_content>.*)[\'|"];)/';

        foreach ($line_array AS $lang)
        {
            if (preg_match($pattern, $lang))
            {
                $out = array();

                if (preg_match($regx, $lang, $out))
                {
                    $matches[] = $out;
                }
            }
        }

        return $matches;
   }
}

/**
 * 設置語言項
 * @access  public
 * @param   string      $file_path     存放語言項列表的文件的絕對路徑
 * @param   array       $src_items     替換前的語言項
 * @param   array       $dst_items     替換後的語言項
 * @return  void        成功就把結果寫入文件，失敗返回false
 */
function set_language_items($file_path, $src_items, $dst_items)
{
    /* 檢查文件是否可寫（修改） */
    if (file_mode_info($file_path) < 2)
    {
        return false;
    }

    /* 獲取文件內容 */
    $line_array = file($file_path);
    if (!$line_array)
    {
        return false;
    }
    else
    {
        $file_content = implode('', $line_array);
    }

    $snum = count($src_items);
    $dnum = count($dst_items);
    if ($snum != $dnum)
    {
        return false;
    }
    /* 對索引進行排序，防止錯位替換 */
    ksort($src_items);
    ksort($dst_items);
    for ($i = 0; $i < $snum; $i++)
    {
        $file_content = str_replace($src_items[$i], $dst_items[$i], $file_content);

    }

    /* 寫入修改後的語言項 */
    $f = fopen($file_path, 'wb');
    if (!$f)
    {
        return false;
    }
    if (!fwrite($f, $file_content))
    {
        return false;
    }
    else
    {
        return true;
    }
}

?>