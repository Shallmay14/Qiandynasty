<?php

/**
 * ECSHOP ?物流程相??言
 * ============================================================================
 * 版?所有 2005-2010 上海商派网?科技有限公司，并保留所有?利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * ?不是一?自由?件！您只能在不用于商?目的的前提下?程序代??行修改和
 * 使用；不允??程序代?以任何形式任何目的的再?布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: shopping_flow.php 17063 2010-03-25 06:35:46Z liuhui $
*/

$_LANG['flow_login_register']['username_not_null'] = '請您輸入會員名稱。';
$_LANG['flow_login_register']['username_invalid'] = '您輸入了一個不正確的會員名稱。';
$_LANG['flow_login_register']['password_not_null'] = '請您輸入密碼。';
$_LANG['flow_login_register']['email_not_null'] = '請您輸入電子郵件。';
$_LANG['flow_login_register']['email_invalid'] = '您輸入的電子郵件不正確。';
$_LANG['flow_login_register']['password_not_same'] = '您輸入的密碼和確認密碼不一致。';
$_LANG['flow_login_register']['password_lt_six'] = '密碼不能小於 6 個字元。';

$_LANG['regist_success'] = "恭喜您，%s 帳號註冊成功！";
$_LANG['login_success'] = '恭喜！您已經成功登入本站！';

/* ?物? */
$_LANG['update_cart'] = '更新購物車';
$_LANG['back_to_cart'] = '返回購物車';
$_LANG['update_cart_notice'] = '購物車更新成功，請您重新選擇您需要的贈品。';
$_LANG['direct_shopping'] = '不打算登入，直接購買';
$_LANG['goods_not_exists'] = '對不起，指定的商品不存在';
$_LANG['drop_goods_confirm'] = '您確定要把該商品移出購物車嗎？';
$_LANG['goods_number_not_int'] = '請您輸入正確的商品數量。';
$_LANG['stock_insufficiency'] = '非常抱歉，您選擇的商品 %s 的庫存數量只有 %d，您最多只能購買 %d 件。';
$_LANG['package_stock_insufficiency'] = '非常抱歉，您選擇的超值組合數量已經超出庫存。請您減少購買量或聯絡商家。';
$_LANG['shopping_flow'] = '購物流程';
$_LANG['username_exists'] = '您輸入的會員名稱已存在，請換一個試試。';
$_LANG['email_exists'] = '您輸入的電子郵件已存在，請換一個試試。';
$_LANG['surplus_not_enough'] = '您使用的餘額不能超過您現有的餘額。';
$_LANG['integral_not_enough'] = '您使用的積分不能超過您現有的積分。';
$_LANG['integral_too_much'] = "您使用的積分不能超過 %d";
$_LANG['invalid_bonus'] = "您選擇的紅利並不存在。";
$_LANG['no_goods_in_cart'] = '您的購物車中沒有商品！';
$_LANG['not_submit_order'] = '您參與本次團購商品的訂單已送出，請勿重複操作！';
$_LANG['pay_success'] = '本次支付已經成功，我們將盡快為您出貨。';
$_LANG['pay_fail'] = '本次支付失敗，請及時和我們取得聯絡。';
$_LANG['pay_disabled'] = '您選用的付款方式已經被停用。';
$_LANG['pay_invalid'] = '您選用了一個不正確的付款方式。該付款方式不存在或者已經被停用。請您立即和我們取得聯絡。';
$_LANG['flow_no_shipping'] = '您必須選取一個配送方式。';
$_LANG['flow_no_payment'] = '您必須選取一個付款方式。';
$_LANG['pay_not_exist'] = '選用的付款方式不存在。';
$_LANG['storage_short'] = '庫存不足';
$_LANG['subtotal'] = '小計';
$_LANG['accessories'] = '配件';
$_LANG['largess'] = '贈品';
$_LANG['shopping_money'] = '購物金額小計 %s';
$_LANG['than_market_price'] = '比市場價 %s 節省了 %s (%s)';
$_LANG['your_discount'] = '根據優惠活動<a href="activity.php"><font color=red>%s</font></a>，您可以享受折扣 %s';
$_LANG['no'] = '無';
$_LANG['not_support_virtual_goods'] = '購物車中存在非實體商品，不支援匿名購買，請登入後在購買';
$_LANG['not_support_insure'] = '不支援保價';
$_LANG['clear_cart'] = '清空購物車';
$_LANG['drop_to_collect'] = '放入我的最愛';
$_LANG['password_js']['show_div_text'] = '請點選更新購物車按鈕';
$_LANG['password_js']['show_div_exit'] = '關閉';
$_LANG['goods_fittings'] = '商品相關配件';
$_LANG['parent_name'] = '相關商品：';
$_LANG['remark_package'] = '組合';

/* 优惠活? */
$_LANG['favourable_name'] = '活動名稱：';
$_LANG['favourable_period'] = '優惠期限：';
$_LANG['favourable_range'] = '優惠範圍：';
$_LANG['far_ext'][FAR_ALL] = '全部商品';
$_LANG['far_ext'][FAR_BRAND] = '以下品牌';
$_LANG['far_ext'][FAR_CATEGORY] = '以下分類';
$_LANG['far_ext'][FAR_GOODS] = '以下商品';
$_LANG['favourable_amount'] = '金額區間：';
$_LANG['favourable_type'] = '優惠方式：';
$_LANG['fat_ext'][FAT_DISCOUNT] = '享受 %d%% 的折扣';
$_LANG['fat_ext'][FAT_GOODS] = '從底下的贈品（特惠品）中選擇 %d 個（0 表示不限制數量）';
$_LANG['fat_ext'][FAT_PRICE] = '直接減少現金 %d';

