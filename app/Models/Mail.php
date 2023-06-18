<?php

namespace App\Models;

use App\Base\Model;

/**
 * This is the model class for table "mail_queue".
 *
 * @property int $id
 * @property string $date_create
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $message
 * @property int $status
 * @property int $attempt
 *
 * @property-read string $statusText
 */
class Mail extends Model
{
    public const STATUS_NEW = 0;
    public const STATUS_PROCESSED = 1;
    public const STATUS_SENT = 2;
    public const STATUS_BLOCKED = 3;

    public const STATUSES = [
        self::STATUS_NEW => 'Waiting',
        self::STATUS_PROCESSED => 'Processing',
        self::STATUS_SENT => 'Sent',
        self::STATUS_BLOCKED => 'Blocked',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'mail_queue';
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return self::STATUSES[$this->status];
    }
}
