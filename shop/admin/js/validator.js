/* *
 * ECSHOP 表單驗證類
 * ============================================================================
 * 版權所有 (C) 2005-2007 康盛創想（北京）科技有限公司，並保留所有權利。
 * 網站地址 : http : // www.ecshop.com
 * ----------------------------------------------------------------------------
 * 這是一個免費開源的軟件；這意味著您可以在不用於商業目的的前提下對程序代碼
 * 進行修改和再發布。
 * ============================================================================
 * $Author : paulgao $
 * $Date : 2007-01-31 16:23:56 +0800 (星期三, 31 一月 2007) $
 * $Id : validator.js 4824 2007-01-31 08:23:56Z paulgao $

 *//* *
 * 表單驗證類
 *
 * @author : weber liu
 * @version : v1.1
 */

var Validator = function(name)
{
  this.formName = name;
  this.errMsg = new Array();

  /* *
  * 檢查用戶是否輸入了內容
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  */
  this.required = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    if (typeof(obj) == "undefined" || Utils.trim(obj.value) == "")
    {
      this.addErrorMsg(msg);
    }
  }
  ;

  /* *
  * 檢查用戶輸入的是否為合法的郵件地址
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  * @param :  required    是否必須
  */
  this.isEmail = function(controlId, msg, required)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = Utils.trim(obj.value);

    if ( ! required && obj.value == '')
    {
      return;
    }

    if ( ! Utils.isEmail(obj.value))
    {
      this.addErrorMsg(msg);
    }
  }

  /* *
  * 檢查兩個表單元素的值是否相等
  *
  * @param : fstControl   表單元素的ID
  * @param : sndControl   表單元素的ID
  * @param : msg         錯誤提示信息
  */
  this.eqaul = function(fstControl, sndControl, msg)
  {
    var fstObj = document.forms[this.formName].elements[fstControl];
    var sndObj = document.forms[this.formName].elements[sndControl];

    if (fstObj != null && sndObj != null)
    {
      if (fstObj.value == '' || fstObj.value != sndObj.value)
      {
        this.addErrorMsg(msg);
      }
    }
  }

  /* *
  * 檢查前一個表單元素是否大於後一個表單元素
  *
  * @param : fstControl   表單元素的ID
  * @param : sndControl	  表單元素的ID
  * @param : msg			    錯誤提示信息
  */
  this.gt = function(fstControl, sndControl, msg)
  {
    var fstObj = document.forms[this.formName].elements[fstControl];
    var sndObj = document.forms[this.formName].elements[sndControl];

    if (fstObj != null && sndObj != null) {
      if (Utils.isNumber(fstObj.value) && Utils.isNumber(sndObj.value)) {
        var v1 = parseFloat(fstObj.value) + 0;
        var v2 = parseFloat(sndObj.value) + 0;
      } else {
        var v1 = fstObj.value;
        var v2 = sndObj.value;
      }

      if (v1 <= v2) this.addErrorMsg(msg);
    }
  }

  /* *
  * 檢查輸入的內容是否是一個數字
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  * @param :  required    是否必須
  */
  this.isNumber = function(controlId, msg, required)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = Utils.trim(obj.value);

    if (obj.value == '' && ! required)
    {
      return;
    }
    else
    {
      if ( ! Utils.isNumber(obj.value))
      {
        this.addErrorMsg(msg);
      }
    }
  }

  /* *
  * 檢查輸入的內容是否是一個整數
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  * @param :  required    是否必須
  */
  this.isInt = function(controlId, msg, required)
  {

    if (document.forms[this.formName].elements[controlId])
    {
      var obj = document.forms[this.formName].elements[controlId];
    }
    else
    {
      return;    
    }

    obj.value = Utils.trim(obj.value);

    if (obj.value == '' && ! required)
    {
      return;
    }
    else
    {
      if ( ! Utils.isInt(obj.value)) this.addErrorMsg(msg);
    }
  }

  /* *
  * 檢查輸入的內容是否是為空
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  * @param :  required    是否必須
  */
  this.isNullOption = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];

    obj.value = Utils.trim(obj.value);

    if (obj.value > '0' )
    {
      return;
    }
    else
    {
      this.addErrorMsg(msg);
    }
  }

  /* *
  * 檢查輸入的內容是否是"2006-11-12 12:00:00"格式
  *
  * @param :  controlId   表單元素的ID
  * @param :  msg         錯誤提示信息
  * @param :  required    是否必須
  */
  this.isTime = function(controlId, msg, required)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = Utils.trim(obj.value);

    if (obj.value == '' && ! required)
    {
      return;
    }
    else
    {
      if ( ! Utils.isTime(obj.value)) this.addErrorMsg(msg);
    }
  }
  
  /* *
  * 檢查前一個表單元素是否小於後一個表單元素(日期判斷)
  *
  * @param : controlIdStart   表單元素的ID
  * @param : controlIdEnd	  表單元素的ID
  * @param : msg              錯誤提示信息
  */
  this.islt = function(controlIdStart, controlIdEnd, msg)
  {
    var start = document.forms[this.formName].elements[controlIdStart];
    var end = document.forms[this.formName].elements[controlIdEnd];
    start.value = Utils.trim(start.value);
    end.value = Utils.trim(end.value);

    if(start.value <= end.value)
    {
      return;
    }
    else
    {
      this.addErrorMsg(msg);
    }
  }

  /* *
  * 檢查指定的checkbox是否選定
  *
  * @param :  controlId   表單元素的name
  * @param :  msg         錯誤提示信息
  */
  this.requiredCheckbox = function(chk, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    var checked = false;

    for (var i = 0; i < objects.length; i ++ )
    {
      if (objects[i].type.toLowerCase() != "checkbox") continue;
      if (objects[i].checked)
      {
        checked = true;
        break;
      }
    }

    if ( ! checked) this.addErrorMsg(msg);
  }

  this.passed = function()
  {
    if (this.errMsg.length > 0)
    {
      var msg = "";
      for (i = 0; i < this.errMsg.length; i ++ )
      {
        msg += "- " + this.errMsg[i] + "\n";
      }

      alert(msg);
      return false;
    }
    else
    {
      return true;
    }
  }

  /* *
  * 增加一個錯誤信息
  *
  * @param :  str
  */
  this.addErrorMsg = function(str)
  {
    this.errMsg.push(str);
  }
}

