<?php

namespace Modules\Users\Http\Controllers;

use DB;
use File;
use Storage;
use SweetAlert;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Modules\Users\Entities\Role;
use Modules\Users\Entities\User;
use Modules\Users\Entities\Avatar;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Modules\Users\Repositories\UserEntity;
use Modules\Users\Transformers\UserTransformer;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Modules\Users\Http\Requests\CreateUserRequest;
use Modules\Users\Http\Requests\ForgotPasswordRequest;
use Hechoenlaravel\JarvisFoundation\Traits\EntryManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Hechoenlaravel\JarvisFoundation\UI\Field\EntityFieldsFormBuilder;
use Hechoenlaravel\JarvisFoundation\Exceptions\EntryValidationException;

/**
 * Class UsersController
 * @package Modules\Users\Http\Controllers
 */
class UsersController extends Controller
{

    use EntryManager, ResetsPasswords;

    /**
     * @var User
     */
    protected $model;

    /**
     * @var string
     */
    protected $subject = "Recuperar Contraseña";

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * list view for users
     * @return $this
     */
    public function index()
    {
        return view('users::users.index');
    }

    /**
     * Create form for User
     * @return $this
     */
    public function create(UserEntity $entity)
    {
        $builder = new EntityFieldsFormBuilder($entity->getEntity());
        return view('users::users.create')
            ->with('roles', Role::all()->pluck('name', 'name')->toArray())
            ->with('profileFields', $builder->render());
    }

    /**
     *
     * @param CreateUserRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(CreateUserRequest $request, UserEntity $entity)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->all());
            $user->syncRoles($request->get('roles', []));
            $this->updateEntry($entity->getEntity()->id, $user->id, ['input' => $request->all()]);
            DB::commit();
            SweetAlert::success('Se ha creado el Usuario', 'Excelente!')->autoclose(3500);
        } catch (EntryValidationException $e) {
            DB::rollBack();

            return back()->withInput($request->all())->withErrors($e->getErrors());
        }

        return redirect()->route('users.index');
    }

    /**
     * Edit a user
     * @param $uuid
     * @return $this
     */
    public function edit(UserEntity $entity, $uuid)
    {
        $user = User::byUuid($uuid)->firstOrFail();
        $builder = new EntityFieldsFormBuilder($entity->getEntity());
        $builder->setRowId($user->id);

        return view('users::users.edit')
            ->with('user', $user)
            ->with('roles', Role::all()->pluck('name', 'name')->toArray())
            ->with('profileFields', $builder->render());
    }

    /**
     * Update a user
     * @param UpdateUserRequest $request
     * @param $uuid
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, UserEntity $entity, $uuid)
    {
        $user = User::byUuid($uuid)->firstOrFail();
        if ($user->email !== $request->get('email')) {
            $this->validate($request, [
                'email' => 'unique:app_users,email'
            ]);
        }
        DB::beginTransaction();
        try {
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            if($request->has('active')) {
                $user->active = $request->get('active');
            }
            if($request->has('password')) {
                $this->validate($request, [
                    'password' => 'required|confirmed|min:6'
                ]);
                $user->password = bcrypt($request->get('password'));
            }
            $user->save();
            $user->syncRoles($request->get('roles', []));
            $this->updateEntry($entity->getEntity()->id, $user->id, ['input' => $request->all()]);
            DB::commit();
            SweetAlert::success('Se ha editado el Usuario', 'Excelente!')->autoclose(3500);
        } catch (EntryValidationException $e) {
            DB::rollBack();
            return back()->withInput($request->all())->withErrors($e->getErrors());
        }
        return redirect()->route('users.index');
    }

    /**
     * Delete a user
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $user = $this->model->findOrFail($id);
            $user->delete();

            return $this->responseNoContent();
        } catch (ModelNotFoundException $e) {
            throw new ApiModelNotFoundException;
        }
    }

    /**
     * Find Users
     * @param Request $request
     * @return mixed
     */
    public function find(Request $request)
    {
        $model = $this->model->with('roles');
        if ($request->has('name')) {
            $model->where('name', 'LIKE', '%' . $request->get('name') . '%');
        }
        if ($request->has('email')) {
            $model->where('email', 'LIKE', '%' . $request->get('email') . '%');
        }
        $pagination = $model->paginate(100);
        $users = $pagination->getCollection();
        $response = fractal()->collection($users, new UserTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($pagination))
            ->addMeta('total', User::count());
        return response()->json($response);
    }

    /**
     * Update the user's avatar
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request, $id)
    {
        $file = $request->file('file');
        $name = md5($id.time()).'.'.$file->getClientOriginalExtension();
        Storage::put($name, File::get($file));
        $fileModel = Avatar::create([
            'name' => $name,
            'originalName' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
            'path' => $name,
            'size' => $file->getClientSize(),
            'from_manager' => 0
        ]);
        $user = User::find($id);
        $user->avatar = $fileModel->id;
        $user->save();
        return response()->json(['url' => url('users/'.$user->id.'/avatar')]);
    }

    public function getAvatar($id)
    {
        $user = User::find($id);
        $avatar = Avatar::find($user->avatar);
        $file = Storage::get($avatar->path);
        $img = app('image')->make($file);
        return $img->response();
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return mixed
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return response()->json(null, 204);
        }
        throw new UpdateResourceFailedException();
    }

}