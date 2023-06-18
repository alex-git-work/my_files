<?php

namespace App\Controllers;

use App\App;
use App\Base\Controller;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidateException;
use App\Helpers\StringHelper;
use App\Models\Mail;
use App\Models\User;
use App\Response;
use App\Traits\Authorization;
use App\Validators\UserCreateValidator;
use App\Validators\UserLoginValidator;
use Exception;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends Controller
{
    use Authorization;

    /**
     * @return Response
     */
    public function index(): Response
    {
        $users = User::findAll();

        $usersInfo = array_map(fn(User $u) => $u->getAttributes(['name', 'email', 'created_at']), $users);

        return $this->asJson([
            'users' => $users ? $usersInfo : $users
        ]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws ValidateException
     * @throws Exception
     */
    public function create(): Response
    {
        $data = App::$request->parsedBody;

        $validator = new UserCreateValidator($data);

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        $userData = $validator->validatedData;

        if (!$this->store($userData)) {
            throw new InvalidConfigException('Something goes wrong...');
        }

        return $this->asJson([
            'message' => 'User created',
            'userId' => App::$db->lastInsertID,
        ]);
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function store(array $data): bool
    {
        $user = new User($data);
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);
        $user->role_id = 2;
        $user->token = null;
        $user->last_request = null;
        $user->created_at = now();

        return $user->save();
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function show(int $id): Response
    {
        $user = $this->findModel($id);

        return $this->asJson([
            'user' => $user->getAttributes(['name', 'email', 'created_at'])
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function edit(int $id): Response
    {
        $currentUserId = $this->authCheck();

        if ($currentUserId !== $id) {
            throw new ValidateException('This action is not allowed');
        }

        $data = App::$request->parsedBody;
        $validator = new UserCreateValidator($data, $id);

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        $userData = $validator->validatedData;

        if (!$this->update($id, $userData)) {
            throw new InvalidConfigException('Something goes wrong...');
        }

        return $this->asJson([
            'message' => 'User updated',
            'userId' => $id,
        ]);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     * @throws NotFoundException
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->findModel($id);
        $user->setAttributes($data);
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->updated_at = now();

        return $user->save();
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ValidateException
     */
    public function destroy(int $id): Response
    {
        $currentUserId = $this->authCheck();

        if ($currentUserId !== $id) {
            throw new ValidateException('This action is not allowed');
        }

        $user = $this->findModel($id);
        $user->delete();

        return $this->asJson([
            'message' => 'User deleted successfully',
            'userId' => $user->id,
        ]);
    }

    /**
     * @return Response
     * @throws UnauthorizedException
     * @throws ValidateException
     * @throws Exception
     */
    public function login(): Response
    {
        $validator = new UserLoginValidator([
            'email' => App::$request->email,
            'password' => App::$request->password,
        ]);

        if (!$validator->validate()) {
            throw new ValidateException($validator->firstError);
        }

        $credentials = $validator->validatedData;

        $user = User::findOne(['email' => $credentials['email']]);

        if ($user === null || !password_verify($credentials['password'], $user->password)) {
            throw new UnauthorizedException('Invalid email or password');
        }

        if ($user->isAuthorized()) {
            $message = 'User already logged in';
        } else {
            $user->login();
            $message = 'User logged in successfully';
        }

        return $this->asJson([
            'message' => $message,
            'userId' => $user->id,
            'token' => $user->token,
        ]);
    }

    /**
     * @return Response
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function logout(): Response
    {
        $currentUserId = $this->authCheck();

        $user = User::findOne($currentUserId);

        if ($user === null) {
            throw new NotFoundException('User not found');
        }

        $user->logout();

        return $this->asJson([
            'message' => 'User logged out successfully',
            'userId' => $user->id,
        ]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws ValidateException
     * @throws Exception
     */
    public function resetPassword(): Response
    {
        $data = App::$request->parsedBody;

        if ($data === null || !isset($data['email'])) {
            throw new InvalidConfigException('email is not specified');
        }

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        if (!$email) {
            throw new ValidateException('email is not valid');
        }

        $user = User::findOne(['email' => $email]);

        if ($user === null) {
            throw new NotFoundException('User not found');
        }

        $user->restoration_key = StringHelper::generateRandomString(64);
        $user->key_exp_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $user->save();

        $message = 'You have been requested for password reset.' . PHP_EOL;
        $message .= 'Your password reset link - ' . App::$params['main_url'] . '/user/password_change/' . $user->restoration_key . PHP_EOL;
        $message .= 'Warning! Link expired at ' . $user->key_exp_date;

        $mail = new Mail([
            'date_create' => now(),
            'from' => App::$params['email'],
            'to' => $user->email,
            'subject' => 'Password reset at ' . App::$params['main_url'],
            'message' => $message,
            'status' => Mail::STATUS_NEW,
            'attempt' => 0,

        ]);
        $mail->save();

        return $this->asJson([
            'message' => 'Your instructions sent to the specified email',
            'email' => $email,
        ]);
    }

    /**
     * @param string $key
     * @return Response
     * @throws ValidateException
     * @throws Exception
     */
    public function passwordChange(string $key): Response
    {
        $user = User::findOne(['restoration_key' => $key]);

        if ($user === null) {
            throw new ValidateException('Incorrect restoration key provided');
        }

        if (now() > $user->key_exp_date) {
            throw new ValidateException('Restoration key expired');
        }

        $newPassword = StringHelper::generateRandomString(8);

        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->restoration_key = null;
        $user->key_exp_date = null;
        $user->save();

        return $this->asJson([
            'message' => 'Your password has been reset successfully',
            'newPassword' => $newPassword,
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
