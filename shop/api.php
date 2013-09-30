<?php
/**
 * ECSHOP OPEN API統一接口
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: goods.php 15921 2009-05-07 05:35:58Z sxc_shop $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_license.php');
require_once('includes/cls_json.php');

define('RETURN_TYPE', empty($_POST['return_data']) ? 1 : ($_POST['return_data'] == 'json' ? 2 : 1));

/* 接收傳遞參數並初步檢驗 */
if (empty($_POST) || empty($_POST['ac']))
{
    api_err('0x003', 'no parameter');   //輸出系統級錯誤:數據異常
}

/* 根據請求類型進入相應的接口處理程序 */
switch ($_POST['act'])
{
    case 'search_goods_list': search_goods_list(); break;
    case 'search_goods_detail': search_goods_detail(); break;
    case 'search_deleted_goods_list': search_deleted_goods_list(); break;
    case 'search_products_list': search_products_list(); break;
    case 'search_site_info': search_site_info(); break;
    default: api_err('0x008', 'no this type api');   //輸出系統級錯誤:數據異常
}


/**
 *  獲取商品列表接口函數
 */
function search_goods_list()
{
    check_auth();   //檢查基本權限

    $version = '1.0';   //版本號

    if ($_POST['api_version'] != $version)      //網店的接口版本低
    {
        api_err('0x008', 'a low version api');
    }

    if (is_numeric($_POST['last_modify_st_time']) && is_numeric($_POST['last_modify_en_time']))
    {
        $sql = 'SELECT COUNT(*) AS count' .
               ' FROM ' . $GLOBALS['ecs']->table('goods') .
               " WHERE is_delete = 0 AND is_on_sale = 1 AND (last_update > '" . $_POST['last_modify_st_time'] . "' OR last_update = 0)";
        $date_count = $GLOBALS['db']->getRow($sql);

        if (empty($date_count))
        {
            api_err('0x003', 'no data to back');    //無符合條件數據
        }

        $page = empty($_POST['pages']) ? 1 : $_POST['pages'];       //確定讀取哪些記錄
        $counts = empty($_POST['counts']) ? 100 : $_POST['counts'];

        $sql = 'SELECT goods_id, last_update AS last_modify' .
               ' FROM ' . $GLOBALS['ecs']->table('goods') .
               " WHERE is_delete = 0 AND is_on_sale = 1 AND (last_update > '" . $_POST['last_modify_st_time'] . "' OR last_update = 0)".
               " LIMIT ".($page - 1) * $counts . ', ' . $counts;
        $date_arr = $GLOBALS['db']->getAll($sql);

        if (!empty($_POST['columns']))
        {
            $column_arr = explode('|', $_POST['columns']);
            foreach ($date_arr as $k => $v)
            {
                foreach ($v as $key => $val)
                {
                    if (in_array($key, $column_arr))
                    {
                        $re_arr['data_info'][$k][$key] = $val;
                    }
                }
            }
        }
        else
        {
            $re_arr['data_info'] = $date_arr;
        }

        /* 處理更新時間等於0的數據 */
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('goods') .
               " SET last_update = 1 WHERE is_delete = 0 AND is_on_sale = 1 AND last_update = 0";
        $GLOBALS['db']->query($sql, 'SILENT');

        $re_arr['counts'] = $date_count['count'];
        data_back($re_arr, '', RETURN_TYPE);  //返回數據
    }
    else
    {
        api_err('0x003', 'required date invalid');   //請求數據異常
    }
}

/**
 *  商品詳細信息接口函數
 */
