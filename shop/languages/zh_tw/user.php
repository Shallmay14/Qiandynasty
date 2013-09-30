<?php

/**
 * ECSHOP 用?中心?言?
 * ============================================================================
 * 版?所有 2005-2010 上海商派網?科技有限公司，並保留所有?利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * ?不是一?自由?件！您只能在不用於商?目的的前提下?程序代??行修改和
 * 使用；不允??程序代?以任何形式任何目的的再?布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: user.php 17063 2010-03-25 06:35:46Z liuhui $
*/



$_LANG['require_login'] = '不合法入口。<br />必須登入才能完成作業。';

$_LANG['no_records'] = '沒有記錄';
$_LANG['shot_message'] = "簡訊";

/* 用?菜? */
$_LANG['label_welcome'] = '歡迎頁';
$_LANG['label_profile'] = '會員資訊';
$_LANG['label_order'] = '我的訂單';
$_LANG['label_address'] = '收貨地址';
$_LANG['label_message'] = '我的留言';
$_LANG['label_tag'] = '我的標籤';
$_LANG['label_collection'] = '我的收藏';
$_LANG['label_bonus'] = '我的紅利';
$_LANG['label_comment'] = '我的評論';
$_LANG['label_affiliate'] = '我的推薦';
$_LANG['label_group_buy'] = '我的團購';
$_LANG['label_booking'] = '缺貨登記';
$_LANG['label_user_surplus'] = '資金管理';
$_LANG['label_track_packages'] = '追蹤包裹';
$_LANG['label_transform_points'] = '積分兌換';
$_LANG['label_logout'] = '登出';

/* ??余?(?付款) */
$_LANG['add_surplus_log'] = '檢視帳戶細節';
$_LANG['view_application'] = '檢視申請記錄';
$_LANG['surplus_pro_type'] = '類型';
$_LANG['repay_money'] = '提現金額';
$_LANG['money'] = '金額';
$_LANG['surplus_type_0'] = '儲值';
$_LANG['surplus_type_1'] = '提現';
$_LANG['deposit_money'] = '儲值金額';
$_LANG['process_notic'] = '會員備註';
$_LANG['admin_notic'] = '管理員備註';
$_LANG['submit_request'] = '送出申請';
$_LANG['process_time'] = '操作時間';
$_LANG['use_time'] = '使用時間';
$_LANG['is_paid'] = '狀態';
$_LANG['is_confirm'] = '已完成';
$_LANG['un_confirm'] = '未確認';
$_LANG['pay'] = '付款';
$_LANG['is_cancel'] = '取消';
$_LANG['account_inc'] = '增加';
$_LANG['account_dec'] = '減少';
$_LANG['change_desc'] = '備註';
$_LANG['surplus_amount'] = '您的儲值金額為：';
$_LANG['payment_name'] = '您選擇的付款方式為：';
$_LANG['payment_fee'] = '支付手續費用為：';
$_LANG['payment_desc'] = '付款方式描述：';
$_LANG['current_surplus'] = '您目前的可用資金為：';
$_LANG['unit_yuan'] = '元';
$_LANG['for_free'] = '贈品免費';
$_LANG['surplus_amount_error'] = '您要申請提現的金額超過了您現有的餘額，本次作業將不可進行！';
$_LANG['surplus_appl_submit'] = '您的提現申請已成功送出，請等待管理員的審核！';
$_LANG['process_false'] = '此次作業失敗，請返回重試！';
$_LANG['confirm_remove_account'] = '您確定要刪除此筆記錄嗎？';
$_LANG['back_page_up'] = '返回上一頁';
$_LANG['back_account_log'] = '返回帳戶細節清單';
$_LANG['amount_gt_zero'] = '請在「金額」欄輸入大於 0 的數字';
$_LANG['select_payment_pls'] = '請選擇付款方式';

//JS?言?
$_LANG['account_js']['surplus_amount_empty'] = '請輸入您要操作的金額數量！';
$_LANG['account_js']['surplus_amount_error'] = '您輸入的金額數量格式不正確！';
$_LANG['account_js']['process_desc'] = '請輸入您此次操作的備註資訊！';
$_LANG['account_js']['payment_empty'] = '請選擇付款方式！';

/* 缺?登? */
$_LANG['oos_booking'] = '缺貨登記';
$_LANG['booking_goods_name'] = '訂購商品名稱';
$_LANG['booking_amount'] = '訂購數量';
$_LANG['booking_time'] = '登記時間';
$_LANG['process_desc'] = '處理備註';
$_LANG['describe'] = '訂購描述';
$_LANG['contact_username'] = '聯絡人';
$_LANG['contact_phone'] = '聯絡電話';
$_LANG['submit_booking_goods'] = '送出缺貨登記';
$_LANG['booking_success'] = '您的商品訂購已經成功送出！';
$_LANG['booking_rec_exist'] = '此商品您已經進行過缺貨登記了！';
$_LANG['back_booking_list'] = '返回缺貨登記清單';
$_LANG['not_dispose'] = '未處理';
$_LANG['no_goods_id'] = '請指定商品 ID';

