<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
