<?php

namespace App\Validators;

use App\Base\Validator;
use App\Models\Directory;

/**
 * Class FileValidator
 * @package App\Validators
 */
class FileValidator extends Validator
{
    protected array $requireKeys = [
        'directory_id',
        'real_name',
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

        if (!empty($this->entityId) && !empty($this->cleanData['directory_id'])) {
            $directory = Directory::findOne([
                'id' => $this->cleanData['directory_id'],
                'user_id' => $this->entityId
            ]);

            if ($directory === null) {
                $this->addError('directory_id', 'Invalid directory_id');
            }
        }

        $this->end();

        return $this->result;
    }
}
