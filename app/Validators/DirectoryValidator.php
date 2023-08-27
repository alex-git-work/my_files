<?php

namespace App\Validators;

use App\Base\Validator;
use App\Models\Directory;

/**
 * Class DirectoryValidator
 * @package App\Validators
 */
class DirectoryValidator extends Validator
{
    protected array $requireKeys = [
        'name',
    ];

    /**
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        $this->required($this->except);

        if (!empty($this->errors)) {
            return false;
        }

        $this->addCleanData($this->except);

        if (!empty($this->entityId) && !empty($this->cleanData['parent_id'])) {
            $directory = Directory::findOne([
                'id' => $this->cleanData['parent_id'],
                'user_id' => $this->entityId,
            ]);

            if ($directory === null) {
                $this->addError('parent_id', 'Invalid parent_id');
            }
        }

        $this->end();

        return $this->result;
    }
}
