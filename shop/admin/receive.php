<?php
/**
 * 聯合註冊返回驗證
 * ============================================================================
 * 版權所有 2005-2010 上海商派網絡科技有限公司，並保留所有權利。
 * 網站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 這不是一個自由軟件！您只能在不用於商業目的的前提下對程序代碼進行修改和
 * 使用；不允許對程序代碼以任何形式任何目的的再發佈。
 * ============================================================================
 * $Author: liuhui $
 * $Id: receive.php 16492 2009-07-27 10:16:09Z liuhui $
*/

//商戶密鑰
$key="65ZS4C5WYKKLLGJN";

//接口版本，不可空
//固定值：150120
$version=$_GET['version'];

//簽名類型，不可空
//固定值：1，1代表MD5加密
$signType=$_GET['signType'];

//商戶在快錢的會員編號，不可空
$merchantMbrCode=$_GET['merchantMbrCode'];

//申請編號，不可空
//只允許是字母、數字、「_」等，以字母或數字開頭
$requestId=$_GET['requestId'];

//用戶在商戶系統的ID，不可空
//只允許是字母、數字、「_」等，以字母或數字開頭
$userId=$_GET['userId'];

//用戶的EMAIL
$userEmail=$_GET['userEmail'];

//用戶的姓名
//中文或英文
$userName=$_GET['userName'];

//單位名稱
//中文或英文
$orgName=$_GET['orgName'];

//擴展參數一
//中文或英文
$ext1=$_GET['ext1'];

//擴展參數二
//中文或英文
$ext2=$_GET['ext2'];

//註冊驗證結果
//固定值：0、1、2
//0：註冊申請成功；1：審核通過；2：激活成功
$applyResult=$_GET['applyResult'];

//錯誤代碼
//失敗時返回的錯誤代碼，可以為空
$errorCode=$_GET['errorCode'];

//快錢返回的簽名字符串
//以上關鍵字的值與密鑰組合，經MD5加密生成的32位字符串
$signMsg=$_GET['signMsg'];

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

//自己生成加密簽名串
///請務必按照如下順序和規則組成加密串！
$$signMsgVal="";
$signMsgVal=appendParam($signMsgVal,"version",$version);
$signMsgVal=appendParam($signMsgVal,"signType",$signType);
$signMsgVal=appendParam($signMsgVal,"merchantMbrCode",$merchantMbrCode);
$signMsgVal=appendParam($signMsgVal,"requestId",$requestId);
$signMsgVal=appendParam($signMsgVal,"userId",$userId);
$signMsgVal=appendParam($signMsgVal,"userEmail",$userEmail);
$signMsgVal=appendParam($signMsgVal,"userName",urlencode($userName));
$signMsgVal=appendParam($signMsgVal,"orgName",urlencode($orgName));
$signMsgVal=appendParam($signMsgVal,"ext1",urlencode($ext1));
$signMsgVal=appendParam($signMsgVal,"ext2",urlencode($ext2));
$signMsgVal=appendParam($signMsgVal,"applyResult",$applyResult);
$signMsgVal=appendParam($signMsgVal,"errorCode",$errorCode);
$signMsgVal=appendParam($signMsgVal,"key",$key);

$mysignMsg=strtoupper(md5($signMsgVal));



if($mysignMsg==$signMsg){

            /**
             *  商戶進行自己的數據庫邏輯處理，比如把接收的信息保存到自己的數據庫中
             *  或者是更新自己數據庫中用戶表的狀態
             */

    $status="1";

    $signMsgVal="";
    $signMsgVal=appendParam($signMsgVal,"version",$version);
    $signMsgVal=appendParam($signMsgVal,"signType",$signType);
    $signMsgVal=appendParam($signMsgVal,"merchantMbrCode",$merchantMbrCode);
    $signMsgVal=appendParam($signMsgVal,"requestId",$requestId);
    $signMsgVal=appendParam($signMsgVal,"userId",$userId);
    $signMsgVal=appendParam($signMsgVal,"status",$status);
    $reParam=$signMsgVal;
    $signMsgVal=appendParam($signMsgVal,"key",key);

    $signMsg=strtoupper(md5($signMsgVal));
    $reParam .="&signMsg=".$signMsg;
    echo $reParam; 
}else{
    echo "驗證錯誤";
}
?>