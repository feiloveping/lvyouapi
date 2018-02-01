$(window).load(function(){
    countheight();
    function countheight(){
        var height = document.documentElement.clientHeight || document.body.clientHeight
        if(height < 1080){
            $(".list").height(500)
        }else{
            $(".list").height(647)
        }
    }
    $(window).resize(function(){
        countheight();
    })
    if(!localStorage.getItem('versions')){
        localStorage.removeItem('all_num');
        localStorage.removeItem('state');
        localStorage.setItem('versions','1.1')
    }
    var all_num = localStorage.getItem('all_num')?JSON.parse(localStorage.getItem('all_num')):[
        {"id":1,"name":"廖凯"},{"id":2,"name":"马学姣"},{"id":3,"name":"闫自珍"},{"id":4,"name":"胡亚维"},
        {"id":5,"name":"刘淑峰"},{"id":6,"name":"陈以鑫"},{"id":7,"name":"殷茂蓉"},{"id":8,"name":"陈艳昌"},
        {"id":9,"name":"李本冬"},{"id":10,"name":"任纪文"},{"id":11,"name":"周文广"},{"id":12,"name":"朱素明"},
        {"id":13,"name":"宝煜"},{"id":14,"name":"陈斌忠"},{"id":15,"name":"李关伟"},{"id":16,"name":"刘旭"},
        {"id":17,"name":"郑兴民"},{"id":18,"name":"周东林"},{"id":19,"name":"何灿林"},{"id":20,"name":"普加双"},
        {"id":21,"name":"阮泽炎"},{"id":22,"name":"杨蓉蕊"},{"id":23,"name":"叶新柱"},{"id":24,"name":"段娜"},
        {"id":25,"name":"范先芹"},{"id":26,"name":"孔琳琳"},{"id":27,"name":"邵建柳"},{"id":28,"name":"张永丽"},
        {"id":29,"name":"徐根虎"},{"id":30,"name":"张智伟"},{"id":31,"name":"邓灵姣"},{"id":32,"name":"金啟菊"},
        {"id":33,"name":"孙红艳"},{"id":34,"name":"王庆招"},{"id":35,"name":"柴斌斌"},{"id":36,"name":"何庞斌"},
        {"id":37,"name":"马明"},{"id":38,"name":"谢蓉"},{"id":39,"name":"许婵"},{"id":40,"name":"尹以桢"},
        {"id":41,"name":"马小菁"},{"id":42,"name":"陈秋景"},{"id":43,"name":"段志敏"},{"id":44,"name":"李欢"},
        {"id":45,"name":"陆赛艳"},{"id":46,"name":"许银雪"},{"id":47,"name":"周荣雪"},{"id":48,"name":"孙鸣"},
        {"id":49,"name":"何建萍"},{"id":50,"name":"葛晓秋"},{"id":51,"name":"郭兴丽"},{"id":52,"name":"杨晓芳"},
        {"id":53,"name":"张丽菊"},{"id":54,"name":"徐福铭"},{"id":55,"name":"孟君"},{"id":56,"name":"邱卿来"},
        {"id":57,"name":"尹兴顺"},{"id":58,"name":"李雯雯"},{"id":59,"name":"吴梦萦"},{"id":60,"name":"张雪"},
        {"id":61,"name":"曾汉举"},{"id":62,"name":"李素敏"},{"id":63,"name":"孟继飞"},{"id":64,"name":"濮娅丽"},
        {"id":65,"name":"严俊文"},{"id":66,"name":"叶辉"},{"id":67,"name":"李其琪"},{"id":68,"name":"段维毓"},
        {"id":69,"name":"魏莹"},{"id":70,"name":"胡煦烨"},{"id":71,"name":"黄宝春"},{"id":72,"name":"李建"},
        {"id":73,"name":"李正彩"},{"id":74,"name":"屈森浩"},{"id":75,"name":"华思俊"},{"id":76,"name":"杨舒雅"},
        {"id":77,"name":"李敏"},{"id":78,"name":"王逍"},{"id":79,"name":"杨韬荣"},{"id":80,"name":"李枝钦"},
        {"id":81,"name":"王萍"},{"id":82,"name":"杨鼎"},{"id":83,"name":"杨玺"},{"id":85,"name":"韩丽"},
        {"id":86,"name":"李云"},{"id":88,"name":"倪知君"},{"id":89,"name":"吴会文"},{"id":90,"name":"朱亚平"},
        {"id":91,"name":"寸林正"},{"id":92,"name":"李欣"},{"id":93,"name":"杨帆"},{"id":94,"name":"余波"},
        {"id":95,"name":"曾庆岩"},{"id":96,"name":"刘伟"},{"id":97,"name":"吕朕超"},{"id":98,"name":"郭丽梅"}];

    //抽奖默认参数
    var state = localStorage.getItem('state')?JSON.parse(localStorage.getItem('state')):{
        default_state:3,
        3:{
            amount:3,//抽几次
            num:6,//每次抽取几人
            times:1,//抽取到第几次
            exhibitors:[]//中奖名单
        },
        2:{
            amount:2,//抽几次
            num:4,//每次抽取几人
            times:1,//抽取到第几次
            exhibitors:[]//中奖名单
        },
        1:{
            amount:3,//抽几次
            num:1,//每次抽取几人
            times:1,//抽取到第几次
            exhibitors:[]//中奖名单
        }
    };

    var candraw = true;//是否可抽奖
    var stateing = window.location.hash?window.location.hash.replace("#",''):state.default_state;//当前抽奖状态
//var all_num = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18];//总人数
    var average = {};//按人数均分为数组，防止重复
    count_average();
    function count_average(){
        average={};
        var numing = 0;
        for(var i = 0; i < all_num.length;i++){
            numing+=1;
            if(!average[numing]){
                average[numing] = [all_num[i]]
            }else {
                average[numing].push(all_num[i])
            }
            numing = numing>=state[stateing].num?0:numing;
        }
    }
//创建抽奖区
    create_draw();
    function create_draw(){
        $(".draw_list").removeClass('two three').empty();
        for(var i = 0; i < state[stateing].num;i++){
            var class_style = '';
            if(stateing == 2){
                class_style = 'two'
            }else if(stateing == 3){
                class_style = 'three'
            }
            $('.draw_list').addClass(class_style).append('<li class="draw_item">'
                +'<img class="defaultimg" src="./image/base/will.png" alt="">'
                +'</li>')
        }
    }
    $(".award").eq(stateing-1).addClass('active');

//  已抽取的中奖名单
    if(localStorage.getItem('state')){
        for(var i = 3;i>0;i--){
            if(state[i].exhibitors){
                for(var j = 0; j < state[i].exhibitors.length;j++){
                    var lottery = state[i].exhibitors[j]
                    $(".list").append('<li class="clear"><span>'+lottery.lottery+'</span><span>'+lottery.name+'</span></li>')
                }
            }
        }
        //取出中奖人的图片
        lottery_img();
    }
//取出中奖人的图片
    function lottery_img(){
        if(state[stateing].exhibitors.length!=0){
            var exhibitors_length = state[stateing].exhibitors.length;
            var this_num = state[stateing].num;
            var newExhibitorsArr = state[stateing].exhibitors.slice(exhibitors_length-this_num,exhibitors_length);
            $(".draw_item").each(function(){
                $(this).find('img').removeClass('defaultimg').addClass("img").attr('src','./image/'+newExhibitorsArr[$(this).index()].id+'.JPG')
            })
        }
    }


//切换抽奖
    $(".award").click(function(){
        $(".award").removeClass('active');
        $(this).addClass('active');
        stateing = $(this).attr('data-state')
        count_average();
        create_draw();
        lottery_img();
        window.location.hash=$(this).index()+1
    })

    var inter_arr = [];
    var music_interval = null;
    function startandstop(){
        if($(".start_btn").hasClass('staring')){//停止抽奖
            $(".draw_item").each(function(){
                var _this = $(this);
                clearInterval(inter_arr[$(this).index()])//先一次清除
                //缓慢停止
                var thisloop = average[_this.index()+1];
                slowFn(200,_this,thisloop,function(){
                    slowFn(300,_this,thisloop,function(){
                        slowFn(400,_this,thisloop,function(){
                            slowFn(500,_this,thisloop,function(){
                                slowFn(600,_this,thisloop,function(){
                                    slowFn(700,_this,thisloop)
                                })
                            })
                        })
                    })
                })
            });
            //缓慢停止
            $(".start_btn").removeClass('staring').find('img').attr('src','./image/base/startthedraw_01.png')
        }else{//开始抽奖
            if(state[stateing].times > state[stateing].amount){//抽奖次数已到达，请切换抽奖
                alert(stateing+'等奖已抽取完毕')
                return;
            }
            if(!candraw){//抽奖还未停止，不能继续抽奖
                return;
            }
            bg_music.pause();
            $(".draw_item").each(function(){
                var _this = $(this);
                var thisloop = average[_this.index()+1] || [];
                inter_arr[_this.index()] = setInterval(function(){
                    var random_num = Math.floor(Math.random() * thisloop.length);
                    //var img = '<img src="./image/'+thisloop[random_num].id+'.JPG" />';
                    //_this.html(img);
                    convertImgToBase64('./image/'+thisloop[random_num].id+'.JPG', function(base64Img){
                        _this.find('img').removeClass('defaultimg').addClass("img").attr('src',base64Img);
                    });
                    //_this.find('img').removeClass('defaultimg').addClass("img").attr('src','./image/'+thisloop[random_num].id+'.JPG');
                },220);
            });
            music_interval = setInterval(function(){
                draw_music.play();
            },100)
            $(".start_btn").addClass('staring').find('img').attr('src','./image/base/startthedraw_02.png')
            candraw = false;
            state[stateing].times += 1
        }

        var thisnum = 0;
        function slowFn(time,_this,thisloop,fn){
            clearInterval(inter_arr[_this.index()]);
            if(time>=700){//获取中奖名单
                $(".list").append('<li class="clear"><span>'+mateLottery(stateing)+'</span><span>'+_this.attr('data-name')+'</span></li>')
                state[stateing].exhibitors.push({lottery:mateLottery(stateing),name:_this.attr('data-name'),id:_this.attr('data-id')})
                //中奖人名的去除
                all_num = removeByValue(all_num,_this.attr('data-id'))
                thisnum++;
                if(thisnum == state[stateing].num){
                    thisnum = 0;
                    //重新生成抽奖数组
                    count_average();
                    localStorage.setItem('state',JSON.stringify(state))
                    localStorage.setItem('all_num',JSON.stringify(all_num))
                    setTimeout(function(){
                        $("#shader").show();
                        var image = new Image();
                        image.src = "./image/base/success.gif?"+Math.random();
                        clearInterval(music_interval);
                        bg_music.pause();
                        draw_music.pause();
                        success_music.currentTime = 0;
                        success_music.play();
                        $("#success").append(image).css('transform','scale(1.3)');
                        setTimeout(function(){
                            candraw = true;//解除禁止抽奖限制
                            $("#shader").hide();
                            $("#success").css({transform:'scale(0)'}).find("img").remove();
                            bg_music.play();
                            success_music.pause();
                            draw_music.pause();
                        },6500)
                    },1000)
                }
                return;
            }
            inter_arr[_this.index()] = setInterval(function(){
                var random_num = Math.floor(Math.random() * thisloop.length)
                _this.attr('data-id',thisloop[random_num].id).attr('data-name',thisloop[random_num].name)
                var img = '<img src="./image/'+thisloop[random_num].id+'.JPG" />';
                //_this.html(img);
                //_this.find('img').removeClass('defaultimg').addClass("img");
                _this.find('img').attr('src','./image/'+thisloop[random_num].id+'.JPG')
                clearInterval(music_interval);
                music_interval = setInterval(function(){
                    if(time<600){
                        draw_music.play();
                    }
                    clearInterval(music_interval);
                },time);
                if(fn)fn();
            },time);
        }

        //去除指定元素
        function removeByValue(arr, val) {
            for(var i=0; i<arr.length; i++) {
                if(arr[i].id == val) {
                    arr.splice(i, 1);
                    break;
                }
            }
            return arr;
        }
        //匹配奖项
        function mateLottery(stateing){
            var lottery = '';
            switch(Number(stateing)){
                case 3:lottery = '万利达多功能电热锅';break;
                case 2:lottery = '凤凰牌山地自行车';break;
                case 1:lottery = '奖品康佳冰箱';break;
            }
            return lottery;
        }
    }
    $(".start_btn").click(function(){
        startandstop();
    });

    $(document).keypress(function (e) {
        if (e.keyCode == 32 || e.keyCode == 13){
            startandstop();
        }
    });

    //查看详细中奖名单
    $("#check_detail").click(function(){
        location.href = './lotteryDetail.html';
    });

    //清除抽奖数据
    $("#clear").click(function(){
        var con=confirm("确定清除抽奖数据?"); //在页面上弹出对话框
        if(con==true){
            localStorage.clear();
            location.reload(true);
            alert('清除成功！')
        }
    });



    function convertImgToBase64(url, callback, outputFormat){
        var canvas = document.createElement('CANVAS'),
            ctx = canvas.getContext('2d'),
            img = new Image;
        img.crossOrigin = 'Anonymous';
        img.onload = function(){
            canvas.height = img.height;
            canvas.width = img.width;
            ctx.drawImage(img,0,0);
            var dataURL = canvas.toDataURL(outputFormat || 'image/jpeg',0.4);
            callback.call(this, dataURL);
            canvas = null;
        };
        img.src = url;
    }

    //$("#success,#shader").click(function(){
    //    candraw = true;//解除禁止抽奖限制
    //    $("#shader").hide();
    //    $("#success").css({transform:'scale(0)'}).find("img").remove();
    //})
});
