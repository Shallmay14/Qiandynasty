<?php

/* 報告所有錯誤 */
@ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

/* 清除所有和文件操作相關的狀態信息 */
clearstatcache();

/* 定義站點根 */
define('ROOT_PATH', str_replace('install/includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (isset($_SERVER['PHP_SELF']))
{
    define('PHP_SELF', $_SERVER['PHP_SELF']);
}
else
{
    define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

/* 定義版本的編碼 */
define('EC_CHARSET','utf-8');
define('EC_DB_CHARSET','utf8');

require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_time.php');

/* 創建錯誤處理對象 */
require(ROOT_PATH . 'includes/cls_error.php');
$err = new ecs_error('message.dwt');

/* 初始化模板引擎 */
require(ROOT_PATH . 'install/includes/cls_template.php');
$smarty = new template(ROOT_PATH . 'install/templates/');

require(ROOT_PATH . 'install/includes/lib_installer.php');

/* 發送HTTP頭部，保證瀏覽器識別UTF8編碼 */
header('Content-type: text/html; charset='.EC_CHARSET);

@set_time_limit(360);

?>