<?php

/**
 * ECSHOP 系統環境檢測函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_env_checker.php 17062 2010-03-25 06:25:22Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 檢查目錄的讀寫權限
 *
 * @access  public
 * @param   array     $checking_dirs     目錄列表
 * @return  array     檢查後的消息數組，
 *    成功格式形如array('result' => 'OK', 'detail' => array(array($dir, $_LANG['can_write']), array(), ...))
 *    失敗格式形如array('result' => 'ERROR', 'd etail' => array(array($dir, $_LANG['cannt_write']), array(), ...))
 */
function check_dirs_priv($checking_dirs)
{
    include_once(ROOT_PATH . 'includes/lib_common.php');

    global $_LANG;
    $msgs = array('result' => 'OK', 'detail' => array());

    foreach ($checking_dirs AS $dir)
    {
        if (!file_exists(ROOT_PATH . $dir))
        {
            $msgs['result'] = 'ERROR';
            $msgs['detail'][] = array($dir, $_LANG['not_exists']);
            continue;
        }

        if (file_mode_info(ROOT_PATH . $dir) < 2)
        {
            $msgs['result'] = 'ERROR';
            $msgs['detail'][] = array($dir, $_LANG['cannt_write']);
        }
        else
        {
            $msgs['detail'][] = array($dir, $_LANG['can_write']);
        }
    }

    return $msgs;
}

/**
 * 檢查模板的讀寫權限
 *
 * @access  public
 * @param   array      $templates_root        模板文件類型所在的根路徑數組，形如：array('dwt'=>'', 'lbi'=>'')
 * @return  array      檢查後的消息數組，全部可寫為空數組，否則是一個以不可寫的文件路徑組成的數組
 */
function check_templates_priv($templates_root)
{
    global $_LANG;

    $msgs = array();
    $filename = '';
    $filepath = '';

    foreach ($templates_root as $tpl_type => $tpl_root)
    {
        if (!file_exists($tpl_root))
        {
            $msgs[] = str_replace(ROOT_PATH, '', $tpl_root . ' ' . $_LANG['not_exists']);
            continue;
        }

        $tpl_handle = @opendir($tpl_root);
        while (($filename = @readdir($tpl_handle)) !== false)
        {
            $filepath = $tpl_root . $filename;
            if (is_file($filepath)
                    && strrpos($filename, '.' . $tpl_type) !== false
                    && file_mode_info($filepath) < 7)
            {
                $msgs[] = str_replace(ROOT_PATH, '', $filepath . ' ' . $_LANG['cannt_write']);
            }
        }
        @closedir($tpl_handle);
    }

    return $msgs;
}

/**
 *  檢查特定目錄是否有執行rename函數權限
 *
 * @access  public
 * @param   void
 *
 * @return void
 */
function check_rename_priv()
{
    /* 獲取要檢查的目錄 */
    $dir_list   = array();
    $dir_list[] = 'temp/caches';
    $dir_list[] = 'temp/compiled';
    $dir_list[] = 'temp/compiled/admin';
    /* 獲取images目錄下圖片目錄 */
    $folder = opendir(ROOT_PATH . 'images');
    while ($dir = readdir($folder))
    {
        if (is_dir(ROOT_PATH . 'images/' . $dir) && preg_match('/^[0-9]{6}$/', $dir))
        {
            $dir_list[] = 'images/' . $dir;
        }
    }
    closedir($folder);
    /* 檢查目錄是否有執行rename函數的權限 */
    $msgs = array();
    foreach ($dir_list AS $dir)
    {
        $mask = file_mode_info(ROOT_PATH .$dir);
        if ((($mask & 2) > 0 ) && (($mask & 8) < 1))
        {
            /* 只有可寫時才檢查rename權限 */
            $msgs[] = $dir . ' ' . $GLOBALS['_LANG']['cannt_modify'];
        }
    }
    return $msgs;
}

?>