<?php
/**
 * ECSHOP 字符集轉換類
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * @author:     未知
 * @version:    v2.2
 * ---------------------------------------------
 * $Id: cls_iconv.php 17063 2010-03-25 06:35:46Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class Chinese
{
    /**
     * 存放 GB <-> UNICODE 對照表的內容
     * @變量類型
     * @訪問      內部
     */
    var $unicode_table = array();

    /**
     * 訪問中文繁簡互換表的文件指針
     *
     * @變量類型  對象
     * @訪問      內部
     */
    var $ctf;

    /**
     * 等待轉換的字符串
     * @變量類型
     * @訪問      內部
     */
    var $SourceText = '';

    /**
     * Chinese 的運行配置
     *
     * @變量類型  數組
     * @訪問      公開
     */
    var $config = array(
        'codetable_dir'    => '',                // 存放各種語言互換表的目錄
        'source_lang'      => '',                // 字符的原編碼
        'target_lang'      => '',                // 轉換後的編碼
        'GBtoBIG5_table'   => 'gb-big5.table',   // 簡體中文轉換為繁體中文的對照表
        'BIG5toGB_table'   => 'big5-gb.table',   // 繁體中文轉換為簡體中文的對照表
        'GBtoUTF8_table'   => 'gb_utf8.php',     // 簡體中文轉換為UTF-8的對照表
        'BIG5toUTF8_table' => 'big5_utf8.php'    // 繁體中文轉換為UTF-8的對照表
    );

    var $iconv_enabled    = false; // 是否存在 ICONV 模塊，默認為否
    var $mbstring_enabled = false; // 是否存在 MBSTRING 模塊，默認為否


    /**
     * Chinese 的悉構函數
     *
     * 詳細說明
     * @形參      字符串 $source_lang 為需要轉換的字符串的原編碼
     *            字符串 $target_lang 為轉換的目標編碼
     *            字符串 $SourceText 為等待轉換的字符串
     * @訪問      公開
     * @返回值    無
     * @throws
     */
    function Chinese($dir = './')
    {
        $this->config['codetable_dir'] = $dir . "includes/codetable/";

        if (function_exists('iconv'))
        {
            $this->iconv_enabled = true;
        }

        if (PHP_VERSION >= '5.0' && function_exists('mb_convert_encoding') && function_exists('mb_list_encodings'))
        {
            $encodings = mb_list_encodings();

            if (in_array('UTF-8', $encodings) == true && in_array('BIG-5', $encodings) == true && in_array('CP936', $encodings) == true) // CP936 就是 GBK 字符集的別名
            {
                $this->mbstring_enabled = true;
            }
        }
    }

    function Convert($source_lang, $target_lang, $source_string = '')
    {
        /* 如果字符串為空或者字符串不需要轉換，直接返回 */
        if ($source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
        {
            return $source_string;
        }

        if ($source_lang)
        {
            $this->config['source_lang'] = $this->_lang($source_lang);
        }

        if ($target_lang)
        {
            $this->config['target_lang'] = $this->_lang($target_lang);
        }

        /* 如果編碼相同，直接返回 */
        if ($this->config['source_lang'] == $this->config['target_lang'])
        {
            return $source_string;
        }

        $this->SourceText = $source_string;

        if (($this->iconv_enabled || $this->mbstring_enabled) && !($this->config['source_lang'] == 'GBK' && $this->config['target_lang'] == 'BIG-5'))
        {
            if ($this->config['target_lang'] != 'UNICODE')
            {
                $string = $this->_convert_iconv_mbstring($this->SourceText, $this->config['target_lang'], $this->config['source_lang']);

                /* 如果正確轉換 */
                if ($string)
                {
                    return $string;
                }
            }
            else
            {
                $string = '';
                $text = $SourceText;
                while ($text)
                {
                    if (ord(substr($text, 0, 1)) > 127)
                    {
                        if ($this->config['source_lang'] != 'UTF-8')
                        {
                            $char = $this->_convert_iconv_mbstring(substr($text, 0, 2), 'UTF-8', $this->config['source_lang']);
                        }
                        else
                        {
                            $char = substr($text, 0, 3);
                        }
                        /* 如果轉換出錯 */
                        if ($char == '')
                        {
                            $string = '';

                            break;
                        }

                        switch (strlen($char))
                        {
                            case 1:
                                $uchar  = ord($char);
                                break;

                            case 2:
                                $uchar  = (ord($char[0]) & 0x3f) << 6;
                                $uchar += ord($char[1])  & 0x3f;
                                break;

                            case 3:
                                $uchar  = (ord($char[0]) & 0x1f) << 12;
                                $uchar += (ord($char[1]) & 0x3f) << 6;
                                $uchar += ord($char[2])  & 0x3f;
                                break;

                            case 4:
                                $uchar  = (ord($char[0]) & 0x0f) << 18;
                                $uchar += (ord($char[1]) & 0x3f) << 12;
                                $uchar += (ord($char[2]) & 0x3f) << 6;
                                $uchar += ord($char[3])  & 0x3f;
                                break;
                        }
                        $string .= '&#x' . dechex($uchar) . ';';

                        if ($this->config['source_lang'] != 'UTF-8')
                        {
                            $text = substr($text, 2);
                        }
                        else
                        {
                            $text = substr($text, 3);
                        }
                    }
                    else
                    {
                        $string .= substr($text, 0, 1);
                        $text    = substr($text, 1);
                    }
                }

                /* 如果正確轉換 */
                if ($string)
                {
                    return $string;
                }
            }
        }

        $this->OpenTable();
        // 判斷是否為中文繁、簡轉換
        if (($this->config['source_lang'] == 'GBK' || $this->config['source_lang'] == 'BIG-5') && ($this->config['target_lang'] == 'GBK' || $this->config['target_lang'] == 'BIG-5'))
        {
            return $this->GBtoBIG5();
        }

        // 判斷是否為簡體、繁體中文與UTF8轉換
        if (($this->config['source_lang'] == 'GBK' || $this->config['source_lang'] == 'BIG-5' || $this->config['source_lang'] == 'UTF-8') && ($this->config['target_lang'] == 'UTF-8' || $this->config['target_lang'] == 'GBK' || $this->config['target_lang'] == 'BIG-5'))
        {
            return $this->CHStoUTF8();
        }

        // 判斷是否為簡體、繁體中文與UNICODE轉換
        if (($this->config['source_lang'] == 'GBK' || $this->config['source_lang'] == 'BIG-5') && $this->config['target_lang'] == 'UNICODE')
        {
            return $this->CHStoUNICODE();
        }
    }

    function _lang($lang)
    {
        $lang = strtoupper($lang);

        if (substr($lang, 0, 2) == 'GB')
        {
            return 'GBK';
        }
        else
        {
            switch(substr($lang, 0, 3))
            {
                case 'BIG':
                    return 'BIG-5';

                case 'UTF':
                    return 'UTF-8';

                case 'UNI':
                    return 'UNICODE';

                default:
                    return '';
            }
        }
    }

    function _convert_iconv_mbstring($string, $target_lang, $source_lang)
    {
        if ($this->iconv_enabled)
        {
            $return_string = @iconv($source_lang, $target_lang, $string);
            if ($return_string !== false)
            {
                return $return_string;
            }
        }

        if ($this->mbstring_enabled)
        {
            if ($source_lang == 'GBK')
            {
                $source_lang = 'CP936';
            }
            if ($target_lang == 'GBK')
            {
                $target_lang = 'CP936';
            }

            $return_string = @mb_convert_encoding($string, $target_lang, $source_lang);
            if ($return_string !== false)
            {
                return $return_string;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * 將 16 進制轉換為 2 進制字符
     *
     * 詳細說明
     * @形參      $hexdata 為16進制的編碼
     * @訪問      內部
     * @返回      字符串
     * @throws
     */
    function _hex2bin($hexdata)
    {
        $bindata = '';

        for ($i = 0, $count = strlen($hexdata); $i < $count; $i += 2)
        {
            $bindata .= chr(hexdec($hexdata{$i} . $hexdata{$i + 1}));
        }

        return $bindata;
    }

    /**
     * 打開對照表
     *
     * 詳細說明
     * @形參
     * @訪問      內部
     * @返回      無
     * @throws
     */
    function OpenTable()
    {
        static $gb_utf8_table      = NULL;
        static $gb_unicode_table   = NULL;
        static $utf8_gb_table      = NULL;

        static $big5_utf8_table    = NULL;
        static $big5_unicode_table = NULL;
        static $utf8_big5_table    = NULL;

        // 假如原編碼為簡體中文的話
        if ($this->config['source_lang'] == 'GBK')
        {
            // 假如轉換目標編碼為繁體中文的話
            if ($this->config['target_lang'] == 'BIG-5')
            {
                $this->ctf = @fopen($this->config['codetable_dir'] . $this->config['GBtoBIG5_table'], 'rb');
                if (is_null($this->ctf))
                {
                    echo '打開打開轉換表文件失敗！';

                    exit;
                }
            }

            // 假如轉換目標編碼為 UTF8 的話
            if ($this->config['target_lang'] == 'UTF-8')
            {
                if ($gb_utf8_table === NULL)
                {
                    require_once($this->config['codetable_dir'] . $this->config['GBtoUTF8_table']);
                }
                $this->unicode_table = $gb_utf8_table;
            }

            // 假如轉換目標編碼為 UNICODE 的話
            if ($this->config['target_lang'] == 'UNICODE')
            {
                if ($gb_unicode_table === NULL)
                {
                    if (isset($gb_utf8_table) === false)
                    {
                        require_once($this->config['codetable_dir'] . $this->config['GBtoUTF8_table']);
                    }
                    foreach ($gb_utf8_table AS $key => $value)
                    {
                        $gb_unicode_table[$key] = substr($value, 2);
                    }
                }
                $this->unicode_table = $gb_unicode_table;
            }
        }

        // 假如原編碼為繁體中文的話
        if ($this->config['source_lang'] == 'BIG-5')
        {
            // 假如轉換目標編碼為簡體中文的話
            if ($this->config['target_lang'] == 'GBK')
            {
                $this->ctf = @fopen($this->config['codetable_dir'] . $this->config['BIG5toGB_table'], 'rb');
                if (is_null($this->ctf))
                {
                    echo '打開打開轉換表文件失敗！';

                    exit;
                }
            }
            // 假如轉換目標編碼為 UTF8 的話
            if ($this->config['target_lang'] == 'UTF-8')
            {
                if ($big5_utf8_table === NULL)
                {
                    require_once($this->config['codetable_dir'] . $this->config['BIG5toUTF8_table']);
                }
                $this->unicode_table = $big5_utf8_table;
            }

            // 假如轉換目標編碼為 UNICODE 的話
            if ($this->config['target_lang'] == 'UNICODE')
            {
                if ($big5_unicode_table === NULL)
                {
                    if (isset($big5_utf8_table) === false)
                    {
                        require_once($this->config['codetable_dir'] . $this->config['BIG5toUTF8_table']);
                    }
                    foreach ($big5_utf8_table AS $key => $value)
                    {
                        $big5_unicode_table[$key] = substr($value, 2);
                    }
                }
                $this->unicode_table = $big5_unicode_table;
            }
        }

        // 假如原編碼為 UTF8 的話
        if ($this->config['source_lang'] == 'UTF-8')
        {
            // 假如轉換目標編碼為 GBK 的話
            if ($this->config['target_lang'] == 'GBK')
            {
                if ($utf8_gb_table === NULL)
                {
                    if (isset($gb_utf8_table) === false)
                    {
                        require_once($this->config['codetable_dir'] . $this->config['GBtoUTF8_table']);
                    }
                    foreach ($gb_utf8_table AS $key => $value)
                    {
                        $utf8_gb_table[hexdec($value)] = '0x' . dechex($key);
                    }
                }
                $this->unicode_table = $utf8_gb_table;
            }

            // 假如轉換目標編碼為 BIG5 的話
            if ($this->config['target_lang'] == 'BIG-5')
            {
                if ($utf8_big5_table === NULL)
                {
                    if (isset($big5_utf8_table) === false)
                    {
                        require_once($this->config['codetable_dir'] . $this->config['BIG5toUTF8_table']);
                    }
                    foreach ($big5_utf8_table AS $key => $value)
                    {
                        $utf8_big5_table[hexdec($value)] = '0x' . dechex($key);
                    }
                }
                $this->unicode_table = $utf8_big5_table;
            }
        }
    }

    /**
     * 將簡體、繁體中文的 UNICODE 編碼轉換為 UTF8 字符
     *
     * 詳細說明
     * @形參      數字 $c 簡體中文漢字的UNICODE編碼的10進制
     * @訪問      內部
     * @返回      字符串
     * @throws
     */
    function CHSUtoUTF8($c)
    {
        $str='';

        if ($c < 0x80)
        {
            $str .= $c;
        }
        elseif ($c < 0x800)
        {
            $str .= (0xC0 | $c >> 6);
            $str .= (0x80 | $c & 0x3F);
        }
        elseif ($c < 0x10000)
        {
            $str .= (0xE0 | $c >> 12);
            $str .= (0x80 | $c >> 6 & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        }
        elseif ($c < 0x200000)
        {
            $str .= (0xF0 | $c >> 18);
            $str .= (0x80 | $c >> 12 & 0x3F);
            $str .= (0x80 | $c >> 6  & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        }

        return $str;
    }

    /**
     * 簡體、繁體中文 <-> UTF8 互相轉換的函數
     *
     * 詳細說明
     * @形參
     * @訪問      內部
     * @返回      字符串
     * @throws
     */
    function CHStoUTF8()
    {
        if ($this->config['source_lang'] == 'BIG-5' || $this->config['source_lang'] == 'GBK')
        {
            $ret = '';

            while ($this->SourceText)
            {
                if (ord($this->SourceText{0}) > 127)
                {
                    if ($this->config['source_lang'] == 'BIG-5')
                    {
                        $utf8 = $this->CHSUtoUTF8(hexdec(@$this->unicode_table[hexdec(bin2hex($this->SourceText{0} . $this->SourceText{1}))]));
                    }
                    if ($this->config['source_lang'] == 'GBK')
                    {
                        $utf8 = $this->CHSUtoUTF8(hexdec(@$this->unicode_table[hexdec(bin2hex($this->SourceText{0} . $this->SourceText{1})) - 0x8080]));
                    }
                    for ($i = 0, $count = strlen($utf8); $i < $count; $i += 3)
                    {
                        $ret .= chr(substr($utf8, $i, 3));
                    }

                    $this->SourceText = substr($this->SourceText, 2, strlen($this->SourceText));
                }
                else
                {
                    $ret .= $this->SourceText{0};
                    $this->SourceText = substr($this->SourceText, 1, strlen($this->SourceText));
                }
            }
            $this->unicode_table = array();
            $this->SourceText = '';

            return $ret;
        }

        if ($this->config['source_lang'] == 'UTF-8')
        {
            $i   = 0;
            $out = '';
            $len = strlen($this->SourceText);
            while ($i < $len)
            {
                $c = ord($this->SourceText{$i++});
                switch($c >> 4)
                {
                    case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
                        // 0xxxxxxx
                        $out .= $this->SourceText{$i - 1};
                        break;
                    case 12: case 13:
                        // 110x xxxx   10xx xxxx
                        $char2 = ord($this->SourceText{$i++});
                        $char3 = @$this->unicode_table[(($c & 0x1F) << 6) | ($char2 & 0x3F)];

                        if ($this->config['target_lang'] == 'GBK')
                        {
                            $out .= $this->_hex2bin(dechex($char3 + 0x8080));
                        }
                        elseif ($this->config['target_lang'] == 'BIG-5')
                        {
                            $out .= $this->_hex2bin(dechex($char3 + 0x0000));
                        }
                        break;
                    case 14:
                        // 1110 xxxx  10xx xxxx  10xx xxxx
                        $char2 = ord($this->SourceText{$i++});
                        $char3 = ord($this->SourceText{$i++});
                        $char4 = @$this->unicode_table[(($c & 0x0F) << 12) | (($char2 & 0x3F) << 6) | (($char3 & 0x3F) << 0)];

                        if ($this->config['target_lang'] == 'GBK')
                        {
                            $out .= $this->_hex2bin(dechex($char4 + 0x8080));
                        } elseif ($this->config['target_lang'] == 'BIG-5')
                        {
                            $out .= $this->_hex2bin(dechex($char4 + 0x0000));
                        }

                        break;
                }
            }

            // 返回結果
            return $out;
        }
    }

    /**
     * 簡體、繁體中文轉換為 UNICODE編碼
     *
     * 詳細說明
     * @形參
     * @訪問      內部
     * @返回      字符串
     * @throws
     */
    function CHStoUNICODE()
    {
        $utf = '';

        while ($this->SourceText)
        {
            if (ord($this->SourceText{0}) > 127)
            {
                if ($this->config['source_lang'] == 'GBK')
                {
                    $utf .= '&#x' . $this->unicode_table[hexdec(bin2hex($this->SourceText{0} . $this->SourceText{1})) - 0x8080] . ';';
                }
                elseif ($this->config['source_lang'] == 'BIG-5')
                {
                    $utf .= '&#x' . $this->unicode_table[hexdec(bin2hex($this->SourceText{0} . $this->SourceText{1}))] . ';';
                }

                $this->SourceText = substr($this->SourceText, 2, strlen($this->SourceText));
            }
            else
            {
                $utf .= $this->SourceText{0};
                $this->SourceText = substr($this->SourceText, 1, strlen($this->SourceText));
            }
        }

        return $utf;
    }

    /**
     * 簡體中文 <-> 繁體中文 互相轉換的函數
     *
     * 詳細說明
     * @訪問      內部
     * @返回值    經過編碼的utf8字符
     * @throws
     */
    function GBtoBIG5()
    {
        // 獲取等待轉換的字符串的總長度
        $max = strlen($this->SourceText) - 1;

        for ($i = 0; $i < $max; $i++)
        {
            $h = ord($this->SourceText{$i});
            if ($h >= 160)
            {
                $l = ord($this->SourceText{$i + 1});

                if ($h == 161 && $l == 64)
                {
                    $gb = '  ';
                }
                else
                {
                    fseek($this->ctf, ($h - 160) * 510 + ($l - 1) * 2);
                    $gb = fread($this->ctf, 2);
                }

                $this->SourceText{$i}     = $gb{0};
                $this->SourceText{$i + 1} = $gb{1};

                $i++;
            }
        }
        fclose($this->ctf);

        // 將轉換後的結果賦予 $result;
        $result = $this->SourceText;

        // 清空 $thisSourceText
        $this->SourceText = '';

        // 返回轉換結果
        return $result;
    }
}

?>
