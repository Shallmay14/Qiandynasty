<?php

/**
 * ECSHOP 用戶帳號相關函數庫
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: lib_passport.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 用戶註冊，登錄函數
 *
 * @access  public
 * @param   string       $username          註冊用戶名
 * @param   string       $password          用戶密碼
 * @param   string       $email             註冊email
 * @param   array        $other             註冊的其他信息
 *
 * @return  bool         $bool
 */
function register($username, $password, $email, $other = array())
{
    /* 檢查註冊是否關閉 */
    if (!empty($GLOBALS['_CFG']['shop_reg_closed']))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['shop_register_closed']);
    }
    /* 檢查username */
    if (empty($username))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['username_empty']);
    }
    else
    {
        if (preg_match('/\'\/^\\s*$|^c:\\\\con\\\\con$|[%,\\*\\"\\s\\t\\<\\>\\&\'\\\\]/', $username))
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['username_invalid'], htmlspecialchars($username)));
        }
    }

    /* 檢查email */
    if (empty($email))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['email_empty']);
    }
    else
    {
        if (!is_email($email))
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['email_invalid'], htmlspecialchars($email)));
        }
    }

    if ($GLOBALS['err']->error_no > 0)
    {
        return false;
    }

    /* 檢查是否和管理員重名 */
    if (admin_registered($username))
    {
        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['username_exist'], $username));
        return false;
    }

    if (!$GLOBALS['user']->add_user($username, $password, $email))
    {
        if ($GLOBALS['user']->error == ERR_INVALID_USERNAME)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['username_invalid'], $username));
        }
        elseif ($GLOBALS['user']->error == ERR_USERNAME_NOT_ALLOW)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['username_not_allow'], $username));
        }
        elseif ($GLOBALS['user']->error == ERR_USERNAME_EXISTS)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['username_exist'], $username));
        }
        elseif ($GLOBALS['user']->error == ERR_INVALID_EMAIL)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['email_invalid'], $email));
        }
        elseif ($GLOBALS['user']->error == ERR_EMAIL_NOT_ALLOW)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['email_not_allow'], $email));
        }
        elseif ($GLOBALS['user']->error == ERR_EMAIL_EXISTS)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['email_exist'], $email));
        }
        else
        {
            $GLOBALS['err']->add('UNKNOWN ERROR!');
        }

        //註冊失敗
        return false;
    }
    else
    {
        //註冊成功

        /* 設置成登錄狀態 */
        $GLOBALS['user']->set_session($username);
        $GLOBALS['user']->set_cookie($username);

        /* 註冊送積分 */
        if (!empty($GLOBALS['_CFG']['register_points']))
        {
            log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $GLOBALS['_LANG']['register_points']);
        }

        /*推薦處理*/
        $affiliate  = unserialize($GLOBALS['_CFG']['affiliate']);
        if (isset($affiliate['on']) && $affiliate['on'] == 1)
        {
            // 推薦開關開啟
            $up_uid     = get_affiliate();
            empty($affiliate) && $affiliate = array();
            $affiliate['config']['level_register_all'] = intval($affiliate['config']['level_register_all']);
            $affiliate['config']['level_register_up'] = intval($affiliate['config']['level_register_up']);
            if ($up_uid)
            {
                if (!empty($affiliate['config']['level_register_all']))
                {
                    if (!empty($affiliate['config']['level_register_up']))
                    {
                        $rank_points = $GLOBALS['db']->getOne("SELECT rank_points FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$up_uid'");
                        if ($rank_points + $affiliate['config']['level_register_all'] <= $affiliate['config']['level_register_up'])
                        {
                            log_account_change($up_uid, 0, 0, $affiliate['config']['level_register_all'], 0, sprintf($GLOBALS['_LANG']['register_affiliate'], $_SESSION['user_id'], $username));
                        }
                    }
                    else
                    {
                        log_account_change($up_uid, 0, 0, $affiliate['config']['level_register_all'], 0, $GLOBALS['_LANG']['register_affiliate']);
                    }
                }

                //設置推薦人
                $sql = 'UPDATE '. $GLOBALS['ecs']->table('users') . ' SET parent_id = ' . $up_uid . ' WHERE user_id = ' . $_SESSION['user_id'];

                $GLOBALS['db']->query($sql);
            }
        }

        //定義other合法的變量數組
        $other_key_array = array('msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone');
        $update_data['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
        if ($other)
        {
            foreach ($other as $key=>$val)
            {
                //刪除非法key值
                if (!in_array($key, $other_key_array))
                {
                    unset($other[$key]);
                }
                else
                {
                    $other[$key] =  htmlspecialchars(trim($val)); //防止用戶輸入javascript代碼
                }
            }
            $update_data = array_merge($update_data, $other);
        }
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $update_data, 'UPDATE', 'user_id = ' . $_SESSION['user_id']);

        update_user_info();      // 更新用戶信息
        recalculate_price();     // 重新計算購物車中的商品價格

        return true;
    }
}

/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function logout()
{
/* todo */
}

/**
 *  將指定user_id的密碼修改為new_password。可以通過舊密碼和驗證字串驗證修改。
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 * @param   string  $new_password   用戶新密碼
 * @param   string  $old_password   用戶舊密碼
 * @param   string  $code           驗證碼（md5($user_id . md5($password))）
 *
 * @return  boolen  $bool
 */
