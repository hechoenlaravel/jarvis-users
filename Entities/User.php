<?php

namespace Modules\Users\Entities;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Users\Transformers\UserTransformer;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package Modules\Users\Entities
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        return empty($this->avatar) ? asset('img/default-avatar.png') : url('users/'.$this->id.'/avatar');
    }


    /**
     * @return string
     */
    public function getNameAttribute($value)
    {
        return ucwords(mb_strtolower($value));
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
        return fractal()->item($this, new UserTransformer())->toArray();
    }

}