//JS?言?
$_LANG['booking_js']['booking_amount_empty'] = '請輸入您要訂購的商品數量！';
$_LANG['booking_js']['booking_amount_error'] = '您輸入的訂購數量格式不正確！';
$_LANG['booking_js']['describe_empty'] = '請輸入您的訂購描述資訊！';
$_LANG['booking_js']['contact_username_empty'] = '請輸入聯絡人姓名！';
$_LANG['booking_js']['email_empty'] = '請輸入聯絡人的電子郵件位址！';
$_LANG['booking_js']['email_error'] = '您輸入的電子郵件位址格式不正確！';
$_LANG['booking_js']['contact_phone_empty'] = '請輸入聯絡人的電話！';

/* ?人?料 */
$_LANG['confirm_submit'] = '　確 定　';
$_LANG['member_rank'] = '會員等級';
$_LANG['member_discount'] = '會員折扣';
$_LANG['rank_integral'] = '等級積分';
$_LANG['consume_integral'] = '消費積分';
$_LANG['account_balance'] = '帳戶餘額';
$_LANG['user_bonus'] = '會員紅利';
$_LANG['user_bonus_info'] = '共計 %d 個，價值 %s';
$_LANG['not_bonus'] = '沒有紅利';
$_LANG['add_user_bonus'] = '加入一個紅利';
$_LANG['bonus_number'] = '紅利序號';
$_LANG['old_password'] = '原密碼';
$_LANG['new_password'] = '新的密碼';
$_LANG['confirm_password'] = '確認密碼';

$_LANG['bonus_sn_exist'] = '此紅利號碼已經被佔用了！';
$_LANG['bonus_sn_not_exist'] = '此紅利號碼不存在！';
$_LANG['add_bonus_sucess'] = '加入新的紅利操作成功！';
$_LANG['add_bonus_false'] = '加入新的紅利作業失敗！';

$_LANG['not_login'] = '會員未登入。無法完成作業';
$_LANG['profile_lnk'] = '檢視我的個人資料';
$_LANG['edit_email_failed'] = '編輯電子郵件位址失敗！';
$_LANG['edit_profile_success'] = '您的個人資料已經成功修改！';
$_LANG['edit_profile_failed'] = '修改個人資料作業失敗！';
$_LANG['oldpassword_error'] = '您輸入的舊密碼有誤!請確認再後輸入！';

//JS?言?
$_LANG['profile_js']['bonus_sn_empty'] = '請輸入您要加入的紅利號碼！';
$_LANG['profile_js']['bonus_sn_error'] = '您輸入的紅利號碼格式不正確！';

$_LANG['profile_js']['email_empty'] = '請輸入您的電子郵件位址！';
$_LANG['profile_js']['email_error'] = '您輸入的電子郵件位址格式不正確！';
$_LANG['profile_js']['old_password_empty'] = '請輸入您的原密碼！';
$_LANG['profile_js']['new_password_empty'] = '請輸入您的新的密碼！';
$_LANG['profile_js']['confirm_password_empty'] = '請輸入您的確認密碼！';
$_LANG['profile_js']['both_password_error'] = '兩次輸入的密碼不一致！';
$_LANG['profile_js']['msg_blank'] = '不能留空';
$_LANG['profile_js']['no_select_question'] = '- 您沒有完成密碼提示問題的作業';

/* 支付方式 */
$_LANG['pay_name'] = '名稱';
$_LANG['pay_desc'] = '描述';
$_LANG['pay_fee'] = '手續費';

/* 收?地址 */
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
$_LANG['yes'] = '是';
$_LANG['no'] = '否';
$_LANG['country'] = '國家';
$_LANG['province'] = '省份';
$_LANG['city'] = '縣市';
$_LANG['area'] = '所在區域';

$_LANG['search_ship'] = '檢視支援的配送方式';

$_LANG['del_address_false'] = '刪除收貨地址失敗！';
$_LANG['add_address_success'] = '加入新位址成功！';
$_LANG['edit_address_success'] = '您的收貨地址資訊已成功更新！';
$_LANG['address_list_lnk'] = '返回位址清單';
$_LANG['add_address'] = '新增收貨地址';
$_LANG['confirm_edit'] = '確認修改';

$_LANG['confirm_drop_address'] = '您確認要刪除該收貨地址嗎？';

/* ??密?找回 */
$_LANG['username_and_email'] = '請輸入您註冊的會員名稱和註冊時填寫的電子郵件位址。';
$_LANG['enter_new_password'] = '請輸入您的新的密碼';
$_LANG['username_no_email'] = '您填寫的會員名稱與電子郵件位址不符，請重新輸入！';
$_LANG['fail_send_password'] = '傳送郵件發生錯誤，請與管理員聯絡！';
$_LANG['send_success'] = '重置密碼的郵件已經發到您的信箱：';
$_LANG['parm_error'] = '參數錯誤，請返回！';
$_LANG['edit_password_failure'] = '您輸入的原密碼不正確！';
$_LANG['edit_password_success'] = '您的新的密碼已設定成功！';
$_LANG['username_not_match_email'] = '會員名稱與電子郵件位址不符，請重新輸入！';
$_LANG['get_question_username'] = '請輸入您註冊的會員名稱以取得您的密碼提示問題。';
$_LANG['no_passwd_question'] = '您沒有設定密碼提示問題，無法透過這種方式取回密碼。';
$_LANG['input_answer'] = '請根據您註冊時設定的密碼問題輸入設定的答案';
$_LANG['wrong_passwd_answer'] = '您輸入的密碼答案是錯誤的';

