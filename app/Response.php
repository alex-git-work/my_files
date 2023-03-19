<?php

namespace App;

use App\Base\BaseObject;

/**
 * Class Response
 * @package App
 */
final class Response extends BaseObject
{
    public array $data = [];

    private array $headers = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {

    }

    /**
     * @return void
     */
    public function send(): void
    {

    }
}
