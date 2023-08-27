<?php

namespace App\Validators;

use App\Base\Validator;

/**
 * Class UserLoginValidator
 * @package App\Validators
 */
class UserLoginValidator extends Validator
{
    public array $keys = [
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
        $this->end();

        return $this->result;
    }
}
