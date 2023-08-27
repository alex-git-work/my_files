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
use App\UploadedFile;
use App\Validators\FileValidator;
use Exception;

/**
 * Class FileController
 * @package App\Controllers
 */
class FileController extends Controller
{
    use Authorization;

    protected const HIDDEN_FILE_ATTRIBUTES = [
        'user_id',
        'directory_id',
        'name',
        'hash',
    ];

    /**
     * @return Response
     * @throws UnauthorizedException
     */
    public function index(): Response
    {
        $currentUserId = $this->authCheck();

        $files = File::findAll(['user_id' => $currentUserId]);

        $filesInfo = array_map(
            fn(File $f) => $f->getAttributes(except: self::HIDDEN_FILE_ATTRIBUTES),
            $files
        );

        return $this->asJson([
            'files' => $filesInfo
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function show(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $file = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);

        return $this->asJson([
            'file' => $file->getAttributes(except: self::HIDDEN_FILE_ATTRIBUTES)
        ]);
    }

    /**
     * @return Response
     * @throws HttpException
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function create(): Response
    {
        $currentUserId = $this->authCheck();

        $data = App::$request->bodyParams;

        if (empty($data['json'])) {
            $directoryId = null;
        } else {
            $data = json_decode($data['json'], true);
            $directoryId = $data['directory_id'] ?? null;

            if ($directoryId && !is_numeric($directoryId)) {
                throw new ValidateException('Invalid directory_id');
            }

            if ($directoryId !== null && Directory::findOne(['id' => $directoryId, 'user_id' => $currentUserId]) === null) {
                throw new ValidateException('Directory not found');
            }
        }

        $files = UploadedFile::getInstances();

        if (empty($files)) {
            throw new ValidateException('No uploaded file');
        }

        array_walk($files, function (UploadedFile $file) {
            if ($file->size > App::$params['max_file_size']) {
                throw new ValidateException(code: 413);
            }
        });

        try {
            App::$db->pdo->beginTransaction();

            $ids = [];

            foreach ($files as $file) {
                $hash = md5_file($file->tempName);

                if ($directoryId) {
                    $model = File::findOne([
                        'real_name' => $file->baseName,
                        'ext' => $file->extension,
                        'user_id' => $currentUserId,
                        'directory_id' => $directoryId,
                    ]);
                } else {
                    $model = File::findOne([
                        ['real_name', '=', $file->baseName],
                        ['ext', '=', $file->extension],
                        ['user_id', '=', $currentUserId],
                        ['directory_id', 'is', null],
                    ]);
                }

                if ($model !== null) {
                    # file already exists

                    if ($model->hash === $hash) {
                        $ids[$model->real_name . '.' . $model->ext] = $model->id;
                        continue;
                    } else {
                        $model->hash = $hash;
                        $model->updated_at = now();

                        unlink(FILES . $model->name . '.' . $model->ext);

                        $fileName = $model->name . '.' . $model->ext;
                    }
                } else {
                    # new file

                    $model = new File([
                        'user_id' => $currentUserId,
                        'directory_id' => $directoryId,
                        'name' => md5($file->baseName . time()),
                        'real_name' => $file->baseName,
                        'ext' => $file->extension,
                        'hash' => $hash,
                        'state' => File::STATE_PRIVATE,
                        'created_at' => now(),
                    ]);

                    $fileName = md5($file->baseName . time()) . '.' . $file->extension;
                }

                if (!$model->save() || !$file->saveAs($fileName)) {
                    throw new HttpException(code: 500);
                }

                $ids[$file->baseName . '.' . $file->extension] = $model->id ?: App::$db->lastInsertID;
            }

            App::$db->pdo->commit();
        } catch (Exception $e) {
            App::$db->pdo->rollBack();

            throw new HttpException($e->getMessage(), 500);
        }

        return $this->asJson([
            'message' => 'Files uploaded',
            'fileIds' => $ids,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function edit(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $file = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);
        $data = App::$request->parsedBody;

        $validator = new FileValidator($data, $currentUserId);

        if (empty($data['directory_id'])) {
            $validator->setRequiredKeys(['real_name']);
        }

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        $attributes = $validator->validatedData;

        $file->setAttributes($attributes);

        if (!empty($data['directory_id'])) {
            $file->changeDir($attributes['directory_id']);
        } else {
            $file->path = '/';
            $file->directory_id = null;
        }

        $file->updated_at = now();
        $file->save();

        return $this->asJson([
            'message' => 'File updated',
            'fileId' => $file->id,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function destroy(int $id): Response
    {
        $currentUserId = $this->authCheck();

        $file = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);

        $file->delete();

        unlink(FILES . $file->name . '.' . $file->ext);

        return $this->asJson([
            'message' => 'File deleted successfully',
            'fileId' => $file->id,
        ]);
    }

    /**
     * @param int $id
     * @return void
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function download(int $id): void
    {
        $currentUserId = $this->authCheck();

        $model = $this->findModel(['id' => $id, 'user_id' => $currentUserId]);
        $file = FILES . $model->name . '.' . $model->ext;

        if (!file_exists($file)) {
            throw new NotFoundException('File not found');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $model->real_name . '.' . $model->ext);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit();
    }

    /**
     * @param int|array $value
     * @return File
     * @throws NotFoundException
     */
    protected function findModel(int|array $value): File
    {
        $model = File::findOne($value);

        if ($model === null) {
            throw new NotFoundException('File not found');
        }

        return $model;
    }
}
