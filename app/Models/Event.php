<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'name',
        'venue',
        'location',
        'district_id',
        'province_id',
        'country_id',
        'capacity',
        'ticket_pricing',
        'description',
        'contact_info',
        'start_date',
        'end_date',
        'category',
        'status',
        'organizer',
        'image_url',
        'tickets_sold',
        'currency',
        'created_by'
    ];

    protected $casts = [
        'ticket_pricing' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }
}
