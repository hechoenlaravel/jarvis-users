<?php

namespace Modules\Users\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Modules\Users\Transformers\UserTransformer;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Class User
 * @package Modules\Users\Entities
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, EntrustUserTrait, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'uuid', 'active'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The dates mutators
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'last_login'];

    /**
     * filter by Uuid
     * @param $query
     * @param $uuid
     * @return mixed
     */
    public function scopeByUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Get the Avatar URL
     * @return string
     */
    public function getAvatarImageUrl()
    {
        return empty($this->avatar) ? asset('img/default-avatar.png') : route('media', ['id' => $this->avatar]);
    }

    /**
     *
     * @return string
     */
    public function getLastLogin()
    {
        if(empty($this->last_login)){
            return "N/A";
        }
        return $this->last_login->format('d/m/Y h:i A');
    }

    /**
     * @return array
     */
    public function transformed()
    {
        $manager = new Manager();
        $resource = new Item($this, new UserTransformer());
        return $manager->createData($resource)->toArray();
    }

}