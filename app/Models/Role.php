<?php

namespace App\Models;

use App\Base\Model;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $name
 */
class Role extends Model
{
    public const ROLE_ADMIN = 1;
    public const ROLE_USER = 2;

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];
}
