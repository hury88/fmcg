<?php

/**
 * 前台
 */
$router->group(['namespace' => 'Index'], function ($router) {

    $router->get('/', 'HomeController@index');              //商家管理首页
    $router->controller('shop', 'ShopController');          //商家商店首页
    $router->controller('order','OrderController');//订单统计
    $router->controller('order-buy', 'OrderBuyController');  //买家订单管理
    $router->controller('order-sell', 'OrderSellController');//卖家订单管理
    $router->resource('goods', 'GoodsController');          //商品管理

});


/**
 * 后台
 */
$router->group(['prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {
    // 首页
    $router->get('/', ['uses' => 'HomeController@getIndex']);  // 后台首页
    $router->delete('admin/batch', 'AdminController@deleteBatch');//批量删除
    $router->get('admin/password', 'AdminController@getPassword');//获取修改密码表单
    $router->put('admin/password', 'AdminController@putPassword');//修改当前管理员密码
    $router->put('admin/switch', 'AdminController@putSwitch');//管理员状态切换
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('advert-index', 'AdvertIndexController'); // 首页广告
    $router->resource('advert-user', 'AdvertUserController'); // 用户端广告
    $router->resource('advert-app', 'AdvertAppController'); // APP广告
    $router->resource('role', 'RoleController');
    $router->delete('user/batch', 'UserController@deleteBatch');//批量删除用户
    $router->put('user/switch', 'UserController@putSwitch');//批量修改用户
    $router->resource('user', 'UserController');            //用户管理
    $router->post('getCategory', 'CategoryController@getCategory');  //获取属性
    $router->resource('category', 'CategoryController');            //属性管理
    $router->post('getAttr', 'AttrController@getAttr');             //获取标签
    $router->resource('attr', 'AttrController');                    //标签管理
    $router->get('attr/create/{id}', 'AttrController@create')->where('id', '[0-9]+'); //添加子标签
    $router->resource('images', 'GoodsImagesController');                    //商品图片管理
    $router->resource('shop', 'ShopController', ['only' => ['edit', 'update']]); //店铺管理
    $router->controller('system-trade', 'SystemTradeInfoController');        //系统交易信息
    $router->controller('feedback', 'FeedbackController');             //反馈管理
    $router->controller('trade', 'TradeController');        //交易信息
    $router->delete('promoter/batch', 'PromoterController@deleteBatch');    //批量删除推广人员
    $router->resource('promoter', 'PromoterController');             //推广人员管理
    $router->resource('operation-record', 'OperationRecordController');    //运维操作记录
    $router->controller('data-statistics', 'DataStatisticsController');    //运营数据统计
});


/**
 * 接口
 */
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function ($router) {
    /**
     * v2 版本
     */
    $router->group(['prefix' => 'v1', 'namespace' => 'v1'], function ($router) {
        // 接口地址
        $router->get('/', [
            'as' => 'api.v1.root',
            function () {
                return redirect('/');
            }
        ]);

        $router->controller('file', 'FileController');                              // 文件上传
        $router->get('categories/{id}/attrs', 'CategoryController@getAttr');         //获取标签
        $router->get('categories', 'CategoryController@getCategory');         //获取标签

    });
});