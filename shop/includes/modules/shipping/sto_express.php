<?php

/**
 * ECSHOP 申通快遞 配送方式插件
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: sto_express.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/sto_express.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}


/* 模塊的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    include_once(ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/admin/shipping.php');

    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代碼必須和文件名保持一致 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'sto_express_desc';

    /* 配送方式是否支持貨到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 插件作者的官方網站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 配送接口需要的參數 */
    $modules[$i]['configure'] = array(
                                    array('name' => 'item_fee',     'value'=>15), /* 單件商品的配送費用 */
                                    array('name' => 'base_fee',    'value'=>15), /* 1000克以內的價格           */
                                    array('name' => 'step_fee',     'value'=>5),  /* 續重每1000克增加的價格 */
                                );

    /* 模式編輯器 */
    $modules[$i]['print_model'] = 2;

    /* 打印單背景 */
    $modules[$i]['print_bg'] = '/images/receipt/dly_sto_express.jpg';

   /* 打印快遞單標籤位置信息 */
    $modules[$i]['config_lable'] = 't_shop_address,' . $_LANG['lable_box']['shop_address'] . ',235,48,131,152,b_shop_address||,||t_shop_name,' . $_LANG['lable_box']['shop_name'] . ',237,26,131,200,b_shop_name||,||t_shop_tel,' . $_LANG['lable_box']['shop_tel'] . ',96,36,144,257,b_shop_tel||,||t_customer_post,' . $_LANG['lable_box']['customer_post'] . ',86,23,578,268,b_customer_post||,||t_customer_address,' . $_LANG['lable_box']['customer_address'] . ',232,49,434,149,b_customer_address||,||t_customer_name,' . $_LANG['lable_box']['customer_name'] . ',151,27,449,231,b_customer_name||,||t_customer_tel,' . $_LANG['lable_box']['customer_tel'] . ',90,32,452,261,b_customer_tel||,||';

    return;
}

/**
 * 申通快遞費用計算方式:
 * ====================================================================================
 * - 江浙滬地區統一資費： 1公斤以內15元， 每增加1公斤加5-6元, 雲南為8元
 * - 其他地區統一資費:    1公斤以內18元， 每增加1公斤加5-6元, 雲南為8元
 * - 對於體大質輕的包裹，我們將按照航空運輸協會的規定，根據體積和實際重量中較重的一種收費，需將包的長、寬、高、相乘，再除以6000
 * - (具體資費請上此網站查詢:http://www.car365.cn/fee.asp 客服電話:021-52238886)
 * -------------------------------------------------------------------------------------
 *
 */
class sto_express
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */

    /**
     * 配置信息參數
     */
    var $configure;

    /*------------------------------------------------------ */
    //-- PUBLIC METHODs
    /*------------------------------------------------------ */

    /**
     * 構造函數
     *
     * @param: $configure[array]    配送方式的參數的數組
     *
     * @return null
     */
    function sto_express($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }

    /**
     * 計算訂單的配送費用的函數
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金額
     * @param   float   $goods_amount   商品件數
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount, $goods_number)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            @$fee = $this->configure['base_fee'];
            $this->configure['fee_compute_mode'] = !empty($this->configure['fee_compute_mode']) ? $this->configure['fee_compute_mode'] : 'by_weight';

             if ($this->configure['fee_compute_mode'] == 'by_number')
            {
                $fee = $goods_number * $this->configure['item_fee'];
            }
            else
            {
                if ($goods_weight > 1)
                {
                    $fee += (ceil(($goods_weight - 1))) * $this->configure['step_fee'];
                }
            }

            return $fee;
        }
    }

    /**
     * 查詢快遞狀態
     *
     * @access  public
     * @param   string  $invoice_sn     發貨單號
     * @return  string  查詢窗口的鏈接地址
     */
    function query($invoice_sn)
    {
        $str = '<form style="margin:0px" methods="post" '.
            'action="http://61.152.237.204:8081/query_result.asp" name="queryForm_' .$invoice_sn. '" target="_blank">'.
            '<input type="hidden" name="wen" value="' .str_replace("<br>","\n",$invoice_sn). '" />'.
            '<a href="javascript:document.forms[\'queryForm_' .$invoice_sn. '\'].submit();">' .$invoice_sn. '</a>'.
            '</form>';

        return $str;
    }
}

?>