/* *
 * 幫助信息的顯隱函數
 */
function showNotice(objId)
{
  var obj = document.getElementById(objId);

  if (obj)
  {
    if (obj.style.display != "block")
    {
      obj.style.display = "block";
    }
    else
    {
      obj.style.display = "none";
    }
  }
}

/* *
 * add one option of a select to another select.
 *
 * @author  Chunsheng Wang < wwccss@263.net >
 */
function addItem(src, dst)
{
  for (var x = 0; x < src.length; x ++ )
  {
    var opt = src.options[x];
    if (opt.selected && opt.value != '')
    {
      var newOpt = opt.cloneNode(true);
      newOpt.className = '';
      newOpt.text = newOpt.innerHTML.replace(/^\s+|\s+$|&nbsp;/g, '');
      dst.appendChild(newOpt);
    }
  }

  src.selectedIndex = -1;
}

/* *
 * move one selected option from a select.
 *
 * @author  Chunsheng Wang < wwccss@263.net >
 */
function delItem(ItemList)
{
  for (var x = ItemList.length - 1; x >= 0; x -- )
  {
    var opt = ItemList.options[x];
    if (opt.selected)
    {
      ItemList.options[x] = null;
    }
  }
}

/* *
 * join items of an select with ",".
 *
 * @author  Chunsheng Wang < wwccss@263.net >
 */
function joinItem(ItemList)
{
  var OptionList = new Array();
  for (var i = 0; i < ItemList.length; i ++ )
  {
    OptionList[OptionList.length] = ItemList.options[i].text + "|" + ItemList.options[i].value;
  }
  return OptionList.join(",");
}
