<?php

namespace Modules\Users\Http\Controllers;

use DB;
use Auth;
use SweetAlert;
use Modules\Users\Entities\User;
use Pingpong\Modules\Routing\Controller;
use Modules\Users\Repositories\UserEntity;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Hechoenlaravel\JarvisFoundation\Traits\EntryManager;
use Hechoenlaravel\JarvisFoundation\UI\Field\EntityFieldPresenter;
use Hechoenlaravel\JarvisFoundation\UI\Field\EntityFieldsFormBuilder;
use Hechoenlaravel\JarvisFoundation\Exceptions\EntryValidationException;

/**
 * Class ProfileController
 * @package Modules\Users\Http\Controllers
 */
class ProfileController extends Controller
{

    use EntryManager;

    /**
     * @var
     */
    protected $user;

    /**
     *
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->middleware('auth');
    }

    public function show(UserEntity $entity, $uuid)
    {
        $user = User::byUuid($uuid)->firstOrFail();
        $additionalFields = new EntityFieldPresenter($entity->getEntity());
        $additionalFields->setRowId($user->id);
        $widgets = app('app.widgets');
        return view('users::users.show')
            ->with('user', $user)
            ->with('widgets', $widgets->getWidgets('user.profile'))
            ->with('fields', $additionalFields->getFields());
    }

    /**
     * @param UserEntity $entity
     * @return $this
     */
    public function edit(UserEntity $entity)
    {
        $builder = new EntityFieldsFormBuilder($entity->getEntity());
        $builder->setRowId($this->user->id);
        return view('users::me.edit')
            ->with('user', $this->user)
            ->with('profileFields', $builder->render());
    }

    /**
     * @param UpdateUserRequest $request
     * @param UserEntity $entity
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, UserEntity $entity)
    {
        if ($this->user->email !== $request->get('email')) {
            $this->validate($request, [
                'email' => 'unique:app_users,email'
            ]);
        }
        DB::beginTransaction();
        try {
            $this->user->name = $request->get('name');
            $this->user->email = $request->get('email');
            if($request->has('password'))
            {
                $this->validate($request, [
                    'password' => 'required|confirmed|min:6'
                ]);
                $this->user->password = bcrypt($request->get('password'));
            }
            $this->user->save();
            $this->updateEntry($entity->getEntity()->id, $this->user->id, ['input' => $request->all()]);
            DB::commit();
            SweetAlert::success('Se ha editado su perfil', 'Excelente!')->autoclose(3500);
        } catch (EntryValidationException $e) {
            DB::rollBack();
            return back()->withInput($request->all())->withErrors($e->getErrors());
        }
        return redirect()->route('me.edit');
    }

}