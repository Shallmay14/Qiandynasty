<?php

/**
 * ECSHOP 管理中心公用文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: init.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('ECS_ADMIN', true);

error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 初始化設置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

if (file_exists('../data/config.php'))
{
    include('../data/config.php');
}
else
{
    include('../includes/config.php');
}

/* 取得當前ecshop所在的根目錄 */
if(!defined('ADMIN_PATH'))
{
    define('ADMIN_PATH','admin');
}
define('ROOT_PATH', str_replace(ADMIN_PATH . '/includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

if (isset($_SERVER['PHP_SELF']))
{
    define('PHP_SELF', $_SERVER['PHP_SELF']);
}
else
{
    define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . ADMIN_PATH . '/includes/lib_main.php');
require(ROOT_PATH . ADMIN_PATH . '/includes/cls_exchange.php');

/* 對用戶傳入的變量進行轉義操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 對路徑進行安全處理 */
if (strpos(PHP_SELF, '.php/') !== false)
{
    ecs_header("Location:" . substr(PHP_SELF, 0, strpos(PHP_SELF, '.php/') + 4) . "\n");
    exit();
}

/* 創建 ECSHOP 對象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化數據庫類 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 創建錯誤處理對象 */
$err = new ecs_error('message.htm');

/* 初始化session */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ECSCP_ID');

/* 初始化 action */
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'login' || $_REQUEST['act'] == 'logout' || $_REQUEST['act'] == 'signin') &&
    strpos(PHP_SELF, '/privilege.php') === false)
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'forget_pwd' || $_REQUEST['act'] == 'reset_pwd' || $_REQUEST['act'] == 'get_pwd') &&
    strpos(PHP_SELF, '/get_password.php') === false)
{
    $_REQUEST['act'] = '';
}

/* 載入系統參數 */
$_CFG = load_config();

// TODO : 登錄部分準備拿出去做，到時候把以下操作一起挪過去
if ($_REQUEST['act'] == 'captcha')
{
    include(ROOT_PATH . 'includes/cls_captcha.php');

    $img = new captcha('../data/captcha/');
    @ob_end_clean(); //清除之前出現的多餘輸入
    $img->generate_image();

    exit;
}

require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/common.php');
require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/log_action.php');

if (file_exists(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/' . basename(PHP_SELF)))
{
    include(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/' . basename(PHP_SELF));
}

if (!file_exists('../temp/caches'))
{
    @mkdir('../temp/caches', 0777);
    @chmod('../temp/caches', 0777);
}

if (!file_exists('../temp/compiled/admin'))
{
    @mkdir('../temp/compiled/admin', 0777);
    @chmod('../temp/compiled/admin', 0777);
}

clearstatcache();

/* 如果有新版本，升級 */
if (!isset($_CFG['ecs_version']))
{
    $_CFG['ecs_version'] = 'v2.0.5';
}

if (preg_replace('/(?:\.|\s+)[a-z]*$/i', '', $_CFG['ecs_version']) != preg_replace('/(?:\.|\s+)[a-z]*$/i', '', VERSION)
        && file_exists('../upgrade/index.php'))
{
    // 轉到升級文件
    ecs_header("Location: ../upgrade/index.php\n");

    exit;
}

/* 創建 Smarty 對象。*/
require(ROOT_PATH . 'includes/cls_template.php');
$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . ADMIN_PATH . '/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/admin';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}


$smarty->assign('lang', $_LANG);
$smarty->assign('help_open', $_CFG['help_open']);

if(isset($_CFG['enable_order_check']))  // 為了從舊版本順利升級到2.5.0
{
    $smarty->assign('enable_order_check', $_CFG['enable_order_check']);
}
else
{
    $smarty->assign('enable_order_check', 0);
}

/* 驗證管理員身份 */
if ((!isset($_SESSION['admin_id']) || intval($_SESSION['admin_id']) <= 0) &&
    $_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    /* session 不存在，檢查cookie */
    if (!empty($_COOKIE['ECSCP']['admin_id']) && !empty($_COOKIE['ECSCP']['admin_pass']))
    {
        // 找到了cookie, 驗證cookie信息
        $sql = 'SELECT user_id, user_name, password, action_list, last_login ' .
                ' FROM ' .$ecs->table('admin_user') .
                " WHERE user_id = '" . intval($_COOKIE['ECSCP']['admin_id']) . "'";
        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 沒有找到這個記錄
            setcookie($_COOKIE['ECSCP']['admin_id'],   '', 1);
            setcookie($_COOKIE['ECSCP']['admin_pass'], '', 1);

            if (!empty($_REQUEST['is_ajax']))
            {
                make_json_error($_LANG['priv_error']);
            }
            else
            {
                ecs_header("Location: privilege.php?act=login\n");
            }

            exit;
        }
        else
        {
            // 檢查密碼是否正確
            if (md5($row['password'] . $_CFG['hash_code']) == $_COOKIE['ECSCP']['admin_pass'])
            {
                !isset($row['last_time']) && $row['last_time'] = '';
                set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_time']);

                // 更新最後登錄時間和IP
                $db->query('UPDATE ' . $ecs->table('admin_user') .
                            " SET last_login = '" . gmtime() . "', last_ip = '" . real_ip() . "'" .
                            " WHERE user_id = '" . $_SESSION['admin_id'] . "'");
            }
            else
            {
                setcookie($_COOKIE['ECSCP']['admin_id'],   '', 1);
                setcookie($_COOKIE['ECSCP']['admin_pass'], '', 1);

                if (!empty($_REQUEST['is_ajax']))
                {
                    make_json_error($_LANG['priv_error']);
                }
                else
                {
                    ecs_header("Location: privilege.php?act=login\n");
                }

                exit;
            }
        }
    }
    else
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}

if ($_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    $admin_path = preg_replace('/:\d+/', '', $ecs->url()) . ADMIN_PATH;
    if (!empty($_SERVER['HTTP_REFERER']) &&
        strpos(preg_replace('/:\d+/', '', $_SERVER['HTTP_REFERER']), $admin_path) === false)
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}

/* 管理員登錄後可在任何頁面使用 act=phpinfo 顯示 phpinfo() 信息 */
if ($_REQUEST['act'] == 'phpinfo' && function_exists('phpinfo'))
{
    phpinfo();

    exit;
}

//header('Cache-control: private');
header('content-type: text/html; charset=' . EC_CHARSET);
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 判斷是否支持gzip模式 */
if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}

?>
