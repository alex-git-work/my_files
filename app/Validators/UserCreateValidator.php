<?php

namespace App\Validators;

use App\Base\Validator;
use App\Models\User;

/**
 * Class UserCreateValidator
 * @package App\Validators
 */
class UserCreateValidator extends Validator
{
    protected array $requireKeys = [
        'name',
        'email',
        'password',
    ];

    protected ?User $user = null;

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->entityId !== null) {
            $this->user = User::findOne($this->entityId);
        }

        $this->required();

        if (!empty($this->errors)) {
            return false;
        }

        $this->addCleanData();

        if (empty($this->cleanData['email']) || !$this->email($this->cleanData['email'])) {
            $this->addError('email', 'Incorrect email address');
        }

        if (isset($this->cleanData['email']) && !$this->emailUnique($this->cleanData['email'])) {
            $this->addError('email', 'Email already exists');
        }

        if (isset($this->cleanData['role_id'])) {
            if ($this->user === null || !$this->isAdminSection) {
                $this->addError('role_id', 'Unknown param role_id');
            }

            if (!in_array($this->cleanData['role_id'], User::ROLES)) {
                $this->addError('email', 'Role not found');
            }
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
        if ($this->user !== null) {
            return $this->user->email === $value || User::findOne(['email' => $value]) === null;
        }

        return User::findOne(['email' => $value]) === null;
    }
}
