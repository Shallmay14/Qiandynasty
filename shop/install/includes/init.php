<?php

/* ���i�Ҧ����~ */
@ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

/* �M���Ҧ��M���ާ@���������A�H�� */
clearstatcache();

/* �w�q���I�� */
define('ROOT_PATH', str_replace('install/includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (isset($_SERVER['PHP_SELF']))
{
    define('PHP_SELF', $_SERVER['PHP_SELF']);
}
else
{
    define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

/* �w�q�������s�X */
define('EC_CHARSET','utf-8');
define('EC_DB_CHARSET','utf8');

require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_time.php');

/* �Ыؿ��~�B�z��H */
require(ROOT_PATH . 'includes/cls_error.php');
$err = new ecs_error('message.dwt');

/* ��l�ƼҪO���� */
require(ROOT_PATH . 'install/includes/cls_template.php');
$smarty = new template(ROOT_PATH . 'install/templates/');

require(ROOT_PATH . 'install/includes/lib_installer.php');

/* �o�eHTTP�Y���A�O���s�����ѧOUTF8�s�X */
header('Content-type: text/html; charset='.EC_CHARSET);

@set_time_limit(360);

?>