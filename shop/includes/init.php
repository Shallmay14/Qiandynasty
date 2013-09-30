<?php

/**
 * ECSHOP 前台公用文件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: init.php 17153 2010-05-05 09:39:12Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得當前ecshop所在的根目錄 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'includes/install.lock')
    && !defined('NO_CHECK_INSTALL'))
{
    header("Location: ./install/index.php\n");

    exit;
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
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_article.php');

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

/* 創建 ECSHOP 對象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());


/* 初始化數據庫類 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 創建錯誤處理對象 */
$err = new ecs_error('message.dwt');

/* 載入系統參數 */
$_CFG = load_config();

/* 載入語言文件 */
require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/common.php');

if ($_CFG['shop_closed'] == 1)
{
    /* 商店關閉了，輸出關閉的消息 */
    header('Content-type: text/html; charset='.EC_CHARSET);

    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['shop_closed'] . '</p><p>' . $_CFG['close_comment'] . '</p></div>');
}

if (is_spider())
{
    /* 如果是蜘蛛的訪問，那麼默認為訪客方式，並且不記錄到日誌中 */
    if (!defined('INIT_NO_USERS'))
    {
        define('INIT_NO_USERS', true);
        /* 整合UC後，如果是蜘蛛訪問，初始化UC需要的常量 */
        if($_CFG['integrate_code'] == 'ucenter')
        {
             $user = & init_users();
        }
    }
    $_SESSION = array();
    $_SESSION['user_id']     = 0;
    $_SESSION['user_name']   = '';
    $_SESSION['email']       = '';
    $_SESSION['user_rank']   = 0;
    $_SESSION['discount']    = 1.00;
}

if (!defined('INIT_NO_USERS'))
{
    /* 初始化session */
    include(ROOT_PATH . 'includes/cls_session.php');

    $sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'));

    define('SESS_ID', $sess->get_session_id());
}
if(isset($_SERVER['PHP_SELF']))
{
    $_SERVER['PHP_SELF']=htmlspecialchars($_SERVER['PHP_SELF']);
}
if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset='.EC_CHARSET);

    /* 創建 Smarty 對象。*/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = $_CFG['cache_time'];
    $smarty->template_dir   = ROOT_PATH . 'themes/' . $_CFG['template'];
    $smarty->cache_dir      = ROOT_PATH . 'temp/caches';
    $smarty->compile_dir    = ROOT_PATH . 'temp/compiled';

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }

    $smarty->assign('lang', $_LANG);
    $smarty->assign('ecs_charset', EC_CHARSET);
    if (!empty($_CFG['stylename']))
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style_' . $_CFG['stylename'] . '.css');
    }
    else
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style.css');
    }

}

if (!defined('INIT_NO_USERS'))
{
    /* 會員信息 */
    $user =& init_users();

    if (!isset($_SESSION['user_id']))
    {
        /* 獲取投放站點的名稱 */
        $site_name = isset($_GET['from'])   ? $_GET['from'] : addslashes($_LANG['self_site']);
        $from_ad   = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

        $_SESSION['from_ad'] = $from_ad; // 用戶點擊的廣告ID
        $_SESSION['referer'] = stripslashes($site_name); // 用戶來源

        unset($site_name);

        if (!defined('INGORE_VISIT_STATS'))
        {
            visit_stats();
        }
    }

    if (empty($_SESSION['user_id']))
    {
        if ($user->get_cookie())
        {
            /* 如果會員已經登錄並且還沒有獲得會員的帳戶余額、積分以及優惠券 */
            if ($_SESSION['user_id'] > 0)
            {
                update_user_info();
            }
        }
        else
        {
            $_SESSION['user_id']     = 0;
            $_SESSION['user_name']   = '';
            $_SESSION['email']       = '';
            $_SESSION['user_rank']   = 0;
            $_SESSION['discount']    = 1.00;
            if (!isset($_SESSION['login_fail']))
            {
                $_SESSION['login_fail'] = 0;
            }
        }
    }

    /* 設置推薦會員 */
    if (isset($_GET['u']))
    {
        set_affiliate();
    }

    /* session 不存在，檢查cookie */
    if (!empty($_COOKIE['ECS']['user_id']) && !empty($_COOKIE['ECS']['password']))
    {
        // 找到了cookie, 驗證cookie信息
        $sql = 'SELECT user_id, user_name, password ' .
                ' FROM ' .$ecs->table('users') .
                " WHERE user_id = '" . intval($_COOKIE['ECS']['user_id']) . "' AND password = '" .$_COOKIE['ECS']['password']. "'";

        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 沒有找到這個記錄
           $time = time() - 3600;
           setcookie("ECS[user_id]",  '', $time, '/');
           setcookie("ECS[password]", '', $time, '/');
        }
        else
        {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            update_user_info();
        }
    }

    if (isset($smarty))
    {
        $smarty->assign('ecs_session', $_SESSION);
    }
}
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

/* 判斷是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}

?>