//JS?言?
$_LANG['password_js']['user_name_empty'] = '請輸入您的會員名稱！';
$_LANG['password_js']['email_address_empty'] = '請輸入您的電子郵件位址！';
$_LANG['password_js']['email_address_error'] = '您輸入的電子郵件位址格式不正確！';
$_LANG['password_js']['new_password_empty'] = '請輸入您的新的密碼！';
$_LANG['password_js']['confirm_password_empty'] = '請輸入您的確認密碼！';
$_LANG['password_js']['both_password_error'] = '您兩次輸入的密碼不一致！';

/* ??留言 */
$_LANG['message_title'] = '主旨';
$_LANG['message_time'] = '留言時間';
$_LANG['reply_time'] = '回覆時間';
$_LANG['shopman_reply'] = '店長回覆';
$_LANG['send_message'] = '發表留言';
$_LANG['message_type'] = '留言類型';
$_LANG['message_content'] = '留言內容';
$_LANG['submit_message'] = '送出留言';
$_LANG['upload_img'] = '上傳檔案';
$_LANG['img_name'] = '圖片名稱';

/* 留言?型 */
$_LANG['type'][M_MESSAGE] = '留言';
$_LANG['type'][M_COMPLAINT] = '投訴';
$_LANG['type'][M_ENQUIRY] = '詢問';
$_LANG['type'][M_CUSTOME] = '售後';
$_LANG['type'][M_BUY] = '求購';
$_LANG['type'][M_BUSINESS] = '商家留言';

$_LANG['add_message_success'] = '發表留言成功';
$_LANG['message_list_lnk'] = '返回留言清單';
$_LANG['msg_title_empty'] = '留言標題為空';
$_LANG['upload_file_limit'] = '檔案大小超過了限制 %dKB';

$_LANG['img_type_tips'] = '<font color="red">小提示：</font>';
$_LANG['img_type_list'] = '您可以上傳以下格式的檔案：<br />gif、jpg、png、word、excel、txt、zip、ppt、pdf';
$_LANG['view_upload_file'] = '檢視上傳的檔案';
$_LANG['upload_file_type'] = '您上傳的檔案類型不正確,請重新上傳！';
$_LANG['upload_file_error'] = '檔案上傳出現錯誤,請重新上傳！';
$_LANG['message_empty'] = '您現在還沒有留言！';
$_LANG['msg_success'] = '您的留言已成功送出！';
$_LANG['confirm_remove_msg'] = '您確定要徹底刪除這條留言嗎？';

/* ???包 */
$_LANG['bonus_is_used'] = '您輸入的紅利您已經領取過了！';
$_LANG['bonus_is_used_by_other'] = '您輸入的紅利已經被其它人領取！';
$_LANG['bonus_add_success'] = '您已經成功的加入了一個新的紅利！';
$_LANG['bonus_not_exist'] = '您輸入的紅利不存在';
$_LANG['user_bonus_empty'] = '您現在還沒有紅利';
$_LANG['add_bonus_sucess'] = '加入新的紅利操作成功！';
$_LANG['add_bonus_false'] = '加入新的紅利作業失敗！';
$_LANG['bonus_add_expire'] = '該紅利已經過期！';
$_LANG['bonus_use_expire'] = '該紅利已經過了使用期！';

/* ???? */
$_LANG['order_list_lnk'] = '我的訂單清單';
$_LANG['order_number'] = '訂單編號';
$_LANG['order_addtime'] = '下單時間';
$_LANG['order_money'] = '訂單總金額';
$_LANG['order_status'] = '訂單狀態';
$_LANG['first_order'] = '主訂單';
$_LANG['second_order'] = '子訂單';
$_LANG['merge_order'] = '合併訂單';
$_LANG['no_priv'] = '您沒有權限操作他人訂單';
$_LANG['buyer_cancel'] = '會員取消';
$_LANG['cancel'] = '取消訂單';
$_LANG['pay_money'] = '付款';
$_LANG['view_order'] = '檢視訂單';
$_LANG['received'] = '確認收貨';
$_LANG['ss_received'] = '已完成';
$_LANG['confirm_cancel'] = '您確認要取消該訂單嗎？取消後此訂單將視為無效訂單';
$_LANG['merge_ok'] = '訂單合併成功！';
$_LANG['merge_invalid_order'] = '對不起，您選擇合併的訂單不允許進行合併的作業。';
$_LANG['select'] = '請選擇...';
$_LANG['order_not_pay'] = "你的訂單狀態為 %s ，不需要付款";
$_LANG['order_sn_empty'] = '合併主訂單號碼不能留空';
$_LANG['merge_order_notice'] = '訂單合併是在出貨前將相同狀態的訂單合併成一新的訂單。<br />收貨地址，送貨方式等以主訂單為準。';
$_LANG['order_exist'] = '該訂單不存在！';
$_LANG['order_is_group_buy'] = '[團購]';
$_LANG['order_is_exchange'] = '[積分商場]';
$_LANG['gb_deposit'] = '（保證金）';
$_LANG['notice_gb_order_amount'] = '（備註：團購如果有保證金，第一次只需支付保證金和相對的支付費用）';
$_LANG['business_message'] = '留言/檢視商家回覆';
$_LANG['pay_order_by_surplus'] = '追加使用餘額支付訂單：%s';
$_LANG['return_surplus_on_cancel'] = '取消訂單 %s，退回支付訂單時使用的預付款';
$_LANG['return_integral_on_cancel'] = '取消訂單 %s，退回支付訂單時使用的積分';