function search_goods_detail()
{
    check_auth();   //檢查基本權限

    $version = '1.0';   //版本號

    if ($_POST['api_version'] != $version)      //網店的接口版本低
    {
        api_err('0x008', 'a low version api');
    }

    if (!empty($_POST['goods_id']) && is_numeric($_POST['goods_id']))
    {
        $sql = 'SELECT g.goods_id, g.last_update AS last_modify, g.cat_id, c.cat_name AS category_name, g.brand_id, b.brand_name, g.shop_price AS price, g.goods_sn AS bn, g.goods_name AS name, g.is_on_sale AS marketable, g.goods_weight AS weight, g.goods_number AS store , g.give_integral AS score, g.add_time AS uptime, g.original_img AS image_default, g.goods_desc AS intro' .
        ' FROM ' . $GLOBALS['ecs']->table('category') . ' AS c, ' . $GLOBALS['ecs']->table('goods') . ' AS g LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id'.
        ' WHERE g.cat_id = c.cat_id AND g.goods_id = ' . $_POST['goods_id'];
        $goods_data = $GLOBALS['db']->getRow($sql);

        if (empty($goods_data))
        {
            api_err('0x003', 'no data to back');    //無符合條件數據
        }

        $goods_data['goods_link'] = 'http://' . $_SERVER['HTTP_HOST'] . '/goods.php?id=' . $goods_data['goods_id'];
        $goods_data['image_default'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $goods_data['image_default'];
        $goods_data['unit'] = '千克';
        $goods_data['brand_name'] = empty($goods_data['brand_name']) ? '' : $goods_data['brand_name'];

        $prop = create_goods_properties($_POST['goods_id']);
        $goods_data['props_name'] = $prop['props_name'];
        $goods_data['props'] = $prop['props'];

        if (!empty($_POST['columns']))
        {
            $column_arr = explode('|', $_POST['columns']);
            foreach ($goods_data as $key=>$val)
            {
                if (in_array($key, $column_arr))
                {
                    $re_arr['data_info'][$key] = $val;
                }
            }
        }
        else
        {
            $re_arr['data_info'] = $goods_data;
        }

        data_back($re_arr, '', RETURN_TYPE);  //返回數據
    }
    else
    {
        api_err('0x003', 'required date invalid');   //請求數據異常
    }
}

/**
 *  被刪除商品列表接口函數
 */
function search_deleted_goods_list()
{
    api_err('0x007', '暫時不提供此服務功能');   //服務不可用
}

/**
 *  獲取貨品列表接口函數
 */
function search_products_list()
{
    check_auth();   //檢查基本權限

    $version = '1.0';   //版本號

    if ($_POST['api_version'] != $version)      //網店的接口版本低
    {
        api_err('0x008', 'a low version api');
    }

    if (!empty($_POST['goods_id']) && is_numeric($_POST['goods_id']) || !empty($_POST['bn']))
    {
        $sql = 'SELECT goods_id, last_update AS last_modify, shop_price AS price, goods_sn AS bn, goods_name AS name,  goods_weight         AS weight, goods_number AS store, add_time AS uptime' .
               ' FROM ' . $GLOBALS['ecs']->table('goods') .
               ' WHERE ' . empty($_POST['bn']) ? "goods_id = $_POST[goods_id]" : "goods_sn = $_POST[bn]";
        $goods_data = $GLOBALS['db']->getRow($sql);

        if (empty($goods_data))
        {
            api_err('0x003', 'no data to back');    //無符合條件數據
        }

        $goods_data['product_id'] = $_POST['goods_id'];
        $goods_data['cost'] = $goods_data['price'];

        $prop = create_goods_properties($_POST['goods_id']);
        $goods_data['props'] = $prop['props'];

        if (!empty($_POST['columns']))
        {
            $column_arr = explode('|', $_POST['columns']);
            foreach ($goods_data as $key=>$val)
            {
                if (in_array($key, $column_arr))
                {
                    $re_arr['data_info'][$key] = $val;
                }
            }
        }
        else
        {
            $re_arr['data_info'] = $goods_data;
        }

        data_back($re_arr, '', RETURN_TYPE);  //返回數據
    }
    else
    {
        api_err('0x003', 'required date invalid');   //請求數據異常
    }
}

/**
 *  獲取站點信息接口函數
 */
function search_site_info()
{
    check_auth();   //檢查基本權限

    $version = '1.0';   //版本號

    if ($_POST['api_version'] != $version)      //網店的接口版本低
    {
        api_err('0x008', 'a low version api');
    }

    $sql = 'SELECT code, value'.
           ' FROM ' . $GLOBALS['ecs']->table('shop_config') .
           " WHERE code IN ('shop_name', 'service_phone')";

    $siteinfo['data_info'] = $GLOBALS['db']->getRow($sql);

    $siteinfo['data_info']['site_address'] = $_SERVER['SERVER_NAME'];

    data_back($siteinfo, '', RETURN_TYPE);  //返回數據
}

/**
 *  權限校驗函數
 */
function check_auth()
{
    $license = get_shop_license();  // 取出網店 license信息
    if (empty($license['certificate_id']) || empty($license['token']) || empty($license['certi']))
    {
        api_err('0x006', 'no certificate');   //沒有證書數據，輸出系統級錯誤:用戶權限不夠
    }

    if (!check_shopex_ac($_POST, $license['token']))
    {
        api_err('0x009');   //輸出系統級錯誤:簽名無效
    }

    /* 對應用申請的session進行驗證 */
    $certi['certificate_id'] = $license['certificate_id']; // 網店證書ID
    $certi['app_id'] = 'ecshop_b2c'; // 說明客戶端來源
    $certi['app_instance_id'] = 'webcollect'; // 應用服務ID
    $certi['version'] = VERSION . '#' .  RELEASE; // 網店軟件版本號
    $certi['format'] = 'json'; // 官方返回數據格式
    $certi['certi_app'] = 'sess.valid_session'; // 證書方法
    $certi['certi_session'] = $_POST['app_session']; //應用服務器申請的session值
    $certi['certi_ac'] = make_shopex_ac($certi, $license['token']); // 網店驗證字符串

    $request_arr = exchange_shop_license($certi, $license);
    if ($request_arr['res'] != 'succ')
    {
        api_err('0x001', 'session is invalid');   //輸出系統級錯誤:身份驗證失敗
    }
}

/**
 *  驗證POST簽名
 *
 *  @param   string   $post_params   POST傳遞參數
 *  @param   string   $token         證書加密碼
 *
 *  @return  boolean                 返回是否有效
 */
function check_shopex_ac($post_params,$token)
{
    ksort($post_params);
    $str = '';
    foreach($post_params as $key=>$value)
    {
        if ($key!='ac')
        {
            $str.=$value;
        }
    }
    if ($post_params['ac'] == md5($str.$token))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 *  系統級錯誤處理
 *
 *  @param   string   $err_type   錯誤類型代號
 *  @param   string   $err_info   錯誤說明
 *
 */
function api_err($err_type, $err_info = '')
{
    /* 系統級錯誤列表 */
    $err_arr = array();
    $err_arr['0x001'] = 'Verify fail';          //身份驗證失敗
    $err_arr['0x002'] = 'Time out';             //請求/執行超時
    $err_arr['0x003'] = 'Data fail';            //數據異常
    $err_arr['0x004'] = 'Db error';             //數據庫執行失敗
    $err_arr['0x005'] = 'Service error';        //服務器導常
    $err_arr['0x006'] = 'User permissions';     //用戶權限不夠
    $err_arr['0x007'] = 'Service unavailable';  //服務不可用
    $err_arr['0x008'] = 'Missing Method';       //方法不可用
    $err_arr['0x009'] = 'Missing signature';    //簽名無效
    $err_arr['0x010'] = 'Missing api version';  //版本丟失
    $err_arr['0x011'] = 'Api verion error';     //API版本異常
    $err_arr['0x012'] = 'Api need update';      //API需要升級
    $err_arr['0x013'] = 'Shop Error';           //網痁服務異常
    $err_arr['0x014'] = 'Shop Space Error';     //網店空間不足

    data_back($err_info == '' ? $err_arr[$err_type] : $err_info, $err_type, RETURN_TYPE, 'fail');  //回覆請求以錯誤信息
}

/**
 *  返回結果集
 *
 *  @param   mixed      $info       返回的有效數據集或是錯誤說明
 *  @param   string     $msg        為空或是錯誤類型代號
 *  @param   string     $result     請求成功或是失敗的標識
 *  @param   int        $post       1為xml方式，2為json方式
 *
 */
function data_back($info, $msg = '', $post, $result = 'success')
{
    /* 分為xml和json兩種方式 */
    $data_arr = array('result'=>$result, 'msg'=>$msg, 'info'=>$info);
    $data_arr = to_utf8_iconv($data_arr);  //確保傳遞的編碼為UTF-8

    if ($post == 1)
    {
        /* xml方式 */
        if (class_exists('DOMDocument'))
        {
            $doc=new DOMDocument('1.0','UTF-8');
            $doc->formatOutput=true;

            $shopex=$doc->createElement('shopex');
            $doc->appendChild($shopex);

            $result=$doc->createElement('result');
            $shopex->appendChild($result);
            $result->appendChild($doc->createCDATASection($data_arr['result']));

            $msg=$doc->createElement('msg');
            $shopex->appendChild($msg);
            $msg->appendChild($doc->createCDATASection($data_arr['msg']));

            $info=$doc->createElement('info');
            $shopex->appendChild($info);

            create_tree($doc, $info, $data_arr['info']);
            die($doc->saveXML());
        }

        die('<?xml version="1.0" encoding="UTF-8"?>' . array2xml($data_arr)) ;
    }
    else
    {
        /* json方式 */
        $json  = new JSON;
        die($json->encode($data_arr));    //把生成的返回字符串打印出來
    }
}

/**
 *  循環生成xml節點
 *
 *  @param  handle      $doc            xml實例句柄
 *  @param  handle      $top            當前父節點
 *  @param  array       $info_arr       需要解析的數組
 *  @param  boolean     $have_item      是否是數據數組，是則需要在每條數據上加item父節點
 *
 */
function create_tree($doc, $top, $info_arr, $have_item = false)
{
    if (is_array($info_arr))
    {
        foreach ($info_arr as $key => $val)
        {
            if (is_array($val))
            {
                if ($have_item == false)
                {
                    $data_info=$doc->createElement('data_info');
                    $top->appendChild($data_info);
                    create_tree($doc, $data_info, $val, true);
                }
                else
                {
                    $item=$doc->createElement('item');
                    $top->appendChild($item);
                    $key_code = $doc->createAttribute('key');
                    $item->appendChild($key_code);
                    $key_code->appendChild($doc->createTextNode($key));
                    create_tree($doc, $item, $val);
                }
            }
            else
            {
                $text_code=$doc->createElement($key);
                $top->appendChild($text_code);
                if (is_string($val))
                {
                    $text_code->appendChild($doc->createCDATASection($val));
                }
                else
                {
                    $text_code->appendChild($doc->createTextNode($val));
                }
            }
        }
    }
    else
    {
        $top->appendChild($doc->createCDATASection($info_arr));
    }
}

function array2xml($data,$root='shopex'){
    $xml='<'.$root.'>';
    _array2xml($data,$xml);
    $xml.='</'.$root.'>';
    return $xml;
}

function _array2xml(&$data,&$xml){
    if(is_array($data)){
        foreach($data as $k=>$v){
            if(is_numeric($k)){
                $xml.='<item key="' . $k . '">';
                $xml.=_array2xml($v,$xml);
                $xml.='</item>';
            }else{
                $xml.='<'.$k.'>';
                $xml.=_array2xml($v,$xml);
                $xml.='</'.$k.'>';
            }
        }
    }elseif(is_numeric($data)){
        $xml.=$data;
    }elseif(is_string($data)){
        $xml.='<![CDATA['.$data.']]>';
    }
}

function create_goods_properties($goods_id)
{
    /* 對屬性進行重新排序和分組
    $sql = "SELECT attr_group ".
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);

    if (!empty($grp))
    {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }
    */

    /* 獲得商品的規格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, ".
                "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    $arr['props_name'] = array();     // props_name
    $arr['props'] = array();          // props

    foreach ($res AS $row)
    {
        if ($row['attr_type'] == 0)
        {
            //$group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];

            //$arr['props_name'][$row['attr_group']]['name'] = $group;
            $arr['props_name'][] = array('name' => $row['attr_name'], 'value' => $row['attr_value']);

            $arr['props'][] = array('pid' => $row['attr_id'], 'vid' => $row['goods_attr_id']);
        }
    }

    return $arr;
}
?>