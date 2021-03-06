<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    protected $modelBindings = [
        'App\Models\User' => 'user',
        'App\Models\Category' => 'category',
        'App\Models\Shop' => 'shop',
        'App\Models\Promoter' => 'promoter',
        'App\Models\Advert' => ['advert-index', 'advert-user', 'advert-app', 'advert-category', 'advert-left-category'],
        'App\Models\Admin' => 'admin',
        'App\Models\OperationRecord' => 'operation-record',
        'App\Models\Goods' => ['my-goods', 'goods'],
        'App\Models\UserBank' => 'bank',
        'App\Models\DeliveryMan' => 'delivery-man',
        'App\Models\ShippingAddress' => 'shipping-address',
        'App\Models\ShopColumn' => 'shop-column',
        'App\Models\VersionRecord' => ['version-record', 'operation'],
        'App\Models\Notice' => 'notice',
        'App\Models\Salesman' => 'salesman',
        'App\Models\SalesmanCustomer' => 'salesman-customer',
        'App\Models\SalesmanVisit' => 'visit',
        'App\Models\SalesmanVisitOrder' => 'salesman-visit-order',
        'App\Models\MortgageGoods' => 'mortgage-goods',
        'App\Models\Coupon' => 'coupon',
        'App\Models\PaymentChannel' => 'payment-channel',
        'App\Models\ChildUser' => 'child-user',
        'App\Models\AssetApply' => 'asset-apply',
        'App\Models\PromoGoods' => 'promo-goods',
        'App\Models\Promo' => 'promo',
        'App\Models\PromoApply' => 'promo-apply',
        'App\Models\WarehouseKeeper' => 'warehouse-keeper',
        'App\Models\DeliveryTruck' => 'delivery-truck',
        'App\Models\WeixinArticle' => 'article'
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
        foreach ($this->modelBindings as $class => $keys) {
            foreach ((array)$keys as $key) {
                $router->model($key, $class);
            }
        }
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }
}