/* ???? */
$_LANG['os'][OS_UNCONFIRMED] = '未確認';
$_LANG['os'][OS_CONFIRMED] = '已確認';
$_LANG['os'][OS_SPLITED] = '已確認';
$_LANG['os'][OS_SPLITING_PART] = '已確認';
$_LANG['os'][OS_CANCELED] = '已取消';
$_LANG['os'][OS_INVALID] = '無效';
$_LANG['os'][OS_RETURNED] = '退貨';

$_LANG['ss'][SS_UNSHIPPED] = '未出貨';
$_LANG['ss'][SS_PREPARING] = '配貨中';
$_LANG['ss'][SS_SHIPPED] = '已出貨';
$_LANG['ss'][SS_RECEIVED] = '收貨確認';
$_LANG['ss'][SS_SHIPPED_PART] = '已出貨(部分商品)';
$_LANG['ss'][SS_SHIPPED_ING] = '配貨中'; // 已分?

$_LANG['ps'][PS_UNPAYED] = '未付款';
$_LANG['ps'][PS_PAYING] = '付款中';
$_LANG['ps'][PS_PAYED] = '已付款';

$_LANG['shipping_not_need'] = '無需使用配送方式';
$_LANG['current_os_not_unconfirmed'] = '目前訂單狀態不是「未確認」。';
$_LANG['current_os_already_confirmed'] = '目前訂單已經被確認，無法取消，請與店長聯絡。';
$_LANG['current_ss_not_cancel'] = '只有在未出貨狀態下才能取消，您可以與店長聯絡。';
$_LANG['current_ps_not_cancel'] = '只有未付款狀態才能取消，要取消請聯絡店長。';
$_LANG['confirm_received'] = '您確認已經收到貨物了嗎？';

/* 合併??及???情 */
$_LANG['merge_order_success'] = '合併的訂單的操作已成功！';
$_LANG['merge_order_failed']  = '合併的訂單的作業失敗！請返回重試！';
$_LANG['order_sn_not_null'] = '請填寫要合併的訂單號碼';
$_LANG['two_order_sn_same'] = '要合併的兩個訂單號碼不能相同';
$_LANG['order_not_exist'] = "訂單 %s 不存在";
$_LANG['os_not_unconfirmed_or_confirmed'] = " %s 的訂單狀態不是「未確認」或「已確認」";
$_LANG['ps_not_unpayed'] = "訂單 %s 的付款狀態不是「未付款」";
$_LANG['ss_not_unshipped'] = "訂單 %s 的出貨狀態不是「未出貨」";
$_LANG['order_user_not_same'] = '要合併的兩個訂單不是同一個會員的';
$_LANG['from_order_sn'] = '第一個訂單號碼：';
$_LANG['to_order_sn'] = '第二個訂單號碼：';
$_LANG['merge'] = '合併';
$_LANG['notice_order_sn'] = '當兩個訂單不一致時，合併後的訂單資訊（如：付款方式、配送方式、包裝、賀卡、紅利等）以第二個為準。';
$_LANG['subtotal'] = '小計';
$_LANG['goods_price'] = '商品價格';
$_LANG['goods_attr'] = '屬性';
$_LANG['use_balance'] = '使用餘額';
$_LANG['order_postscript'] = '訂單留言';
$_LANG['order_number'] = '訂單號碼';
$_LANG['consignment'] = '出貨單號';
$_LANG['shopping_money'] = '商品總價';
$_LANG['invalid_order_id'] = '訂單號碼錯誤';
$_LANG['shipping'] = '配送方式';
$_LANG['payment'] = '付款方式';
$_LANG['use_pack'] = '使用包裝';
$_LANG['use_card'] = '使用賀卡';
$_LANG['order_total_fee'] = '訂單總金額';
$_LANG['order_money_paid'] = '已付款金額';
$_LANG['order_amount'] = '應付款金額';
$_LANG['accessories'] = '配件';
$_LANG['largess'] = '贈品';
$_LANG['use_more_surplus'] = '追加使用餘額';
$_LANG['max_surplus'] = '（您的帳戶餘額：%s）';
$_LANG['button_submit'] = '確定';
$_LANG['order_detail'] = '訂單詳情';
$_LANG['error_surplus_invalid'] = '您輸入的數字不正確！';
$_LANG['error_order_is_paid'] = '該訂單不需要付款！';
$_LANG['error_surplus_not_enough'] = '您的帳戶餘額不足！';

