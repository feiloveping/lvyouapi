
window.alert = function(name){
	 var iframe = document.createElement("IFRAME");
	iframe.style.display="none";
	iframe.setAttribute("src", 'data:text/plain,');
	document.documentElement.appendChild(iframe);
	window.frames[0].window.alert(name);
	iframe.parentNode.removeChild(iframe);
}

function GetQueryString(name){
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r!=null) return unescape(r[2]); return null;
}
/*获取url后中文参数*/
function getRequest() {   
   var url = window.location.search; //获取url中"?"符后的字串   
   var theRequest = new Object();   
   if (url.indexOf("?") != -1) {   
      var str = url.substr(1);   
      strs = str.split("&");   
      for(var i = 0; i < strs.length; i ++) { 
         theRequest[strs[i].split("=")[0]]=decodeURI(strs[i].split("=")[1]); 
      }   
   }   
   return theRequest;   
}
function addcookie(name,value,expireHours){
	var cookieString=name+"="+escape(value)+"; path=/";
	//判断是否设置过期时间
	if(expireHours>0){
		var date=new Date();
		date.setTime(date.getTime+expireHours*3600*1000);
		cookieString=cookieString+"; expire="+date.toGMTString();
	}
	document.cookie=cookieString;
}

function getcookie(name){
	var strcookie=document.cookie;
	var arrcookie=strcookie.split("; ");
	for(var i=0;i<arrcookie.length;i++){
	var arr=arrcookie[i].split("=");
	if(arr[0]==name)return arr[1];
	}
	return "";
}
/*获取图片尾椎*/
function suffix(str){
	var index1=str.lastIndexOf(".");
	var index2=str.length;
	var suffix=str.substring(index1+1,index2);//后缀名
	return suffix;
};

function delCookie(name){//删除cookie
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval=getcookie(name);
	if(cval!=null) document.cookie= name + "="+cval+"; path=/;expires="+exp.toGMTString();
}

function checklogin(state){
	if(state == 0){
		location.href = WapSiteUrl+'/tmpl/member/login.html';
		return false;
	}else {
		return true;
	}
}
/*获取数字的差*/
function Subtr(arg1,arg2){  
     var r1,r2,m,n;  
     try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}  
     try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}  
     m=Math.pow(10,Math.max(r1,r2));  
     //last modify by deeka  
     //动态控制精度长度  
     n=(r1>=r2)?r1:r2;  
     return ((arg1*m-arg2*m)/m).toFixed(2);  
} 
// 根据时间戳格式化日期
function add0(m){return m<10?'0'+m:m }
function formatDate(stortime,type){
    if(stortime==undefined){
        return "0000-00-00";
    }else{
	    var time = new Date(parseFloat(stortime));
	    var y = time.getFullYear();
	    var m = time.getMonth()+1;
	    var d = time.getDate();
	    var s = time.getHours();
	    var mm= time.getMinutes();
	    if(type){
    		return y+'-'+add0(m)+'-'+add0(d)+' '+add0(s)+':'+add0(mm);
    	}else{
    		return y+'-'+add0(m)+'-'+add0(d);
    	};
    };
}
function getLocalTime(nS) {     
   return new Date(parseInt(nS) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");      
}    
function contains(arr, str) {
    var i = arr.length;
    while (i--) {
           if (arr[i] === str) {
           return true;
           }
    }
    return false;
}

/*验证手机号*/
function checkMobile(num){ 
    var sMobile = document.getElementById(num).value; 
    if(!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(sMobile))){ 
        return false; 
    } 
    return true
} 

/*获取base64*/
//function getBase64Image(img) {  
//var canvas = document.createElement("canvas");  
//canvas.width = img.width;  
//canvas.height = img.height;  
//var ctx = canvas.getContext("2d");  
//ctx.drawImage(img, 0, 0, img.width, img.height);  
//var ext = img.src.substring(img.src.lastIndexOf(".")+1).toLowerCase();  
//var dataURL = canvas.toDataURL("image/"+ext); 
//return dataURL;  
//}  
function getBase64(img){//传入图片路径，返回base64
    function getBase64Image(img,width,height) {//width、height调用时传入具体像素值，控制大小 ,不传则默认图像大小
      var canvas = document.createElement("canvas");
          canvas.width = width ? width : img.width;
          canvas.height = height ? height : img.height;
 
          var ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      var dataURL = canvas.toDataURL();
//    $('.loadinger').hide();
      return dataURL;
    }
    var image = new Image();
//  image.crossOrigin = '';
    image.src = img;
    var deferred=$.Deferred();
    if(img){
//    $('.loadinger').show();
      image.onload =function (){
        deferred.resolve(getBase64Image(image));//将base64传给done上传处理
      }
      return deferred.promise();//问题要让onload完成后再return sessionStorage['imgTest']
    }
}
// 压缩图片的方法
function cutDowmImg(img, width){
    var canvas = document.createElement("canvas");
    canvas.width = Math.min(img.width, width);
    canvas.height = img.height*width/img.width;
    var cxt = canvas.getContext("2d");
    cxt.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
    return canvas.toDataURL('image/jpeg',0.1);
}
/*提示信息*/
function textTip(text){
    $(".textTips span").text(text);
    $(".textTips").fadeIn(150);
    setTimeout(function(){
        $(".textTips").fadeOut(500);
    },1500);
}
//判断是否微信浏览器
function is_weixn(){  
    var ua = navigator.userAgent.toLowerCase();  
    if(ua.match(/MicroMessenger/i)=="micromessenger") {  
        return true;  
    } else {  
        return false;  
    }  
}  
function buildUrl(type, data) {
	console.log(type+'---'+data);
    switch (type) {
        case 'keyword':
            return WapSiteUrl + '/tmpl/product_listnew.html?keyword=' + encodeURIComponent(data);
        case 'special':
            // return WapSiteUrl + '/special.html?special_id=' + data;
            return WapSiteUrl + '/tmpl/product_listnew.html?gc_id=' + data;
        case 'goods':
            return WapSiteUrl + '/tmpl/product_detail.html?goods_id=' + data;
        //case 'url':
            //return data;
		case 'zhuanti':
			return SiteUrl + '/index.php?act=mspecial&op=index&id='+data;
		break;
    }
    return WapSiteUrl;
}
/*关闭loading*/
function closeLoading(){
    $('.loadinger').hide();
}

$.fn.extend({
	show2:function(){
		$(this).css('display','-webkit-box');
	}
})