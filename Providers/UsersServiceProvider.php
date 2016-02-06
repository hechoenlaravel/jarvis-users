<?php namespace Modules\Users\Providers;

use Auth;
use MenuPing;
use Modules\Users\Entities\Role;
use Modules\Users\Entities\User;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Modules\Users\Observers\RoleObserver;
use Modules\Users\Observers\UserObserver;
use Modules\Users\Widgets\TotalUsersWidget;

/**
 * Class UsersServiceProvider
 * @package Modules\Users\Providers
 */
class UsersServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $providers = [
        \Zizaco\Entrust\EntrustServiceProvider::class,
        \LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class,
        \LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $aliases = [
        'Entrust' => \Zizaco\Entrust\EntrustFacade::class,
        'Authorizer' => \LucaDegasperi\OAuth2Server\Facades\Authorizer::class,
    ];

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
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerViews();
        User::observe(new UserObserver());
        Role::observe(new RoleObserver());
        $this->setMenu();
        $this->registerWidget();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            \Modules\Users\Console\CreateEntities::class,
            \Modules\Users\Console\GenerateAdmin::class,
            \Modules\Users\Console\GenerateDefaultRoleAndPerms::class,
        ]);
        $this->registerOtherProviders()->registerAliases();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('users.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'users'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/users');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom([$viewPath, $sourcePath], 'users');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/users');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'users');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'users');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Register other Service Providers
     * @return $this
     */
    private function registerOtherProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
        return $this;
    }

    /**
     * Register some Aliases
     * @return $this
     */
    protected function registerAliases()
    {
        foreach ($this->aliases as $alias => $original) {
            AliasLoader::getInstance()->alias($alias, $original);
        }
        return $this;
    }

    /**
     * Set the user Menu
     */
    protected function setMenu()
    {
        $menu = MenuPing::instance('sidebar');
        $menu->route('users.index', 'Usuarios', [], 2, ['icon' => 'fa fa-users', 'active' => function(){
            $request = app('Illuminate\Http\Request');
            return $request->is('users*');
        }])->hideWhen(function(){
            if(Auth::user()->ability('administrador-del-sistema', 'user-create,user-edit,user-delete,user-activate'))
            {
                return false;
            }
            return true;
        });
        $menuConfig = MenuPing::instance('config');
        $menuConfig->whereTitle('Configuración', function($sub){
            $sub->dropdown('Usuarios', function($sub){
                $sub->url('me/edit', 'Editar Perfil', [], 1, ['active' => function(){
                    $request = app('Illuminate\Http\Request');
                    return $request->is('me/edit*');
                }]);
                $sub->route('users.config', 'Campos de perfil', [], 1, ['active' => function(){
                    $request = app('Illuminate\Http\Request');
                    return $request->is('config/users*');
                }])->hideWhen(function(){
                    if(Auth::user()->ability('administrador-del-sistema', 'user-configuration')){
                        return false;
                    }
                    return true;
                });
                $sub->route('roles.index', 'Roles y Permisos', [], 2, ['active' => function(){
                    $request = app('Illuminate\Http\Request');
                    return $request->is('roles*');
                }])->hideWhen(function(){
                    if(Auth::user()->ability('administrador-del-sistema', 'create-role,edit-role,delete-role,admin-permissions')){
                        return false;
                    }
                    return true;
                });
            }, [], ['active' => function(){
                $request = app('Illuminate\Http\Request');
                return $request->is('config/users*') || $request->is('me/edit*');
            }]);
        });
    }

    /**
     * Register Widgets
     */
    public function registerWidget()
    {
        $widgets = app('app.widgets');
        $widgets->registerWidget([
            [
                'name' => 'totalUsers',
                'class' => TotalUsersWidget::class,
                'order' => 1,
                'group' => 'demo'
            ]
        ]);
    }

}
