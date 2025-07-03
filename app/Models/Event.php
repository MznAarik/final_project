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
        'event_category',
        'ticket_category_price',
        'tickets_sold',
        'capacity',
        'description',
        'contact_info',
        'start_date',
        'end_date',
        'status',
        'organizer',
        'image_url',
        'currency',
        'created_by',
        'updated_by',
        'popularity_score',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
