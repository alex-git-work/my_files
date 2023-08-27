<?php

namespace App\Models;

use App\Base\Model;
use App\Helpers\DirectoryHelper;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property int $user_id
 * @property int $directory_id
 * @property string $name
 * @property string $real_name
 * @property string $ext
 * @property string $hash
 * @property int $state
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read bool $isPrivate
 * @property-read bool $isShared
 * @property-read string $fullName
 */
class File extends Model
{
    public const STATE_PRIVATE = 0;
    public const STATE_SHARED = 1;

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->path = DirectoryHelper::makePath($this->directory_id);
            DirectoryHelper::reset();
        }
    }

    /**
     * @return bool
     */
    public function getIsPrivate(): bool
    {
        return $this->state === self::STATE_PRIVATE;
    }

    /**
     * @return bool
     */
    public function getIsShared(): bool
    {
        return $this->state === self::STATE_SHARED;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->path . $this->real_name . '.' . $this->ext;
    }

    /**
     * @param int $id
     * @return void
     */
    public function changeDir(int $id): void
    {
        $this->path = DirectoryHelper::makePath($id);
        DirectoryHelper::reset();
    }
}
