<?php

namespace Modules\Users\Http\Controllers;

Use Module;
use SweetAlert;
use Illuminate\Http\Request;
use Modules\Users\Entities\Role;
use Modules\Users\Entities\Permission;
use Nwidart\Modules\Routing\Controller;
use Modules\Users\Http\Requests\CreateRoleRequest;

/**
 * Class RolesController
 * @package Modules\Users\Http\Controllers
 */
class RolesController extends Controller
{

    /**
     * RolesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @return $this
     */
    public function index()
    {
        $roles = Role::paginate(20);
        return view('users::roles.index')->with('roles', $roles);
    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function create()
    {
        return view('users::roles.create');
    }

    /**
     * @param CreateRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRoleRequest $request)
    {
        Role::create($request->all());
        SweetAlert::success('Se ha creado el rol', 'Excelente!')->autoclose(3500);
        return redirect()->route('roles.index');
    }

    /**
     * @param $id
     * @return $this
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('users::roles.edit')->with('role', $role);
    }

    /**
     * @param CreateRoleRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->fill($request->only('name'));
        $role->save();
        SweetAlert::success('Se ha actualizado el rol', 'Excelente!')->autoclose(3500);
        return redirect()->route('roles.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        SweetAlert::success('Se ha eliminado el rol', 'Excelente!')->autoclose(3500);
        return redirect()->route('roles.index');
    }

    /**
     * @param $id
     * @return $this
     */
    public function permissions($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $modules = Module::all();
        return view('users::roles.permissions')
            ->with('modules', $modules)
            ->with('permissions', $permissions)
            ->with('role', $role);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permissionsUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->get('permissions'), []);
        SweetAlert::success('Se han actualizado los permisos del rol', 'Excelente!')->autoclose(3500);
        return redirect()->back();
    }

}