/* ???? */
$_LANG['detail_order_sn'] = '訂單號碼';
$_LANG['detail_order_status'] = '訂單狀態';
$_LANG['detail_pay_status'] = '付款狀態';
$_LANG['detail_shipping_status'] = '配送狀態';
$_LANG['detail_order_sn'] = '訂單號碼';
$_LANG['detail_to_buyer'] = '賣家留言';

$_LANG['confirm_time'] = '確認於 %s';
$_LANG['pay_time'] = '付款於 %s';
$_LANG['shipping_time'] = '出貨於 %s';

$_LANG['select_payment'] = '所選付款方式';
$_LANG['order_amount'] = '應付款金額';
$_LANG['update_address'] = '更新收貨人資訊';
$_LANG['virtual_card_info'] = '虛擬卡資訊';

/* 取回密? */
$_LANG['back_home_lnk'] = '返回首頁';
$_LANG['get_password_lnk'] = '返回取得密碼頁面';
$_LANG['get_password_by_question'] = '密碼問題找回密碼';
$_LANG['get_password_by_mail'] = '註冊郵件取回密碼';
$_LANG['back_retry_answer'] = '返回重試';

/* 登? 注? */
$_LANG['label_username'] = '會員名稱';
$_LANG['label_email'] = 'Email';
$_LANG['label_password'] = '密碼';
$_LANG['label_confirm_password'] = '確認密碼';
$_LANG['label_password_intensity'] = '密碼強度';
$_LANG['want_login'] = '我已有帳號，我要登入';
$_LANG['other_msn'] = 'MSN';
$_LANG['other_qq'] = 'Skype';
$_LANG['other_office_phone'] = '公司電話';
$_LANG['other_home_phone'] = '家用電話';
$_LANG['other_mobile_phone'] = '手機';
$_LANG['remember'] = '請儲存我這次的登入資訊。';

$_LANG['msg_un_blank'] = '會員名稱不能留空';
$_LANG['msg_un_length'] = '會員名稱最長不得超過 7 個中文字';
$_LANG['msg_un_format'] = '會員名稱含有不合法字元';
$_LANG['msg_un_registered'] = '會員名稱已經存在,請重新輸入';
$_LANG['msg_can_rg'] = '可以註冊';
$_LANG['msg_email_blank'] = '郵件位址不能留空';
$_LANG['msg_email_registered'] = '信箱已存在,請重新輸入';
$_LANG['msg_email_format'] = '郵件位址不合法';

$_LANG['login_success'] = '登入成功';
$_LANG['confirm_login'] = '確認登入';
$_LANG['profile_lnk'] = '檢視我的個人資訊';
$_LANG['login_failure'] = '會員名稱或密碼錯誤';
$_LANG['relogin_lnk'] = '重新登入';

$_LANG['sex'] = '性　別';
$_LANG['male'] = '男';
$_LANG['female'] = '女';
$_LANG['secrecy'] = '保密';
$_LANG['birthday'] = '出生日期';

$_LANG['logout'] = '您已經成功的離開了。';
$_LANG['username_empty'] = '會員名稱為空';
$_LANG['username_invalid'] = '會員名稱 %s 含有敏感字元';
$_LANG['username_exist'] = '會員名稱 %s 已經存在';
$_LANG['username_not_allow'] = '會員名稱 %s 不允許註冊';
$_LANG['confirm_register'] = '確認註冊';

$_LANG['agreement'] = "我已看過並接受《<a href=\"article.php?cat_id=-1\" style=\"color:blue\" target=\"_blank\">會員協議</a>》";

$_LANG['email_empty'] = 'Email 為空';
$_LANG['email_invalid'] = '%s 不是合法的 Email 位址';
$_LANG['email_exist'] = '%s 已經存在';
$_LANG['email_not_allow'] = 'Email %s 不允許註冊';
$_LANG['register'] = '註冊新會員名稱';
$_LANG['register_success'] = '會員名稱 %s 註冊成功';

$_LANG['passwd_question'] = '密碼提示問題';
$_LANG['sel_question'] = '請選擇密碼提示問題';
$_LANG['passwd_answer'] = '密碼問題答案';
$_LANG['passwd_balnk'] = '密碼中不能包含空格';

