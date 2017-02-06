<?php

namespace Modules\Users\Menu;

use Illuminate\Support\Facades\Auth;

/**
 * Class MenuDefinition
 * @package Modules\Users\Menu
 */
class MenuDefinition
{

    /**
     * @var \Illuminate\Support\Collection
     */
    public $items;

    /**
     * MenuDefinition constructor.
     */
    public function __construct()
    {
        $this->items = collect();
        $this->items->push($this->getList());
        $this->items->push($this->getConfiguration());
        $this->items->push($this->getRoles());
        $this->items->push($this->getProfileFields());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "Usuarios";
    }

    /**
     * @return string
     */
    public function getInstance()
    {
        return 'sidebar';
    }

    /**
     * @return string
     */
    public function isDropdown()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return [
            'route' => 'users.index',
            'type' => 'route',
            'name' => 'Listar',
            'active-state' => function() {
                $request = app('Illuminate\Http\Request');
                return $request->is('users*');
            },
            'ability' => function() {
                return !Auth::user()->hasPermissionTo('Listar Usuarios');
            }
        ];
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'name' => 'CONFIGURACION',
            'type' => 'header',
        ];
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [
            'route' => 'roles.index',
            'type' => 'route',
            'name' => 'Roles',
            'active-state' => function() {
                $request = app('Illuminate\Http\Request');
                return $request->is('roles*');
            },
            'ability' => function() {
                return !Auth::user()->hasPermissionTo('Listar roles');
            }
        ];
    }

    /**
     * @return array
     */
    public function getProfileFields()
    {
        return [
            'route' => 'users.config',
            'type' => 'route',
            'name' => 'Campos de perfil',
            'active-state' => function() {
                $request = app('Illuminate\Http\Request');
                return $request->is('config/users*');
            },
            'ability' => function() {
                return !Auth::user()->hasPermissionTo('Editar campos de perfil');
            }
        ];
    }

}