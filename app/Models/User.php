<?php

namespace App\Models;

use App\Base\Model;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $active
 * @property int $admin
 * @property int last_login
 */
class User extends Model
{
    /**
     * {@inheritdoc}
     */
    public function attributes(): array
    {
        return [
            'id',
            'name',
            'email',
            'active',
            'admin',
            'last_login',
        ];
    }
}
