<?php

namespace App\Models;

use App\App;
use App\Base\Model;

/**
 * This is the model class for table "directories".
 *
 * @property int $id
 * @property int $user_id
 * @property int $parent_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Directory extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'directories';
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function deleteContent(?int $id = null): void
    {
        if ($id === null) {
            $id = $this->id;
        }

        $files = App::$db
            ->createCommand('SELECT id, name, ext FROM ' . File::tableName() . ' WHERE user_id = ' . $this->user_id . ' AND directory_id = ' . $id)
            ->queryAll();

        if (!empty($files)) {
            $fileIds = array_column($files, 'id');

            App::$db
                ->createCommand('DELETE FROM ' . File::tableName() . ' WHERE id IN (' . implode(', ', $fileIds) . ')')
                ->query();

            foreach ($files as $file) {
                unlink(FILES . $file['name'] . '.' . $file['ext']);
            }
        }

        $subDirs = App::$db
            ->createCommand('SELECT id FROM ' . Directory::tableName() . ' WHERE user_id = ' . $this->user_id . ' AND parent_id = ' . $id)
            ->queryAll();

        $subDirIds = array_column($subDirs, 'id');

        if (empty($subDirIds)) {
            return;
        }

        App::$db
            ->createCommand('DELETE FROM ' . Directory::tableName() . ' WHERE user_id = ' . $this->user_id . ' AND id IN (' . implode(', ', $subDirIds) . ')')
            ->query();

        foreach ($subDirIds as $id) {
            $this->deleteContent($id);
        }
    }
}
