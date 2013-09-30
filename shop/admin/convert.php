<?php

/**
 * ECSHOP 轉換程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: convert.php 17063 2010-03-25 06:35:46Z liuhui $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 轉換程序主頁面
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'main')
{
    admin_priv('convert');

    /* 取得插件文件中的轉換程序 */
    $modules = read_modules('../includes/modules/convert');
    for ($i = 0; $i < count($modules); $i++)
    {
        $code = $modules[$i]['code'];
        $lang_file = ROOT_PATH.'languages/' . $_CFG['lang'] . '/convert/' . $code . '.php';
        if (file_exists($lang_file))
        {
            include_once($lang_file);
        }
        $modules[$i]['desc'] = $_LANG[$modules[$i]['desc']];
    }
    $smarty->assign('module_list', $modules);

    /* 設置默認值 */
    $def_val = array(
        'host'      => $db_host,
        'db'        => '',
        'user'      => $db_user,
        'pass'      => $db_pass,
        'prefix'    => 'sdb_',
        'path'      => ''
    );
    $smarty->assign('def_val', $def_val);

    /* 取得字符集數組 */
    $smarty->assign('charset_list', get_charset_list());

    /* 顯示模板 */
    $smarty->assign('ur_here', $_LANG['convert']);
    assign_query_info();
    $smarty->display('convert_main.htm');
}

/*------------------------------------------------------ */
//-- 轉換前檢查
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'check')
{
    /* 檢查權限 */
    check_authz_json('convert');

    /* 取得參數 */
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
//    $_POST['JSON'] = '{"host":"localhost","db":"shopex","user":"root","pass":"123456","prefix":"sdb_","code":"shopex48","path":"../shopex","charset":"UTF8"}';
    $config = $json->decode($_POST['JSON']);

    /* 測試連接數據庫 */
    $sdb = new cls_mysql($config->host, $config->user, $config->pass, $config->db);

    /* 檢查必需的表是否存在 */
    $sprefix = $config->prefix;
    $config->path = rtrim(str_replace('\\', '/', $config->path), '/');  // 把斜線替換為反斜線，去掉結尾的反斜線
    include_once(ROOT_PATH . 'includes/modules/convert/' . $config->code . '.php');
    $convert = new $config->code($sdb, $sprefix, $config->path);
    $required_table_list = $convert->required_tables();

    $sql = "SHOW TABLES";
    $table_list = $sdb->getCol($sql);

    $diff_arr = array_diff($required_table_list, $table_list);
    if ($diff_arr)
    {
        make_json_error(sprintf($_LANG['table_error'], join(',', $table_list)));
    }

    /* 檢查源目錄是否存在，是否可讀 */
    $dir_list = $convert->required_dirs();
    foreach ($dir_list AS $dir)
    {
        $cur_dir = ($config->path . $dir);
        if (!file_exists($cur_dir) || !is_dir($cur_dir))
        {
            make_json_error(sprintf($_LANG['dir_error'], $cur_dir));
        }

        if (file_mode_info($cur_dir) & 1 != 1)
        {
            make_json_error(sprintf($_LANG['dir_not_readable'], $cur_dir));
        }

        $res = check_files_readable($cur_dir);
        if ($res !== true)
        {
            make_json_error(sprintf($_LANG['file_not_readable'], $res));
        }
    }

    /* 創建圖片目錄 */
    $img_dir = ROOT_PATH . IMAGE_DIR . '/' . date('Ym') . '/';
    if (!file_exists($img_dir))
    {
        make_dir($img_dir);
    }

    /* 需要檢查可寫的目錄 */
    $to_dir_list = array(
        ROOT_PATH . IMAGE_DIR . '/upload/',
        $img_dir,
        ROOT_PATH . DATA_DIR . '/afficheimg/',
        ROOT_PATH . 'cert/'
    );

    /* 檢查目的目錄是否存在，是否可寫 */
    foreach ($to_dir_list AS $to_dir)
    {
        if (!file_exists($to_dir) || !is_dir($to_dir))
        {
            make_json_error(sprintf($_LANG['dir_error'], $to_dir));
        }

        if (file_mode_info($to_dir) & 4 != 4)
        {
            make_json_error(sprintf($_LANG['dir_not_writable'], $to_dir));
        }
    }

    /* 保存配置信息 */
    $_SESSION['convert_config'] = $config;

    /* 包含插件語言文件 */
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/convert/' . $config->code . '.php');

    /* 取得第一步操作 */
    $step = $convert->next_step('');

    /* 返回 */
    make_json_result($step, $_LANG[$step]);
}

