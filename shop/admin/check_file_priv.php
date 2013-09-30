<?php

/**
 * ECSHOP 系統文件檢測
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: check_file_priv.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act']== 'check')
{
    /* 檢查權限 */
    admin_priv('file_priv');

    /* 要檢查目錄文件列表 */
    $goods_img_dir = array();
    $folder = opendir(ROOT_PATH . 'images');
    while ($dir = readdir($folder))
    {
        if (is_dir(ROOT_PATH . IMAGE_DIR . '/' . $dir) && preg_match('/^[0-9]{6}$/', $dir))
        {
            $goods_img_dir[] = IMAGE_DIR . '/' . $dir;
        }
    }
    closedir($folder);

    $dir[]                     = ADMIN_PATH;
    $dir[]                     = 'cert';

    $dir_subdir['images'][]    = IMAGE_DIR;
    $dir_subdir['images'][]    = IMAGE_DIR . '/upload';
    $dir_subdir['images'][]    = IMAGE_DIR . '/upload/Image';
    $dir_subdir['images'][]    = IMAGE_DIR . '/upload/File';
    $dir_subdir['images'][]    = IMAGE_DIR . '/upload/Flash';
    $dir_subdir['images'][]    = IMAGE_DIR . '/upload/Media';
    $dir_subdir['data'][]      = DATA_DIR;
    $dir_subdir['data'][]      = DATA_DIR . '/afficheimg';
    $dir_subdir['data'][]      = DATA_DIR . '/brandlogo';
    $dir_subdir['data'][]      = DATA_DIR . '/cardimg';
    $dir_subdir['data'][]      = DATA_DIR . '/feedbackimg';
    $dir_subdir['data'][]      = DATA_DIR . '/packimg';
    $dir_subdir['data'][]      = DATA_DIR . '/sqldata';
    $dir_subdir['temp'][] = 'temp';
    $dir_subdir['temp'][] = 'temp/backup';
    $dir_subdir['temp'][] = 'temp/caches';
    $dir_subdir['temp'][] = 'temp/compiled';
    $dir_subdir['temp'][] = 'temp/compiled/admin';
    $dir_subdir['temp'][] = 'temp/query_caches';
    $dir_subdir['temp'][] = 'temp/static_caches';

    /* 將商品圖片目錄加入檢查範圍 */
    foreach ($goods_img_dir as $val)
    {
        $dir_subdir['images'][] = $val;
    }

    $tpl = 'themes/'.$_CFG['template'].'/';



    $list = array();

    /* 檢查目錄 */
    foreach ($dir AS $val)
    {
        $mark = file_mode_info(ROOT_PATH .$val);
        $list[] = array('item' => $val.$_LANG['dir'], 'r' => $mark&1, 'w' => $mark&2, 'm' => $mark&4);
    }

    /* 檢查目錄及子目錄 */
    $keys = array_unique(array_keys($dir_subdir));
    foreach ($keys AS $key)
    {
        $err_msg = array();
        $mark = check_file_in_array($dir_subdir[$key], $err_msg);
        $list[] = array('item' => $key.$_LANG['dir_subdir'], 'r' => $mark&1, 'w' => $mark&2, 'm' => $mark&4, 'err_msg' => $err_msg);
    }

    /* 檢查當前模板可寫性 */
    $dwt = @opendir(ROOT_PATH .$tpl);
    $tpl_file = array(); //獲取要檢查的文件
    while ($file = readdir($dwt))
    {
        if (is_file(ROOT_PATH .$tpl .$file) && strrpos($file, '.dwt') > 0)
        {
            $tpl_file[] = $tpl .$file;
        }
    }
    @closedir($dwt);
    $lib = @opendir(ROOT_PATH .$tpl.'library/');
    while ($file = readdir($lib))
    {
        if (is_file(ROOT_PATH .$tpl.'library/'.$file) && strrpos($file, '.lbi') > 0 )
        {
             $tpl_file[] = $tpl . 'library/' . $file;
        }
    }
    @closedir($lib);

    /* 開始檢查 */
    $err_msg = array();
    $mark = check_file_in_array($tpl_file, $err_msg);
    $list[] = array('item' => $tpl.$_LANG['tpl_file'], 'r' => $mark&1, 'w' => $mark & 2, 'm' => $mark & 4, 'err_msg' => $err_msg);

    /* 檢查smarty的緩存目錄和編譯目錄及image目錄是否有執行rename()函數的權限 */
    $tpl_list   = array();
    $tpl_dirs[] = 'temp/caches';
    $tpl_dirs[] = 'temp/compiled';
    $tpl_dirs[] = 'temp/compiled/admin';

    /* 將商品圖片目錄加入檢查範圍 */
    foreach ($goods_img_dir as $val)
    {
        $tpl_dirs[] = $val;
    }

    foreach ($tpl_dirs AS $dir)
    {
        $mask = file_mode_info(ROOT_PATH .$dir);

        if (($mask & 4) > 0)
        {
            /* 之前已經檢查過修改權限，只有有修改權限才檢查rename權限 */
            if (($mask & 8) < 1)
            {
                $tpl_list[] = $dir;
            }
        }
    }
    $tpl_msg = implode(', ', $tpl_list);
    $smarty->assign('ur_here',      $_LANG['check_file_priv']);
    $smarty->assign('list',    $list);
    $smarty->assign('tpl_msg', $tpl_msg);
    $smarty->display('file_priv.html');
}

/**
 *  檢查數組中目錄權限
 *
 * @access  public
 * @param   array    $arr           要檢查的文件列表數組
 * @param   array    $err_msg       錯誤信息回饋數組
 *
 * @return int       $mark          文件權限掩碼
 */
function check_file_in_array($arr, &$err_msg)
{
    $read   = true;
    $writen = true;
    $modify = true;
    foreach ($arr AS $val)
    {
        $mark = file_mode_info(ROOT_PATH . $val);
        if (($mark & 1) < 1)
        {
            $read = false;
            $err_msg['r'][] = $val;
        }
        if (($mark & 2) <1)
        {
            $writen = false;
            $err_msg['w'][] = $val;

        }
        if (($mark & 4) <1)
        {
            $modify = false;
            $err_msg['m'][] = $val;
        }
    }

    $mark = 0;
    if ($read)
    {
        $mark ^= 1;
    }
    if ($writen)
    {
        $mark ^= 2;
    }
    if ($modify)
    {
        $mark ^= 4;
    }

    return $mark;
}

?>