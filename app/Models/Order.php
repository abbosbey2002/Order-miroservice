<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = ['user_email', 'product_name', 'price', 'status'];

    /**
     * Возможные значения статуса.
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSED = 'processed';
    public const STATUS_SHIPPED = 'shipped';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSED,
            self::STATUS_SHIPPED,
        ];
    }

    public function setStatus($status)
    {
        if (in_array($status, self::getStatus())) {
            throw new \InvalidArgumentException("Недопустимый статус: {$status}");
        }

        $this->attributes['status'] = $status;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }
}
