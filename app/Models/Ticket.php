<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
       "user_id",
       "event_id",
       "status",
       "price",
       "quantity",
       "total_price",
       "deadline",
       "cancellation_reason",
       "qr_code",
   ];
   public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

   public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}