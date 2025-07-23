<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'event_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'payment_date',
        'created_by',
        'updated_by',
        'delete_flag',
        'gateway'
    ];
    public function user()
    {
        return $this->belongsTo('User::class');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
