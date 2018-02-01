countheight();
function countheight(){
    var height = document.documentElement.clientHeight || document.body.clientHeight
    if(height < 1080){
        $(".center_bg").css({'marginTop':30})
    }else{
        $(".center_bg").css({'marginTop':130})
    }
}
$(window).resize(function(){
    countheight();
})
if(!localStorage.getItem('versions')){
    localStorage.removeItem('all_num');
    localStorage.removeItem('state');
}
var state_num = [3,8,18];
for(var i = 0; i < state_num.length;i++){
    for(var j = 0;j < state_num[i];j++){
        var state = localStorage.getItem('state');
        var headimg = '';
        var name = '';
        if(state && JSON.parse(state)[i+1] && JSON.parse(state)[i+1].exhibitors[j]){
            var id = JSON.parse(state)[i+1].exhibitors[j].id;
            name = JSON.parse(state)[i+1].exhibitors[j].name;
            headimg = './image/'+id+'.JPG';
        }else{
            headimg = './image/base/questionmark.png';
            name = '***';
        }
        $(".lettery_item").eq(i).find('.names').append('<div class="name_item">'
            +'<p class="head"><img src="'+headimg+'" alt=""></p>'
            +'<p>姓名：'+name+'</p>'
            +'</div>')
    }
}