function edit_password($user_id, $old_password, $new_password='', $code ='')
{
    if (empty($user_id)) $GLOBALS['err']->add($GLOBALS['_LANG']['not_login']);

    if ($GLOBALS['user']->edit_password($user_id, $old_password, $new_password, $code))
    {
        return true;
    }
    else
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['edit_password_failure']);

        return false;
    }
}

/**
 *  會員找回密碼時，對輸入的用戶名和郵件地址匹配
 *
 * @access  public
 * @param   string  $user_name    用戶帳號
 * @param   string  $email        用戶Email
 *
 * @return  boolen
 */
function check_userinfo($user_name, $email)
{
    if (empty($user_name) || empty($email))
    {
        ecs_header("Location: user.php?act=get_password\n");

        exit;
    }

    /* 檢測用戶名和郵件地址是否匹配 */
    $user_info = $GLOBALS['user']->check_pwd_info($user_name, $email);
    if (!empty($user_info))
    {
        return $user_info;
    }
    else
    {
        return false;
    }
}

/**
 *  用戶進行密碼找回操作時，發送一封確認郵件
 *
 * @access  public
 * @param   string  $uid          用戶ID
 * @param   string  $user_name    用戶帳號
 * @param   string  $email        用戶Email
 * @param   string  $code         key
 *
 * @return  boolen  $result;
 */
function send_pwd_email($uid, $user_name, $email, $code)
{
    if (empty($uid) || empty($user_name) || empty($email) || empty($code))
    {
        ecs_header("Location: user.php?act=get_password\n");

        exit;
    }

    /* 設置重置郵件模板所需要的內容信息 */
    $template    = get_mail_template('send_password');
    $reset_email = $GLOBALS['ecs']->url() . 'user.php?act=get_password&uid=' . $uid . '&code=' . $code;

    $GLOBALS['smarty']->assign('user_name',   $user_name);
    $GLOBALS['smarty']->assign('reset_email', $reset_email);
    $GLOBALS['smarty']->assign('shop_name',   $GLOBALS['_CFG']['shop_name']);
    $GLOBALS['smarty']->assign('send_date',   date('Y-m-d'));
    $GLOBALS['smarty']->assign('sent_date',   date('Y-m-d'));

    $content = $GLOBALS['smarty']->fetch('str:' . $template['template_content']);

    /* 發送確認重置密碼的確認郵件 */
    if (send_mail($user_name, $email, $template['template_subject'], $content, $template['is_html']))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 *  發送激活驗證郵件
 *
 * @access  public
 * @param   int     $user_id        用戶ID
 *
 * @return boolen
 */
function send_regiter_hash ($user_id)
{
    /* 設置驗證郵件模板所需要的內容信息 */
    $template    = get_mail_template('register_validate');
    $hash = register_hash('encode', $user_id);
    $validate_email = $GLOBALS['ecs']->url() . 'user.php?act=validate_email&hash=' . $hash;

    $sql = "SELECT user_name, email FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'";
    $row = $GLOBALS['db']->getRow($sql);

    $GLOBALS['smarty']->assign('user_name',         $row['user_name']);
    $GLOBALS['smarty']->assign('validate_email',    $validate_email);
    $GLOBALS['smarty']->assign('shop_name',         $GLOBALS['_CFG']['shop_name']);
    $GLOBALS['smarty']->assign('send_date',         date($GLOBALS['_CFG']['date_format']));

    $content = $GLOBALS['smarty']->fetch('str:' . $template['template_content']);

    /* 發送確認重置密碼的確認郵件 */
    if (send_mail($row['user_name'], $row['email'], $template['template_subject'], $content, $template['is_html']))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 *  生成郵件驗證hash
 *
 * @access  public
 * @param
 *
 * @return void
 */
function register_hash ($operation, $key)
{
    if ($operation == 'encode')
    {
        $user_id = intval($key);
        $sql = "SELECT reg_time ".
               " FROM " . $GLOBALS['ecs'] ->table('users').
               " WHERE user_id = '$user_id' LIMIT 1";
        $reg_time = $GLOBALS['db']->getOne($sql);

        $hash = substr(md5($user_id . $GLOBALS['_CFG']['hash_code'] . $reg_time), 16, 4);

        return base64_encode($user_id . ',' . $hash);
    }
    else
    {
        $hash = base64_decode(trim($key));
        $row = explode(',', $hash);
        if (count($row) != 2)
        {
            return 0;
        }
        $user_id = intval($row[0]);
        $salt = trim($row[1]);

        if ($user_id <= 0 || strlen($salt) != 4)
        {
            return 0;
        }

        $sql = "SELECT reg_time ".
               " FROM " . $GLOBALS['ecs'] ->table('users').
               " WHERE user_id = '$user_id' LIMIT 1";
        $reg_time = $GLOBALS['db']->getOne($sql);

        $pre_salt = substr(md5($user_id . $GLOBALS['_CFG']['hash_code'] . $reg_time), 16, 4);

        if ($pre_salt == $salt)
        {
            return $user_id;
        }
        else
        {
            return 0;
        }
    }
}

/**
 * 判斷超級管理員用戶名是否存在
 * @param   string      $adminname 超級管理員用戶名
 * @return  boolean
 */
function admin_registered( $adminname )
{
    $res = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('admin_user') .
                                  " WHERE user_name = '$adminname'");
    return $res;
}

?>