<?php

/**
 * ECSHOP 調查程序
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: vote.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

if (!isset($_REQUEST['vote']) || !isset($_REQUEST['options']) || !isset($_REQUEST['type']))
{
    ecs_header("Location: ./\n");
    exit;
}

$res        = array('error' => 0, 'message' => '', 'content' => '');

$vote_id    = intval($_POST['vote']);
$options    = trim($_POST['options']);
$type       = intval($_POST['type']);
$ip_address = real_ip();

if (vote_already_submited($vote_id, $ip_address))
{
    $res['error']   = 1;
    $res['message'] = $_LANG['vote_ip_same'];
}
else
{
    save_vote($vote_id, $ip_address, $options);

    $vote = get_vote($vote_id);
    if (!empty($vote))
    {
        $smarty->assign('vote_id', $vote['id']);
        $smarty->assign('vote',    $vote['content']);
    }

    $str = $smarty->fetch("library/vote.lbi");

    $pattern = '/(?:<(\w+)[^>]*> .*?)?<div\s+id="ECS_VOTE">(.*)<\/div>(?:.*?<\/\1>)?/is';

    if (preg_match($pattern, $str, $match))
    {
        $res['content'] = $match[2];
    }
    $res['message'] = $_LANG['vote_success'];
}

$json = new JSON;

echo $json->encode($res);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 檢查是否已經提交過投票
 *
 * @access  private
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @return  boolean
 */
function vote_already_submited($vote_id, $ip_address)
{
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('vote_log')." ".
           "WHERE ip_address = '$ip_address' AND vote_id = '$vote_id' ";

    return ($GLOBALS['db']->GetOne($sql) > 0);
}

/**
 * 保存投票結果信息
 *
 * @access  public
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @param   string      $option_id
 * @return  void
 */
function save_vote($vote_id, $ip_address, $option_id)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('vote_log') . " (vote_id, ip_address, vote_time) " .
           "VALUES ('$vote_id', '$ip_address', " . gmtime() .")";
    $res = $GLOBALS['db']->query($sql);

    /* 更新投票主題的數量 */
    $sql = "UPDATE " .$GLOBALS['ecs']->table('vote'). " SET ".
           "vote_count = vote_count + 1 ".
           "WHERE vote_id = '$vote_id'";
    $GLOBALS['db']->query($sql);

    /* 更新投票選項的數量 */
    $sql = "UPDATE " . $GLOBALS['ecs']->table('vote_option') . " SET " .
           "option_count = option_count + 1 " .
           "WHERE " . db_create_in($option_id, 'option_id');
    $GLOBALS['db']->query($sql);
}

?>