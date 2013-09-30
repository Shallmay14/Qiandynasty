/* $Id: showdiv.js 15469 2008-12-19 06:34:44Z testyang $ */
//創建顯示層
var swtemp  = 0;
var timer   = null;

//顯示層
function showdiv(obj)
{
    var inputid = obj.id;

    if (swtemp == 1)
    {
        hidediv("messagediv");
    }
    if (!getobj("messagediv"))
    {
        //若尚未創建就創建層
        crertdiv("" , "div" , "messagediv" , "messagediv");//創建層"messagediv"

        crertdiv("messagediv" , "li" , "messageli" , "messageli");//創建"請刷新"li
        getobj("messageli").innerHTML = show_div_text;

        crertdiv("messagediv" , "a" , "messagea" , "");//創建"關閉"a
        getobj("messagea").innerHTML = show_div_exit;
        getobj("messagea").onclick = function(){hidediv("messagediv");};
    }

    var newdiv = getobj("messagediv");

    newdiv.style.left    = (getAbsoluteLeft(obj) - 50) + "px";
    newdiv.style.top     = (getAbsoluteTop(obj) - 65) + "px";
    newdiv.style.display = "block";

    timer = setTimeout(function(){hidediv("messagediv");} , 3000);

    swtemp  = 1;
}

//創建層
function crertdiv(parent , element , id , css)
{
    var newObj = document.createElement(element);

    if(id && id != "")
    {
        newObj.id = id;
    }
    if(css && css != "")
    {
        newObj.className = css;
    }
    if(parent && parent!="")
    {
        var theObj = getobj(parent);
        var parent = theObj.parentNode;
        if(parent.lastChild == theObj)
        {
            theObj.appendChild(newObj);
        }
        else
        {
            theObj.insertBefore(newObj, theObj.nextSibling);
        }
    }
    else
    {
        document.body.appendChild(newObj);
    }
}

//隱藏層
function hidediv(objid)
{
    getobj(objid).style.display = "none";
    swtemp = 0;
    clearTimeout(timer);
}

//獲取對象
function getobj(obj)
{
    return document.getElementById(obj);
}

function getAbsoluteHeight(obj)
{
    return obj.offsetHeight;
}

function getAbsoluteWidth(obj)
{
    return obj.offsetWidth;
}

function getAbsoluteLeft(obj)
{
    var s_el = 0;
    var el   = obj;
    while(el)
    {
        s_el = s_el + el.offsetLeft;
        el   = el.offsetParent;
    }
    return s_el;
}

function getAbsoluteTop(obj)
{
    var s_el = 0;
    var el   = obj;
    while(el)
    {
        s_el = s_el + el.offsetTop;
        el   = el.offsetParent;
    }
    return s_el;
}