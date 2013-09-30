<?php

/**
 * ECSHOP 管理中心模版管理程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: mail_template.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

admin_priv('mail_template');

/*------------------------------------------------------ */
//-- 模版列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 類文件

    /* 包含插件語言項 */
    $sql = "SELECT code FROM ".$ecs->table('plugins');
    $rs = $db->query($sql);
    while ($row = $db->FetchRow($rs))
    {
        /* 取得語言項 */
        if (file_exists('../plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php');
        }

    }

    /* 獲得所有郵件模板 */
    $sql = "SELECT template_id, template_code FROM " .$ecs->table('mail_templates') . " WHERE  type = 'template'";
    $res = $db->query($sql);
    $cur = null;

    while ($row = $db->FetchRow($res))
    {
        if ($cur == null)
        {
            $cur = $row['template_id'];
        }

        $len = strlen($_LANG[$row['template_code']]);
        $templates[$row['template_id']] = $len < 18 ?
            $_LANG[$row['template_code']].str_repeat('&nbsp;', (18-$len)/2) ." [$row[template_code]]" :
            $_LANG[$row['template_code']] . " [$row[template_code]]";
    }

    assign_query_info();

    $content = load_template($cur);

    /* 創建 html editor */
    $editor = new FCKeditor('content');
    $editor->BasePath   = '../includes/fckeditor/';
    $editor->ToolbarSet = 'Mail';
    $editor->Width      = '100%';
    $editor->Height     = '320';
    $editor->Value      = $content['template_content'];
    $FCKeditor = $editor->CreateHtml();
    $smarty->assign('FCKeditor', $FCKeditor);

    $smarty->assign('cur',          $cur);
    $smarty->assign('ur_here',      $_LANG['mail_template_manage']);
    $smarty->assign('templates',    $templates);
    $smarty->assign('template',     $content);
    $smarty->assign('full_page',    1);
    $smarty->display('mail_template.htm');
}

/*------------------------------------------------------ */
//-- 載入指定模版
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'loat_template')
{
    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 類文件

    $tpl = intval($_GET['tpl']);
    $mail_type = isset($_GET['mail_type']) ? $_GET['mail_type'] : -1;

    /* 包含插件語言項 */
    $sql = "SELECT code FROM ".$ecs->table('plugins');
    $rs = $db->query($sql);
    while ($row = $db->FetchRow($rs))
    {
        /* 取得語言項 */
        if (file_exists('../plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php');
        }

    }

    /* 獲得所有郵件模板 */
    $sql = "SELECT template_id, template_code FROM " .$ecs->table('mail_templates') . " WHERE  type = 'template'";
    $res = $db->query($sql);

    while ($row = $db->FetchRow($res))
    {
        $len = strlen($_LANG[$row['template_code']]);
        $templates[$row['template_id']] = $len < 18 ?
            $_LANG[$row['template_code']].str_repeat('&nbsp;', (18-$len)/2) ." [$row[template_code]]" :
            $_LANG[$row['template_code']] . " [$row[template_code]]";
    }

    $content = load_template($tpl);

    if (($mail_type == -1 && $content['is_html'] == 1) || $mail_type == 1)
    {
        /* 創建 html editor */
        $editor = new FCKeditor('content');
        $editor->BasePath   = '../includes/fckeditor/';
        $editor->ToolbarSet = 'Mail';
        $editor->Width      = '100%';
        $editor->Height     = '320';
        $editor->Value      = $content['template_content'];
        $FCKeditor = $editor->CreateHtml();
        $smarty->assign('FCKeditor', $FCKeditor);

        $content['is_html'] = 1;
    }
    elseif ($mail_type == 0)
    {
        $content['is_html'] = 0;
    }


    $smarty->assign('cur',          $tpl);
    $smarty->assign('templates',    $templates);
    $smarty->assign('template',     $content);

    make_json_result($smarty->fetch('mail_template.htm'));
}

/*------------------------------------------------------ */
//-- 保存模板內容
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'save_template')
{
    $_POST['subject'] = json_str_iconv($_POST['subject']);
    $_POST['content'] = json_str_iconv($_POST['content']);
    if (empty($_POST['subject']))
    {
        make_json_error($_LANG['subject_empty']);
    }
    else
    {
        $subject = trim($_POST['subject']);
    }

    if (empty($_POST['content']))
    {
        make_json_result($_LANG['content_empty']);
    }
    else
    {
        $content = trim($_POST['content']);
    }

    $type   = intval($_POST['is_html']);
    $tpl_id = intval($_POST['tpl']);
    if ($type)
    {
        $content = str_replace(array("\r\n", "\n"), array('<br />', '<br />'), $content);
    }
    else
    {
        $content = str_replace('<br />', '\n', $content);
    }
    $sql = "UPDATE " .$ecs->table('mail_templates'). " SET ".
                "template_subject = '" .str_replace('\\\'\\\'', '\\\'', $subject). "', ".
                "template_content = '" .str_replace('\\\'\\\'', '\\\'', $content). "', ".
                "is_html = '$type', ".
                "last_modify = '" .gmtime(). "' ".
            "WHERE template_id='$tpl_id'";

    if ($db->query($sql, "SILENT"))
    {
        make_json_result('',  $_LANG['update_success']);
    }
    else
    {
        make_json_error($_LANG['update_failed'] ."\n". $GLOBALS['db']->error());
    }
}

/**
 * 加載指定的模板內容
 *
 * @access  public
 * @param   string  $temp   郵件模板的ID
 * @return  array
 */
function load_template($temp_id)
{
    $sql = "SELECT template_subject, template_content, is_html ".
            "FROM " .$GLOBALS['ecs']->table('mail_templates'). " WHERE template_id='$temp_id'";
    $row = $GLOBALS['db']->GetRow($sql);

    return $row;
}

?>