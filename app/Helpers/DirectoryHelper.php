<?php

namespace App\Helpers;

use App\Models\Directory;

/**
 * Class DirectoryHelper
 * @package App\Helpers
 */
class DirectoryHelper
{
    protected static string $path = '';

    /**
     * @param int|null $id
     * @return string
     */
    public static function makePath(?int $id): string
    {
        if ($id === null) {
            return '/';
        }

        $model = Directory::findOne($id);

        if ($model->parent_id === null && !empty(self::$path)) {
            self::$path =  '/' . $model->name . self::$path;
        } else {
            self::$path = empty(self::$path) ? '/' . $model->name : '/' . $model->name . self::$path;
            self::makePath($model->parent_id);
        }

        return self::$path . '/';
    }

    /**
     * @return void
     */
    public static function reset(): void
    {
        self::$path = '';
    }
}