$_LANG['favourable_not_exist'] = '您要加入購物車的優惠活動不存在';
$_LANG['favourable_not_available'] = '您不能享受該優惠';
$_LANG['favourable_used'] = '該優惠活動已加入購物車了';
$_LANG['pls_select_gift'] = '請選擇贈品（特惠品）';
$_LANG['gift_count_exceed'] = '您選擇的贈品（特惠品）數量超過上限了';
$_LANG['gift_in_cart'] = '您選擇的贈品（特惠品）已經在購物車中了：%s';
$_LANG['label_favourable'] = '優惠活動';
$_LANG['label_collection'] = '我的收藏';
$_LANG['collect_to_flow'] = '立即購買';

/* 登?注? */
$_LANG['forthwith_login'] = '登入';
$_LANG['forthwith_register'] = '註冊新會員';
$_LANG['signin_failed'] = '對不起，登入失敗，請檢查您的會員名稱和密碼是否正確';
$_LANG['gift_remainder'] = '說明：在您登入或註冊後，請到購物車頁面重新選擇贈品。';

/* 收?人信息 */
$_LANG['flow_js']['consignee_not_null'] = '收貨人姓名不能留空！';
$_LANG['flow_js']['country_not_null'] = '請您選擇收貨人所在國家！';
$_LANG['flow_js']['province_not_null'] = '請您選擇收貨人所在地區！';
$_LANG['flow_js']['city_not_null'] = '請您選擇收貨人所在縣市！';
$_LANG['flow_js']['district_not_null'] = '請您選擇收貨人所在區域！';
$_LANG['flow_js']['invalid_email'] = '您輸入的郵件位址不是一個合法的郵件位址。';
$_LANG['flow_js']['address_not_null'] = '收貨人的詳細地址不能留空！';
$_LANG['flow_js']['tele_not_null'] = '電話不能留空！';
$_LANG['flow_js']['shipping_not_null'] = '請您選擇配送方式！';
$_LANG['flow_js']['payment_not_null'] = '請您選擇付款方式！';
$_LANG['flow_js']['goodsattr_style'] = 1;
$_LANG['flow_js']['tele_invaild'] = '電話號碼不有效的號碼';
$_LANG['flow_js']['zip_not_num'] = '郵遞區號只能填寫數字';
$_LANG['flow_js']['mobile_invaild'] = '手機號碼不是合法號碼';

$_LANG['new_consignee_address'] = '新收貨地址';
$_LANG['consignee_address'] = '收貨地址';
$_LANG['consignee_name'] = '收貨人姓名稱';
$_LANG['country_province'] = '配送區域';
$_LANG['please_select'] = '請選擇';
$_LANG['city_district'] = '縣市/地區';
$_LANG['email_address'] = '電子郵件位址';
$_LANG['detailed_address'] = '詳細地址';
$_LANG['postalcode'] = '郵遞區號';
$_LANG['phone'] = '電話';
$_LANG['mobile'] = '手機';
$_LANG['backup_phone'] = '手機';
$_LANG['sign_building'] = '大樓名稱';
$_LANG['deliver_goods_time'] = '最佳送貨時間';
$_LANG['default'] = '預設';
$_LANG['default_address'] = '預設地址';
$_LANG['confirm_submit'] = '確認送出';
$_LANG['confirm_edit'] = '確認修改';
$_LANG['country'] = '國家';
$_LANG['province'] = '省份';
$_LANG['city'] = '縣市';
$_LANG['area'] = '所在區域';
$_LANG['consignee_add'] = '加入新收貨地址';
$_LANG['shipping_address'] = '配送至這個位址';
$_LANG['address_amount'] = '您的收貨地址最多只能是三個';
$_LANG['not_fount_consignee'] = '對不起，您選取的收貨地址不存在。';

/*------------------------------------------------------ */
//-- ??提交
/*------------------------------------------------------ */

$_LANG['goods_amount_not_enough'] = '您購買的商品沒有達到本店的最低限購金額 %s ，不能送出訂單。';
$_LANG['balance_not_enough'] = '您的餘額不足以支付整個訂單，請選擇其它付款方式';
$_LANG['select_shipping'] = '您選取的配送方式為';
$_LANG['select_payment'] = '您選取的付款方式為';
$_LANG['order_amount'] = '您的應付款金額為';
$_LANG['remember_order_number'] = '感謝您在本店購物！您的訂單已送出成功，請記住您的訂單號碼';
$_LANG['back_home'] = '<a href="index.php">返回首頁</a>';
$_LANG['goto_user_center'] = '<a href="user.php">會員中心</a>';
$_LANG['order_submit_back'] = '您可以 %s 或前往 %s';

$_LANG['order_placed_sms'] = "您有新訂單。收貨人:%s 電話:%s";
$_LANG['sms_paid'] = '已付款';

$_LANG['notice_gb_order_amount'] = '（備註：團購如果有保證金，第一次只需支付保證金和相對的支付費用）';

$_LANG['pay_order'] = '支付訂單 %s';
$_LANG['validate_bonus'] = '驗證紅利';
$_LANG['input_bonus_no'] = '或者輸入紅利序號';
$_LANG['select_bonus'] = '選擇已有紅利';
$_LANG['bonus_sn_error'] = '該紅利序號不正確';
$_LANG['bonus_min_amount_error'] = '訂單商品金額沒有達到使用該紅利的最低金額 %s';
$_LANG['bonus_is_ok'] = '該紅利序號可以使用，可以抵扣 %s';


$_LANG['shopping_myship'] = '我的配送';
$_LANG['shopping_activity'] = '活動清單';
$_LANG['shopping_package'] = '超值組合';
?>
