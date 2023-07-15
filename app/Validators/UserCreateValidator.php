<?php

namespace App\Validators;

use App\Base\Validator;
use App\Models\Role;
use App\Models\User;

/**
 * Class UserCreateValidator
 * @package App\Validators
 */
class UserCreateValidator extends Validator
{
    public array $keys = [
        'name',
        'email',
        'password',
    ];

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $this->required();
        $this->addCleanData();

        if (empty($this->cleanData['email']) || !$this->email($this->cleanData['email'])) {
            $this->addError('email', 'Incorrect email address');
        }

        if (isset($this->cleanData['email']) && !$this->emailUnique($this->cleanData['email'])) {
            $this->addError('email', 'Email already exists');
        }

        if (isset($this->cleanData['role_id']) && !in_array($this->cleanData['role_id'], Role::ROLES)) {
            $this->addError('email', 'Role not found');
        }

        $this->end();

        if ($this->result) {
            $this->cleanData['email'] = mb_strtolower($this->cleanData['email']);
        }

        return $this->result;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function emailUnique(string $value): bool
    {
        if ($this->id !== null) {
            $user = User::findOne($this->id);
            return $user->email === $value || User::findOne(['email' => $value]) === null;
        }

        return User::findOne(['email' => $value]) === null;
    }
}
