<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/5
 * Time: 11:18
 */

return [
    /*  -------------------------------------------首頁接口--------------------------------------------     */
    'GET v1/home'                               => 'v1/home/home',                    //首頁home
    'GET v1/hotkeyword'                         =>'v1/search-keyword/hot-keyword',    //主頁 - 熱門搜索
    'GET v1/acity'                              =>'v1/article/city',
    'GET v1/i'                                  =>'v1/default/insert',
    /*      ---------------------------------------景點列表頁 -----------------------------------------     */
    'GET v1/spotindex'                          =>      'v1/spot/spot-index',         // 景点首页
    'GET v1/spotlist/<param>/<page:\d+>'      =>'v1/spot/spot-list',                // 景點列表頁
    'GET v1/spotcondition'                      =>      'v1/spot/condition'   ,       // 景点列表页搜索条件综合
    'GET v1/spotnear/<lng>/<lat>/<page:\d+>'  =>'v1/spot/spot-near',                // 景點列表頁
    /*      ----------------------------------------景点详情页---------------------------------------------*/
    'GET v1/spotdetail/<id:\d+>'            =>'v1/spot/spot-detail',                  // 景點详情页
    'GET v1/spotaddcollection/<id:\d+>'     =>'v1/spot/spot-add-collection',          // 景點收藏
    'GET v1/spotdelcollection/<id:\d+>'     =>'v1/spot/spot-del-collection',          // 景點取消
    'GET v1/spotticketnote/<id:\d+>'        =>'v1/spot/ticket-notes',                 // 景點门票描述
    'GET v1/spotpiclist/<id:\d+>'           => 'v1/spot/spot-pic-list',               // 景区图集列表
    'GET v1/spotquestionlist/<id:\d+>'      => 'v1/spot/spot-question',               //景区问答列表
    'POST v1/addspotquestion'               => 'v1/spot/question-add',                // 景区提交问答
    /*      ------------------------------------------景点订单页面-----------------------------------------*/
    'GET v1/spotorderindex/<id:\d+>'        =>'v1/spot-order/get-spot-order-message', // 景點订单 - 门票类型和时间
    'GET v1/ticketmessage/<id:\d+>'         =>'v1/spot-order/get-spot-ticket-time',  // 景点订单 - 门票信息每天的售价和库存

    /* ---------------------------------------------------相册------------------------------------------------------*/
    'GET v1/photocondition'                 =>'v1/photo/condition',                // 相册 - 条件
    'GET v1/photolister/<param>/<page:\d+>'=>'v1/photo/lister',                  // 相册 - 列表
    'GET v1/photodetail/<id:\d+>'            =>'v1/photo/detail',                  // 相册 - 列表
    'GET v1/photofavorite/<id:\d+>'            =>'v1/photo/favorite',              // 相册 - 点赞
    /* ----------------------------------------------线路模块 ------------------------------------------------------*/
    'GET v1/linecondition'                    => 'v1/line/condition',               // 线路列表页搜索条件综合
    'GET v1/linelist/<param>/<page:\d+>'    => 'v1/line/lister',                  // 线路列表页
    'GET v1/linedetail/<id:\d+>'              => 'v1/line/detail',                  // 线路列详情
    'GET v1/linesuit/<id:\d+>'                => 'v1/line/suit',                    // 线路套餐
    'GET v1/linesuitmessages/<id:\d+>'        => 'v1/line/suit-line-id-day',        // 线路订单提交页面的套餐类型和时间日历
    'GET v1/linesuitmessage/<id:\d+>'         => 'v1/line/suit-id-day',             // 根据套餐id获得时间日历
    'POST v1/addlineorder'                    => 'v1/line/add-order',               // 订单提交

    /*----------------------------------------评论-问答-收藏通用---------------------------------------------------------------*/
    'POST v1/questionadd'                     => 'v1/question/add',                     // 提交问答
    'GET  v1/questionlisterpage/<id:\d+>/<typeid:\d+>/<page:\d+>'=> 'v1/question/lister',// 问答列表

    'GET v1/comment-head/<typeid:\d+>/<id:\d+>'=> 'v1/comment/comment-head',            // 点评头部信息
    'GET v1/commentlist/<typeid:\d+>/<id:\d+>/<page:\d+>' => 'v1/comment/comment-list', // 评论列表
    'POST v1/commentadd'                       => 'v1/comment/add',                     // 评论添加
    'GET v1/collectionadd/<typeid:\d+>/<id:\d+>' => 'v1/collection/add-collection-tong', // 添加收藏
    'GET v1/collectiondel/<typeid:\d+>/<id:\d+>' => 'v1/collection/del-collection-tong', // 取消收藏


    /*     -----------------------------------------酒店列表頁------------------------------------    */
    'GET v1/hotelbanner'                        => 'v1/hotel/banner' ,             // 酒店详情页
    'GET v1/hotellist/<param>/<page:\d+>'         =>'v1/hotel/hotel-list',       // 酒店列表頁
    'GET v1/hotellist-condition'                =>'v1/hotel/hotel-condition',      // 酒店列表条件-整合
    'GET v1/hoteldetail/<id:\d+>'               => 'v1/hotel/hotel-detail' ,       // 酒店详情页
    'GET v1/roomdetail/<id:\d+>'                => 'v1/hotel/room-detail' ,        // 酒店房间详情页
    'GET v1/hotelpiclist/<id:\d+>'              => 'v1/hotel/hotel-pic-list',      // 酒店图集列表
    'GET v1/hotelcomment/<id:\d+>/<page:\d+>'   => 'v1/hotel/comment-list',        // 酒店评论列表
    'GET v1/hotelcommentcount/<id:\d+>'         => 'v1/hotel/comment-count',       // 酒店评论总数
    'GET v1/hoteladdcollection/<id:\d+>'        =>'v1/hotel/hotel-add-collection', // 酒店收藏
    'GET v1/hoteldelcollection/<id:\d+>'        =>'v1/hotel/hotel-del-collection', // 酒店取消
    'GET v1/hotelquestionlist/<id:\d+>'         => 'v1/hotel/hotel-question',      //酒店问答列表
    'POST v1/addhotelquestion'                  => 'v1/hotel/question-add',        // 酒店提交问答
    /*  --------------------------------------------酒店订单页面--------------------------------------------*/
    'GET v1/hotelorderindex/<hotelid:\d+>'  => 'v1/hotel-order/get-hotel-order-message' ,// 酒店详情页
    'GET v1/roomrmessage/<id:\d+>'          => 'v1/hotel-order/get-hotel-room-time' ,    // 房间的详情时间库存

    /*  -----------------------------------------   攻略   ------------------------------------------------*/
    'GET v1/articleindex'                     =>      'v1/article/article-index',      // 景点首页
    'GET v1/articlecondition'                 => 'v1/article/article-condition' ,      // 攻略条件列表
    'GET v1/articlelist/<param>/<page:\d+>' => 'v1/article/article-lister' ,         // 攻略列表
    'GET v1/articledetail/<id:\d+>'           => 'v1/article/article-detail' ,         // 攻略详情页
    /*  ----------------------------------------------游记------------------------------------------------*/
    'GET v1/notesindex'                     => 'v1/notes/notes-index',           // 游记首页
    'GET v1/noteslist/<page:\d+>' => 'v1/notes/notes-lister' ,                   // 游记列表
    'GET v1/notesdetail/<id:\d+>'           => 'v1/notes/notes-detail' ,         // 游记详情页

    /*     --------------------------------------联系人地址---------------------------------------------------       */
    'GET v1/linkman'=>'v1/member-linkman/linkman-lister',                           // 联系人地址列表
    'POST v1/addlinkman'=>'v1/member-linkman/add-linkman',                          // 联系人地址添加
    'POST v1/editlinkman'=>'v1/member-linkman/edit-linkman',                        // 联系人地址修改
    'POST v1/dellinkman'=>'v1/member-linkman/del-linkman',                          // 联系人地址删除
    'GET v1/cardtype'=>'v1/member-linkman/card-type',                               // 证件类型列表

    /* -------------------------------------------订单模块------------------------------------------------ */
    'POST v1/addspotorder'                          =>'v1/spot-order/add-order',      // 提交景点订单
    'POST v1/addhotelorder'                         =>'v1/hotel-order/add-order',     // 提交酒店订单
    'GET  v1/orderstatus'                           =>'v1/member-order/order-status', // 订单所有状态信息
    'GET  v1/orderstypeid'                          =>'v1/member-order/orders-type',  // 订单模块类型的id
    'GET  v1/orderlister/<typeid:\d+>/<page:\d+>'   =>'v1/member-order/lister',       // 订单列表
    'GET  v1/orderstatus/<status:\d+>/<page:\d+>'   =>'v1/member-order/order-condition', // 订单状态下的列表
    'GET  v1/orderdetail/<id:\d+>'                  =>'v1/member-order/detail',         // 订单详情
    'GET  v1/ordercancel/<id:\d+>'                  =>'v1/member-order/cancel',         // 订单取消
    'GET  v1/reorder/<id:\d+>'                      =>'v1/member-order/reorder',        // 订单再次预定

    /*  ------------------------------------------登陆模块 -------------------------------------------------*/
    'POST v1/login'=>'v1/login/login',                                                  // 用户登陆
    'POST v1/logout'=>'v1/login/logout',                                                // 用户退出
    'POST v1/sendmessage'=>'v1/login/send-message',                                     // 用户注册 - 发送短信
    'POST v1/checkmessage'=>'v1/login/check-message',                                   // 用户注册 - 验证验证码
    'POST v1/reg'=>'v1/login/register',                                                 // 用户注册
    'POST v1/modifypass'=>'v1/login/modify-pass',                                       // 修改密码

    /*  --------------------------------------------用户中心-----------------------------------------------*/
    'GET v1/membercenter'                           => 'v1/user-center/member-center',      // 用户中心-首页
    'GET v1/edit-membercenter'                      => 'v1/user-center/edit-member-center', // 用户中心-获取个人信息
    'POST v1/edit-membercenter'                     => 'v1/user-center/edit-member-center', // 用户中心-提交个人信息
    'POST v1/headpic-upload'                        => 'v1/user-center/upload-head-pic',    // 上传用户头像
    'POST v1/checkpass'                             => 'v1/user-center/check-pass',         // 验证密码
    'GET v1/phonecitycode'                          => 'v1/user-center/phone-city-code',    // 提供手机国别号
    'POST v1/membercenter-checkphone'               => 'v1/user-center/check-phone',        // 验证手机号
    'POST v1/membercenter-checkverify'              => 'v1/user-center/check-verify-phone', // 验证手机验证码
    'POST  v1/member-modifypass'                    =>  'v1/user-center/modify-pass',       // 确认修改密码

    'POST v1/addsuggest'                            =>  'v1/user-center/add-suggest',       // 提交反馈

    'GET  v1/addressdetail/<id:\d+>'                =>  'v1/member-address/address-detail', // 获得地址详情
    'GET  v1/addresslist'                           =>  'v1/member-address/lister',         // 地址列表页
    'GET  v1/addressdel/<id:\d+>'                   =>  'v1/member-address/address-delete', // 地址删除
    'POST  v1/address-add'                          =>  'v1/member-address/address-add',    // 地址添加

    'GET v1/mycollectionhead'                       => 'v1/user-center/my-collection-head', // 我的收藏-头部
    'GET v1/mycollectionlist/<id:\d+>/<page:\d+>'   => 'v1/user-center/my-collection-list', // 我的收藏-列表
    'GET v1/delcollection/<ids>'                  =>  'v1/user-center/del-collection',    // 我的收藏-删除

    /*  ----------------------------------厕所模块--------------------------------------*/
    'GET v1/toilet-list/<page>'                   =>  'v1/toilet/toilet-list2',
    'GET v1/toilet-detail/<id>'                   =>  'v1/toilet/toilet-detail',
    'GET v1/toilenear'                              =>  'v1/toilet/member-near-toilet3',
    'GET v1/toiletgeo'                              =>  'v1/toilet/update-toilet',
    'POST v1/toiletup'                              =>   'v1/toilet/post-data',
    'GET v1/changehotel'                            =>   'v1/init-all/change-hotel-url',
    'GET v1/changenotes'                            =>   'v1/init-all/change-notes-url',

    'GET v1/detail'                                 => 'v1/resource/details',                // 攻略和内容的详情 - webview
    'GET v1/initstringshort'                        => 'v1/resource/init-str-short',         // 攻略和内容的详情 - webview
    'GET v1/initstringlong/<param>/<id:\d+>'      => 'v1/resource/init-str-long',                // 攻略和内容的详情 - webview

    /*-----------------------------------------------------*/
];