/* 用?中心默??面 */
$_LANG['welcome_to'] = '歡迎您回到';
$_LANG['last_time'] = '您的上一次登入時間';
$_LANG['your_account'] = '您的帳戶';
$_LANG['your_notice'] = '會員提醒';
$_LANG['your_surplus'] = '餘額';
$_LANG['credit_line'] = '信用額度';
$_LANG['your_bonus'] = '紅利';
$_LANG['your_message'] = '留言';
$_LANG['your_order'] = '訂單';
$_LANG['your_integral'] = '積分';
$_LANG['your_level'] = '您的等級是 %s ';
$_LANG['next_level'] = ',您還差 %s 積分達到 %s ';
$_LANG['attention'] = '關注';
$_LANG['no_attention'] = '取消關注';
$_LANG['del_attention'] = '確認取消此商品的關注嗎？';
$_LANG['add_to_attention'] = '確定將此商品加入關注清單嗎？';
$_LANG['label_need_image'] = '是否顯示商品圖片：';
$_LANG['need'] = '顯示';
$_LANG['need_not'] = '不顯示';
$_LANG['horizontal'] = '橫排';
$_LANG['verticle'] = '直排';
$_LANG['generate'] = '產生代碼';
$_LANG['label_goods_num'] = '顯示商品數量：';
$_LANG['label_rows_num'] = '選擇商品排列方式：';
$_LANG['label_arrange'] = '排列顯示項目數：';
$_LANG['label_charset'] = '選擇編碼：';
$_LANG['charset']['utf8'] = '萬國碼（utf8）';
$_LANG['charset']['zh_cn'] = '簡體中文';
$_LANG['charset']['zh_tw'] = '正體中文';
$_LANG['goods_num_must_be_int'] = '商品數量應該是整數';
$_LANG['goods_num_must_over_0'] = '商品數量應該大於 0';
$_LANG['rows_num_must_be_int'] = '排列顯示項目數應該是整數';
$_LANG['rows_num_must_over_0'] = '排列顯示項目數應該大於 0';

$_LANG['last_month_order'] = '您最近 30 天內送出了';
$_LANG['order_unit'] = '個訂單';
$_LANG['please_received'] = '以下訂單已出貨，請注意查收';
$_LANG['your_auction'] = '您競拍到了<strong>%s</strong> ，現在前往 <a href="auction.php?act=view&amp;id=%s"><span style="color:#06c;text-decoration:underline;">購買</span></a>';
$_LANG['your_snatch'] = '您奪寶奇兵競拍到了<strong>%s</strong> ，現在前往 <a href="snatch.php?act=main&amp;id=%s"><span style="color:#06c;text-decoration:underline;">購買</span></a>';

/* 我的?? */
$_LANG['no_tag'] = '暫時沒有標籤';
$_LANG['confirm_drop_tag'] = '您確認要刪除此標籤嗎？';

/* user_passport.dwt js?言文件 */
$_LANG['passport_js']['username_empty'] = '- 會員名稱不能留空。';
$_LANG['passport_js']['username_shorter'] = '- 會員名稱長度不能少於 3 個字元。';
$_LANG['passport_js']['username_invalid'] = '- 會員名稱只能是由字母數字以及底線組成。';
$_LANG['passport_js']['password_empty'] = '- 登入密碼不能留空。';
$_LANG['passport_js']['password_shorter'] = '- 登入密碼不能少於 6 個字元。';
$_LANG['passport_js']['confirm_password_invalid'] = '- 兩次輸入密碼不一致';
$_LANG['passport_js']['email_empty'] = '- Email 為空';
$_LANG['passport_js']['email_invalid'] = '- Email 不是合法的位址';
$_LANG['passport_js']['agreement'] = '- 您沒有接受協定';
$_LANG['passport_js']['msn_invalid'] = '- msn 位址不是一個有效的郵件位址';
$_LANG['passport_js']['qq_invalid'] = '- Skype 帳號不是一個有效的帳號';
$_LANG['passport_js']['home_phone_invalid'] = '- 家用電話不是一個有效號碼';
$_LANG['passport_js']['office_phone_invalid'] = '- 公司電話不是一個有效號碼';
$_LANG['passport_js']['mobile_phone_invalid'] = '- 手機號碼不是一個有效號碼';
$_LANG['passport_js']['msg_un_blank'] = '* 會員名稱不能留空';
$_LANG['passport_js']['msg_un_length'] = '* 會員名稱最長不得超過 7 個中文字';
$_LANG['passport_js']['msg_un_format'] = '* 會員名稱含有不合法字元';
$_LANG['passport_js']['msg_un_registered'] = '* 會員名稱已經存在,請重新輸入';
$_LANG['passport_js']['msg_can_rg'] = '* 可以註冊';
$_LANG['passport_js']['msg_email_blank'] = '* 郵件位址不能留空';
$_LANG['passport_js']['msg_email_registered'] = '* 信箱已存在，請重新輸入';
$_LANG['passport_js']['msg_email_format'] = '* 郵件位址不合法';
$_LANG['passport_js']['msg_blank'] = '不能留空';
$_LANG['passport_js']['no_select_question'] = '- 您沒有完成密碼提示問題的作業';
$_LANG['passport_js']['passwd_balnk'] = '- 密碼中不能包含空格';


/* user_clips.dwt js ?言文件 */
$_LANG['clips_js']['msg_title_empty'] = '留言標題為空';
$_LANG['clips_js']['msg_content_empty'] = '留言內容為空';
$_LANG['clips_js']['msg_title_limit'] = '留言標題不能超過 200 個字';

