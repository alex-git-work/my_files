<?php

namespace App\Controllers;

use App\App;
use App\Base\Controller;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidateException;
use App\Models\User;
use App\Response;
use App\Traits\Authorization;
use App\Validators\UserCreateValidator;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends Controller
{
    use Authorization;

    protected const HIDDEN_USER_ATTRIBUTES = [
        'password',
        'token',
        'restoration_key',
        'key_exp_date'
    ];

    /**
     * @return Response
     * @throws UnauthorizedException
     */
    public function index(): Response
    {
        $this->authCheck(true);

        $users = User::findAll();

        return $this->asJson([
            'users' => $users
                ? array_map(fn(User $u) => $u->getAttributes(except: self::HIDDEN_USER_ATTRIBUTES), $users)
                : $users
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
        $currentUserId = $this->authCheck(true);

        if ($currentUserId !== $id) {
            $user = $this->findModel($id);
        } else {
            $user = $this->findModel($currentUserId);
        }

        $data = App::$request->parsedBody;

        if (empty($data)) {
            throw new ValidateException('Empty request');
        }

        $validator = new UserCreateValidator($data, $user->id, ['keys' => array_keys($data)]);
        $validator->isAdminSection = true;
        $validator->setRequiredKeys([
            'name',
            'email',
            'password',
            'role_id',
        ]);

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        $attributes = $validator->validatedData;

        $user->setAttributes($attributes);
        $user->password = password_hash($attributes['password'], PASSWORD_DEFAULT);
        $user->updated_at = now();
        $user->save();

        return $this->asJson([
            'message' => 'User updated',
            'userId' => $user->id,
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
        $this->authCheck(true);

        $user = $this->findModel($id);

        return $this->asJson([
            'user' => $user->getAttributes(except: self::HIDDEN_USER_ATTRIBUTES)
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
        $currentUserId = $this->authCheck(true);

        if ($currentUserId !== $id) {
            $user = $this->findModel($id);
        } else {
            $user = $this->findModel($currentUserId);
        }

        $user->delete();

        return $this->asJson([
            'message' => 'User deleted successfully',
            'userId' => $user->id,
        ]);
    }

    /**
     * @param int $id
     * @return User
     * @throws NotFoundException
     */
    protected function findModel(int $id): User
    {
        $model = User::findOne($id);

        if ($model === null) {
            throw new NotFoundException('User not found');
        }

        return $model;
    }
}
