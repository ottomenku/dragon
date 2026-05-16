<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const TYPE_PAYMENT = 'payment';

    public const TYPE_REFUND = 'refund';

    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'type',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_REFUND => 'Visszatérítés',
            default => 'Fizetés',
        };
    }
}
