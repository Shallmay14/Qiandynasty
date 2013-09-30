<?php
/**
 * ECSHOP 快錢聯合註冊接口
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: send.php 15013 2008-10-23 09:31:42Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

//商戶密鑰
$key='LHLEF8EA4ZY853NF';

//接口版本，不可空
//固定值：150120
$version='150120';

//編碼字符串格式
//固定值：1、2、3
//1代表UTF-8;2代表GBK；3代表GB2312
$inputCharset='3';

//簽名類型，不可空
//固定值：1，1代表MD5加密
$signType='1';

//商戶在快錢的會員編號，不可空
$merchantMbrCode='10017518267';

//申請編號，不可空
//只允許是字母、數字、「_」等，以字母或數字開頭
$requestId=date('YmdHis');


//註冊類型，不可空
//固定值：1、2
//1代表新註冊用戶；2代表重複註冊用戶
$registerType='1';

//用戶在商戶系統的ID，不可空
//只允許是字母、數字、「_」等，以字母或數字開頭
$userId=date('YmdHis');

//用戶類型，不可空
//固定值：1、2
//1代表個人；2代表企業
$userType='1';

//用戶的EMAIL
$userEmail='payment@shopex.cn';

//用戶的手機
$userMobile='';

//用戶的姓名
//中文或英文
$userName='';

//聯繫人
//中文或英文
$linkMan='';

//聯繫電話
//手機或固定電話
$linkTel='';

//單位名稱
//中文或英文
$orgName='';

//網站地址
$websiteAddr='';

//商戶接收返回頁面的地址，不可空
//商戶服務器接收快錢返回結果的後台地址
//快錢通過服務器連接的方式將交易結果參數傳遞給商戶提供的這個url，商戶處理後輸出接收結果和返回頁面地址
$backUrl=$ecs->url() . ADMIN_PATH . '/receive.php';
//擴展參數一
//中文或英文
$ext1='';

//擴展參數二
//中文或英文
$ext2='';

//功能函數。將變量值不為空的參數組成字符串
Function appendParam($returnStr,$paramId,$paramValue){
    if($returnStr!=""){
        if($paramValue!=""){
            $returnStr.="&".$paramId."=".$paramValue;
        }
    }else{
        If($paramValue!=""){
            $returnStr=$paramId."=".$paramValue;
        }
    }
    return $returnStr;
}
//功能函數。將變量值不為空的參數組成字符串。結束

//生成加密簽名串
///請務必按照如下順序和規則組成加密串！
$signMsgVal="";
$signMsgVal=appendParam($signMsgVal,"version",$version);
$signMsgVal=appendParam($signMsgVal,"inputCharset",$inputCharset);
$signMsgVal=appendParam($signMsgVal,"signType",$signType);
$signMsgVal=appendParam($signMsgVal,"merchantMbrCode",$merchantMbrCode);
$signMsgVal=appendParam($signMsgVal,"requestId",$requestId);
$signMsgVal=appendParam($signMsgVal,"registerType",$registerType);
$signMsgVal=appendParam($signMsgVal,"userId",$userId);
$signMsgVal=appendParam($signMsgVal,"userType",$userType);
$signMsgVal=appendParam($signMsgVal,"userEmail",$userEmail);
$signMsgVal=appendParam($signMsgVal,"userMobile",$userMobile);
$signMsgVal=appendParam($signMsgVal,"userName",$userName);
$signMsgVal=appendParam($signMsgVal,"linkMan",$linkMan);
$signMsgVal=appendParam($signMsgVal,"linkTel",$linkTel);
$signMsgVal=appendParam($signMsgVal,"orgName",$orgName);
$signMsgVal=appendParam($signMsgVal,"websiteAddr",$websiteAddr);
$signMsgVal=appendParam($signMsgVal,"backUrl",$backUrl);
$signMsgVal=appendParam($signMsgVal,"ext1",$ext1);
$signMsgVal=appendParam($signMsgVal,"ext2",$ext2);
$signMsgVal=appendParam($signMsgVal,"key",$key);
//echo $signMsgVal;exit;
$signMsg=strtoupper(md5($signMsgVal));

header("location:https://www.99bill.com/website/signup/memberunitedsignup.htm?version=".$version."&inputCharset=".$inputCharset."&signType=".$signType."&merchantMbrCode=".$merchantMbrCode."&requestId=".$requestId."&registerType=".$registerType."&userId=".$userId."&userType=".$userType."&userEmail=".$userEmail."&userMobile=".$userMobile."&userName=".$userName."&linkMan=".$linkMan."&linkTel=".$linkTel."&orgName=".$orgName."&websiteAddr=".$websiteAddr."&backUrl=".$backUrl."&ext1=".$ext1."&ext2=".$ext2."&signMsg=".$signMsg);

?>
