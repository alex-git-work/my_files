<?php

namespace App\Models;

use App\App;
use App\Base\Model;
use App\Helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int $role_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $token
 * @property string $last_request
 * @property string $restoration_key
 * @property string $key_exp_date
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    public const ROLE_ADMIN = 1;
    public const ROLE_USER = 2;

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];

    /**
     * @return void
     * @throws Exception
     */
    public function login(): void
    {
        $this->token = StringHelper::generateToken();
        $this->last_request = now();
        $this->save();
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $this->token = null;
        $this->last_request = null;
        $this->save();
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        if (empty($this->last_request)) {
            return false;
        }

        return strtotime($this->last_request) >= strtotime('now - ' . App::$params['token_ttl'] . ' minutes');
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role_id === self::ROLE_ADMIN;
    }

    /**
     * @return void
     */
    public function deleteUserData(): void
    {
        $files = App::$db
            ->createCommand('SELECT id, name, ext FROM ' . File::tableName() . ' WHERE user_id = ' . $this->id)
            ->queryAll();

        $fileIds = array_column($files, 'id');

        App::$db
            ->createCommand('DELETE FROM ' . File::tableName() . ' WHERE id IN (' . implode(', ', $fileIds) . ')')
            ->query();

        App::$db
            ->createCommand('DELETE FROM ' . Directory::tableName() . ' WHERE user_id = ' . $this->id)
            ->query();

        foreach ($files as $file) {
            unlink(FILES . $file['name'] . '.' . $file['ext']);
        }
    }
}
