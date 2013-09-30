<?php

/**
 * ECSHOP 證書反查文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: wangleisvn $
 * $Id: index.php 16075 2009-05-22 02:19:40Z wangleisvn $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 證書反查
/*------------------------------------------------------ */

if (!empty($_POST['session_id']))
{
    $_POST['session_id'] = trim($_POST['session_id']);
    $sql = "SELECT sesskey FROM " . $ecs->table('sessions') . " WHERE sesskey = '" . $_POST['session_id'] . "' ";
    $sesskey = $db->getOne($sql);
    if ($sesskey != '')
    {
        exit('{"res":"succ","msg":"","info":""}');
    }
    else
    {
        exit('{"res":"fail","msg":"error:000002","info":""}');
    }
}
else
{
    exit('{"res":"fail","msg":"error:000001","info":""}');
}

?>