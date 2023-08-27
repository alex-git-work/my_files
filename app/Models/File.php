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
 * @property string $shared_to
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $sharedToUsers
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

    /**
     * @return array
     */
    public function getSharedToUsers(): array
    {
        return $this->shared_to ? explode(',', $this->shared_to) : [];
    }

    /**
     * @param array $ids
     * @return void
     */
    public function setSharedToUsers(array $ids): void
    {
        $this->shared_to = implode(',', $ids);
    }

    /**
     * @param int $id
     * @return void
     */
    public function share(int $id): void
    {
        if (in_array($id, $this->sharedToUsers)) {
            return;
        }

        $this->sharedToUsers = array_merge($this->sharedToUsers, [$id]);
        $this->state = File::STATE_SHARED;
        $this->updated_at = now();
    }

    /**
     * @param int $id
     * @return void
     */
    public function makePrivate(int $id): void
    {
        if (!in_array($id, $this->sharedToUsers)) {
            return;
        }

        $this->sharedToUsers = array_filter($this->sharedToUsers, fn($v) => (int)$v !== $id);

        if (empty($this->sharedToUsers)) {
            $this->state = File::STATE_PRIVATE;
        }

        $this->updated_at = now();
    }
}
