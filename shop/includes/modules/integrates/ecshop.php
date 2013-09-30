<?php

/**
 * ECSHOP 會員數據處理類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 這是一個免費開源的軟件；這意味著您可以在不用於商業目的的前提下對程序代碼
 * 進行修改、使用和再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: ecshop.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 會員數據整合插件的代碼必須和文件名保持一致 */
    $modules[$i]['code']    = 'ecshop';

    /* 被整合的第三方程序的名稱 */
    $modules[$i]['name']    = 'ECSHOP';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP R&D TEAM';

    /* 插件作者的官方網站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    return;
}

require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
class ecshop extends integrate
{
    var $is_ecshop = 1;

    function __construct($cfg)
    {
        $this->ecshop($cfg);
    }

    /**
     *
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function ecshop($cfg)
    {
        parent::integrate(array());
        $this->user_table = 'users';
        $this->field_id = 'user_id';
        $this->field_name = 'user_name';
        $this->field_pass = 'password';
        $this->field_email = 'email';
        $this->field_gender = 'sex';
        $this->field_bday = 'birthday';
        $this->field_reg_date = 'reg_time';
        $this->need_sync = false;
        $this->is_ecshop = 1;
    }


    /**
     *  檢查指定用戶是否存在及密碼是否正確(重載基類check_user函數，支持zc加密方法)
     *
     * @access  public
     * @param   string  $username   用戶名
     *
     * @return  int
     */
    function check_user($username, $password = null)
    {
        if ($this->charset != 'UTF8')
        {
            $post_username = ecs_iconv('UTF8', $this->charset, $username);
        }
        else
        {
            $post_username = $username;
        }

        if ($password === null)
        {
            $sql = "SELECT " . $this->field_id .
                   " FROM " . $this->table($this->user_table).
                   " WHERE " . $this->field_name . "='" . $post_username . "'";

            return $this->db->getOne($sql);
        }
        else
        {
            $sql = "SELECT user_id, password, salt " .
                   " FROM " . $this->table($this->user_table).
                   " WHERE user_name='$post_username'";
            $row = $this->db->getRow($sql);

            if (empty($row))
            {
                return 0;
            }

            if (empty($row['salt']))
            {
                if ($row['password'] != $this->compile_password(array('password'=>$password)))
                {
                    return 0;
                }
                else
                {
                    return $row['user_id'];
                }
            }
            else
            {
                /* 如果salt存在，使用salt方式加密驗證，驗證通過洗白用戶密碼 */
                $encrypt_type = substr($row['salt'], 0, 1);
                $encrypt_salt = substr($row['salt'], 1);

                /* 計算加密後密碼 */
                $encrypt_password = '';
                switch ($encrypt_type)
                {
                    case ENCRYPT_ZC :
                        $encrypt_password = md5($encrypt_salt.$password);
                        break;
                    /* 如果還有其他加密方式添加到這裡  */
                    //case other :
                    //  ----------------------------------
                    //  break;
                    case ENCRYPT_UC :
                        $encrypt_password = md5(md5($password).$encrypt_salt);
                        break;

                    default:
                        $encrypt_password = '';

                }

                if ($row['password'] != $encrypt_password)
                {
                    return 0;
                }

                $sql = "UPDATE " . $this->table($this->user_table) .
                       " SET password = '".  $this->compile_password(array('password'=>$password)) . "', salt=''".
                       " WHERE user_id = '$row[user_id]'";
                $this->db->query($sql);

                return $row['user_id'];
            }
        }
    }


}

?>