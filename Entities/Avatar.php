<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Avatar
 *
 */
class Avatar extends Model
{

    /**
     * @var string
     */
    protected $table = 'image_manager_files';

    /**
     * @var array
     */
    protected $fillable = ['name', 'originalName', 'type', 'path', 'size', 'from_manager'];


}