/* 合併??js */
$_LANG['merge_order_js']['from_order_empty'] = '請選擇要合併的子訂單';
$_LANG['merge_order_js']['to_order_empty'] = '請選擇要合併的主訂單';
$_LANG['merge_order_js']['order_same'] = '主訂單和子訂單相同，請重新選擇';
$_LANG['merge_order_js']['confirm_merge'] = '您確定要合併這兩個訂單嗎？';

/* ?用???中商品加入?物? */
$_LANG['order_id_empty'] = '未指定訂單號碼';
$_LANG['return_to_cart_success'] = '訂單中商品已經成功加入購物車中';

/* 保存用???收?地址 */
$_LANG['consigness_empty'] = '收貨人姓名為空';
$_LANG['address_empty'] = '收貨地址詳情為空';
$_LANG['require_unconfirmed'] = '該訂單狀態下不能再修改地址';

/* ?包?情 */
$_LANG['bonus_sn'] = '紅利序號';
$_LANG['bonus_name'] = '紅利名稱';
$_LANG['bonus_amount'] = '紅利金額';
$_LANG['min_goods_amount'] = '最小訂單金額';
$_LANG['bonus_end_date'] = '截止使用日期';
$_LANG['bonus_status'] = '紅利狀態';

$_LANG['not_start'] = '未開始';
$_LANG['overdue'] = '已過期';
$_LANG['not_use'] = '未使用';
$_LANG['had_use'] = '已使用';

/* 用?推薦 */
$_LANG['affiliate_mode'] = '分紅模式';
$_LANG['affiliate_detail'] = '分紅細節';
$_LANG['affiliate_member'] = '我推薦的會員';
$_LANG['affiliate_code'] = '推薦代碼';
$_LANG['firefox_copy_alert'] = "您的 firefox 安全限制限制您進行剪貼簿操作，請開啟「about:config」將 signed.applets.codebase_principal_support 設定為 true 之後重試";
$_LANG['affiliate_type'][0] = '推薦註冊分紅';
$_LANG['affiliate_type'][1] = '推薦訂單分紅';
$_LANG['affiliate_type'][-1] = '推薦註冊分紅';
$_LANG['affiliate_type'][-2] = '推薦訂單分紅';

$_LANG['affiliate_codetype'] = '格式';

$_LANG['affiliate_introduction'] = '分紅模式介紹';
$_LANG['affiliate_intro'][0] = '　　本商店為鼓勵推薦新會員註冊，現開展<b>推薦註冊分紅</b>活動，活動流程如下：

　　１、將本站提供給您的推薦代碼，寄出到論壇、部落格上。
　　２、瀏覽者點選連結，瀏覽商店。
　　３、在瀏覽者點選連結的 <b>%d%s</b> 內，若該瀏覽者在本站註冊，即認定這個會員是您推薦的，您將獲得等級積分 <b>%d</b> 的獎勵 (當您的等級積分超過 <b>%d</b> 時，不再獲得獎勵)。
　　４、這個會員今後在本站的一切消費，您均能獲得一定比例的分紅。目前實施的分紅總額為訂單金額的 <b>%s</b> 、積分的 <b>%s</b> ，分配給您、推薦您的人等，具體分配規則請參閱 <b><a href="#myrecommend">我推薦的會員</a></b>。
　　５、分紅由管理員人工審核發放，請您耐心等待。
　　６、您可以透過分紅細節來檢視您的介紹、分紅情況。';
$_LANG['affiliate_intro'][1] = '　　本商店為鼓勵推薦新會員註冊，現開展<b>推薦訂單分紅</b>活動，活動流程如下：

　　１、在瀏覽商品時，點選推薦此商品，獲得推薦代碼，將其寄出到論壇、部落格上。
　　２、瀏覽者點選連結，瀏覽商店。
　　３、在瀏覽者點選連結的 <b>%d%s</b> 內，若該瀏覽者在本站有訂單，即認定該訂單是您推薦的。
　　４、您將獲得該訂單金額的 <b>%s</b> 、積分的 <b>%s</b>的獎勵。
　　５、分紅由管理員人工審核發放，請您耐心等待。
　　６、您可以透過分紅細節來檢視您的介紹、分紅情況。';

$_LANG['level_point_all'] = '積分分紅總額百分比';
$_LANG['level_money_all'] = '現金分紅總額百分比';
$_LANG['level_register_all'] = '註冊積分分紅數';
$_LANG['level_register_up'] = '等級積分分紅上限';

$_LANG['affiliate_stats'][0] = '等待處理';
$_LANG['affiliate_stats'][1] = '已分紅';
$_LANG['affiliate_stats'][2] = '取消分紅';
$_LANG['affiliate_stats'][3] = '已復原';
$_LANG['affiliate_stats'][4] = '等待買家付款';

$_LANG['level_point'] = '積分分紅百分比';
$_LANG['level_money'] = '現金分紅百分比';

$_LANG['affiliate_status'] = '分紅狀態';

$_LANG['affiliate_point'] = '積分分紅';
$_LANG['affiliate_money'] = '現金分紅';

$_LANG['affiliate_expire'] = '有效時間';

$_LANG['affiliate_lever'] = '等級';
$_LANG['affiliate_num'] = '人數';

$_LANG['affiliate_view'] = '效果';
$_LANG['affiliate_code'] = '代碼';

