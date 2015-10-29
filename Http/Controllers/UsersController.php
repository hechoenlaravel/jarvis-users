<?php namespace Modules\Users\Http\Controllers;

use DB;
use Auth;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Joselfonseca\LaravelApiTools\Exceptions\ValidationException;
use Module;
use SweetAlert;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Modules\Users\Entities\Role;
use Modules\Users\Entities\User;
use Illuminate\Support\Facades\Password;
use Pingpong\Modules\Routing\Controller;
use Modules\Users\Repositories\UserEntity;
use Joselfonseca\ImageManager\ImageManager;
use Modules\Users\Transformers\UserTransformer;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Modules\Users\Http\Requests\CreateUserRequest;
use Modules\Users\Http\Requests\ForgotPasswordRequest;
use Joselfonseca\LaravelApiTools\Traits\ResponderTrait;
use Hechoenlaravel\JarvisFoundation\Traits\EntryManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Hechoenlaravel\JarvisFoundation\EntityGenerator\EntityModel;
use Hechoenlaravel\JarvisFoundation\UI\Field\EntityFieldsFormBuilder;
use Joselfonseca\LaravelApiTools\Exceptions\ApiModelNotFoundException;
use Hechoenlaravel\JarvisFoundation\Exceptions\EntryValidationException;

/**
 * Class UsersController
 * @package Modules\Users\Http\Controllers
 */
class UsersController extends Controller
{

    use ResponderTrait, EntryManager, ResetsPasswords;

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
            ->with('roles', Role::all()->pluck('name', 'id')->toArray())
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
            if($request->has('roles'))
            {
                $user->roles()->sync($request->get('roles'));
            }else{
                $user->roles()->sync([]);
            }
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
            ->with('roles', Role::all()->pluck('display_name', 'id')->toArray())
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
            if($request->has('active'))
            {
                $user->active = $request->get('active');
            }
            if($request->has('password'))
            {
                $this->validate($request, [
                    'password' => 'required|confirmed|min:6'
                ]);
                $user->password = bcrypt($request->get('password'));
            }
            $user->save();
            if($request->has('roles'))
            {
                $user->roles()->sync($request->get('roles'));
            }else{
                $user->roles()->sync([]);
            }
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
        return $this->responseWithPaginator(100, $model, new UserTransformer(),null, null, [], function($resource, $fractal){
            $resource->setMetaValue('total', User::count());
        });
    }

    /**
     * Update the user's avatar
     * @param ImageManager $manager
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(ImageManager $manager, $id)
    {
        $file = $manager->doUpload(0);
        $user = User::find($id);
        $user->avatar = $file->id;
        $user->save();
        return $this->simpleArray($file->toArray());
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
                return $this->responseNoContent();
        }
        throw new UpdateResourceFailedException();
    }

}