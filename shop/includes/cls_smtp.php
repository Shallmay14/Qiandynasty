<?php

/**
 * ECSHOP SMTP 郵件類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_smtp.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('SMTP_STATUS_NOT_CONNECTED', 1, true);
define('SMTP_STATUS_CONNECTED',     2, true);

class smtp
{
    var $connection;
    var $recipients;
    var $headers;
    var $timeout;
    var $errors;
    var $status;
    var $body;
    var $from;
    var $host;
    var $port;
    var $helo;
    var $auth;
    var $user;
    var $pass;

    /**
     *  參數為一個數組
     *  host        SMTP 服務器的主機       默認：localhost
     *  port        SMTP 服務器的端口       默認：25
     *  helo        發送HELO命令的名稱      默認：localhost
     *  user        SMTP 服務器的用戶名     默認：空值
     *  pass        SMTP 服務器的登陸密碼   默認：空值
     *  timeout     連接超時的時間          默認：5
     *  @return  bool
     */
    function smtp($params = array())
    {
        if (!defined('CRLF'))
        {
            define('CRLF', "\r\n", true);
        }

        $this->timeout  = 10;
        $this->status   = SMTP_STATUS_NOT_CONNECTED;
        $this->host     = 'localhost';
        $this->port     = 25;
        $this->auth     = false;
        $this->user     = '';
        $this->pass     = '';
        $this->errors   = array();

        foreach ($params AS $key => $value)
        {
            $this->$key = $value;
        }

        $this->helo     = $this->host;

        //  如果沒有設置用戶名則不驗證
        $this->auth = ('' == $this->user) ? false : true;
    }

    function connect($params = array())
    {
        if (!isset($this->status))
        {
            $obj = new smtp($params);

            if ($obj->connect())
            {
                $obj->status = SMTP_STATUS_CONNECTED;
            }

            return $obj;
        }
        else
        {
            if (!empty($GLOBALS['_CFG']['smtp_ssl']))
            {
                $this->host = "ssl://" . $this->host;
            }
            $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

            if ($this->connection === false)
            {
                $this->errors[] = 'Access is denied.';

                return false;
            }

            @socket_set_timeout($this->connection, 0, 250000);

            $greeting = $this->get_data();

            if (is_resource($this->connection))
            {
                $this->status = 2;

                return $this->auth ? $this->ehlo() : $this->helo();
            }
            else
            {
                log_write($errstr, __FILE__, __LINE__);
                $this->errors[] = 'Failed to connect to server: ' . $errstr;

                return false;
            }
        }
    }

    /**
     * 參數為數組
     * recipients      接收人的數組
     * from            發件人的地址，也將作為回覆地址
     * headers         頭部信息的數組
     * body            郵件的主體
     */

    function send($params = array())
    {
        foreach ($params AS $key => $value)
        {
            $this->$key = $value;
        }

        if ($this->is_connected())
        {
            //  服務器是否需要驗證
            if ($this->auth)
            {
                if (!$this->auth())
                {
                    return false;
                }
            }

            $this->mail($this->from);

            if (is_array($this->recipients))
            {
                foreach ($this->recipients AS $value)
                {
                    $this->rcpt($value);
                }
            }
            else
            {
                $this->rcpt($this->recipients);
            }

            if (!$this->data())
            {
                return false;
            }

            $headers = str_replace(CRLF . '.', CRLF . '..', trim(implode(CRLF, $this->headers)));
            $body    = str_replace(CRLF . '.', CRLF . '..', $this->body);
            $body    = substr($body, 0, 1) == '.' ? '.' . $body : $body;

            $this->send_data($headers);
            $this->send_data('');
            $this->send_data($body);
            $this->send_data('.');

            return (substr($this->get_data(), 0, 3) === '250');
        }
        else
        {
            $this->errors[] = 'Not connected!';

            return false;
        }
    }

    function helo()
    {
        if (is_resource($this->connection)
                AND $this->send_data('HELO ' . $this->helo)
                AND substr($error = $this->get_data(), 0, 3) === '250' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'HELO command failed, output: ' . trim(substr($error, 3));

            return false;
        }
    }

    function ehlo()
    {
        if (is_resource($this->connection)
                AND $this->send_data('EHLO ' . $this->helo)
                AND substr($error = $this->get_data(), 0, 3) === '250' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'EHLO command failed, output: ' . trim(substr($error, 3));

            return false;
        }
    }

    function auth()
    {
        if (is_resource($this->connection)
                AND $this->send_data('AUTH LOGIN')
                AND substr($error = $this->get_data(), 0, 3) === '334'
                AND $this->send_data(base64_encode($this->user))            // Send username
                AND substr($error = $this->get_data(),0,3) === '334'
                AND $this->send_data(base64_encode($this->pass))            // Send password
                AND substr($error = $this->get_data(),0,3) === '235' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'AUTH command failed: ' . trim(substr($error, 3));

            return false;
        }
    }

    function mail($from)
    {
        if ($this->is_connected()
            AND $this->send_data('MAIL FROM:<' . $from . '>')
            AND substr($this->get_data(), 0, 2) === '250' )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function rcpt($to)
    {
        if ($this->is_connected()
            AND $this->send_data('RCPT TO:<' . $to . '>')
            AND substr($error = $this->get_data(), 0, 2) === '25')
        {
            return true;
        }
        else
        {
            $this->errors[] = trim(substr($error, 3));

            return false;
        }
    }

    function data()
    {
        if ($this->is_connected()
            AND $this->send_data('DATA')
            AND substr($error = $this->get_data(), 0, 3) === '354' )
        {
            return true;
        }
        else
        {
            $this->errors[] = trim(substr($error, 3));

            return false;
        }
    }

    function is_connected()
    {
        return (is_resource($this->connection) AND ($this->status === SMTP_STATUS_CONNECTED));
    }

    function send_data($data)
    {
        if (is_resource($this->connection))
        {
            return fwrite($this->connection, $data . CRLF, strlen($data) + 2);
        }
        else
        {
            return false;
        }
    }

    function get_data()
    {
        $return = '';
        $line   = '';

        if (is_resource($this->connection))
        {
            while (strpos($return, CRLF) === false OR $line{3} !== ' ')
            {
                $line    = fgets($this->connection, 512);
                $return .= $line;
            }

            return trim($return);
        }
        else
        {
            return '';
        }
    }

    /**
     * 獲得最後一個錯誤信息
     *
     * @access  public
     * @return  string
     */
    function error_msg()
    {
        if (!empty($this->errors))
        {
            $len = count($this->errors) - 1;
            return $this->errors[$len];
        }
        else
        {
            return '';
        }
    }
}

?>