<?php

namespace Modules\Users\Console;

use Illuminate\Console\Command;
use Modules\Users\Entities\Role;
use Modules\Users\Entities\Permission;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateDefaultRoleAndPerms extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'users:generateDefaultRoles';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate default roles and permissions for the system';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 * This will create the User's permissions for module User
	 * @return mixed
	 */
	public function fire()
	{
        $admin = Role::create([
            'name' => 'Administrador del sistema',
            'module' => 'users'
        ]);
        $list = Permission::create([
            'name' => 'Listar usuarios',
            'module' => 'users'
        ]);
        $create = Permission::create([
            'name' => 'Crear usuario',
            'module' => 'users'
        ]);
        $edit = Permission::create([
            'name' => 'Editar usuario',
            'module' => 'users'
        ]);
        $delete = Permission::create([
            'name' => 'Eliminar usuario',
            'module' => 'users',
        ]);
        $activate = Permission::create([
            'name' => 'Activar usuario',
            'module' => 'users'
        ]);
        $config = Permission::create([
            'name' => 'Configuracion de usuarios',
            'module' => 'users'
        ]);
        $profileFields = Permission::create([
            'name' => 'Editar campos de perfil',
            'module' => 'users'
        ]);
        $listRoles = Permission::create([
            'name' => 'Listar roles',
            'module' => 'users'
        ]);
        $createRoles = Permission::create([
            'name' => 'Crear roles',
            'module' => 'users'
        ]);
        $editRoles = Permission::create([
            'name' => 'Editar roles',
            'module' => 'users'
        ]);
        $deleteRoles = Permission::create([
            'name' => 'Eliminar roles',
            'module' => 'users'
        ]);
        $adminPermissions = Permission::create([
            'name' => 'Asignación de permisos a roles',
            'module' => 'users'
        ]);
        $admin->givePermissionTo($list, $create, $edit, $delete, $activate, $config, $profileFields, $listRoles, $createRoles, $editRoles, $deleteRoles, $adminPermissions);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [

		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [

		];
	}

}
