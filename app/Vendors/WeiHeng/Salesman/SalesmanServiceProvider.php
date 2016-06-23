<?php

namespace WeiHeng\Salesman;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\ServiceProvider;

class SalesmanServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('salesman.auth', function ($app) {
            $provider = new EloquentUserProvider($app['hash'], \App\Models\Salesman::class);
            $guard = new Guard($provider, $app['session.store']);

            // 设置变量
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'salesman.auth',
        ];
    }

}
