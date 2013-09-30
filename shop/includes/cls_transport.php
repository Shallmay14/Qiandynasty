<?php

/**
 * ECSHOP 服務器之間數據傳輸器。採集到的信息包括HTTP頭和HTTP體，
 * 並以一維數組的形式返回，如：array('header' => 'bar', 'body' => 'foo')。
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: cls_transport.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class transport
{
    /**
     * 腳本執行時間。－1表示採用PHP的默認值。
     *
     * @access  private
     * @var     integer     $time_limit
     */
    var $time_limit                  = -1;

    /**
     * 在多少秒之內，如果連接不可用，腳本就停止連接。－1表示採用PHP的默認值。
     *
     * @access  private
     * @var     integer     $connect_timeout
     */
    var $connect_timeout             = -1;

    /**
     * 連接後，限定多少秒超時。－1表示採用PHP的默認值。此項僅當採用CURL庫時啟用。
     *
     * @access  private
     * @var     integer    $stream_timeout
     */
    var $stream_timeout              = -1;

    /**
     * 是否使用CURL庫來連接。false表示採用fsockopen進行連接。
     *
     * @access  private
     * @var     boolean     $use_curl
     */
    var $use_curl                    = false;

    /**
     * 構造函數
     *
     * @access  public
     * @param   integer     $time_limit
     * @param   integer     $connect_timeout
     * @param   integer     $stream_timeout
     * @param   boolean     $use_curl
     * @return  void
     */
    function __construct($time_limit = -1, $connect_timeout = -1, $stream_timeout = -1, $use_curl = false)
    {
        $this->transport($time_limit, $connect_timeout, $stream_timeout, $use_curl);
    }

    /**
     * 構造函數
     *
     * @access  public
     * @param   integer     $time_limit
     * @param   integer     $connect_timeout
     * @param   integer     $stream_timeout
     * @param   boolean     $use_curl
     * @return  void
     */
    function transport($time_limit = -1, $connect_timeout = -1, $stream_timeout = -1, $use_curl = false)
    {
        $this->time_limit = $time_limit;
        $this->connect_timeout = $connect_timeout;
        $this->stream_timeout = $stream_timeout;
        $this->use_curl = $use_curl;
    }

    /**
     * 請求遠程服務器
     *
     * @access  public
     * @param   string      $url            遠程服務器的URL
     * @param   mix         $params         查詢參數，形如bar=foo&foo=bar；或者是一維關聯數組，形如array('a'=>'aa',...)
     * @param   string      $method         請求方式，是POST還是GET
     * @param   array       $my_header      用戶要發送的頭部信息，為一維關聯數組，形如array('a'=>'aa',...)
     * @return  array                       成功返回一維關聯數組，形如array('header'=>'bar', 'body'=>'foo')，
     *                                      重大錯誤程序直接停止運行，否則返回false。
     */
    function request($url, $params = '', $method = 'POST', $my_header = '')
    {
        $fsock_exists = function_exists('fsockopen');
        $curl_exists = function_exists('curl_init');

        if (!$fsock_exists && !$curl_exists)
        {
            die('No method available!');
        }

        if (!$url)
        {
            die('Invalid url!');
        }

        if ($this->time_limit > -1)//如果為0，不限制執行時間
        {
            set_time_limit($this->time_limit);
        }

        $method = $method === 'GET' ? $method : 'POST';
        $response = '';
        $temp_str = '';

        /* 格式化將要發要送的參數 */
        if ($params && is_array($params))
        {
            foreach ($params AS $key => $value)
            {
                $temp_str .= '&' . $key . '=' . $value;
            }
            $params = preg_replace('/^&/', '', $temp_str);
        }

        /* 如果fsockopen存在，且用戶不指定使用curl，則調用use_socket函數 */
        if ($fsock_exists && !$this->use_curl)
        {
            $response = $this->use_socket($url, $params, $method, $my_header);
        }
        /* 只要上述條件中的任一個不成立，流程就轉向這裡，這時如果curl模塊可用，就調用use_curl函數 */
        elseif ($curl_exists)
        {
            $response = $this->use_curl($url, $params, $method, $my_header);
        }

        /* 空響應或者傳輸過程中發生錯誤，程序將返回false */
        if (!$response)
        {
            return false;
        }

        return $response;
    }

    /**
     * 使用fsockopen進行連接
     *
     * @access  private
     * @param   string      $url            遠程服務器的URL
     * @param   string      $params         查詢參數，形如bar=foo&foo=bar
     * @param   string      $method         請求方式，是POST還是GET
     * @param   array       $my_header      用戶要發送的頭部信息，為一維關聯數組，形如array('a'=>'aa',...)
     * @return  array                       成功返回一維關聯數組，形如array('header'=>'bar', 'body'=>'foo')，
     *                                      否則返回false。
     */
    function use_socket($url, $params, $method, $my_header)
    {
        $query = '';
        $auth = '';
        $content_type = '';
        $content_length = '';
        $request_body = '';
        $request = '';
        $http_response = '';
        $temp_str = '';
        $error = '';
        $errstr = '';
        $crlf = $this->generate_crlf();

        if ($method === 'GET')
        {
            $query = $params ? "?$params" : '';
        }
        else
        {
            $request_body  = $params;
            $content_type = 'Content-Type: application/x-www-form-urlencoded' . $crlf;
            $content_length = 'Content-Length: ' . strlen($request_body) . $crlf . $crlf;
        }

        $url_parts = $this->parse_raw_url($url);
        $path = $url_parts['path'] . $query;

        if (!empty($url_parts['user']))
        {
            $auth = 'Authorization: Basic '
                    . base64_encode($url_parts['user'] . ':' . $url_parts['pass']) . $crlf;
        }

        /* 格式化自定義頭部信息 */
        if ($my_header && is_array($my_header))
        {
            foreach ($my_header AS $key => $value)
            {
                $temp_str .= $key . ': ' . $value . $crlf;
            }
            $my_header = $temp_str;
        }

        /* 構造HTTP請求頭部 */
        $request = "$method $path HTTP/1.0$crlf"
                . 'Host: ' . $url_parts['host'] . $crlf
                . $auth
                . $my_header
                . $content_type
                . $content_length
                . $request_body;

        if ($this->connect_timeout > -1)
        {
            $fp = @fsockopen($url_parts['host'], $url_parts['port'], $error, $errstr, $connect_timeout);
        }
        else
        {
            $fp = @fsockopen($url_parts['host'], $url_parts['port'], $error, $errstr);
        }

        if (!$fp)
        {
            return false;//打開失敗
        }

        if (!@fwrite($fp, $request))
        {
            return false;//寫入失敗
        }

        while (!feof($fp))
        {
            $http_response .= fgets($fp);
        }

        if (!$http_response)
        {
            return false;//空響應
        }

        $separator = '/\r\n\r\n|\n\n|\r\r/';
        list($http_header, $http_body) = preg_split($separator, $http_response, 2);

        $http_response = array('header' => $http_header,//header肯定有值
                               'body'   => $http_body);//body可能為空
        @fclose($fp);

        return $http_response;
    }

    /**
     * 使用curl進行連接
     *
     * @access  private
     * @param   string      $url            遠程服務器的URL
     * @param   string      $params         查詢參數，形如bar=foo&foo=bar
     * @param   string      $method         請求方式，是POST還是GET
     * @param   array       $my_header      用戶要發送的頭部信息，為一維關聯數組，形如array('a'=>'aa',...)
     * @return  array                       成功返回一維關聯數組，形如array('header'=>'bar', 'body'=>'foo')，
     *                                      失敗返回false。
     */
    function use_curl($url, $params, $method, $my_header)
    {
        /* 開始一個新會話 */
        $curl_session = curl_init();

        /* 基本設置 */
        curl_setopt($curl_session, CURLOPT_FORBID_REUSE, true); // 處理完後，關閉連接，釋放資源
        curl_setopt($curl_session, CURLOPT_HEADER, true);//結果中包含頭部信息
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);//把結果返回，而非直接輸出
        curl_setopt($curl_session, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);//採用1.0版的HTTP協議

        $url_parts = $this->parse_raw_url($url);

        /* 設置驗證策略 */
        if (!empty($url_parts['user']))
        {
            $auth = $url_parts['user'] . ':' . $url_parts['pass'];
            curl_setopt($curl_session, CURLOPT_USERPWD, $auth);
            curl_setopt($curl_session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        $header = array();

        /* 設置主機 */
        $header[] = 'Host: ' . $url_parts['host'];

        /* 格式化自定義頭部信息 */
        if ($my_header && is_array($my_header))
        {
            foreach ($my_header AS $key => $value)
            {
                $header[] = $key . ': ' . $value;
            }
        }

        if ($method === 'GET')
        {
            curl_setopt($curl_session, CURLOPT_HTTPGET, true);
            $url .= $params ? '?' . $params : '';
        }
        else
        {
            curl_setopt($curl_session, CURLOPT_POST, true);
            $header[] = 'Content-Type: application/x-www-form-urlencoded';
            $header[] = 'Content-Length: ' . strlen($params);
            curl_setopt($curl_session, CURLOPT_POSTFIELDS, $params);
        }

        /* 設置請求地址 */
        curl_setopt($curl_session, CURLOPT_URL, $url);

        /* 設置頭部信息 */
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);

        if ($this->connect_timeout > -1)
        {
            curl_setopt($curl_session, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        }

        if ($this->stream_timeout > -1)
        {
            curl_setopt($curl_session, CURLOPT_TIMEOUT, $this->stream_timeout);
        }

        /* 發送請求 */
        $http_response = curl_exec($curl_session);

        if (curl_errno($curl_session) != 0)
        {
            return false;
        }

        $separator = '/\r\n\r\n|\n\n|\r\r/';
        list($http_header, $http_body) = preg_split($separator, $http_response, 2);

        $http_response = array('header' => $http_header,//肯定有值
                               'body'   => $http_body); //可能為空

        curl_close($curl_session);

        return $http_response;
    }

    /**
     * Similar to PHP's builtin parse_url() function, but makes sure what the schema,
     * path and port keys are set to http, /, 80 respectively if they're missing
     *
     * @access     private
     * @param      string    $raw_url    Raw URL to be split into an array
     * @author     http://www.cpaint.net/
     * @return     array
     */
    function parse_raw_url($raw_url)
    {
        $retval   = array();
        $raw_url  = (string) $raw_url;

        // make sure parse_url() recognizes the URL correctly.
        if (strpos($raw_url, '://') === false)
        {
          $raw_url = 'http://' . $raw_url;
        }

        // split request into array
        $retval = parse_url($raw_url);

        // make sure a path key exists
        if (!isset($retval['path']))
        {
          $retval['path'] = '/';
        }

        // set port to 80 if none exists
        if (!isset($retval['port']))
        {
          $retval['port'] = '80';
        }

        return $retval;
    }

    /**
     * 產生一個換行符，不同的操作系統會有不同的換行符
     *
     * @access     private
     * @return     string       用雙引號引用的換行符
     */
    function generate_crlf()
    {
        $crlf = '';

        if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN'))
        {
            $crlf = "\r\n";
        }
        elseif (strtoupper(substr(PHP_OS, 0, 3) === 'MAC'))
        {
            $crlf = "\r";
        }
        else
        {
            $crlf = "\n";
        }

        return $crlf;
    }
}

?>