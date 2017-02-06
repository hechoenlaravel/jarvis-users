<?php

namespace Modules\Users\Providers;

use Modules\Users\Menu\MenuDefinition;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $menuService = app('menu.service');
        $menuService->addMenuDefinition(MenuDefinition::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

}