/*------------------------------------------------------ */
//-- 轉換操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'process')
{
    /* 設置執行時間 */
    set_time_limit(0);

    /* 檢查權限 */
    check_authz_json('convert');

    /* 取得參數 */
    $step = json_str_iconv($_POST['step']);

    /* 連接原數據庫 */
    $config = $_SESSION['convert_config'];

    $sdb = new cls_mysql($config->host, $config->user, $config->pass, $config->db);
    $sdb->set_mysql_charset($config->charset);

    /* 創建插件對像 */
    include_once(ROOT_PATH . 'includes/modules/convert/' . $config->code . '.php');
    $convert = new $config->code($sdb, $config->prefix, $config->path, $config->charset);

    /* 包含插件語言文件 */
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/convert/' . $config->code . '.php');

    /* 執行步驟 */
    $result = $convert->process($step);
    if ($result !== true)
    {
        make_json_error($result);
    }

    /* 取得下一步操作 */
    $step = $convert->next_step($step);

    /* 返回 */
    make_json_result($step, empty($_LANG[$step]) ? '' : $_LANG[$step]);
}

/**
 * 檢查某個目錄的文件是否可讀（不包括子目錄）
 * 前提：$dirname 是目錄且存在且可讀
 *
 * @param   string  $dirname    目錄名：以 / 結尾，以 / 分隔
 * @return  mix     如果所有文件可讀，返回true；否則，返回第一個不可讀的文件名
 */
function check_files_readable($dirname)
{
    /* 遍歷文件，檢查文件是否可讀 */
    if ($dh = opendir($dirname))
    {
        while (($file = readdir($dh)) !== false)
        {
            if (filetype($dirname . $file) == 'file' && strtolower($file) != 'thumbs.db')
            {
                if (file_mode_info($dirname . $file) & 1 != 1)
                {
                    return $dirname . $file;
                }
            }
        }
        closedir($dh);
    }

    /* 全部可讀的返回值 */
    return true;
}

/**
 * 把一個目錄的文件複製到另一個目錄（不包括子目錄）
 * 前提：$from_dir 是目錄且存在且可讀，$to_dir 是目錄且存在且可寫
 *
 * @param   string  $from_dir   源目錄
 * @param   string  $to_dir     目標目錄
 * @param   string  $file_prefix    文件名前綴
 * @return  mix     成功返回true，否則返回第一個失敗的文件名
 */
function copy_files($from_dir, $to_dir, $file_prefix = '')
{
    /* 遍歷並複製文件 */
    if ($dh = opendir($from_dir))
    {
        while (($file = readdir($dh)) !== false)
        {
            if (filetype($from_dir . $file) == 'file' && strtolower($file) != 'thumbs.db')
            {
                if (!copy($from_dir . $file, $to_dir . $file_prefix . $file))
                {
                    return $from_dir . $file;
                }
            }
        }
        closedir($dh);
    }

    /* 全部複製成功，返回true */
    return true;
}

/**
 * 把一個目錄的文件複製到另一個目錄（包括子目錄）
 * 前提：$from_dir 是目錄且存在且可讀，$to_dir 是目錄且存在且可寫
 *
 * @param   string  $from_dir   源目錄
 * @param   string  $to_dir     目標目錄
 * @param   string  $file_prefix 文件前綴
 * @return  mix     成功返回true，否則返回第一個失敗的文件名
 */
function copy_dirs($from_dir, $to_dir, $file_prefix = '')
{
    $result = true;
    if(! is_dir($from_dir))
    {
        die("It's not a dir");
    }
    if(! is_dir($to_dir))
    {
        if(! mkdir($to_dir, 0700))
        {
            die("can't mkdir");
        }
    }
    $handle = opendir($from_dir);
    while(($file = readdir($handle)) !== false)
    {
        if($file != '.' && $file != '..')
        {
            $src = $from_dir . DIRECTORY_SEPARATOR . $file;
            $dtn = $to_dir . DIRECTORY_SEPARATOR . $file_prefix . $file;
            if(is_dir($src))
            {
                copy_dirs($src, $dtn);
            }
            else
            {
                if(! copy($src, $dtn))
                {
                    $result = false;
                    break;
                }
            }
        }
    }
    closedir($handle);
    return $result;
}

?>