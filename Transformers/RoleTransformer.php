<?php

namespace modules\Users\Transformers;

use Modules\Users\Entities\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract{

    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'name' => $role->name,
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at
        ];
    }

}