var _https = 'https://youapi.wmqt.net/'
//截取url地址然后转化为json格式；
function toJson(){
    var urlArr = location.href.split("?")[1].split("&");
    var objStr = "";
    for(var i = 0; i < urlArr.length;i++){
        if(i==0){
            objStr += ''+urlArr[i].split("=")[0]+':"'+decodeURI(urlArr[i].split("=")[1])+'"'
        }else{
            objStr += ','+urlArr[i].split("=")[0]+':"'+decodeURI(urlArr[i].split("=")[1])+'"'
        }
    }
    var urlObj = "{"+objStr+"}";
    console.log(eval('(' + urlObj + ')'));
    return eval('(' + urlObj + ')');
}

//重新拼接url地址用于返回上一页携带
function createStr(numb){//numb代表上一页url参数与本页参数相差个数，应减去；
    var num = 0;
    if(numb){
        num = numb;
    }
    var localurl = location.href.split("?")[1].split("&");
    var newCreateStr = "";
    for(var i = 0; i < localurl.length-num;i++){
        if(i == 0){
            newCreateStr = localurl[i].split("=")[0]+"="+decodeURI(localurl[i].split("=")[1]);
        }else{
            newCreateStr += "&"+localurl[i].split("=")[0]+"="+decodeURI(localurl[i].split("=")[1])+"";
        }
    }
    return newCreateStr;
}