$_LANG['register_affiliate'] = '推薦會員 ID %s ( %s ) 註冊送積分';
$_LANG['register_points'] = '註冊送積分';

$_LANG['validate_ok'] = '%s 您好，您的 Email %s 已通過驗證';
$_LANG['validate_fail'] = '驗證失敗，請確認您的驗證連結是否正確';
$_LANG['validate_mail_ok'] = '驗證郵件寄出成功';

$_LANG['not_validated'] = '您還沒有通過郵件認證';
$_LANG['resend_hash_mail'] = '點此寄出認證郵件';

$_LANG['query_status'] = '查詢狀態';

$_LANG['change_payment'] = '改用其它線上付款方式';

$_LANG['copy_to_clipboard'] = '已複製至剪貼簿。';


$_LANG['expire_unit']['hour'] = '小時';
$_LANG['expire_unit']['day'] = '天';
$_LANG['expire_unit']['week'] = '週';

$_LANG['recommend_webcode'] = '網頁簽名代碼';
$_LANG['recommend_bbscode'] = '論壇簽名代碼';
$_LANG['im_code'] = '聊天分享';
$_LANG['code_copy'] = '複製代碼';
$_LANG['show_good_to_you'] = '推薦給你一個好東西';


/* ?分?? */
$_LANG['transform_points'] = '積分兌換';
$_LANG['invalid_points'] = '您輸入的積分是不一個合法值';
$_LANG['invalid_input'] = '無效';
$_LANG['overflow_points'] = '您輸入的兌換積分超過您的實際積分';
$_LANG['to_pay_points'] = '恭喜您，您的 %s 論壇 %s 兌換了 %s 的商場消費積分';
$_LANG['to_rank_points'] = '恭喜您，您的 %s 論壇 %s 兌換了 %s 的商場等級積分';
$_LANG['from_pay_points'] = '恭喜您，您的 %s 的商場消費積分兌換 %s 論壇 %s';
$_LANG['from_rank_points'] = '恭喜您，您的 %s 論壇 %s 兌換了 %s 的商場消費積分';
$_LANG['cur_points'] = '您的目前積分';
$_LANG['rule_list'] = '兌換規則';
$_LANG['transform'] = '兌換';
$_LANG['rate_is'] = '比例為';
$_LANG['rule'] = '兌換規則';
$_LANG['transform_num'] = '兌換數量';
$_LANG['transform_result'] = '兌換結果';
$_LANG['bbs'] = '論壇';
$_LANG['exchange_amount'] = '支出';
$_LANG['exchange_desamount'] = '收入';
$_LANG['exchange_ratio'] = '兌換比率';
$_LANG['exchange_points'][0] = '商場等級積分';
$_LANG['exchange_points'][1] = '商場消費積分';
$_LANG['exchange_action'] = '換';
$_LANG['exchange_js']['deny'] = '禁止兌換';
$_LANG['exchange_js']['balance'] = '您的{%s}餘額不足，請重新輸入';
$_LANG['exchange_deny'] = '該積分不允許兌換';
$_LANG['exchange_success'] = '恭喜您， 您用 %s 個 %s 兌換了 %s 個 %s';
$_LANG['exchange_error_1'] = 'UCenter 送出積分兌換時發生錯誤';
$_LANG['rank_points'] = '商場等級積分';
$_LANG['pay_points'] = '商場消費積分';

/* 密??度 */
$_LANG['pwd_lower'] = '弱';
$_LANG['pwd_middle'] = '中';
$_LANG['pwd_high'] = '強';
$_LANG['user_reg_info'][0] = '如果您不是會員，請註冊';
$_LANG['user_reg_info'][1] = '友情提示';
$_LANG['user_reg_info'][2] = '不註冊為會員也可在本店購買商品';
$_LANG['user_reg_info'][8] = '不註冊為會員無法在本店購買商品';
$_LANG['user_reg_info'][3] = '但註冊之後您可以';
$_LANG['user_reg_info'][4] = '儲存您的個人資料';
$_LANG['user_reg_info'][5] = '收藏您關注的商品';
$_LANG['user_reg_info'][6] = '享受會員積分制度';
$_LANG['user_reg_info'][7] = '訂閱本店商品資訊';
$_LANG['add_bonus'] = '加入紅利';

/* 密?找回?? */
$_LANG['passwd_questions']['friend_birthday'] = '我最好朋友的生日？';
$_LANG['passwd_questions']['old_address']     = '我兒時居住地的住址？';
$_LANG['passwd_questions']['motto']           = '我的座右銘？';
$_LANG['passwd_questions']['favorite_movie']  = '我最喜愛的電影？';
$_LANG['passwd_questions']['favorite_song']   = '我最喜愛的歌曲？';
$_LANG['passwd_questions']['favorite_food']   = '我最喜愛的食物？';
$_LANG['passwd_questions']['interest']        = '我最大的愛好？';
$_LANG['passwd_questions']['favorite_novel']  = '我最喜歡的小說？';
$_LANG['passwd_questions']['favorite_equipe'] = '我最喜歡的運動隊伍？';

?>