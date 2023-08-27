<?php

namespace App\Controllers;

use App\App;
use App\Base\Controller;
use App\Exceptions\HttpException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidateException;
use App\Models\Directory;
use App\Models\File;
use App\Response;
use App\Traits\Authorization;
use App\Validators\DirectoryValidator;
use PDOException;

/**
 * Class DirectoryController
 * @package App\Controllers
 */
class DirectoryController extends Controller
{
    use Authorization;

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function show(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $directory = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);

        $files = File::findAll(['user_id' => $directory->user_id, 'directory_id' => $directory->id]);
        $filesInfo = array_map(
            fn(File $f) => $f->getAttributes(except: [
                'user_id',
                'directory_id',
                'name',
                'path',
            ]),
            $files
        );

        $subDirs = Directory::findAll(['user_id' => $directory->user_id, 'parent_id' => $directory->id]);
        $subDirsInfo = array_map(fn(Directory $d) => $d->getAttributes(except: ['user_id', 'parent_id']), $subDirs);

        return $this->asJson([
            'directory' => $directory->getAttributes(except: ['user_id']),
            'files' => $filesInfo,
            'subDirs' => $subDirsInfo,
        ]);
    }

    /**
     * @return Response
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function create(): Response
    {
        $currentUserId = $this->authCheck();

        $data = App::$request->parsedBody;
        $config = $this->validate($data, $currentUserId);

        $dir = new Directory($config);
        $dir->user_id = $currentUserId;
        $dir->created_at = now();
        $dir->save();

        return $this->asJson([
            'message' => 'Directory created',
            'directoryId' => App::$db->lastInsertID,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws HttpException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function edit(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $dir = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);
        $files = File::findAll(['directory_id' => $id, 'user_id' => $currentUserId]);

        $data = App::$request->parsedBody;
        $attributes = $this->validate($data, $currentUserId);

        try {
            App::$db->pdo->beginTransaction();

            $dir->setAttributes($attributes);
            $dir->updated_at = now();
            $dir->save();

            if (!empty($files)) {
                foreach ($files as $file) {
                    $file->changeDir($id);
                    $file->save();
                }
            }

            App::$db->pdo->commit();
        } catch (PDOException $e) {
            App::$db->pdo->rollBack();

            throw new HttpException($e->getMessage(), 500);
        }

        return $this->asJson([
            'message' => 'Directory updated',
            'directoryId' => $dir->id,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws HttpException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function destroy(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $dir = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);

        try {
            App::$db->pdo->beginTransaction();

            $dir->deleteContent();
            $dir->delete();

            App::$db->pdo->commit();
        } catch (PDOException $e) {
            App::$db->pdo->rollBack();

            throw new HttpException($e->getMessage(), 500);
        }

        return $this->asJson([
            'message' => 'Directory and its content deleted successfully',
            'directoryId' => $dir->id,
        ]);
    }

    /**
     * @param int|array $value
     * @return Directory
     * @throws NotFoundException
     */
    protected function findModel(int|array $value): Directory
    {
        $model = Directory::findOne($value);

        if ($model === null) {
            throw new NotFoundException('Directory not found');
        }

        return $model;
    }

    /**
     * @param array|null $data
     * @param int $userId
     * @return array
     * @throws ValidateException
     */
    protected function validate(?array $data, int $userId): array
    {
        $validator = new DirectoryValidator($data, $userId);

        if (empty($data['parent_id'])) {
            $validator->except = ['parent_id'];
        }

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        return $validator->validatedData;
    }
}
